<?php
/**
 * Script to reset purchase status for a user and a specific course.
 *
 * Usage: php reset_purchase_status.php <user_id> <course_id>
 * Example: php reset_purchase_status.php 14 1
 *
 * WARNING: This script deletes data from your database.
 * ALWAYS BACKUP YOUR DATABASE BEFORE RUNNING.
 */

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

// Bootstrap CakePHP
// Try a more robust way to bootstrap for CLI scripts
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', __DIR__); // Script is in the project root
define('APP_DIR', 'src');
define('CONFIG', ROOT . DS . 'config' . DS);
define('WWW_ROOT', ROOT . DS . 'webroot' . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('LOGS', ROOT . DS . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

require ROOT . '/vendor/autoload.php';
require CONFIG . 'bootstrap.php'; // This should now work better

// Ensure this script is run from CLI
if (PHP_SAPI !== 'cli') {
    die('This script must be run from the command line.');
}

if ($argc < 3) {
    echo "Usage: php reset_purchase_status.php <user_id> <course_id>\n";
    echo "Example: php reset_purchase_status.php 14 1\n";
    exit(1);
}

$userId = filter_var($argv[1], FILTER_VALIDATE_INT);
$courseId = filter_var($argv[2], FILTER_VALIDATE_INT);

if ($userId === false || $userId <= 0) {
    echo "Invalid user_id provided.\n";
    exit(1);
}

if ($courseId === false || $courseId <= 0) {
    echo "Invalid course_id provided.\n";
    exit(1);
}

echo "Attempting to reset purchase status for User ID: {$userId} and Course ID: {$courseId}\n";
echo "WARNING: This will delete associated orders, order items, purchases, and progress records.\n";
echo "Type 'yes' to continue: ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if ($line !== 'yes') {
    echo "Operation cancelled.\n";
    exit(0);
}

echo "Proceeding with deletion...\n";

try {
    $ordersTable = TableRegistry::getTableLocator()->get('Orders');
    $orderItemsTable = TableRegistry::getTableLocator()->get('OrderItems');
    $purchasesTable = TableRegistry::getTableLocator()->get('Purchases');
    $userCourseProgressTable = TableRegistry::getTableLocator()->get('UserCourseProgress');
    $userModuleProgressTable = TableRegistry::getTableLocator()->get('UserModuleProgress');
    $coursesTable = TableRegistry::getTableLocator()->get('Courses'); // To find bundled modules

    $connection = ConnectionManager::get('default');
    $connection->begin();

    echo "----------------------------------------\n";

    // Find OrderItems related to the main course for this user
    // This helps identify the specific orders to target.
    $relatedOrderItems = $orderItemsTable->find()
        ->where([
            'OrderItems.item_type' => 'Course',
            'OrderItems.item_id' => $courseId,
        ])
        ->matching('Orders', function ($q) use ($userId) {
            return $q->where(['Orders.user_id' => $userId]);
        })
        ->select(['OrderItems.order_id'])
        ->distinct(['OrderItems.order_id'])
        ->toArray();

    $orderIdsToDelete = array_column($relatedOrderItems, 'order_id');
    $transactionIdsFromOrders = [];

    if (empty($orderIdsToDelete)) {
        echo "No orders found directly linking User ID {$userId} to Course ID {$courseId} as a main order item.\n";
    } else {
        echo "Found Order IDs to process: " . implode(', ', $orderIdsToDelete) . "\n";

        // Get transaction_ids from these orders to link to purchases
        $ordersToDeleteDetails = $ordersTable->find()
            ->where(['id IN' => $orderIdsToDelete, 'user_id' => $userId])
            ->select(['transaction_id'])
            ->toArray();
        $transactionIdsFromOrders = array_unique(array_filter(array_column($ordersToDeleteDetails, 'transaction_id')));

        if (!empty($transactionIdsFromOrders)) {
            echo "Found Transaction IDs from these orders: " . implode(', ', $transactionIdsFromOrders) . "\n";
            // Delete Purchases linked by transaction_id
            $deletedPurchases = $purchasesTable->deleteAll([
                'transaction_id IN' => $transactionIdsFromOrders,
                'user_id' => $userId // Ensure we only delete for the correct user
            ]);
            echo "Deleted {$deletedPurchases} Purchase record(s) associated with these transaction IDs for User ID {$userId}.\n";
        } else {
            echo "No transaction IDs found on the orders to link to purchases. Skipping purchase deletion by transaction_id.\n";
        }

        // Delete OrderItems for these Orders
        $deletedOrderItems = $orderItemsTable->deleteAll(['order_id IN' => $orderIdsToDelete]);
        echo "Deleted {$deletedOrderItems} OrderItem record(s) for these orders.\n";

        // Delete Orders
        $deletedOrders = $ordersTable->deleteAll(['id IN' => $orderIdsToDelete, 'user_id' => $userId]);
        echo "Deleted {$deletedOrders} Order record(s).\n";
    }
    echo "----------------------------------------\n";

    // Delete UserCourseProgress
    $deletedCourseProgress = $userCourseProgressTable->deleteAll([
        'user_id' => $userId,
        'course_id' => $courseId
    ]);
    echo "Deleted {$deletedCourseProgress} UserCourseProgress record(s) for Course ID {$courseId}.\n";

    // Find modules belonging to the course
    $course = $coursesTable->findById($courseId)->contain(['Modules'])->first();
    if ($course && !empty($course->modules)) {
        $moduleIds = array_map(function ($module) {
            return $module->id;
        }, $course->modules);

        if (!empty($moduleIds)) {
            $deletedModuleProgress = $userModuleProgressTable->deleteAll([
                'user_id' => $userId,
                'module_id IN' => $moduleIds
            ]);
            echo "Deleted {$deletedModuleProgress} UserModuleProgress record(s) for modules bundled with Course ID {$courseId} (Modules: " . implode(', ', $moduleIds) . ").\n";
        } else {
            echo "Course ID {$courseId} has no associated modules to clear progress for.\n";
        }
    } else {
        echo "Could not find Course ID {$courseId} or it has no modules, skipping bundled module progress cleanup.\n";
    }
    echo "----------------------------------------\n";

    $connection->commit();
    echo "Operation completed successfully. Purchase status reset.\n";

} catch (\Throwable $e) {
    if (isset($connection) && $connection->inTransaction()) {
        $connection->rollback();
    }
    echo "An error occurred: " . $e->getMessage() . "\n";
    echo "Operation failed and was rolled back.\n";
    exit(1);
}

exit(0);