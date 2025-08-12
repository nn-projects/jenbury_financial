<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Log\Log; // <--- ADD THIS LINE
 
/**
 * Orders Controller
 *
 * Handles displaying order history and confirmation.
 */
class OrdersController extends AppController
{
    /**
     * Index method (Order History)
     *
     * Displays the logged-in user's order history.
     *
     * @return \Cake\Http\Response|null|void Renders view.
     */
    public function index()
    {
        // Placeholder for fetching user's order history
        // $orders = $this->Orders->findByUserId($this->Auth->user('id'))->all(); // Example
        $this->set('orders', []); // Replace with actual orders later
        $this->Flash->info(__('Order history (index) action placeholder.'));
    }

    /**
     * Confirmation method
     *
     * Displays the order confirmation page. If no ID is provided, it attempts
     * to load the most recent successful order for the current user.
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Renders view for specific order.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found or not accessible by user.
     */
    public function confirmation($id = null)
    {
        Log::debug('OrdersController::confirmation() called.', ['scope' => ['orders_confirmation']]);
        error_log('OrdersController::confirmation() called.');

        $userId = $this->Authentication->getIdentity()->getIdentifier();
        Log::debug('UserID: ' . $userId . ', Requested Order ID: ' . ($id ?? 'None - fetch latest'), ['scope' => ['orders_confirmation']]);
        error_log('OrdersController::confirmation() - UserID: ' . $userId . ', Requested Order ID: ' . ($id ?? 'None - fetch latest'));
        $order = null;
 
        try {
            if ($id) {
                Log::debug('Fetching specific order by ID: ' . $id . ' for User ID: ' . $userId, ['scope' => ['orders_confirmation']]);
                error_log('OrdersController::confirmation() - Fetching specific order by ID: ' . $id . ' for User ID: ' . $userId);
                // Use find to incorporate user_id check and handle deprecation for get() with conditions
                $order = $this->Orders->find()
                    ->where(['Orders.id' => $id, 'Orders.user_id' => $userId])
                    ->contain(['OrderItems', 'OrderItems.Courses', 'OrderItems.Modules'])
                    ->firstOrFail(); // Throws RecordNotFoundException if not found or user_id doesn't match
            } else {
                Log::debug('No Order ID provided, fetching latest order for user ID: ' . $userId, ['scope' => ['orders_confirmation']]);
                error_log('OrdersController::confirmation() - No Order ID, fetching latest for UserID: ' . $userId);
                $query = $this->Orders->find()
                    ->where([
                        'Orders.user_id' => $userId,
                        'Orders.payment_status' => 'completed', // Assuming you only want to show completed orders
                    ])
                    ->contain(['OrderItems', 'OrderItems.Courses', 'OrderItems.Modules'])
                    ->orderBy(['Orders.created' => 'DESC']);
                
                // Log the SQL for debugging
                // Log::debug('Latest order query SQL: ' . $query->sql(), ['scope' => ['orders_confirmation', 'sql']]); // May not work directly, alternative below
                // error_log('Latest order query: ' . print_r($query, true)); // For more detailed query object view if sql() fails

                $order = $query->firstOrFail();
            }

            if ($order) {
                Log::info('Order found. Order ID: ' . $order->id . '. Contains ' . count($order->order_items ?? []) . ' order items.', ['scope' => ['orders_confirmation']]);
                error_log('OrdersController::confirmation() - Order found. ID: ' . $order->id . '. Items: ' . count($order->order_items ?? []));
                // Log::debug('Order data: ' . json_encode($order->toArray(), JSON_PRETTY_PRINT), ['scope' => ['orders_confirmation', 'order_data']]); // Can be verbose
                if (!empty($order->order_items)) {
                    foreach($order->order_items as $idx => $item) {
                        Log::debug('Item ' . $idx . ': type=' . $item->item_type . ', id=' . $item->item_id . ', name=' . ($item->item_name ?? 'N/A') . ', price=' . $item->unit_price, ['scope' => ['orders_confirmation', 'order_item_details']]);
                    }
                }
            } else {
                Log::warning('Order logic resulted in null order object before RecordNotFoundException.', ['scope' => ['orders_confirmation']]);
                error_log('OrdersController::confirmation() - Order object is null unexpectedly.');
            }

            // --- Refresh User Identity in Session ---
            if ($userId && $order) { // Ensure we have a user and the order was successful
                Log::debug('Attempting to refresh user identity in session. UserID: ' . $userId, ['scope' => ['orders_confirmation', 'auth_refresh']]);
                error_log('OrdersController::confirmation() - Attempting to refresh user identity for UserID: ' . $userId);
                try {
                    $UsersTable = $this->fetchTable('Users');
                    $freshUser = $UsersTable->get($userId);

                    if ($freshUser) {
                        $this->Authentication->logout();
                        $this->Authentication->setIdentity($freshUser);
                        $currentIdentity = $this->Authentication->getIdentity();
                        Log::info('User identity refreshed in session. New role: ' . ($currentIdentity ? $currentIdentity->role : 'null'), ['scope' => ['orders_confirmation', 'auth_refresh']]);
                        error_log('OrdersController::confirmation() - User identity refreshed. New role: ' . ($currentIdentity ? $currentIdentity->role : 'null'));
                        
                        // Also update the view variable for currentUser if it was set by AppController
                        if ($this->viewBuilder()->getVar('currentUser')) {
                            $this->set('currentUser', $currentIdentity);
                            Log::debug('View variable "currentUser" updated with refreshed identity.', ['scope' => ['orders_confirmation', 'auth_refresh']]);
                        }
                    } else {
                        Log::warning('Could not fetch fresh user entity for ID: ' . $userId . ' during identity refresh.', ['scope' => ['orders_confirmation', 'auth_refresh']]);
                        error_log('OrdersController::confirmation() - Could not fetch fresh user for ID: ' . $userId);
                    }
                } catch (\Throwable $authRefreshError) {
                    Log::error('Error during user identity refresh: ' . $authRefreshError->getMessage(), ['scope' => ['orders_confirmation', 'auth_refresh_error'], 'exception' => $authRefreshError]);
                    error_log('OrdersController::confirmation() - Error during user identity refresh: ' . $authRefreshError->getMessage());
                    // Do not let this break the confirmation page itself
                }
            }
            // --- End Refresh User Identity ---

        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            Log::error('RecordNotFoundException in confirmation: ' . $e->getMessage(), ['scope' => ['orders_confirmation', 'exception']]);
            error_log('OrdersController::confirmation() - RecordNotFoundException: ' . $e->getMessage());
            $this->Flash->error(__('Order not found or you do not have permission to view it.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        } catch (\Throwable $e) {
            Log::emergency('Unexpected Throwable in confirmation: ' . $e->getMessage(), ['scope' => ['orders_confirmation', 'critical_exception'], 'exception' => $e]);
            error_log('OrdersController::confirmation() - Unexpected Throwable: ' . $e->getMessage());
            $this->Flash->error(__('An unexpected error occurred. Please try again.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
 
        $this->set(compact('order'));
        $this->viewBuilder()->setTemplate('confirmation');
    }
}