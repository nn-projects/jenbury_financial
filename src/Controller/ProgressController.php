<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;

/**
 * Progress Controller
 *
 * Handles AJAX requests to update user progress on modules.
 *
 * @property \App\Model\Table\UserModuleProgressTable $UserModuleProgress
 * @property \App\Model\Table\ModulesTable $Modules // Needed to find course ID
use Cake\Controller\Component\FormProtectionComponent; // <-- Add this use statement

/**
 * Progress Controller
 *
 * Handles AJAX requests to update user progress on modules.
 *
 * @property \App\Model\Table\UserModuleProgressTable $UserModuleProgress
 * @property \App\Model\Table\ModulesTable $Modules // Needed to find course ID
 * @property \App\Model\Table\UserContentProgressTable $UserContentProgress
 * @property \App\Model\Table\UserCourseProgressTable $UserCourseProgress
 * @property \App\Model\Table\ContentsTable $Contents
 * @property FormProtectionComponent $FormProtection // <-- Add property hint
 */
class ProgressController extends AppController
{
    public function initialize(): void // <-- Add initialize method
    {
        parent::initialize();
        $this->loadComponent('FormProtection');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Ensure this controller requires authentication
        // The specific actions might be handled by AuthenticationComponent config,
        // but explicitly checking here adds clarity.
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            // If using AJAX, throwing exception might be better than redirect
            throw new ForbiddenException('Authentication required.');
        }

        // Load necessary models using fetchTable()
        $this->UserModuleProgress = $this->fetchTable('UserModuleProgress');
        $this->UserContentProgress = $this->fetchTable('UserContentProgress'); // Added
        $this->UserCourseProgress = $this->fetchTable('UserCourseProgress');  // Added
        $this->Modules = $this->fetchTable('Modules'); // To get course_id from module_id
        $this->Contents = $this->fetchTable('Contents'); // Added - To get module/course from content_id

        // Enable AJAX layout/view
        $this->viewBuilder()->setLayout('ajax');
        $this->viewBuilder()->setTemplate(null); // No view template needed for JSON response
        $this->autoRender = false; // We will manually echo JSON

        // Disable form tampering validation for AJAX actions
        if ($this->request->is('ajax')) {
            $this->FormProtection->setConfig('validate', false);
            // Optionally, unlock specific actions if needed, though disabling validate might be enough
            // $this->FormProtection->setConfig('unlockedActions', ['update', 'markContentComplete']);
        }
    }

    /**
     * Update method
     *
     * Handles POST requests to update a user's progress status for a module.
     *
     * @return \Cake\Http\Response|null JSON response
     */
    public function update()
    {
        $this->request->allowMethod(['post']); // Allow only POST requests

        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();
        $moduleId = $this->request->getData('module_id');
        $newStatus = $this->request->getData('status');

        // Basic validation
        if (!$moduleId || !$newStatus) {
            throw new BadRequestException('Missing module_id or status.');
        }
        if (!in_array($newStatus, ['not_started', 'in_progress', 'completed'])) {
             throw new BadRequestException('Invalid status value provided.');
        }

        try {
            // Find or create the progress record
            $progress = $this->UserModuleProgress->find()
                ->where(['user_id' => $userId, 'module_id' => $moduleId])
                ->first();

            if (!$progress) {
                $progress = $this->UserModuleProgress->newEntity([
                    'user_id' => $userId,
                    'module_id' => $moduleId,
                    'status' => $newStatus,
                ]);
            } else {
                // Only update if the status is actually changing
                if ($progress->status === $newStatus) {
                    // Status is the same, no need to update, but recalculate percentage just in case
                    // (though ideally this request wouldn't be sent if status hasn't changed)
                } else {
                     $progress->status = $newStatus;
                }
            }

            if ($this->UserModuleProgress->save($progress)) {
                // --- Added: Update Course Progress status if module is starting ---
                if ($newStatus === 'in_progress' && $progress->isNew() === false && $progress->getOriginal('status') === 'not_started') {
                    $moduleForCourse = $this->Modules->get($moduleId, contain: ['Courses']);
                    if ($moduleForCourse && $moduleForCourse->course) {
                        $courseIdForUpdate = $moduleForCourse->course->id;
                        $courseProgressForUpdate = $this->UserCourseProgress->findOrCreate(
                            ['user_id' => $userId, 'course_id' => $courseIdForUpdate]
                        );
                        if ($courseProgressForUpdate->status === 'not_started') {
                            $courseProgressForUpdate->status = 'in_progress';
                            if (!$this->UserCourseProgress->save($courseProgressForUpdate)) {
                                $this->log("Failed to update course progress status to in_progress for user {$userId}, course {$courseIdForUpdate} when starting module {$moduleId}", 'warning');
                            }
                        }
                    }
                }
                // --- End Added ---

                // Recalculate overall course progress (Based on MODULE completion - might need adjustment later if we want content-based % here too)
                $module = $this->Modules->get($moduleId, contain: ['Courses']); // Get course info
                if (!$module || !$module->course) {
                     throw new NotFoundException('Associated course not found for module.');
                }
                $courseId = $module->course->id;

                // Fetch all module IDs for the course
                $courseModules = $this->Modules->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'id' // We just need the IDs
                    ])
                    ->where(['course_id' => $courseId, 'is_active' => true])
                    ->toArray();

                $totalModules = count($courseModules);
                $completedModules = 0;
                $progressPercentage = 0;

                if ($totalModules > 0) {
                    // Fetch completed count for this user and course
                    $completedModules = $this->UserModuleProgress->find()
                        ->where([
                            'user_id' => $userId,
                            'module_id IN' => array_keys($courseModules),
                            'status' => 'completed'
                        ])
                        ->count();

                    $progressPercentage = ($completedModules / $totalModules) * 100;
                }

                // Return success response
                $this->response = $this->response->withType('application/json');
                $this->response = $this->response->withStringBody(json_encode([
                    'success' => true,
                    'newPercentage' => round($progressPercentage) // Send rounded percentage
                ]));
                return $this->response;

            } else {
                 throw new \Exception('Failed to save progress.'); // Generic server error
            }

        } catch (\Exception $e) {
            $this->log('Error saving progress: ' . $e->getMessage(), 'error');
            $this->response = $this->response->withStatus(500); // Internal Server Error
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode([
                'success' => false,
                'message' => 'Could not update progress. Please try again.'
            ]));
            return $this->response;
        }
    }

    /**
     * Mark Content Complete method
     *
     * Handles POST requests to mark a content item as completed for a user.
     * Updates module and course progress accordingly.
     *
:start_line:199
-------
     * @param string|null $contentId Content id.
     * @return \Cake\Http\Response|null JSON response
     */
    public function markContentComplete($contentId = null)
    {
        $this->request->allowMethod(['post']); // Allow only POST requests

        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();

        // Basic validation
        if (!$contentId) {
            throw new BadRequestException('Missing content_id.');
        }
try {
    // 1. Fetch Content, Module, and Course info
    $content = $this->Contents->get($contentId, [
        'contain' => ['Modules' => ['Courses']]
    ]);
    if (!$content->module || !$content->module->course) {
        throw new NotFoundException('Associated module or course not found for this content.');
    }
    $moduleId = $content->module_id;
    $courseId = $content->module->course_id;

    // 2. Save Content Completion Status
    $contentProgress = $this->UserContentProgress->findOrCreate(
        ['user_id' => $userId, 'content_id' => $contentId],
        function ($entity) use ($userId, $contentId) {
            $entity->user_id = $userId;
            $entity->content_id = $contentId;
            $entity->status = 'completed'; // Default is 'completed' anyway from migration
        }
    );
    // Ensure status is completed even if record existed
    if ($contentProgress->status !== 'completed') {
        $contentProgress->status = 'completed';
    }
    if (!$this->UserContentProgress->save($contentProgress)) {
         throw new \Exception('Failed to save content progress.');
    }

    // 3. Update Last Accessed Content ID & Course Status
    $courseProgress = $this->UserCourseProgress->findOrCreate(
        ['user_id' => $userId, 'course_id' => $courseId],
        function ($entity) use ($userId, $courseId) {
            $entity->user_id = $userId;
            $entity->course_id = $courseId;
            $entity->status = 'in_progress'; // Start as in_progress
        }
    );
    $courseProgress->last_accessed_content_id = $contentId;
    // If course was 'not_started', mark it as 'in_progress'
    if ($courseProgress->status === 'not_started') {
        $courseProgress->status = 'in_progress';
    }
    if (!$this->UserCourseProgress->save($courseProgress)) {
        // Log error but don't necessarily stop the whole process
        $this->log("Failed to save course progress update for user {$userId}, course {$courseId}", 'error');
    }

    // 4. Check and Update Module Status
    $moduleCompleted = false;
    // Get total active content count for the module
    $totalModuleContents = $this->Contents->find()
        ->where(['module_id' => $moduleId, 'is_active' => true])
        ->count();

    if ($totalModuleContents > 0) {
        // Get completed content count for this user in this module
        $completedModuleContents = $this->UserContentProgress->find()
            ->where(['user_id' => $userId, 'content_id IN' => $this->Contents->find('list', [
                'keyField' => 'id',
                'valueField' => 'id'
            ])->where(['module_id' => $moduleId, 'is_active' => true])])
            ->count();

        if ($completedModuleContents >= $totalModuleContents) {
            $moduleProgress = $this->UserModuleProgress->findOrCreate(
                ['user_id' => $userId, 'module_id' => $moduleId]
            );
            if ($moduleProgress->status !== 'completed') {
                $moduleProgress->status = 'completed';
                if ($this->UserModuleProgress->save($moduleProgress)) {
                    $moduleCompleted = true; // Flag that module status was updated
                } else {
                     $this->log("Failed to update module progress to completed for user {$userId}, module {$moduleId}", 'error');
                }
            } else {
                 $moduleCompleted = true; // Already completed
            }
        }
    }

    // 5. Check and Update Course Status (if module was completed)
    $courseCompleted = false;
    if ($moduleCompleted) { // Only check if the module completion status might have changed
         // Get total active module count for the course
        $totalCourseModules = $this->Modules->find()
            ->where(['course_id' => $courseId, 'is_active' => true])
            ->count();

        if ($totalCourseModules > 0) {
            // Get completed module count for this user in this course
            $completedCourseModules = $this->UserModuleProgress->find()
                ->where([
                    'user_id' => $userId,
                    'module_id IN' => $this->Modules->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'id'
                    ])->where(['course_id' => $courseId, 'is_active' => true]),
                    'status' => 'completed'
                ])
                ->count();

            if ($completedCourseModules >= $totalCourseModules) {
                // $courseProgress should already be loaded from Step 3
                if ($courseProgress && $courseProgress->status !== 'completed') {
                    $courseProgress->status = 'completed';
                    $courseProgress->completion_date = new \Cake\I18n\DateTime(); // Set completion date
                    if ($this->UserCourseProgress->save($courseProgress)) {
                        $courseCompleted = true;
                    } else {
                        $this->log("Failed to update course progress to completed for user {$userId}, course {$courseId}", 'error');
                    }
                } elseif ($courseProgress && $courseProgress->status === 'completed') {
                     $courseCompleted = true; // Already completed
                }
            }
        }
    }

    // 6. Calculate Final Percentages and Prepare Response
    // Reload progress records to get the latest status after potential updates
    $finalModuleProgress = $this->UserModuleProgress->find()
        ->where(['user_id' => $userId, 'module_id' => $moduleId])
        ->first();
    $finalCourseProgress = $this->UserCourseProgress->find()
        ->where(['user_id' => $userId, 'course_id' => $courseId])
        ->first(); // Should exist after step 3

    // Calculate Module Percentage based on content
    $totalModuleContents = $this->Contents->find()
        ->where(['module_id' => $moduleId, 'is_active' => true])
        ->count();
    $completedModuleContents = $this->UserContentProgress->find()
        ->where(['user_id' => $userId, 'content_id IN' => $this->Contents->find('list', [
            'keyField' => 'id', 'valueField' => 'id'
        ])->where(['module_id' => $moduleId, 'is_active' => true])])
        ->count();
    $modulePercentage = ($totalModuleContents > 0) ? round(($completedModuleContents / $totalModuleContents) * 100) : 0;

    // Calculate Course Percentage based on content
    $allCourseContentIds = $this->Contents->find('list', ['keyField' => 'id', 'valueField' => 'id'])
        ->innerJoinWith('Modules', function ($q) use ($courseId) {
            return $q->where(['Modules.course_id' => $courseId, 'Modules.is_active' => true]);
        })
        ->where(['Contents.is_active' => true])
        ->toArray();
    $totalCourseContents = count($allCourseContentIds);
    $completedCourseContents = 0;
    if ($totalCourseContents > 0) {
         $completedCourseContents = $this->UserContentProgress->find()
            ->where(['user_id' => $userId, 'content_id IN' => $allCourseContentIds])
            ->count();
    }
    $coursePercentage = ($totalCourseContents > 0) ? round(($completedCourseContents / $totalCourseContents) * 100) : 0;


    // Prepare JSON response
    $this->response = $this->response->withType('application/json');
    $this->response = $this->response->withStringBody(json_encode([
        'success' => true,
        'message' => 'Content marked complete.',
        'moduleStatus' => $finalModuleProgress ? $finalModuleProgress->status : 'not_started',
        'courseStatus' => $finalCourseProgress ? $finalCourseProgress->status : 'not_started',
        'coursePercentage' => $coursePercentage,
        'modulePercentage' => $modulePercentage,
    ]));
    return $this->response;

} catch (\Exception $e) {
            $this->log('Error marking content complete: ' . $e->getMessage(), 'error');
            $this->response = $this->response->withStatus(500); // Internal Server Error
            $this->response = $this->response->withType('application/json');
            $this->response = $this->response->withStringBody(json_encode([
                'success' => false,
                'message' => 'Could not mark content as complete. Please try again.'
            ]));
            return $this->response;
        }
    }
}