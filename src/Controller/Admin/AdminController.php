<?php
declare(strict_types=1);

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Log\Log;
use Cake\Utility\Inflector; // Added for humanizing slugs
use Cake\Filesystem\Folder; // Added for directory operations
use Cake\Filesystem\File;   // Ensure this line exists for file operations
use Cake\Collection\Collection;

/**
 * Admin Controller
 */
class AdminController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->viewBuilder()->setLayout('jenbury'); // Set layout for Admin prefix
        $this->loadComponent('Upload');
    }

    /**
     * beforeFilter method
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        Log::debug('AdminController::beforeFilter() reached for URL: ' . $this->request->getRequestTarget() . ' with prefix: ' . ($this->request->getParam('prefix') ?? 'none'));
        parent::beforeFilter($event);
        // Only admin users can access these actions
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->role !== 'admin') {
            $this->Flash->error(__('You are not authorized to access that location.'));
            return $this->redirect('/Error/error400');
        }

        // Disable FormProtection for the AJAX action
        if ($this->request->getParam('action') === 'updateModuleOrder') {
            $this->FormProtection->setConfig('validate', false);
        }
    }

    /**
     * Dashboard method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function dashboard()
    {
        // Load required models
        $users = $this->fetchTable('Users');
        $courses = $this->fetchTable('Courses');
        $modules = $this->fetchTable('Modules');
        $purchases = $this->fetchTable('Purchases');
        
        // Get counts for dashboard
        $userCount = $users->find()->count();
        $courseCount = $courses->find()->count();
        $moduleCount = $modules->find()->count();
        $purchaseCount = $purchases->find()->where(['payment_status' => 'completed'])->count();
        $revenueTotal = $purchases->find()
            ->where(['payment_status' => 'completed'])
            ->select(['total' => $purchases->find()->func()->sum('amount')])
            ->first()
            ->get('total') ?? 0;
        
        // Get recent purchases
        $recentPurchases = $purchases->find()
            ->contain(['Users', 'Courses', 'Modules'])
            ->order(['Purchases.created' => 'DESC'])
            ->limit(10)
            ->all();
        
        $this->set(compact('userCount', 'courseCount', 'moduleCount', 'purchaseCount', 'revenueTotal', 'recentPurchases'));
    }
    
    /**
     * Manage Courses method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function manageCourses()
    {
        $courses = $this->fetchTable('Courses');
        
        $coursesList = $courses->find()
            ->contain(['Modules'])
            ->all();
        
        $this->set(compact('coursesList'));
    }
    
    /**
     * Add Course method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addCourse()
    {
        $courses = $this->fetchTable('Courses');
        
        $course = $courses->newEmptyEntity();
        if ($this->request->is('post')) {
            try {
                $data = $this->request->getData();
                
                
                // Handle chunked upload if it exists
                $image = $this->request->getUploadedFile('image_file');
                $uploadResult = null; // Initialize uploadResult

                if ($image && !$image->getError()) { // Image processing logic
                    if ($this->request->getData('chunks')) {
                        $uploadResult = $this->Upload->handleChunkedUpload($image, $data);
                        
                        if (!$uploadResult['complete']) {
                            return $this->response->withStringBody(json_encode($uploadResult));
                        }

                        $filename = time() . '_' . $uploadResult['filename'];
                        $destination = WWW_ROOT . 'img' . DS . 'courses' . DS . $filename;
                        
                        if (!file_exists(dirname($destination))) {
                            mkdir(dirname($destination), 0775, true);
                        }

                        rename($uploadResult['temp_path'], $destination);
                        $data['image'] = 'courses/' . $filename;
                    } else {
                        $filename = time() . '_' . $image->getClientFilename();
                        $destination = WWW_ROOT . 'img' . DS . 'courses' . DS . $filename;
    
                        if (!file_exists(dirname($destination))) {
                            mkdir(dirname($destination), 0775, true);
                        }
    
                        $image->moveTo($destination);
                        $data['image'] = 'courses/' . $filename;
                    }
                } // End of image processing

                // This part is now OUTSIDE the image check
                $course = $courses->patchEntity($course, $data);
                    
                if ($courses->save($course)) {
                    // Clean up temporary files if chunked upload was used
                    if (isset($uploadResult) && isset($data['upload_id'])) { // Check $data['upload_id'] exists
                        $this->Upload->cleanup($data['upload_id']);
                    }
                    
                    $this->Flash->success(__('The course has been saved.'));
                    return $this->redirect(['action' => 'manageCourses']);
                }
                // If save fails, the error flash below will be shown.

            } catch (\Exception $e) {
                // Clean up any temporary files
                if (isset($data['upload_id'])) { // Check $data['upload_id'] exists
                    $this->Upload->cleanup($data['upload_id']);
                }
                // Log the full exception for better debugging
                Log::error('Error saving course: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                $this->Flash->error(__('An unexpected error occurred. Please check the logs.')); // More generic error to user
            }
            
            // This flash message is shown if $courses->save($course) returns false,
            // or if an exception wasn't caught and handled with a redirect/response.
            $this->Flash->error(__('The course could not be saved. Please, try again. Validation errors: {0}', json_encode($course->getErrors())));
        }
        $this->set(compact('course'));
    }
    
    /**
     * Edit Course method
     *
     * @param string|null $id Course id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editCourse($id = null)
    {
        $courses = $this->fetchTable('Courses');
        
        $course = $courses->get(
            $id,
            contain: ['Modules']
        );
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            try {
                $data = $this->request->getData();
                $originalCourseImage = $course->image; // Store original image path
                $deleteImageChecked = !empty($data['delete_image']) && $data['delete_image'] == '1';

                if ($deleteImageChecked) {
                    if ($originalCourseImage) {
                        $oldImagePath = WWW_ROOT . 'img' . DS . $originalCourseImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $data['image'] = null; // Prepare to set image field to null
                }

                // Handle chunked upload if it exists
                $image = $this->request->getUploadedFile('image_file');

                if ($image && !$image->getError()) {
                    if ($this->request->getData('chunks')) {
                        $uploadResult = $this->Upload->handleChunkedUpload($image, $data);

                        if (!$uploadResult['complete']) {

                            // Send progress response for chunked upload
                            return $this->response->withStringBody(json_encode($uploadResult));
                        }

                        $originalFilename = $uploadResult['filename'];
                        $safeFilename = preg_replace('/[^\w.-]/', '_', $originalFilename); // Sanitize
                        $filename = time() . '_' . $safeFilename; // Use sanitized name
                        // Ensure the target directory exists before moving
                        $destinationDir = WWW_ROOT . 'img' . DS . 'courses';
                        if (!file_exists($destinationDir)) {
                            mkdir($destinationDir, 0775, true);
                        }
                        $destination = $destinationDir . DS . $filename;
                        // Use rename for chunked uploads as per addCourse logic
                        if (isset($uploadResult['temp_path']) && file_exists($uploadResult['temp_path'])) {
                             rename($uploadResult['temp_path'], $destination);
                        } else {
                            // Fallback or error handling if temp_path isn't set or doesn't exist
                             throw new \RuntimeException('Chunked upload failed processing.');
                        }
                        $data['image'] = 'courses/' .$filename;
                    } else {
                        // Standard image upload (non-chunked)
                        $originalFilename = $image->getClientFilename();
                        $safeFilename = preg_replace('/[^\w.-]/', '_', $originalFilename); // Sanitize
                        $filename = time() . '_' . $safeFilename; // Use sanitized name
                        $destinationDir = WWW_ROOT . 'img' . DS . 'courses';
                         if (!file_exists($destinationDir)) { // Add directory check
                            mkdir($destinationDir, 0775, true);
                        }
                        $destination = $destinationDir . DS . $filename;
                        $image->moveTo($destination); // Remove logging around moveTo
                        $data['image'] = 'courses/' . $filename;
                    }

                    // Delete old image if it exists and was not already deleted by checkbox logic
                    if ($originalCourseImage && !$deleteImageChecked) {
                        $oldImagePath = WWW_ROOT . 'img' . DS . $originalCourseImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                } else {
                    // No new image uploaded
                    unset($data['image_file']); // Prevent patching with empty file data
                    if (!$deleteImageChecked) { // If delete checkbox was NOT ticked
                        // Keep existing image only if it exists
                        if ($originalCourseImage) {
                             $data['image'] = $originalCourseImage;
                        } else {
                             // If no new image and no old image (and not deleting), ensure 'image' isn't incorrectly set
                             unset($data['image']);
                        }
                    }
                    // If deleteImageChecked was true, $data['image'] is already null from earlier logic
                }

                // Ensure 'delete_image' is not passed to patchEntity if it's not a DB field
                if (isset($data['delete_image'])) {
                    unset($data['delete_image']);
                }

                $course = $courses->patchEntity($course, $data);
                // Removed log: \Cake\Log\Log::debug('Entity errors before save: ' . print_r($course->getErrors(), true));

                // Removed log: \Cake\Log\Log::debug('Attempting to save course...');
                // Attempt to save the course
                if ($courses->save($course)) {
                    // Check if this was the final chunk of an AJAX upload
                    // Use getData('chunks') as the primary indicator from our JS FileUploader
                    $isAjaxChunkUpload = $this->request->getData('chunks') !== null;

                    // Attempt cleanup *before* sending response/redirecting
                    if (isset($uploadResult)) {
                        try {
                            $this->Upload->cleanup($data['upload_id']);
                        } catch (\Exception $cleanupException) {
                            // Log cleanup error but don't prevent response/redirect
                            Log::error('Failed to cleanup upload ID ' . ($data['upload_id'] ?? 'N/A') . ': ' . $cleanupException->getMessage());
                        }
                    }

                    // Check if the UploadComponent processed a chunk and if it was the final one
                    if (isset($uploadResult) && $uploadResult['complete']) {
                        // For AJAX upload completion, return JSON success
                        return $this->response->withType('application/json')
                                              ->withStringBody(json_encode([
                                                  'complete' => true,
                                                  'path' => $data['image'] ?? null // Send back the saved path
                                              ]));
                    } else {
                        // For a standard form submission (or if AJAX upload wasn't used/completed), set flash and redirect
                        $this->Flash->success(__('The course has been updated.'));
                        return $this->redirect(['action' => 'manageCourses']);
                    }
                } else {
                    // Handle save failure
                    $errorMessage = __('The course could not be updated. Please, try again.');
                    // If it was an AJAX request that failed to save, return JSON error
                    if ($this->request->getData('chunks') !== null) { // Check if it was our AJAX uploader
                         return $this->response->withType('application/json')
                                               ->withStatus(500) // Internal Server Error
                                               ->withStringBody(json_encode([
                                                   'error' => $errorMessage,
                                                   'details' => $course->getErrors() // Include validation errors if any
                                               ]));
                    }
                    // Otherwise, set the standard flash message for page render
                    $this->Flash->error($errorMessage);
                }
            } catch (\Exception $e) {
                 // Catch any other unexpected exceptions during file handling etc.
                 // Removed log: \Cake\Log\Log::error("Exception during course edit process: " . $e->getMessage() . "\n" . $e->getTraceAsString()); // Ensure this log is removed
                // Clean up any temporary files
                if (isset($data['upload_id'])) {
                    $this->Upload->cleanup($data['upload_id']);
                }
                 // Show the actual error message caught, even if it's the misleading one.
                 $this->Flash->error($e->getMessage());
            }
            // Note: The generic flash error previously on line 282 is removed as failure is handled within the if/else/catch.
        } // End of if ($this->request->is(['patch', 'post', 'put']))
        $this->set(compact('course'));
    }
    
    /**
     * Delete Course method
     *
     * @param string|null $id Course id.
     * @return \Cake\Http\Response|null|void Redirects to manageCourses.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteCourse($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courses = $this->fetchTable('Courses');
        
        $course = $courses->get($id, contain: []);
        if ($courses->delete($course)) {
            $this->Flash->success(__('The course has been deleted.'));
        } else {
            $this->Flash->error(__('The course could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageCourses']);
    }
    
    /**
     * Manage Modules method
     *
     * @param string|null $courseId Course id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function manageModules($courseId = null)
    {
        $courses = $this->fetchTable('Courses');
        $modules = $this->fetchTable('Modules');
        
        $course = $courses->get(
            $courseId,
            contain: [
                'Modules' => function ($q) {
                    return $q->contain(['Contents'])->order(['Modules.order' => 'ASC']);
                }
            ]
        );
        
        $this->set(compact('course'));
    }
    
    /**
     * Add Module method
     *
     * @param string|null $courseId Course id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addModule($courseId = null)
    {
        $courses = $this->fetchTable('Courses');
        $modules = $this->fetchTable('Modules');
        
        $course = $courses->get($courseId, contain: []);
        $module = $modules->newEmptyEntity();
        
        if ($this->request->is('post')) {
            $module = $modules->patchEntity($module, $this->request->getData());
            $module->course_id = $courseId;
            
            // Get the highest order value and add 1
            $maxOrder = $modules->find()
                ->select(['max_order' => $modules->find()->func()->max('Modules.order')])
                ->where(['course_id' => $courseId])
                ->first();
            $module->order = ($maxOrder && $maxOrder->max_order) ? $maxOrder->max_order + 1 : 1;
            
            if ($modules->save($module)) {
                $this->Flash->success(__('The module has been saved.'));
                return $this->redirect(['action' => 'manageModules', $courseId]);
            }
            $this->Flash->error(__('The module could not be saved. Please, try again.'));
        }
        
        $this->set(compact('module', 'course'));
    }
    
    /**
     * Edit Module method
     *
     * @param string|null $id Module id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editModule($id = null)
    {
        $modules = $this->fetchTable('Modules');
        
        $module = $modules->get($id, [
            'contain' => ['Courses', 'Contents' => function ($q) {
                return $q->order(['Contents.order' => 'ASC']);
            }],
        ]);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $module = $modules->patchEntity($module, $this->request->getData());
            if ($modules->save($module)) {
                $this->Flash->success(__('The module has been updated.'));
                return $this->redirect(['action' => 'manageModules', $module->course_id]);
            }
            $this->Flash->error(__('The module could not be updated. Please try again.'));
        }
        
        $this->set(compact('module'));
    }
    
    /**
     * Delete Module method
     *
     * @param string|null $id Module id.
     * @return \Cake\Http\Response|null|void Redirects to manageModules.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteModule($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $modules = $this->fetchTable('Modules');
        
        $module = $modules->get($id);
        $courseId = $module->course_id;
        
        if ($modules->delete($module)) {
            $this->Flash->success(__('The module has been deleted.'));
        } else {
            $this->Flash->error(__('The module could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageModules', $courseId]);
    }
    
    /**
     * Manage Contents method
     *
     * @param string|null $moduleId Module id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function manageContents($moduleId = null)
    {
        $modules = $this->fetchTable('Modules');
        $contents = $this->fetchTable('Contents');
        
        $module = $modules->get($moduleId, [
            'contain' => [
                'Courses',
                'Contents' => function ($q) {
                    return $q->order(['Contents.order' => 'ASC']);
                }
            ],
        ]);
        
        $this->set(compact('module'));
    }
    
    /**
     * Add Content method
     *
     * @param string|null $moduleId Module id.
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
public function addContent($moduleId)
    {
        $contents = $this->fetchTable('Contents');
        $modules = $this->fetchTable('Modules');

        $module = $modules->get($moduleId, contain: ['Courses']);
        $content = $contents->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['module_id'] = $moduleId;
            
            // Set default content type if not provided (e.g., 'text' or 'lesson')
            // You might want to make this configurable or add a form field for it.
            if (empty($data['type'])) {
                $data['type'] = 'text'; // Default content type
            }

            // Calculate the next order value for the content within this module
            $lastContent = $contents->find()
                ->select(['max_order' => $contents->find()->func()->max('`order`')]) // Ensure 'order' is quoted if it's a reserved keyword
                ->where(['module_id' => $moduleId])
                ->first();
            $data['order'] = ($lastContent && $lastContent->max_order !== null) ? (int)$lastContent->max_order + 1 : 1;
            $data['order'] = (int)$data['order']; // Ensure the final value is an integer

            $content = $contents->patchEntity($content, $data);

            if ($contents->save($content)) {
                $this->Flash->success(__('The lesson content has been saved.'));
                return $this->redirect(['action' => 'manageContents', $moduleId]);
            }
            Log::debug('Content save failed. Validation errors: ' . print_r($content->getErrors(), true));
            $this->Flash->error(__('The lesson content could not be saved. Please try again.'));
        }

        $this->set(compact('content', 'module'));
        $this->viewBuilder()->setTemplate('add_content');
    }

    /**
     * Edit Lesson Content method
     *
     * @param string|null $id Content id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
public function editLessonContent($id)
    {
        $contents = $this->fetchTable('Contents');

        $content = $contents->get($id, contain: ['Modules.Courses']);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $content = $contents->patchEntity($content, $data);

            if ($contents->save($content)) {
                $this->Flash->success(__('The lesson content has been updated.'));
                return $this->redirect(['action' => 'manageContents', $content->module_id]);
            }
            $this->Flash->error(__('The lesson content could not be updated. Please try again.'));
        }

        $this->set(compact('content'));
        $this->viewBuilder()->setTemplate('edit_lesson_content');
    }

/**
     * Updates the display order of modules for a course.
     * This action is intended to be called via AJAX.
     *
     * @param string|null $courseId The ID of the course being updated.
     * @return \Cake\Http\Response|null
     */
    public function updateModuleOrder($courseId = null)
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false; // We will return a JSON response

        $orderedIds = $this->request->getData('module_ids');

        // 1. Validation
        if (empty($courseId) || empty($orderedIds) || !is_array($orderedIds)) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Invalid data provided.']))
                ->withStatus(400);
        }

        $modulesTable = $this->fetchTable('Modules');
        
        // Verify that all submitted module IDs belong to the specified course
        $dbModuleCount = $modulesTable->find()
            ->where(['id IN' => $orderedIds, 'course_id' => $courseId])
            ->count();

        if (count($orderedIds) !== $dbModuleCount) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'Module list mismatch or invalid ID.']))
                ->withStatus(400);
        }

        // 2. Database Transaction
        $connection = $modulesTable->getConnection();
        try {
            $connection->begin();

            // 3. Efficient Update (Two-Pass)
            // First pass: Set a temporary, non-conflicting order.
            // Using negative values of the ID ensures they won't conflict with existing positive order numbers.
            foreach ($orderedIds as $index => $moduleId) {
                $modulesTable->updateAll(
                    ['order' => -($index + 1)], // Use negative index to avoid conflicts
                    ['id' => $moduleId]
                );
            }

            // Second pass: Set the final, correct order.
            foreach ($orderedIds as $index => $moduleId) {
                $modulesTable->updateAll(
                    ['order' => $index + 1], // Set final positive order
                    ['id' => $moduleId]
                );
            }

            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollback();
            Log::error('Error updating module order for course ' . $courseId . ': ' . $e->getMessage());
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => 'An internal server error occurred.']))
                ->withStatus(500);
        }

        // 4. Success Response
        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['success' => true, 'message' => 'Module order updated.']));
    }

    /**
     * Edit Site Content Block method (Renamed from editContent)
     *
     * @param string|null $id Content id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editSiteContentBlock($id = null)
{
    return $this->redirect(['controller' => 'ContentBlocks', 'action' => 'edit', $id]);
}
    
    /**
     * Delete Content method
     *
     * @param string|null $id Content id.
     * @return \Cake\Http\Response|null|void Redirects to manageContents.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
public function deleteContent($id = null)
{
    $this->request->allowMethod(['post', 'delete']);
    $contents = $this->fetchTable('Contents');
    
    $content = $contents->get($id);
    $moduleId = $content->module_id;

    if ($contents->delete($content)) {
        $this->Flash->success(__('The content has been deleted.'));
    } else {
        $this->Flash->error(__('The content could not be deleted. Please, try again.'));
    }

    return $this->redirect(['action' => 'manageContents', $moduleId]);
}

    /**
     * Manage Site-wide Content method
     *
     * Fetches or creates content blocks based on predefined slugs and passes them to the view.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function siteContent()
{
    Log::debug('AdminController::siteContent() reached. Request URL: ' . $this->request->getRequestTarget());
    // Redirect to the non-prefixed ContentBlocksController
    return $this->redirect(['prefix' => false, 'controller' => 'ContentBlocks', 'action' => 'index']);
}

    /**
     * Manage Users method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function manageUsers()
    {
        $users = $this->fetchTable('Users');
        
        $usersList = $users->find()->all();
        
        $this->set(compact('usersList'));
    }
    
    /**
     * Edit User method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editUser($id = null)
    {
        $users = $this->fetchTable('Users');
        $user = $users->get($id); // Fetch user regardless of request method

        $originalRole = $user->role; // Store original role

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Primary Admin Role Protection
            if ($user->email === 'admin@jenburyfinancial.com') {
                if (isset($data['role']) && $data['role'] !== 'admin') {
                    $this->Flash->error(__('The primary administrator role cannot be changed.'));
                    // Unset the role from data to prevent saving the change
                    unset($data['role']);
                    // Note: The form will still show the attempted selection unless reloaded,
                    // but the save operation will ignore the role change.
                }
            }

            // Explicitly list fields allowed for patching, including 'role'
            $user = $users->patchEntity($user, $data, [
                 'fieldList' => ['first_name', 'last_name', 'email', 'is_active', 'email_verified', 'role']
            ]);

            if ($users->save($user)) {
                $this->Flash->success(__('The user has been updated.'));

                // Role Change Confirmation
                if ($originalRole !== $user->role) {
                    // Use h() for security if displaying user-provided data, though role is controlled here.
                    $this->Flash->info(__('User role successfully updated to {0}.', ucfirst(h($user->role))));
                }

                return $this->redirect(['action' => 'manageUsers']);
            }
            $this->Flash->error(__('The user could not be updated. Please, try again.'));
        }
        
        // Prepare roles for dropdown
        $roles = ['member' => 'Member', 'admin' => 'Administrator', 'student' => 'Student'];
        
        $this->set(compact('user', 'roles'));
        // Pass the user email specifically if needed for complex frontend logic,
        // though the whole user entity is usually sufficient.
        // $this->set('userEmail', $user->email);
    } // End of editUser

    /**
     * Delete User method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to manageUsers.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteUser($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $users = $this->fetchTable('Users');

        try {
            $user = $users->get($id);

            // Prevent deletion of the primary admin account
            if ($user->email === 'admin@jenburyfinancial.com') {
                $this->Flash->error(__('Cannot delete the primary administrator account.'));
            } else {
                if ($users->delete($user)) {
                    $this->Flash->success(__('User {0} ({1}) has been deleted.', $id, h($user->email)));
                } else {
                    $this->Flash->error(__('The user could not be deleted. Please, try again.'));
                }
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('User not found.'));
        } catch (\Exception $e) {
            // Catch broader exceptions during delete process
            Log::error('Error deleting user ID ' . $id . ': ' . $e->getMessage());
            $this->Flash->error(__('An unexpected error occurred while deleting the user. Please try again.'));
        }

        return $this->redirect(['action' => 'manageUsers']);
    } // End of deleteUser

    /**
     * Restore Content Block method
     *
     * Restores a specific content block to its default value based on the slug.
     *
     * @param string $slug The slug of the content block to restore.
     * @return \Cake\Http\Response|null|void Redirects back to siteContent.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function restoreContent($id = null)
    {
        return $this->redirect(['controller' => 'ContentBlocks', 'action' => 'restore', $id]);
    }

    /**
     * Restore All Content Blocks method
     *
     * Restores all known content blocks to their default values.
     *
     * @return \Cake\Http\Response|null|void Redirects back to siteContent.
     */
    public function restoreAllContent()
{
    return $this->redirect(['controller' => 'ContentBlocks', 'action' => 'restoreAll']);
}

    /**
     * View Stats method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewStats($id = null)
    {
        $purchases = $this->fetchTable('Purchases');
        $users = $this->fetchTable('Users');
        $usercontentprogress = $this->fetchTable('UserContentProgress');
        $usercourseprogress = $this->fetchTable('UserCourseProgress');
        $contents = $this->fetchTable('Contents');
        $user = $users->get($id); // Fetch user regardless of request method
        $userId = $user->id;
        
        

        // Get recent purchases
        $recentPurchases = $purchases->find()
            ->where(['Purchases.user_id' => $id])
            ->contain(['Users', 'Courses', 'Modules'])
            ->order(['Purchases.created' => 'DESC'])
            ->limit(10)
            ->all();
            
        // Get all course purchases
        $courseQuery = $purchases->find()
            ->where([
                'user_id' => $userId,
                'course_id IS NOT NULL',
                'payment_status' => 'completed',
            ])
            ->contain(['Courses' => ['Modules','UserCourseProgress'=> function ($q) use ($userId) {
                return $q->where(['user_id' => $userId]);}]]); // Contain courses and their modules
        $coursePurchases = $this->paginate($courseQuery, ['limit' => 10]); // Removed deprecated alias

        // Get all module purchases
        $moduleQuery = $purchases->find()
            ->where([
                'user_id' => $userId,
                'module_id IS NOT NULL',
                'payment_status' => 'completed',
            ])
            ->contain(['Modules'=> [
                'UserModuleProgress'=> function ($q) use ($userId) {
                    return $q->where(['user_id' => $userId]);}]]); // Contain modules

        $modulePurchases = $this->paginate($moduleQuery, ['limit' => 10]); // Removed deprecated alias

        // --- Filter out modules that are part of a purchased course ---
        $purchasedCourseIds = [];
        if (isset($coursePurchases) && !$coursePurchases->isEmpty()) {
            // Extract course IDs directly from the contained Course entity within each purchase
             $purchasedCourseIds = $coursePurchases->extract(function ($purchase) {
                 return $purchase->course->id ?? null; // Extract course ID from the purchase's related course
             })->filter()->toList(); // Filter out nulls and convert to list
        }

        $standaloneModulePurchases = new Collection([]); // Initialize empty collection
        if (isset($modulePurchases) && !$modulePurchases->isEmpty()) {
            $standaloneModulePurchases = $modulePurchases->filter(function ($purchase) use ($purchasedCourseIds) {
                // Keep if module is null or has no course_id (standalone module)
                if (empty($purchase->module) || empty($purchase->module->course_id)) {
                    return true;
                }
                // Keep if module's course_id is NOT in the list of purchased course IDs
                return !in_array($purchase->module->course_id, $purchasedCourseIds);
            });
        }
        // --- End filtering ---


        // --- Calculate Progress for each Standalone Module ---
        // Use the filtered list for progress calculation
        if (isset($standaloneModulePurchases) && !$standaloneModulePurchases->isEmpty()) {
            $ContentsTable = $this->fetchTable('Contents');
            $UserContentProgressTable = $this->fetchTable('UserContentProgress');

            foreach ($standaloneModulePurchases as $purchase) { // Use filtered list
                if (isset($purchase->module)) {
                    $moduleId = $purchase->module->id;

                    // 1. Get all active content IDs for this specific module
                    $allModuleContentIdsQuery = $ContentsTable->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                        ->where(['module_id' => $moduleId, 'is_active' => true])
                        ->toArray();
                    $totalModuleContents = count($allModuleContentIdsQuery);

                    // 2. Get completed content IDs for the user in this module
                    $completedModuleContentsCount = 0;
                    if ($totalModuleContents > 0) {
                        $completedModuleIds = $UserContentProgressTable->find('list', [
                            'keyField' => 'content_id',
                            'valueField' => 'content_id'
                        ])
                        ->where(['user_id' => $userId, 'content_id IN' => $allModuleContentIdsQuery])
                        ->toArray();
                        $completedModuleContentsCount = count($completedModuleIds);

                        // 3. Calculate percentage
                        $percentage = round(($completedModuleContentsCount / $totalModuleContents) * 100);
                    } else {
                        $percentage = 0; // Or 100 if no content means complete? Defaulting to 0.
                    }

                    // 4. Attach percentage to the module entity
                    $purchase->module->user_progress_percentage = $percentage;
                }
            }
        }

        // --- Calculate Progress for each Course ---
        // This logic remains unchanged as it operates on $coursePurchases
        if (isset($coursePurchases) && !$coursePurchases->isEmpty()) { // Use isEmpty() for collections
            $ContentsTable = $this->fetchTable('Contents');
            $UserContentProgressTable = $this->fetchTable('UserContentProgress');

            foreach ($coursePurchases as $purchase) {
                if (isset($purchase->course) && !empty($purchase->course->modules)) {
                    $courseId = $purchase->course->id;

                    // 1. Get all active content IDs for this specific course
                    $allCourseContentIdsQuery = $ContentsTable->find('list', keyField: 'id', valueField: 'id') // Use named arguments
                        ->innerJoinWith('Modules', function ($q) use ($courseId) {
                            return $q->where(['Modules.course_id' => $courseId, 'Modules.is_active' => true]);
                        })
                        ->where(['Contents.is_active' => true]);
                    $allCourseContentIds = $allCourseContentIdsQuery->toArray();
                    $totalCourseContents = count($allCourseContentIds);

                    // 2. Get completed content IDs for the user in this course
                    $completedCourseContentsCount = 0;
                    if ($totalCourseContents > 0) {
                        $completedContentIds = $UserContentProgressTable->find('list', keyField: 'content_id', valueField: 'content_id') // Use named arguments
                        ->where(['user_id' => $userId, 'content_id IN' => $allCourseContentIds])
                        ->toArray();
                        $completedCourseContentsCount = count($completedContentIds);

                        // 3. Calculate percentage
                        $percentage = round(($completedCourseContentsCount / $totalCourseContents) * 100);
                    } else {
                        $percentage = 0; // Or 100 if no content means complete? Defaulting to 0.
                    }

                    // 4. Attach percentage to the course entity
                    $purchase->course->user_progress_percentage = $percentage;

                } else if (isset($purchase->course)) {
                     // Handle courses with no modules (or modules not loaded) - progress is 0
                     $purchase->course->user_progress_percentage = 0;
                }
            }
        }
        // --- End Progress Calculation ---
        
        $lastContent = $usercontentprogress->find()
            ->contain(['Contents' => ['Modules.Courses']])
            ->where(['user_id' => $userId])
            ->order(['UserContentProgress.created' => 'DESC'])
            ->first();
        
        $recentlyFinishedContents = $usercontentprogress->find()
            ->contain(['Contents' => ['Modules.Courses']])
            ->where(['user_id' => $userId])
            ->order(['UserContentProgress.created' => 'DESC'])
            ->limit(5)
            ->all();
        
        $totalActiveCourses = $usercourseprogress->find()
            ->where([
                'user_id' => $userId,
                'completion_date IS' => null // Active = not completed
            ])
            ->count();



        $this->set(compact( 'user', 'recentPurchases', 'coursePurchases', 'standaloneModulePurchases', 'lastContent','recentlyFinishedContents', 'totalActiveCourses'));
        
    }


    public function activityFeed($id = null)
    {
        // This method remains unchanged for this task
        $this->request->allowMethod(['get']);
        $this->viewBuilder()->setLayout('ajax');
        $this->viewBuilder()->setClassName('Json');

        $recentActivities = []; // Default to empty array

        try {
            $users = $this->fetchTable('Users');
            $user = $users->get($id); // Fetch user regardless of request method
            $userId = $user->id;

            if (!$userId || !is_numeric($userId)) {
                throw new \Cake\Http\Exception\NotFoundException(__('User Not Found!'));
            }

            $limit = 5;
            $activities = [];

            // 1. Fetch Content Completions
            try {
                $contentProgressTable = $this->fetchTable('UserContentProgress');
                $contentCompletions = $contentProgressTable->find()
                    ->select([
                        'description' => 'CONCAT("Completed lesson: ", Contents.title)', // Restore original description
                        'timestamp' => 'UserContentProgress.created',
                        'type' => "'content_completion'",
                    ])
                    ->contain(['Contents']) // Restore contain
                    ->where(['UserContentProgress.user_id' => $userId])
                    ->orderBy(['UserContentProgress.created' => 'DESC']) // Use orderBy
                    ->limit($limit)
                    ->toArray();
                $activities = array_merge($activities, $contentCompletions);
            } catch (Throwable $e) {
                Log::error('Error fetching content completions: ' . $e->getMessage());
                // Optionally continue without this data or re-throw
            }

            // 2. Fetch Module Completions
            try {
                $moduleProgressTable = $this->fetchTable('UserModuleProgress');
                $moduleCompletions = $moduleProgressTable->find()
                    ->select([
                        'description' => 'CONCAT("Completed module: ", Modules.title)', // Restore original description
                        'timestamp' => 'UserModuleProgress.modified',
                        'type' => "'module_completion'",
                    ])
                    ->contain(['Modules']) // Restore contain
                    ->where(['UserModuleProgress.user_id' => $userId])
                    ->orderBy(['UserModuleProgress.modified' => 'DESC']) // Use orderBy
                    ->limit($limit)
                    ->toArray();
                $activities = array_merge($activities, $moduleCompletions);
            } catch (Throwable $e) {
                Log::error('Error fetching module completions: ' . $e->getMessage());
                // Optionally continue without this data or re-throw
            }

            // 3. Fetch Course Completions
            try {
                $courseProgressTable = $this->fetchTable('UserCourseProgress');
                $courseCompletions = $courseProgressTable->find()
                    ->select([
                        'description' => 'CONCAT("Completed course: ", Courses.title)', // Restore original description
                        'timestamp' => 'UserCourseProgress.completion_date',
                        'type' => "'course_completion'",
                    ])
                    ->contain(['Courses']) // Restore contain
                    ->where(['UserCourseProgress.user_id' => $userId])
                    ->orderBy(['UserCourseProgress.completion_date' => 'DESC']) // Use orderBy
                    ->limit($limit)
                    ->toArray();
                $activities = array_merge($activities, $courseCompletions);
            } catch (Throwable $e) {
                Log::error('Error fetching course completions: ' . $e->getMessage());
                // Optionally continue without this data or re-throw
            }

            // 4. Sort all activities by timestamp DESC
            try {
                if (!empty($activities)) { // Avoid sorting empty array
                    usort($activities, function ($a, $b) {
                        $timeA = $a->timestamp instanceof \Cake\I18n\FrozenTime ? $a->timestamp->getTimestamp() : strtotime((string)$a->timestamp);
                        $timeB = $b->timestamp instanceof \Cake\I18n\FrozenTime ? $b->timestamp->getTimestamp() : strtotime((string)$b->timestamp);
                        return $timeB <=> $timeA;
                    });
                    // 5. Take the top $limit activities after sorting
                    $recentActivities = array_slice($activities, 0, $limit);
                } else {
                    $recentActivities = [];
                }
            } catch (Throwable $e) {
                Log::error('Error sorting activities: ' . $e->getMessage());
                $recentActivities = array_slice($activities, 0, $limit); // Use unsorted data as fallback
            }


            // 6. Format timestamps for display
            try {
                foreach ($recentActivities as $activity) {
                     // Ensure timestamp exists and is not null before accessing
                    if (isset($activity->timestamp) && $activity->timestamp !== null) {
                        if ($activity->timestamp instanceof \Cake\I18n\FrozenTime) {
                            $activity->formatted_timestamp = $activity->timestamp->nice();
                            $activity->time_ago = $activity->timestamp->timeAgoInWords(['accuracy' => 'day', 'end' => '+1 year']);
                        } else {
                            // Attempt conversion for string timestamps
                            $ts = strtotime((string)$activity->timestamp);
                            if ($ts !== false) {
                                $activity->formatted_timestamp = date('M d, Y, g:i A', $ts);
                                // Basic time ago calculation for strings (less accurate)
                                $diff = time() - $ts;
                                if ($diff < 60) $activity->time_ago = 'Just now';
                                elseif ($diff < 3600) $activity->time_ago = floor($diff / 60) . ' mins ago';
                                elseif ($diff < 86400) $activity->time_ago = floor($diff / 3600) . ' hours ago';
                                else $activity->time_ago = floor($diff / 86400) . ' days ago';
                            } else {
                                $activity->formatted_timestamp = 'Invalid Date';
                                $activity->time_ago = 'N/A';
                            }
                        }
                    } else {
                         // Handle cases where timestamp might be missing
                         $activity->formatted_timestamp = 'No Date';
                         $activity->time_ago = 'N/A';
                    }
                }
            } catch (Throwable $e) {
                Log::error('Error formatting timestamps: ' . $e->getMessage());
                // Continue with potentially unformatted data
            }

        } catch (Throwable $e) {
            // Catch any other unexpected errors during setup or identity fetching
            Log::error('General error in activityFeed action: ' . $e->getMessage());
            // Ensure $recentActivities is set for serialization
            $recentActivities = ['error' => 'Could not load activity feed due to an internal error.'];
            // Optionally set response status code
            // $this->response = $this->response->withStatus(500);
        }

        $this->set('activities', $recentActivities);
        $this->viewBuilder()->setOption('serialize', ['activities']);
    }

    public function manageForumCategories()
    {
        $forumCategoriesTable = $this->fetchTable('ForumCategories');

        $categories = $forumCategoriesTable->find()->all();

        // Update post_count for each category
        foreach ($categories as $category) {
            $postCount = $forumCategoriesTable->sumPostCountFromThreads($category->id);
            $category->post_count = $postCount;
            $forumCategoriesTable->save($category);
        }

        $forumCategoriesList = $forumCategoriesTable->find()->all();

        $this->set(compact('forumCategoriesList'));
    }



    public function addForumCategory()
    {
        $forumCategories = $this->fetchTable('ForumCategories');
        $forumCategory = $forumCategories->newEmptyEntity();

        if ($this->request->is('post')) {
            $forumCategory = $forumCategories->patchEntity($forumCategory, $this->request->getData());

            if ($forumCategories->save($forumCategory)) {
                $this->Flash->success(__('The forum category has been added.'));
                return $this->redirect(['action' => 'manageForumCategories']);
            }
            $this->Flash->error(__('The forum category could not be added. Please, try again.'));
        }

        $this->set(compact('forumCategory'));

    }

    public function editForumCategory($id = null)
    {
        $forumCategories = $this->fetchTable('ForumCategories');
        $forumCategory = $forumCategories->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $forumCategory = $forumCategories->patchEntity($forumCategory, $this->request->getData());

            if ($forumCategories->save($forumCategory)) {
                $this->Flash->success(__('The forum category has been updated.'));
                return $this->redirect(['action' => 'manageForumCategories']);
            }
            $this->Flash->error(__('The forum category could not be updated. Please, try again.'));        
        }

        $this->set(compact('forumCategory'));
    }

    public function deleteForumCategory($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $forumCategories = $this->fetchTable('ForumCategories');
        
        $forumCategory = $forumCategories->get($id, contain: []);
        if ($forumCategories->delete($forumCategory)) {
            $this->Flash->success(__('The forum category has been deleted.'));
        } else {
            $this->Flash->error(__('The forum category could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageForumCategories']);
    }

    public function manageForumThreads()
    {
        $forumThreads = $this->fetchTable('ForumThreads');
        
        $forumThreadsList = $forumThreads->find()
            ->contain(['ForumCategories', 'Users']) 
            ->order(['ForumThreads.created' => 'DESC'])
            ->all();
        
        $this->set(compact('forumThreadsList'));
    }

    public function editForumThread($id = null)
    {
        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $forumThread = $forumThreads->patchEntity($forumThread, $this->request->getData());

            if ($forumThreads->save($forumThread)) {
                $this->Flash->success(__('The forum thread has been updated.'));
                return $this->redirect(['action' => 'manageForumThreads']);
            }
            $this->Flash->error(__('The forum thread could not be updated. Please, try again.'));        
        }

        $this->set(compact('forumThread'));
    }

    public function deleteForumThread($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $forumThreads = $this->fetchTable('ForumThreads');
        
        $forumThread = $forumThreads->get($id, contain: []);
        if ($forumThreads->delete($forumThread)) {
            $this->Flash->success(__('The forum thread has been deleted.'));
        } else {
            $this->Flash->error(__('The forum thread could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageForumThreads']);
    }

    public function lockForumThread($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id);
        $forumThread->is_locked = true;

        if ($forumThreads->save($forumThread)) {
            $this->Flash->success(__('The thread has been locked!'));
        } else {
            $this->Flash->error(__('The thread could not be locked!'));
        }
    
        return $this->redirect(['action' => 'manageForumThreads']);
    
    }

    public function unlockForumThread($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id);
        $forumThread->is_locked = false;

        if ($forumThreads->save($forumThread)) {
            $this->Flash->success(__('The thread has been unlocked!'));
        } else {
            $this->Flash->error(__('The thread could not be unlocked!'));
        }
    
        return $this->redirect(['action' => 'manageForumThreads']);
    
    }

    public function stickyForumThread($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id);
        $forumThread->is_sticky = true;

        if ($forumThreads->save($forumThread)) {
            $this->Flash->success(__('This thread is now a sticky thread!'));
        } else {
            $this->Flash->error(__('The thread could not be marked as a sticky thread!'));
        }
    
        return $this->redirect(['action' => 'manageForumThreads']);
    
    }

    public function unstickyForumThread($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id);
        $forumThread->is_sticky = false;

        if ($forumThreads->save($forumThread)) {
            $this->Flash->success(__('This thread is now no longer a sticky thread!'));
        } else {
            $this->Flash->error(__('The thread could not be unmarked as a sticky thread!'));
        }
    
        return $this->redirect(['action' => 'manageForumThreads']);
    
    }

    public function approveForumThread($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id);
        $forumThread->is_approved = true;

        if ($forumThreads->save($forumThread)) {
            $this->Flash->success(__('This thread is now approved!'));
        } else {
            $this->Flash->error(__('The thread could not be approved!'));
        }
    
        return $this->redirect(['action' => 'manageForumThreads']);
    
    }

    public function disapproveForumThread($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumThreads = $this->fetchTable('ForumThreads');
        $forumThread = $forumThreads->get($id);
        $forumThread->is_approved = false;

        if ($forumThreads->save($forumThread)) {
            $this->Flash->success(__('This thread is now not approved!'));
        } else {
            $this->Flash->error(__('The thread could not be disapproved!'));
        }
    
        return $this->redirect(['action' => 'manageForumThreads']);
    
    }

    public function manageForumPosts()
    {
        $forumPosts = $this->fetchTable('ForumPosts');
        
        $forumPostsList = $forumPosts->find()
            ->contain(['ForumThreads' => ['ForumCategories'], 'Users']) 
            ->order(['ForumPosts.created' => 'DESC'])
            ->all();
        
        $this->set(compact('forumPostsList'));
    }



    public function editForumPost($id = null)
    {
        $forumPosts = $this->fetchTable('ForumPosts');
        $forumPost = $forumPosts->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $forumPost = $forumPosts->patchEntity($forumPost, $this->request->getData());

            if ($forumPosts->save($forumPost)) {
                $this->Flash->success(__('The forum post has been updated.'));
                return $this->redirect(['action' => 'manageForumPosts']);
            }
            $this->Flash->error(__('The forum post could not be updated. Please, try again.'));        
        }

        $this->set(compact('forumPost'));    
    }

    public function deleteForumPost($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $forumPosts = $this->fetchTable('ForumPosts');
        
        $forumPost = $forumPosts->get($id, contain: []);
        if ($forumPosts->delete($forumPost)) {
            $this->Flash->success(__('The forum post has been deleted.'));
        } else {
            $this->Flash->error(__('The forum post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageForumPosts']);
    }

    public function approveForumPost($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumPosts = $this->fetchTable('ForumPosts');
        $forumPost = $forumPosts->get($id);
        $forumPost->is_approved = true;

        if ($forumPosts->save($forumPost)) {
            $this->Flash->success(__('This post is now approved!'));
        } else {
            $this->Flash->error(__('The post could not be approved!'));
        }
    
        return $this->redirect(['action' => 'manageForumPosts']);
    
    }

    public function disapproveForumPost($id = null)
    {
        $this->request->allowMethod(['post', 'patch']);

        $forumPosts = $this->fetchTable('ForumPosts');
        $forumPost = $forumPosts->get($id);
        $forumPost->is_approved = false;

        if ($forumPosts->save($forumPost)) {
            $this->Flash->success(__('This post is now not approved!'));
        } else {
            $this->Flash->error(__('The post could not be disapproved!'));
        }
    
        return $this->redirect(['action' => 'manageForumPosts']);
    
    }


  
}