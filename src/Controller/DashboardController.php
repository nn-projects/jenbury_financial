<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Log\Log; // Import Log class
use Throwable; // Import base Throwable interface for catching exceptions/errors
use Cake\Collection\Collection; // Import Collection class

/**
 * Dashboard Controller
 */
class DashboardController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Get the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();

        // Load the Purchases model
        $purchases = $this->fetchTable('Purchases');

        // Get all course purchases
        $courseQuery = $purchases->find()
            ->where([
                'user_id' => $userId,
                'course_id IS NOT NULL',
                'payment_status' => 'completed',
            ])
            ->contain(['Courses' => ['Modules']]); // Contain courses and their modules

        // Use all() instead of paginate if we need all course IDs for filtering later
        // Or paginate first, then extract IDs if the dataset isn't too large
        $coursePurchases = $this->paginate($courseQuery, ['limit' => 10]); // Removed deprecated alias

        // Get all module purchases
        $moduleQuery = $purchases->find()
            ->where([
                'user_id' => $userId,
                'module_id IS NOT NULL',
                'payment_status' => 'completed',
            ])
            ->contain(['Modules']); // Contain modules

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

        // Pass course purchases and STANDALONE module purchases to the view
        $this->set(compact('coursePurchases', 'standaloneModulePurchases')); // Pass filtered modules
    }

    /**
     * Purchase History method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function purchaseHistory()
    {
        // Get the current user
        $user = $this->Authentication->getIdentity();
        $userId = $user->getIdentifier();

        // Load the Purchases model
        $purchases = $this->fetchTable('Purchases');

        // Get all purchases
        $query = $purchases->find()
            ->where([
                'user_id' => $userId,
            ])
            ->contain(['Courses', 'Modules'])
            ->order(['Purchases.created' => 'DESC']);

        $allPurchases = $query->all(); // Get all purchases before filtering for pagination

        $filteredPurchases = [];
        $purchasedCourseIds = [];

        // First, collect all purchased course IDs
        foreach ($allPurchases as $p) {
            if ($p->course_id && $p->payment_status === 'completed') {
                $purchasedCourseIds[$p->course_id] = true;
            }
        }

        // Then, filter the purchases
        foreach ($allPurchases as $purchase) {
            if ($purchase->course_id) { // It's a course purchase
                $filteredPurchases[] = $purchase;
            } elseif ($purchase->module_id) { // It's a module purchase
                // Check if the module belongs to an already purchased course
                $moduleBelongsToPurchasedCourse = false;
                if ($purchase->module && $purchase->module->course_id) {
                    if (isset($purchasedCourseIds[$purchase->module->course_id])) {
                        $moduleBelongsToPurchasedCourse = true;
                    }
                }
                // Only add module purchase if it's standalone (not part of an already listed course purchase)
                // and its price is not $0 (unless it's genuinely free)
                // The original problem stated $0 modules appear when a course is bought.
                // So, if it belongs to a purchased course, we skip it.
                if (!$moduleBelongsToPurchasedCourse) {
                    $filteredPurchases[] = $purchase;
                }
            } else {
                // Unknown item type, include for now or decide how to handle
                $filteredPurchases[] = $purchase;
            }
        }

        // Paginate the filtered results
        // Note: The PaginatorComponent typically works with a Query object.
        // For an array, we might need to manually slice or use Collection pagination.
        // For simplicity with the existing Paginator, we'll re-query with IDs if this becomes an issue,
        // or adjust if there's a direct way to paginate an array of entities.
        // For now, let's pass the filtered array and see if the view/paginator handles it.
        // A more robust way would be to adjust the initial query based on this logic,
        // but that can be more complex.

        // Convert array to Collection for pagination
        $purchasesCollection = new Collection($filteredPurchases);
        $paginatedPurchases = $this->paginate($purchasesCollection, ['limit' => 10]);


        $this->set('purchases', $paginatedPurchases); // Pass paginated filtered purchases
    }

    /**
     * Activity Feed method (AJAX/JSON)
     * Fetches recent user progress events.
     *
     * @return \Cake\Http\Response Returns JSON response
     */
    public function activityFeed()
    {
        // This method remains unchanged for this task
        $this->request->allowMethod(['get']);
        $this->viewBuilder()->setLayout('ajax');
        $this->viewBuilder()->setClassName('Json');

        $recentActivities = []; // Default to empty array

        try {
            $user = $this->Authentication->getIdentity();
            $userId = $user->getIdentifier();
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
                        'timestamp' => 'UserModuleProgress.created',
                        'type' => "'module_completion'",
                    ])
                    ->contain(['Modules']) // Restore contain
                    ->where(['UserModuleProgress.user_id' => $userId])
                    ->orderBy(['UserModuleProgress.created' => 'DESC']) // Use orderBy
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
} // Closing brace for the DashboardController class