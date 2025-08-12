<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Log\Log; // Add this line for logging

/**
 * Modules Controller
 *
 * @property \App\Model\Table\ModulesTable $Modules
 * @method \App\Model\Entity\Module[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ModulesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow unauthenticated users to access the view action
        $this->Authentication->addUnauthenticatedActions(['view']);
    }

    /**
     * View method
     *
     * @param string|null $id Module id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $module = $this->Modules->get(
            $id,
            contain: [
                'Courses',
                'Contents' => function ($q) {
                    return $q->where(['Contents.is_active' => true])
                        ->order(['Contents.order' => 'ASC']);
                }
            ]
        );

        // Determine Module Status for the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user ? $user->getIdentifier() : null;
        $moduleStatus = 'available'; // Default status
        $hasPurchased = false; // Keep for content access/progress checks
        $ownsCourse = false; // Initialize ownsCourse
        $savingsPercentage = 0.0; // Initialize savingsPercentage

        if ($userId) {
            $CartItemsTable = $this->fetchTable('CartItems');

            // Check ownership via OrderItems (module or parent course)
            $ownsModule = $CartItemsTable->isItemOwnedByUser($userId, $module->id, 'Module');
            $ownsCourse = $module->course_id ? $CartItemsTable->isItemOwnedByUser($userId, $module->course_id, 'Course') : false;
            $hasPurchased = $ownsModule || $ownsCourse;

            // Determine button status: owned, in_cart, or available
            if ($hasPurchased) {
                $moduleStatus = 'owned';
            } else {
                $CartsTable = $this->fetchTable('Carts');
                $cart = $CartsTable->find()->where(['user_id' => $userId])->first();
                if ($cart) {
                    $isInCart = $CartItemsTable->exists([
                        'cart_id' => $cart->id,
                        'item_id' => $module->id,
                        'item_type' => 'Module',
                    ]);
                    if ($isInCart) {
                        $moduleStatus = 'in_cart';
                    }
                }
                // If not owned and not in cart, status remains 'available'
            }
        }
        // Note: $hasPurchased is still used below for progress calculation logic,
        // but $moduleStatus is specifically for the Add to Cart button state.

        // Calculate savings percentage if module is part of a course and user doesn't own the course
        if ($module->course && !$ownsCourse) {
            $ModulesTable = $this->fetchTable('Modules');
            $allCourseModulesQuery = $ModulesTable->find()
                ->where(['course_id' => $module->course->id, 'is_active' => true]);

            $sumOfIndividualModulePrices = 0.0;
            // Iterate over the query result
            foreach ($allCourseModulesQuery as $courseModule) {
                $sumOfIndividualModulePrices += (float)$courseModule->price;
            }

            $coursePrice = (float)$module->course->price;

            if ($sumOfIndividualModulePrices > 0 && $sumOfIndividualModulePrices > $coursePrice) {
                $savingsPercentage = (($sumOfIndividualModulePrices - $coursePrice) / $sumOfIndividualModulePrices) * 100;
            }
        }

        // --- Fetch Module & Content Progress ---
        $moduleProgressData = [
            'moduleStatus' => 'not_started',
            'contentStatuses' => [], // [contentId => status]
            'modulePercentage' => 0,
        ];
 
        if ($user && $hasPurchased && !empty($module->contents)) { // Only fetch progress if purchased and has content
             $userId = $user->getIdentifier();
             $UserModuleProgressTable = $this->fetchTable('UserModuleProgress');
             $UserContentProgressTable = $this->fetchTable('UserContentProgress');
 
             // 1. Get Module Status
             $moduleProgress = $UserModuleProgressTable->find() // Use variable
                 ->where(['user_id' => $userId, 'module_id' => $id])
                 ->first();
             $moduleProgressData['moduleStatus'] = $moduleProgress ? $moduleProgress->status : 'not_started';
 
             // 2. Get Content Statuses & Calculate Percentage
             $moduleContentIds = collection($module->contents)->extract('id')->toList();
             $totalModuleContents = count($moduleContentIds);
             $completedModuleContentsCount = 0;
 
             if ($totalModuleContents > 0) {
                 $completedContentIds = $UserContentProgressTable->find('list', [ // Use variable
                     'keyField' => 'content_id',
                     'valueField' => 'content_id'
                 ])
                 ->where(['user_id' => $userId, 'content_id IN' => $moduleContentIds])
                 ->toArray();
 
                 $completedModuleContentsCount = count($completedContentIds);
 
                 // Populate contentStatuses map
                 foreach ($moduleContentIds as $contentId) {
                      $moduleProgressData['contentStatuses'][$contentId] = isset($completedContentIds[$contentId]) ? 'completed' : 'not_started';
                 }
 
                 // Calculate module percentage
                 $moduleProgressData['modulePercentage'] = round(($completedModuleContentsCount / $totalModuleContents) * 100);
             }
             // Attach content status to each content entity for view convenience
             foreach ($module->contents as $contentItem) {
                 $contentItem->user_status = $moduleProgressData['contentStatuses'][$contentItem->id] ?? 'not_started';
             }
        } else {
             // Set default status for content items if user is guest or module has no content
             if (!empty($module->contents)) {
                 foreach ($module->contents as $contentItem) {
                     $contentItem->user_status = 'not_started';
                 }
             }
        }
        // --- End Progress Fetch ---
 
        $this->set(compact('module', 'hasPurchased', 'moduleProgressData', 'moduleStatus', 'ownsCourse', 'savingsPercentage'));
    }

    /**
     * Purchase method
     *
     * @param string|null $id Module id.
     * @return \Cake\Http\Response|null|void Redirects on successful purchase, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function purchase($id = null)
    {
        $this->request->allowMethod(['post']);
        
        $module = $this->Modules->get($id, contain: ['Courses']);
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();
        
        // Check if already purchased this module
        $existingModulePurchase = $this->Modules->Purchases->find()
            ->where([
                'user_id' => $userId,
                'module_id' => $id,
                'payment_status' => 'completed',
            ])
            ->first();
        
        // Check if already purchased the parent course
        $existingCoursePurchase = $this->Modules->Purchases->find()
            ->where([
                'user_id' => $userId,
                'course_id' => $module->course_id,
                'payment_status' => 'completed',
            ])
            ->first();
        
        if ($existingModulePurchase || $existingCoursePurchase) {
            $this->Flash->success(__('You have already purchased this module or its parent course.'));
            return $this->redirect(['action' => 'view', $id]);
        }
        
        // Create a new purchase record
        $purchase = $this->Modules->Purchases->newEmptyEntity();
        $purchase->user_id = $userId;
        $purchase->module_id = $id;
        $purchase->amount = $module->price;
        $purchase->payment_status = 'pending'; // Will be updated after payment processing
        
        if ($this->Modules->Purchases->save($purchase)) {
            // In a real application, redirect to payment gateway
            // For this prototype, we'll simulate successful payment
            $purchase->payment_status = 'completed';
            $this->Modules->Purchases->save($purchase);
            if ($user->role === 'member') {
                Log::write('debug', 'Attempting to upgrade user ' . $userId . ' from member to student.');
                $UsersTable = $this->fetchTable('Users');
                $userEntity = $UsersTable->get($userId); // Use a different variable name to avoid confusion
                Log::write('debug', 'User ' . $userId . ' current role: ' . $userEntity->role);
                $userEntity->role = 'student';
                if ($UsersTable->save($userEntity)) {
                    Log::write('debug', 'User ' . $userId . ' role updated to student in DB.');
                    // Re-fetch the identity to ensure it's the most up-to-date version
                    $updatedUser = $UsersTable->get($userId);

                    // Log out the current user to clear any old session state
                    $this->Authentication->logout();

                    // Log the user back in with their updated entity
                    $this->Authentication->setIdentity($updatedUser);
                    
                    // Log the new identity from the component
                    $currentIdentity = $this->Authentication->getIdentity();
                    Log::write('debug', 'User ' . $userId . ' role in auth session after logout and setIdentity: ' . ($currentIdentity ? $currentIdentity->role : 'null'));
                    // The previous log 'Purchase Action - User ...' is now covered by the one above with more context.
                } else {
                    Log::write('error', 'Failed to update user ' . $userId . ' role to student. Errors: ' . json_encode($userEntity->getErrors()));
                }
            }
            
            $this->Flash->success(__('Module purchased successfully.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
        
        $this->Flash->error(__('Unable to process your purchase. Please try again.'));
        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Content method
     *
     * @param string|null $moduleId Module id.
     * @param string|null $contentId Content id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function content($moduleId = null, $contentId = null)
    {
        $module = $this->Modules->get(
            $moduleId,
            contain: [
                'Courses',
                'Contents' => function ($q) {
                    return $q->where(['Contents.is_active' => true])
                        ->order(['Contents.order' => 'ASC']);
                }
            ]
        );

        $content = $this->Modules->Contents->get($contentId, contain: []);
        
        // Verify content belongs to this module
        if ($content->module_id != $moduleId) {
            $this->Flash->error(__('Invalid content.'));
            return $this->redirect(['action' => 'view', $moduleId]);
        }

        // Check if user has purchased this module or its parent course
        $hasPurchased = false;
        $user = $this->Authentication->getIdentity();

        if ($user) {
            Log::write('debug', 'Content Action - User ' . $user->getIdentifier() . ' attempting to access content. Role from Auth: ' . $user->role);
        } else {
            Log::write('debug', 'Content Action - Unauthenticated user attempting to access content.');
        }
        
        if ($user) {
            if (!in_array($user->role, ['admin', 'student'])) {
                $this->Flash->error(__('You are not able to access this content. Please purchase the module to access it!'));
                return $this->redirect(['controller' => 'Courses', 'action' => 'index']);
            }

            $userId = $user->getIdentifier();
            
            // Check for module purchase
            $modulePurchase = $this->Modules->Purchases->find()
                ->where([
                    'user_id' => $userId,
                    'module_id' => $moduleId,
                    'payment_status' => 'completed',
                ])
                ->first();
            
            // Check for course purchase
            $coursePurchase = $this->Modules->Purchases->find()
                ->where([
                    'user_id' => $userId,
                    'course_id' => $module->course_id,
                    'payment_status' => 'completed',
                ])
                ->first();
            
            $hasPurchased = !empty($modulePurchase) || !empty($coursePurchase);
        }

        // If not purchased, redirect to module view
        if (!$hasPurchased && $user->role !== 'admin') {
            $this->Flash->error(__('You need to purchase this module to access its content.'));
            return $this->redirect(['action' => 'view', $moduleId]);
        }

        // --- Fetch Content Progress Status ---
        $contentStatus = 'not_started'; // Default
        if ($user && $hasPurchased) {
            $userId = $user->getIdentifier();
            $UserContentProgressTable = $this->fetchTable('UserContentProgress'); // Use fetchTable
            $contentProgress = $UserContentProgressTable->find() // Use variable
                ->where(['user_id' => $userId, 'content_id' => $contentId])
                ->first();
            if ($contentProgress) {
                $contentStatus = $contentProgress->status; // Should be 'completed' if record exists
            }
        }
        // Attach status to content entity for view convenience
        $content->user_status = $contentStatus;
        // --- End Content Progress Fetch ---

        // Fetch module progress data as well for the sidebar progress bar
        // This duplicates some logic from the view action, consider refactoring to a component/helper later
        $moduleProgressData = [
            'modulePercentage' => 0,
            'contentStatuses' => [], // Needed for sidebar item status
        ];
        if ($user && $hasPurchased && !empty($module->contents)) {
            $userId = $user->getIdentifier(); // Already defined above
            // We already fetched UserContentProgressTable earlier in this action (line 255)
            // No need to load/fetch again if $UserContentProgressTable variable is still in scope.
            // Let's assume it is. If not, we'd use:
            // $UserContentProgressTable = $this->fetchTable('UserContentProgress');

            $moduleContentIds = collection($module->contents)->extract('id')->toList();
            $totalModuleContents = count($moduleContentIds);
            $completedModuleContentsCount = 0;

            if ($totalModuleContents > 0) {
                $completedContentIds = $UserContentProgressTable->find('list', [ // Use variable
                    'keyField' => 'content_id',
                    'valueField' => 'content_id'
                ])
                ->where(['user_id' => $userId, 'content_id IN' => $moduleContentIds])
                ->toArray();

                $completedModuleContentsCount = count($completedContentIds);

                // Populate contentStatuses map for sidebar
                foreach ($moduleContentIds as $mcId) {
                     $moduleProgressData['contentStatuses'][$mcId] = isset($completedContentIds[$mcId]) ? 'completed' : 'not_started';
                }

                $moduleProgressData['modulePercentage'] = round(($completedModuleContentsCount / $totalModuleContents) * 100);
            }
        }

        // Determine previous and next content
        $prevContent = null;
        $nextContent = null;
        $contents = $module->contents; // Already ordered by 'order' from the initial query
        
        if (!empty($contents)) {
            $currentContentIndex = -1;
            foreach ($contents as $index => $moduleContent) {
                if ($moduleContent->id === $content->id) {
                    $currentContentIndex = $index;
                    break;
                }
            }

            if ($currentContentIndex !== -1) {
                if ($currentContentIndex > 0) {
                    $prevContent = $contents[$currentContentIndex - 1];
                }
                if ($currentContentIndex < count($contents) - 1) {
                    $nextContent = $contents[$currentContentIndex + 1];
                }
            }
        }

        $this->set(compact('module', 'content', 'moduleProgressData', 'prevContent', 'nextContent'));
    }
}