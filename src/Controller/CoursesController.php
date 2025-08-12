<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Courses Controller
 *
 * @property \App\Model\Table\CoursesTable $Courses
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CoursesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow unauthenticated users to access the index and view actions
        $this->Authentication->addUnauthenticatedActions(['index', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Fetch all active courses and their active modules
        $courses = $this->Courses->find()
            ->where(['Courses.is_active' => true])
            ->contain(['Modules' => function ($q) {
                $q->select($q->getRepository());
                $q->select(['lesson_count' => $q->func()->count('Contents.id')])
                  ->leftJoinWith('Contents')
                  ->groupBy(['Modules.id'])
                  ->where(['Modules.is_active' => true])
                  ->orderBy(['Modules.order' => 'ASC']);
                return $q;
            }])
            ->orderBy(['Courses.title' => 'ASC'])
            ->all();

        // Calculate derived data and determine item status for the user
        $user = $this->Authentication->getIdentity();
        $userId = $user ? $user->getIdentifier() : null;
        $cartItemsMap = [];
        $itemStatuses = []; // ['Course' => [courseId => status], 'Module' => [moduleId => status]]

        if ($userId) {
            $CartsTable = $this->fetchTable('Carts');
            $CartItemsTable = $this->fetchTable('CartItems');

            // Get user's cart ID
            $cart = $CartsTable->find()
                ->select(['id'])
                ->where(['user_id' => $userId])
                ->first();

            // Fetch cart items if cart exists
            if ($cart) {
                $cartItems = $CartItemsTable->find()
                    ->where(['cart_id' => $cart->id])
                    ->all();
                foreach ($cartItems as $item) {
                    $cartItemsMap[$item->item_type][$item->item_id] = true;
                }
            }
        }

        foreach ($courses as $course) {
            // Calculate total module price
            $totalModulePrice = 0;
            if (!empty($course->modules)) {
                foreach ($course->modules as $module) {
                    $totalModulePrice += $module->price ?? 0;
                }
            }
            $course->total_module_price = $totalModulePrice;

            // Determine Course Status
            if ($userId) {
                if ($CartItemsTable->isItemOwnedByUser($userId, $course->id, 'Course')) {
                    $itemStatuses['Course'][$course->id] = 'owned';
                } elseif (isset($cartItemsMap['Course'][$course->id])) {
                    $itemStatuses['Course'][$course->id] = 'in_cart';
                } else {
                    $itemStatuses['Course'][$course->id] = 'available';
                }
            } else {
                $itemStatuses['Course'][$course->id] = 'available'; // Default for guests
            }

            // Determine Module Statuses
            if (!empty($course->modules)) {
                foreach ($course->modules as $module) {
                    if ($userId) {
                        // $CartItemsTable is loaded above if $userId is true (line 53)
                        if ($CartItemsTable->isItemOwnedByUser($userId, $module->id, 'Module')) {
                            $itemStatuses['Module'][$module->id] = 'owned';
                        } elseif (isset($cartItemsMap['Module'][$module->id])) {
                            $itemStatuses['Module'][$module->id] = 'in_cart';
                        } else {
                            $itemStatuses['Module'][$module->id] = 'available';
                        }
                    } else {
                        $itemStatuses['Module'][$module->id] = 'available';
                    }
                }
            }
        }

        $this->set(compact('courses', 'itemStatuses'));
    }

    /**
     * View method
     *
     * @param string|null $id Course id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $course = $this->Courses->get(
            $id,
            contain: [
                'Modules' => function ($q) {
                    return $q->where(['Modules.is_active' => true])
                        ->orderBy(['Modules.order' => 'ASC']); // Changed order() to orderBy()
                }
            ]
        );

        // Determine Course Status for the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user ? $user->getIdentifier() : null;
        $courseStatus = 'available'; // Default status
        $hasPurchased = false; // Keep this for content access checks

        if ($userId) {
            // Check if user role allows viewing purchased content (existing logic)
            if (!in_array($user->role, ['admin', 'student'])) {
                 // Check if they own it via OrderItems before denying access
                 $CartItemsTable = $this->fetchTable('CartItems'); // Need this table
                 if (!$CartItemsTable->isItemOwnedByUser($userId, $course->id, 'Course')) {
                    $this->Flash->error(__('You need to purchase this course to view its content.'));
                    // Redirect or just set status? Let's set status and let the template decide.
                    // return $this->redirect(['controller' => 'Courses', 'action' => 'index']);
                 } else {
                     $hasPurchased = true; // They own it, allow access even if role isn't student/admin yet
                 }
            } else {
                 // Admins/Students might own it or not, still need to check ownership for button status
                 $CartItemsTable = $this->fetchTable('CartItems'); // Need this table
                 $hasPurchased = $CartItemsTable->isItemOwnedByUser($userId, $course->id, 'Course');
            }


            // Determine button status: owned, in_cart, or available
            if ($hasPurchased) {
                $courseStatus = 'owned';
            } else {
                $CartsTable = $this->fetchTable('Carts');
                $CartItemsTable = $this->fetchTable('CartItems'); // Already fetched above if user exists

                $cart = $CartsTable->find()->where(['user_id' => $userId])->first();
                if ($cart) {
                    $isInCart = $CartItemsTable->exists([
                        'cart_id' => $cart->id,
                        'item_id' => $course->id,
                        'item_type' => 'Course',
                    ]);
                    if ($isInCart) {
                        $courseStatus = 'in_cart';
                    }
                }
                // If not owned and not in cart, status remains 'available'
            }
        }
        // Note: $hasPurchased is still used below for progress calculation logic,
        // but $courseStatus is specifically for the Add to Cart button state.

        // --- Determine Individual Module Statuses (owned, in_cart, available) ---
        $individualModuleItemStatuses = [];
        if ($userId && !empty($course->modules)) {
            $CartItemsTable = $this->fetchTable('CartItems'); // Ensure it's loaded
            $CartsTable = $this->fetchTable('Carts'); // For cart check

            $userCart = $CartsTable->find()->where(['user_id' => $userId])->first();
            $userCartItemsMap = [];
            if ($userCart) {
                $cartItems = $CartItemsTable->find()
                    ->where(['cart_id' => $userCart->id, 'item_type' => 'Module']) // Only fetch modules for this check
                    ->all();
                foreach ($cartItems as $item) {
                    $userCartItemsMap[$item->item_id] = true;
                }
            }

            foreach ($course->modules as $module) {
                if ($CartItemsTable->isItemOwnedByUser($userId, $module->id, 'Module')) {
                    $individualModuleItemStatuses[$module->id] = 'owned';
                } elseif (isset($userCartItemsMap[$module->id])) {
                    $individualModuleItemStatuses[$module->id] = 'in_cart';
                } else {
                    $individualModuleItemStatuses[$module->id] = 'available';
                }
            }
        } elseif (!empty($course->modules)) { // Guest user
            foreach ($course->modules as $module) {
                $individualModuleItemStatuses[$module->id] = 'available';
            }
        }
  
        // --- NEW Progress Calculation ---
        $courseProgressData = [
            'courseStatus' => 'not_started',
            'moduleStatuses' => [], // [moduleId => status]
            'modulePercentages' => [], // [moduleId => percentage]
            'contentStatuses' => [], // [contentId => status]
            'coursePercentage' => 0,
            'lastAccessedContentUrl' => null,
            'firstUncompletedContentUrl' => null, // Added for fallback
        ];
 
        if ($user && !empty($course->modules)) {
            $userId = $user->getIdentifier();
            $UserCourseProgressTable = $this->fetchTable('UserCourseProgress');
            $UserModuleProgressTable = $this->fetchTable('UserModuleProgress');
            $UserContentProgressTable = $this->fetchTable('UserContentProgress');
            $ContentsTable = $this->fetchTable('Contents'); // Needed for calculations
 
            // 1. Get Overall Course Progress
            $courseProgress = $UserCourseProgressTable->find() // Use variable
                ->where(['user_id' => $userId, 'course_id' => $id])
                ->contain(['LastAccessedContents']) // Contain the last accessed content entity
                ->first();
 
            $courseProgressData['courseStatus'] = $courseProgress ? $courseProgress->status : 'not_started';
 
            // 2. Get Module Statuses
            $moduleIds = collection($course->modules)->extract('id')->toList();
            if (!empty($moduleIds)) {
                $courseProgressData['moduleStatuses'] = $UserModuleProgressTable->find('list', [ // Use variable
                    'keyField' => 'module_id',
                    'valueField' => 'status',
                ])
                ->where(['user_id' => $userId, 'module_id IN' => $moduleIds])
                ->toArray();
            }
 
            // 3. Get Content Statuses & Calculate Percentages
            $allCourseContentIdsQuery = $ContentsTable->find('list', ['keyField' => 'id', 'valueField' => 'id']) // Use variable
                ->innerJoinWith('Modules', function ($q) use ($id) {
                    return $q->where(['Modules.course_id' => $id, 'Modules.is_active' => true]);
                })
                ->where(['Contents.is_active' => true]);
 
            $allCourseContentIds = $allCourseContentIdsQuery->toArray();
            $totalCourseContents = count($allCourseContentIds);
            $completedCourseContentsCount = 0;
 
            if ($totalCourseContents > 0) {
                $completedContentIds = $UserContentProgressTable->find('list', [ // Use variable
                    'keyField' => 'content_id',
                    'valueField' => 'content_id' // Just need the IDs
                ])
                ->where(['user_id' => $userId, 'content_id IN' => $allCourseContentIds])
                ->toArray();
 
                $completedCourseContentsCount = count($completedContentIds);
 
                // Populate contentStatuses map
                foreach ($allCourseContentIds as $contentId) {
                     $courseProgressData['contentStatuses'][$contentId] = isset($completedContentIds[$contentId]) ? 'completed' : 'not_started';
                }
 
                // Calculate overall course percentage
                $courseProgressData['coursePercentage'] = round(($completedCourseContentsCount / $totalCourseContents) * 100);
            }
 
            // Calculate individual module percentages and find first uncompleted content URL
            $firstUncompletedContent = null;
            foreach ($course->modules as $module) {
                $moduleContentIds = $ContentsTable->find('list', ['keyField' => 'id', 'valueField' => 'id']) // Use variable
                    ->where(['module_id' => $module->id, 'is_active' => true])
                    ->order(['Contents.order' => 'ASC']) // Ensure order for finding first uncompleted
                    ->toArray();
 
                $totalModuleContents = count($moduleContentIds);
                $completedModuleContentsCount = 0;
                if ($totalModuleContents > 0) {
                    foreach ($moduleContentIds as $contentId) {
                        if (isset($courseProgressData['contentStatuses'][$contentId]) && $courseProgressData['contentStatuses'][$contentId] === 'completed') {
                            $completedModuleContentsCount++;
                        } elseif (!$firstUncompletedContent) {
                            // Found the first uncompleted content item in sequence
                            $firstUncompletedContent = $ContentsTable->get($contentId); // Use variable
                        }
                    }
                    $courseProgressData['modulePercentages'][$module->id] = round(($completedModuleContentsCount / $totalModuleContents) * 100);
                } else {
                    $courseProgressData['modulePercentages'][$module->id] = 0;
                }
                 // Attach user_status to module entity for view convenience (using fetched status)
                 $module->user_status = $courseProgressData['moduleStatuses'][$module->id] ?? 'not_started';
            }
 
            // 4. Determine Last Accessed / Next Content URL
            if ($courseProgress && $courseProgress->last_accessed_content_id && $courseProgress->last_accessed_content) {
                 // Generate URL for the last accessed content item
                 $courseProgressData['lastAccessedContentUrl'] = \Cake\Routing\Router::url([
                     'controller' => 'Modules',
                     'action' => 'content',
                     $courseProgress->last_accessed_content->module_id, // Need module_id
                     $courseProgress->last_accessed_content_id
                 ]);
            }
            // Determine the fallback URL (first uncompleted)
            if ($firstUncompletedContent) {
                 $courseProgressData['firstUncompletedContentUrl'] = \Cake\Routing\Router::url([
                     'controller' => 'Modules',
                     'action' => 'content',
                     $firstUncompletedContent->module_id,
                     $firstUncompletedContent->id
                 ]);
            } elseif (!empty($course->modules) && $totalCourseContents > 0) {
                 // If all content is completed, maybe link back to course view or first item?
                 // For now, let's link to the first content item of the first module as a default if all complete.
                 $firstModule = $course->modules[0]; // Assuming modules are ordered
                 $firstContentOfCourse = $ContentsTable->find() // Use variable
                    ->where(['module_id' => $firstModule->id, 'is_active' => true])
                    ->order(['Contents.order' => 'ASC'])
                    ->first();
                 if ($firstContentOfCourse) {
                      $courseProgressData['firstUncompletedContentUrl'] = \Cake\Routing\Router::url([
                         'controller' => 'Modules',
                         'action' => 'content',
                         $firstContentOfCourse->module_id,
                         $firstContentOfCourse->id
                     ]);
                 }
            }
 
        } else {
            // Ensure modules have a default status for guests
            if (!empty($course->modules)) {
                foreach ($course->modules as $module) {
                    $module->user_status = 'not_started';
                    $courseProgressData['modulePercentages'][$module->id] = 0;
                }
            }
        }
        // --- End NEW Progress Calculation ---
 
        $this->set(compact('course', 'hasPurchased', 'courseProgressData', 'courseStatus', 'individualModuleItemStatuses'));
    }

    /**
     * Purchase method
     *
     * @param string|null $id Course id.
     * @return \Cake\Http\Response|null|void Redirects on successful purchase, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function purchase($id = null)
    {
        // If accessed via GET (likely post-login redirect), redirect to the view page
        if ($this->request->is('get')) {
            $this->log('Redirecting GET request for purchase to view page for course ID: ' . $id, 'debug');
            // Optionally add a flash message explaining why they landed here, though maybe not necessary
            // $this->Flash->info(__('Please review the course details before purchasing.'));
            return $this->redirect(['action' => 'view', $id]);
        }

        // If not GET, proceed with POST-only logic
        try {
            // This will now only be checked for non-GET requests (implicitly POST, PUT, DELETE etc.)
            $this->request->allowMethod(['post']);
        } catch (\Cake\Http\Exception\MethodNotAllowedException $e) {
            // Log the exception specifically for non-GET, non-POST methods
            $this->log('MethodNotAllowedException in CoursesController::purchase for method: ' . $this->request->getMethod() . ' course ID: ' . $id, 'error');
            $this->Flash->error(__('Invalid request method to purchase this course. Use the purchase button on the course page.'));
            return $this->redirect(['action' => 'view', $id]);
            // throw $e; // Or re-throw
        }

        // --- Existing POST logic remains below ---
        $course = $this->Courses->get($id, contain: []);
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();
        
        // Check if already purchased
        $existingPurchase = $this->Courses->Purchases->find()
            ->where([
                'user_id' => $userId,
                'course_id' => $id,
                'payment_status' => 'completed',
            ])
            ->first();
        
        if ($existingPurchase) {
            $this->Flash->success(__('You have already purchased this course.'));
            return $this->redirect(['action' => 'view', $id]);
        }
        
        // Create a new purchase record
        $purchase = $this->Courses->Purchases->newEmptyEntity();
        $purchase->user_id = $userId;
        $purchase->course_id = $id;
        $purchase->amount = $course->price;
        $purchase->payment_status = 'pending'; // Will be updated after payment processing
        
        if ($this->Courses->Purchases->save($purchase)) {
            // In a real application, redirect to payment gateway
            // For this prototype, we'll simulate successful payment
            $purchase->payment_status = 'completed';
            $this->Courses->Purchases->save($purchase);

            if ($user->role === 'member') {
                $UsersTable = $this->fetchTable('Users');
                $userRole = $UsersTable->get($userId);
                $userRole->role = 'student';
                if ($UsersTable->save($userRole)) {
                    $this->Authentication->setIdentity($userRole); 
                }
            }
            
            $this->Flash->success(__('Course purchased successfully.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
        
        $this->Flash->error(__('Unable to process your purchase. Please try again.'));
        return $this->redirect(['action' => 'view', $id]);
    }
}