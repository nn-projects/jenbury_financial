<?php
declare(strict_types=1);
 
namespace App\Controller;

// Ensure Cake\Log\Log is correctly imported
use Cake\Log\Log; // THIS IS THE PRIMARY LOGGING FACADE WE EXPECT TO WORK

// Keep other essential imports that might be needed by parent or basic functionality
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException; // Restoring for potential use
use Cake\Http\Exception\InternalErrorException; // Restoring for potential use
use Cake\ORM\TableRegistry; // Will be needed when we restore DB logic
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
 
/**
 * Payments Controller
 *
 * Handles incoming webhooks from payment providers (e.g., Stripe).
 */
class PaymentsController extends AppController
{
    /**
     * Initialization hook method.
     */
    public function initialize(): void
    {
        parent::initialize();
        error_log('PaymentsController::initialize() - PHP error_log START');
        Log::info('PaymentsController::initialize() - Cake\Log\Log ATTEMPT.', ['scope' => ['payment_controller_critical_log_test']]);
        
        // Restore unloading FormProtectionComponent for webhook action
        if ($this->request->getParam('action') === 'webhook') {
            if ($this->components()->has('FormProtection')) {
                $this->components()->unload('FormProtection');
                Log::info('FormProtectionComponent unloaded for webhook action.', ['scope' => ['payment_controller_critical_log_test', 'form_protection']]);
                error_log('PaymentsController::initialize() - FormProtection unloaded.');
            } else {
                Log::warning('FormProtectionComponent not found, cannot unload for webhook.', ['scope' => ['payment_controller_critical_log_test', 'form_protection']]);
                error_log('PaymentsController::initialize() - FormProtection NOT found for unload.');
            }
        }
        
        Log::info('PaymentsController::initialize() completed (with FormProtection handling).', ['scope' => ['payment_controller_critical_log_test']]);
        error_log('PaymentsController::initialize() - PHP error_log END');
    }
 
    /**
     * Before Filter callback.
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        error_log('PaymentsController::beforeFilter() - PHP error_log START');
        Log::info('PaymentsController::beforeFilter() - Cake\Log\Log ATTEMPT.', ['scope' => ['payment_controller_critical_log_test']]);

        if ($this->components()->has('Authentication')) { 
            $this->Authentication->allowUnauthenticated(['webhook']);
            Log::info('Authentication::allowUnauthenticated(["webhook"]) called.', ['scope' => ['payment_controller_critical_log_test', 'auth']]);
            error_log('PaymentsController::beforeFilter() - Authentication allowUnauthenticated called.');
        } else {
            Log::warning('Authentication component not loaded.', ['scope' => ['payment_controller_critical_log_test', 'auth']]);
            error_log('PaymentsController::beforeFilter() - Authentication component NOT loaded.');
        }
        Log::info('PaymentsController::beforeFilter() completed.', ['scope' => ['payment_controller_critical_log_test']]);
        error_log('PaymentsController::beforeFilter() - PHP error_log END');
    }
 
    /**
     * Webhook method
     */
    public function webhook()
    {
        // STEP 1: Log entry and basic request info
        error_log('PaymentsController::webhook() - PHP error_log START - Stage 2: Event Handling');
        Log::info('PaymentsController::webhook() - METHOD ENTRY (Stage 2: Event Handling).', ['scope' => ['payment', 'webhook', 'method_entry_stage2']]);
        
        $this->autoRender = false; 
        $this->request->allowMethod(['post']); 
        Log::debug('Request method validated and autoRender set to false.', ['scope' => ['payment', 'webhook', 'initial_setup_stage2']]);
        error_log('PaymentsController::webhook() - autoRender false, method POST allowed.');

        // STEP 2: Attempt to read payload
        $payload = null;
        try {
            $payload = @file_get_contents('php://input');
            Log::info('Read php://input. Payload length: ' . strlen($payload ?? ''), ['scope' => ['payment', 'webhook', 'payload_read_success_stage2']]);
            error_log('PaymentsController::webhook() - Payload length: ' . strlen($payload ?? ''));
            if (empty($payload)) {
                Log::error('Webhook payload is empty after reading php://input.', ['scope' => ['payment', 'webhook', 'empty_payload_error_stage2']]);
                error_log('PaymentsController::webhook() - Payload is empty.');
                return $this->response->withStatus(400, 'Empty webhook payload.');
            }
        } catch (\Throwable $e) {
            Log::emergency('CRITICAL ERROR reading php://input: ' . $e->getMessage(), ['scope' => ['payment', 'webhook', 'payload_read_failure_stage2'], 'exception' => $e]);
            error_log('PaymentsController::webhook() - CRITICAL ERROR reading php://input: ' . $e->getMessage());
            return $this->response->withStatus(500, 'Error reading webhook payload.');
        }

        // STEP 3: Read Stripe configuration & Signature
        $sigHeader = $this->request->getHeaderLine('Stripe-Signature');
        $endpointSecret = null;
        $stripeApiKey = null; 
        try {
            $endpointSecret = Configure::read('Stripe.webhookSecret');
            $stripeApiKey = Configure::read('Stripe.secretKey'); 
            Log::info('Stripe configuration read. Webhook secret is set: ' . !empty($endpointSecret) . ', API key is set: ' . !empty($stripeApiKey), ['scope' => ['payment', 'webhook', 'stripe_config_read_stage2']]);
            error_log('PaymentsController::webhook() - Stripe config read. SecretSet: ' . !empty($endpointSecret) . ' KeySet: ' . !empty($stripeApiKey));

            if (empty($sigHeader) || empty($endpointSecret) || empty($stripeApiKey)) {
                Log::error('Missing Stripe signature, webhook secret, or API key.', [
                    'has_signature' => !empty($sigHeader),
                    'has_webhook_secret' => !empty($endpointSecret),
                    'has_api_key' => !empty($stripeApiKey), 
                    'scope' => ['payment', 'webhook', 'stripe_config_missing_stage2']
                ]);
                error_log('PaymentsController::webhook() - Missing Stripe config. Sig: '.!empty($sigHeader).' Secret: '.!empty($endpointSecret).' Key: '.!empty($stripeApiKey));
                return $this->response->withStatus(400, 'Missing Stripe configuration.');
            }
        } catch (\Throwable $e) {
            Log::emergency('CRITICAL ERROR reading Stripe configuration: ' . $e->getMessage(), ['scope' => ['payment', 'webhook', 'stripe_config_failure_stage2'], 'exception' => $e]);
            error_log('PaymentsController::webhook() - CRITICAL ERROR reading Stripe config: ' . $e->getMessage());
            return $this->response->withStatus(500, 'Error reading Stripe configuration.');
        }
        
        // STEP 4: Set Stripe API key and construct event
        $event = null;
        try {
            Stripe::setApiKey($stripeApiKey);
            Stripe::setApiVersion('2024-04-10'); // Or your Stripe API version
            Log::info('Stripe API key set. Attempting to construct event.', ['scope' => ['payment', 'webhook', 'event_construct_attempt_stage2']]);
            error_log('PaymentsController::webhook() - Stripe API key set. Constructing event.');

            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            Log::info('Stripe event constructed successfully. Event ID: ' . ($event->id ?? 'N/A') . ', Type: ' . ($event->type ?? 'N/A'), ['scope' => ['payment', 'webhook', 'event_construct_success_stage2']]);
            error_log('PaymentsController::webhook() - Stripe event constructed. Type: ' . ($event->type ?? 'N/A'));
            // Log::debug('Full event object: ' . print_r($event, true), ['scope' => ['payment', 'webhook', 'full_event_debug_stage2']]); 

        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage(), ['scope' => ['payment', 'webhook', 'signature_error_stage2'], 'exception' => $e]);
            error_log('PaymentsController::webhook() - Signature verification failed: ' . $e->getMessage());
            return $this->response->withStatus(400, 'Invalid signature.');
        } catch (\UnexpectedValueException $e) { 
            Log::error('Stripe webhook invalid payload: ' . $e->getMessage(), ['scope' => ['payment', 'webhook', 'payload_error_stage2'], 'exception' => $e]);
            error_log('PaymentsController::webhook() - Invalid payload: ' . $e->getMessage());
            return $this->response->withStatus(400, 'Invalid payload.');
        } catch (\Throwable $e) { 
            Log::emergency('CRITICAL ERROR during Stripe event construction or API key setting: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine(), ['scope' => ['payment', 'webhook', 'event_construct_critical_failure_stage2'], 'exception' => $e]);
            error_log('PaymentsController::webhook() - CRITICAL during event construction: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            return $this->response->withStatus(500, 'Webhook processing error during event construction.');
        }

        // STEP 5: Handle the constructed event
        Log::info('Event construction successful. Proceeding to handle event type: ' . ($event->type ?? 'N/A'), ['scope' => ['payment', 'webhook', 'event_handling_entry_stage2']]);
        error_log('PaymentsController::webhook() - Event handling entry. Type: ' . ($event->type ?? 'N/A'));

        try {
            if (!is_object($event) || !isset($event->type)) {
                Log::error('Event object invalid or missing type before switch. Event dump: ' . print_r($event, true), ['scope' => ['payment', 'stripe', 'webhook', 'event_type_missing_preswitch_stage2']]);
                error_log('PaymentsController::webhook() - Event object invalid or missing type before switch.');
                return $this->response->withStatus(400, 'Invalid event format: missing type or not an object.');
            }
            
            Log::debug('Handling event type (inside try): ' . $event->type, ['scope' => ['payment', 'stripe', 'webhook', 'event_handling_start_stage2']]);
            error_log('PaymentsController::webhook() - Handling event type (inside try): ' . $event->type);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    Log::info('Webhook event: payment_intent.succeeded - CASE ENTERED.', ['scope' => ['payment', 'stripe', 'webhook', 'pi_succeeded_case_entry_stage2']]);
                    error_log('PaymentsController::webhook() - payment_intent.succeeded CASE ENTERED.');

                    /** @var \Stripe\PaymentIntent $paymentIntent */
                    $paymentIntent = $event->data->object; 
                    
                    $userId = $paymentIntent->metadata->user_id ?? null;
                    $cartId = $paymentIntent->metadata->cart_id ?? null;
                    $transactionId = $paymentIntent->id ?? 'N/A';
                    $amountReceived = isset($paymentIntent->amount_received) ? ($paymentIntent->amount_received / 100) : 'N/A';

                    Log::info('payment_intent.succeeded: Extracted metadata. UserID: ' . ($userId ?? 'null') . ', CartID: ' . ($cartId ?? 'null') . ', PI_ID: ' . $transactionId . ', Amount: ' . $amountReceived, 
                        ['scope' => ['payment', 'stripe', 'webhook', 'pi_succeeded_metadata_stage2']]);
                    error_log('PaymentsController::webhook() - PI Succeeded Metadata: UserID: ' . ($userId ?? 'null') . ', CartID: ' . ($cartId ?? 'null'));

                    if (!$userId || !$cartId) {
                        Log::error('Missing critical metadata (user_id or cart_id) in payment_intent.succeeded.', ['scope' => ['payment', 'stripe', 'webhook', 'pi_succeeded_metadata_error_stage2']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Missing critical metadata.');
                        return $this->response->withStringBody('Webhook acknowledged, but metadata missing.')->withStatus(200); // Acknowledge, but error logged
                    }

                    // Restore DB connection and table loading
                    Log::debug('Attempting to get DB connection and load tables.', ['scope' => ['payment', 'stripe', 'webhook', 'db_setup_stage3']]);
                    error_log('PaymentsController::webhook() - PI Succeeded: Attempting DB setup.');

                    $connection = null;
                    try {
                        /** @var \App\Model\Table\OrdersTable $ordersTable */
                        $ordersTable = TableRegistry::getTableLocator()->get('Orders');
                        $connection = $ordersTable->getConnection();
                        $connection->begin();
                        Log::info('Database transaction started.', ['scope' => ['payment', 'stripe', 'webhook', 'transaction_begun_stage3']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: DB Transaction started.');

                        /** @var \App\Model\Table\CartsTable $cartsTable */
                        $cartsTable = TableRegistry::getTableLocator()->get('Carts');
                        /** @var \App\Model\Table\CartItemsTable $cartItemsTable */
                        $cartItemsTable = TableRegistry::getTableLocator()->get('CartItems');
                        /** @var \App\Model\Table\OrderItemsTable $orderItemsTable */
                        $orderItemsTable = TableRegistry::getTableLocator()->get('OrderItems');
                        /** @var \App\Model\Table\CoursesTable $coursesTable */
                        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
                        /** @var \App\Model\Table\ModulesTable $modulesTable */
                        $modulesTable = TableRegistry::getTableLocator()->get('Modules');
                        /** @var \App\Model\Table\UsersTable $usersTable */
                        $usersTable = TableRegistry::getTableLocator()->get('Users'); // Add UsersTable
                        Log::info('All required tables loaded.', ['scope' => ['payment', 'stripe', 'webhook', 'tables_loaded_stage3']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Tables loaded.');

                    } catch (\Throwable $e) {
                        Log::emergency('CRITICAL ERROR during DB setup or transaction start: ' . $e->getMessage(), ['scope' => ['payment', 'stripe', 'webhook', 'db_setup_critical_error_stage3'], 'exception' => $e]);
                        error_log('PaymentsController::webhook() - PI Succeeded: CRITICAL ERROR during DB setup: ' . $e->getMessage());
                        if ($connection && $connection->inTransaction()) {
                            $connection->rollback();
                            Log::info('Transaction rolled back due to DB setup error.', ['scope' => ['payment', 'stripe', 'webhook', 'db_setup_rollback_stage3']]);
                            error_log('PaymentsController::webhook() - PI Succeeded: Transaction rolled back (DB setup error).');
                        }
                        return $this->response->withStatus(500, 'Server error during DB setup.');
                    }

                    // Restore Cart fetching and Order creation logic
                    Log::debug('Attempting to fetch cart and create order.', ['scope' => ['payment', 'stripe', 'webhook', 'order_creation_stage4']]);
                    error_log('PaymentsController::webhook() - PI Succeeded: Attempting cart fetch and order creation.');

                    try {
                        $cart = $cartsTable->find()
                            ->where(['id' => $cartId, 'user_id' => $userId])
                            ->first();

                        if (!$cart) {
                            Log::warning(sprintf('Cart ID %s not found for user ID %s during webhook PI %s.', $cartId, $userId, $transactionId), ['scope' => ['payment', 'stripe', 'webhook', 'cart_not_found_stage4']]);
                            error_log('PaymentsController::webhook() - PI Succeeded: Cart not found. CartID: ' . $cartId . ' UserID: ' . $userId);
                            if ($connection && $connection->inTransaction()) $connection->commit(); // Commit as it's not an error state for this webhook if cart is gone
                            return $this->response->withStatus(200, 'Cart already processed or not found.');
                        }
                        Log::debug('Cart found: ' . json_encode($cart), ['scope' => ['payment', 'stripe', 'webhook', 'cart_found_stage4']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Cart found.');

                        $cartItems = $cartItemsTable->find()->where(['cart_id' => $cartId])->all();
                        if ($cartItems->isEmpty()) {
                            Log::warning(sprintf('Cart ID %s was empty for PI %s.', $cartId, $transactionId), ['scope' => ['payment', 'stripe', 'webhook', 'cart_empty_stage4']]);
                            error_log('PaymentsController::webhook() - PI Succeeded: Cart empty. CartID: ' . $cartId);
                            $cartsTable->delete($cart); // Clean up empty cart
                            if ($connection && $connection->inTransaction()) $connection->commit();
                            return $this->response->withStatus(200, 'Empty cart processed.');
                        }
                        Log::debug('Found ' . $cartItems->count() . ' cart items.', ['scope' => ['payment', 'stripe', 'webhook', 'cart_items_found_stage4']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Found ' . $cartItems->count() . ' cart items.');
                        
                        // Calculate subtotal from cart items for proper order creation
                        // This might be a source of error if $cartItem->price is not set or invalid
                        $subtotal = 0;
                        foreach ($cartItems as $ci) { // Use different variable to avoid conflict if $cartItem is used later
                            // Assuming $ci->price and $ci->quantity exist and are valid
                            if (isset($ci->price) && is_numeric($ci->price) && isset($ci->quantity) && is_numeric($ci->quantity)) {
                                $subtotal += $ci->price * $ci->quantity;
                            } else {
                                Log::error('Invalid price or quantity for cart item ID: ' . ($ci->id ?? 'N/A') . ' in cart ID: ' . $cartId, ['scope' => ['payment', 'stripe', 'webhook', 'subtotal_calc_error_stage4']]);
                                error_log('PaymentsController::webhook() - PI Succeeded: Error calculating subtotal for cart item ' . ($ci->id ?? 'N/A'));
                                // Decide handling: throw error, or skip item from subtotal? For now, log and continue.
                            }
                        }
                        Log::debug('Calculated subtotal: ' . $subtotal, ['scope' => ['payment', 'stripe', 'webhook', 'subtotal_calculated_stage4']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Calculated subtotal: ' . $subtotal);

                        // Extract discount info if present in metadata (already available from $paymentIntent)
                        $discountCodeVal = $paymentIntent->metadata->discount_code ?? null;
                        $discountAmountVal = isset($paymentIntent->metadata->discount_amount) ? (float)$paymentIntent->metadata->discount_amount : 0.0;

                        $orderData = [
                            'user_id' => (int)$userId, // Ensure integer
                            'total_amount' => $amountReceived,
                            'subtotal_amount' => $subtotal,
                            'discount_amount' => $discountAmountVal,
                            'payment_status' => 'completed',
                            'discount_code' => $discountCodeVal,
                            'transaction_id' => $transactionId,
                        ];
                        Log::debug('Order data PREPARED for saving: ' . json_encode($orderData), ['scope' => ['payment', 'stripe', 'webhook', 'order_data_save_attempt_stage4']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Order data PREPARED for saving: ' . json_encode($orderData));

                        $order = $ordersTable->newEntity($orderData);
                        if (!$ordersTable->save($order)) {
                            Log::error(sprintf('Failed to save order. Errors: %s', json_encode($order->getErrors())), ['scope' => ['payment', 'database', 'webhook', 'order_save_failure_stage4']]);
                            error_log('PaymentsController::webhook() - PI Succeeded: FAILED to save order. Errors: ' . json_encode($order->getErrors()));
                            if ($connection && $connection->inTransaction()) $connection->rollback();
                            return $this->response->withStatus(500, 'Failed to save order.');
                        }
                        Log::info('Order created successfully with ID: ' . $order->id, ['scope' => ['payment', 'stripe', 'webhook', 'order_created_stage4']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Order created with ID: ' . $order->id);

                        // Restore OrderItem creation logic
                        Log::debug('Attempting to create order items for order ID: ' . $order->id, ['scope' => ['payment', 'stripe', 'webhook', 'order_item_creation_stage5']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Attempting OrderItem creation for OrderID ' . $order->id);
                        $orderItemsData = [];

                        // Calculate and distribute order-level discount among items
                        $itemSpecificDiscounts = [];
                        $cartItemsArray = $cartItems->toArray(); // Convert to array for count and reliable iteration
                        $numCartItems = count($cartItemsArray);
                        $totalDiscountToDistribute = $discountAmountVal; // from $paymentIntent->metadata->discount_amount
                        $runningDistributedDiscount = 0.00;

                        if ($totalDiscountToDistribute > 0 && $subtotal > 0) {
                            Log::debug('Distributing order discount of ' . $totalDiscountToDistribute . ' over subtotal ' . $subtotal, ['scope' => ['payment', 'stripe', 'webhook', 'discount_distribution_start_stage5']]);
                            for ($i = 0; $i < $numCartItems; $i++) {
                                $ci = $cartItemsArray[$i];
                                $itemOriginalTotal = (isset($ci->price) && is_numeric($ci->price) && isset($ci->quantity) && is_numeric($ci->quantity))
                                                    ? (float)$ci->price * (int)$ci->quantity
                                                    : 0;
                                $itemDiscount = 0.00;
                                if ($itemOriginalTotal > 0) {
                                    if ($i === $numCartItems - 1) { // Last item gets the remainder to avoid rounding issues
                                        $itemDiscount = $totalDiscountToDistribute - $runningDistributedDiscount;
                                    } else {
                                        $itemDiscount = round(($itemOriginalTotal / $subtotal) * $totalDiscountToDistribute, 2);
                                    }
                                    // Ensure item discount doesn't make item price negative or exceed item total
                                    $itemDiscount = max(0, min($itemDiscount, $itemOriginalTotal));
                                    $runningDistributedDiscount += $itemDiscount;
                                }
                                $itemSpecificDiscounts[$ci->id] = $itemDiscount; // Assuming $ci->id is unique cart_item_id
                                Log::debug('CartItem ID ' . $ci->id . ': original_total=' . $itemOriginalTotal . ', calculated_discount=' . $itemDiscount, ['scope' => ['payment', 'stripe', 'webhook', 'discount_distribution_item_stage5']]);
                            }
                             // Sanity check: if runningDistributedDiscount is not equal to totalDiscountToDistribute due to rounding with multiple items,
                            // the last item adjustment should handle it. If it's still off, it might indicate an issue.
                            if (abs($runningDistributedDiscount - $totalDiscountToDistribute) > 0.01 && $numCartItems > 0) { // Allow for small float discrepancies
                                Log::warning('Total distributed discount ' . $runningDistributedDiscount . ' does not exactly match order discount ' . $totalDiscountToDistribute . '. This might be due to rounding. Check logic if significant.', ['scope' => ['payment', 'stripe', 'webhook', 'discount_distribution_warning_stage5']]);
                            }

                        } else {
                            foreach ($cartItemsArray as $ci) {
                                $itemSpecificDiscounts[$ci->id] = 0.00;
                            }
                        }
                        Log::debug('Final itemSpecificDiscounts: ' . json_encode($itemSpecificDiscounts), ['scope' => ['payment', 'stripe', 'webhook', 'discount_distribution_final_array_stage5']]);


                        foreach ($cartItemsArray as $cartItem) {
                            Log::debug('Processing cartItem ID: ' . ($cartItem->id ?? 'N/A') . ', Type: ' . ($cartItem->item_type ?? 'N/A'), ['scope' => ['payment', 'stripe', 'webhook', 'cart_item_processing_stage5']]);
                            error_log('PaymentsController::webhook() - Processing cartItem ID: ' . ($cartItem->id ?? 'N/A') . ', Type: ' . ($cartItem->item_type ?? 'N/A'));
                            $itemPrice = 0.0; // This is unit price
                            $itemName = 'Unknown Item';

                            if ($cartItem->item_type === 'Course') {
                                Log::info('Entered COURSE processing block for cartItem ID: ' . ($cartItem->item_id ?? 'N/A'), ['scope' => ['payment', 'stripe', 'webhook', 'course_block_entry_stage5']]);
                                $course = $coursesTable->findById($cartItem->item_id)->contain(['Modules'])->first();
                                if ($course) {
                                    Log::debug('Course FOUND. ID: ' . $course->id . ', Title: ' . ($course->title ?? 'N/A') . ', Price: ' . ($course->price ?? 'N/A') . '. Modules: ' . count($course->modules ?? []), ['scope' => ['payment', 'stripe', 'webhook', 'course_fetch_success_stage5']]);
                                    $itemPrice = (float)($course->price ?? 0.0);
                                    $itemName = $course->title ?? 'Course Title Missing';

                                    // Bundled modules are free, no discount applies to them individually here
                                    if (!empty($course->modules)) {
                                        Log::debug('Course bundle "' . $itemName . '" has ' . count($course->modules) . ' modules.', ['scope' => ['payment', 'stripe', 'webhook', 'course_bundle_processing_stage5']]);
                                        foreach ($course->modules as $moduleInBundle) {
                                            $moduleOrderItemData = [
                                                'order_id' => $order->id,
                                                'item_type' => 'Module',
                                                'item_id' => $moduleInBundle->id,
                                                'quantity' => 1, 'unit_price' => 0.00, 'item_total' => 0.00,
                                                'discount_amount' => 0.00, 'final_price' => 0.00, // Free, so no discount needed
                                                'item_status' => 'purchased', 'refunded_amount' => 0.00,
                                                'item_name' => $moduleInBundle->title ?? 'Module in Bundle',
                                            ];
                                            $orderItemsData[] = $moduleOrderItemData;
                                            Log::debug('Added $0 OrderItem for bundled module ID: ' . $moduleInBundle->id, ['scope' => ['payment', 'stripe', 'webhook', 'course_bundle_item_added_stage5']]);
                                        }
                                    }
                                } else {
                                    Log::warning('Course NOT FOUND with ID: ' . $cartItem->item_id, ['scope' => ['payment', 'stripe', 'webhook', 'course_fetch_failure_stage5']]);
                                }
                            } elseif ($cartItem->item_type === 'Module') {
                                Log::info('Entered MODULE processing block for cartItem ID: ' . ($cartItem->item_id ?? 'N/A'), ['scope' => ['payment', 'stripe', 'webhook', 'module_block_entry_stage5']]);
                                $module = $modulesTable->findById($cartItem->item_id)->first();
                                if ($module) {
                                    Log::debug('Module FOUND. ID: ' . $module->id . ', Title: ' . ($module->title ?? 'N/A') . ', Price: ' . ($module->price ?? 'N/A'), ['scope' => ['payment', 'stripe', 'webhook', 'module_fetch_success_stage5']]);
                                    $itemPrice = (float)($module->price ?? 0.0);
                                    $itemName = $module->title ?? 'Module Title Missing';
                                } else {
                                    Log::warning('Module NOT FOUND with ID: ' . $cartItem->item_id, ['scope' => ['payment', 'stripe', 'webhook', 'module_fetch_failure_stage5']]);
                                }
                            }

                            $itemTotal = $itemPrice * $cartItem->quantity;
                            $itemDiscountToApply = $itemSpecificDiscounts[$cartItem->id] ?? 0.00;
                            // Ensure discount doesn't exceed item total for the main item
                            $itemDiscountToApply = min($itemDiscountToApply, $itemTotal);
                            $itemFinalPrice = $itemTotal - $itemDiscountToApply;

                            $mainOrderItemData = [
                                'order_id' => $order->id, 'item_type' => $cartItem->item_type,
                                'item_id' => $cartItem->item_id, 'quantity' => $cartItem->quantity,
                                'unit_price' => $itemPrice,
                                'item_total' => $itemTotal,
                                'discount_amount' => $itemDiscountToApply, // Use calculated discount
                                'final_price' => $itemFinalPrice,       // Use calculated final price
                                'item_status' => 'purchased', 'refunded_amount' => 0.00,
                                'item_name' => $itemName,
                            ];
                            $orderItemsData[] = $mainOrderItemData;
                            Log::debug('Prepared main OrderItem for cartItem ID ' . ($cartItem->id ?? 'N/A') . ': ' . json_encode($mainOrderItemData), ['scope' => ['payment', 'stripe', 'webhook', 'main_order_item_prep_stage5']]);
                        }

                        if (!empty($orderItemsData)) {
                            $orderItemEntities = $orderItemsTable->newEntities($orderItemsData);
                            if ($orderItemsTable->saveMany($orderItemEntities)) {
                                Log::info('Successfully saved ' . count($orderItemEntities) . ' order items for order ID: ' . $order->id, ['scope' => ['payment', 'stripe', 'webhook', 'order_items_saved_stage5']]);
                                error_log('PaymentsController::webhook() - PI Succeeded: OrderItems saved for OrderID ' . $order->id);
                            } else {
                                $errors = [];
                                foreach($orderItemEntities as $entity) $errors[] = $entity->getErrors();
                                Log::error('Failed to save order items for order ID: ' . $order->id . '. Errors: ' . json_encode($errors), ['scope' => ['payment', 'database', 'webhook', 'order_items_save_failure_stage5']]);
                                error_log('PaymentsController::webhook() - PI Succeeded: FAILED to save OrderItems. Errors: ' . json_encode($errors));
                                if ($connection && $connection->inTransaction()) $connection->rollback();
                                return $this->response->withStatus(500, 'Failed to save order items.');
                            }
                        } else {
                            Log::warning('No order items data prepared for order ID: ' . $order->id, ['scope' => ['payment', 'stripe', 'webhook', 'no_order_items_stage5']]);
                            error_log('PaymentsController::webhook() - PI Succeeded: No OrderItems prepared for OrderID ' . $order->id);
                        }

                        // Restore Cart clearing and Purchase/Progress record creation
                        Log::debug('Attempting to clear cart and create purchase/progress records for OrderID: ' . $order->id, ['scope' => ['payment', 'stripe', 'webhook', 'final_steps_stage6']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Attempting final steps for OrderID ' . $order->id);

                        // Clear the cart
                        $cartItemsTable->deleteAll(['cart_id' => $cartId]);
                        Log::info('Cart items cleared for cart ID: ' . $cartId, ['scope' => ['payment', 'stripe', 'webhook', 'cart_cleared_stage6']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: CartItems cleared for CartID ' . $cartId);
                        
                        $cartsTable->delete($cart);
                        Log::info('Cart record deleted for cart ID: ' . $cartId, ['scope' => ['payment', 'stripe', 'webhook', 'cart_deleted_stage6']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Cart record deleted for CartID ' . $cartId);

                        // --- Grant Access Implementation --- (Restoring with detailed logging)
                        Log::debug('Starting Grant Access Implementation for OrderID: ' . $order->id, ['scope' => ['payment', 'access', 'webhook', 'grant_access_start_stage7']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: Starting Grant Access for OrderID ' . $order->id);

                        /** @var \App\Model\Table\PurchasesTable $purchasesTable */
                        $purchasesTable = TableRegistry::getTableLocator()->get('Purchases');
                        /** @var \App\Model\Table\UserCourseProgressTable $userCourseProgressTable */
                        $userCourseProgressTable = TableRegistry::getTableLocator()->get('UserCourseProgress');
                        /** @var \App\Model\Table\UserModuleProgressTable $userModuleProgressTable */
                        $userModuleProgressTable = TableRegistry::getTableLocator()->get('UserModuleProgress');
                        
                        // Use the $orderItemEntities that were successfully saved
                        foreach ($orderItemEntities as $savedOrderItem) {
                            Log::debug('Grant Access: Processing OrderItem ID ' . $savedOrderItem->id . ', Type: ' . $savedOrderItem->item_type . ', ItemID: ' . $savedOrderItem->item_id, ['scope' => ['payment', 'access', 'webhook', 'grant_access_item_stage7']]);
                            error_log('Grant Access: Processing OrderItem ID ' . $savedOrderItem->id . ', Type: ' . $savedOrderItem->item_type);

                            if (!$userId || !is_numeric($userId)) {
                                Log::error('Invalid user_id for purchase record: ' . var_export($userId, true), ['scope' => ['payment', 'access', 'webhook', 'purchase_userid_error_stage7']]);
                                throw new InternalErrorException('Invalid user_id for purchase record.');
                            }
                            $purchaseData = [
                                'user_id' => (int)$userId,
                                'course_id' => ($savedOrderItem->item_type === 'Course' ? $savedOrderItem->item_id : null),
                                'module_id' => ($savedOrderItem->item_type === 'Module' ? $savedOrderItem->item_id : null),
                                'amount' => $savedOrderItem->final_price ?? 0.00,
                                'payment_status' => 'completed', // Should always be completed here
                                'transaction_id' => $transactionId
                                // 'order_item_id' => $savedOrderItem->id, // Removed: Column does not exist in purchases table
                            ];
                            Log::debug('Purchase data prepared: ' . json_encode($purchaseData), ['scope' => ['payment', 'access', 'webhook', 'purchase_data_prep_stage7']]);
                            $purchase = $purchasesTable->newEntity($purchaseData);
                            
                            error_log('Attempting to save Purchase for OrderItem ID ' . $savedOrderItem->id);
                            if (!$purchasesTable->save($purchase)) {
                                Log::error('Failed to save purchase record: ' . json_encode($purchase->getErrors()) . ' Data: ' . json_encode($purchaseData), ['scope' => ['payment', 'access', 'webhook', 'purchase_save_error_stage7']]);
                                error_log('Failed to save purchase record: ' . json_encode($purchase->getErrors()));
                                throw new InternalErrorException('Failed to save purchase record.');
                            }
                            Log::debug('Purchase record created ID: ' . $purchase->id . ' for OrderItem ID ' . $savedOrderItem->id, ['scope' => ['payment', 'access', 'webhook', 'purchase_created_stage7']]);
                            error_log('Purchase record created ID: ' . $purchase->id . ' for OrderItem ID ' . $savedOrderItem->id);

                            // Progress Records
                            if ($savedOrderItem->item_type === 'Course') {
                                Log::debug('Attempting UserCourseProgress for Course ID ' . $savedOrderItem->item_id, ['scope' => ['payment', 'access', 'webhook', 'course_progress_attempt_stage7']]);
                                error_log('Attempting UserCourseProgress for Course ID ' . $savedOrderItem->item_id);
                                // Check if progress already exists
                                $existingCourseProgress = $userCourseProgressTable->find()->where(['user_id' => (int)$userId, 'course_id' => $savedOrderItem->item_id])->first();
                                if (!$existingCourseProgress) {
                                    $courseProgress = $userCourseProgressTable->newEntity(['user_id' => (int)$userId, 'course_id' => $savedOrderItem->item_id, 'status' => 'not_started']);
                                    if (!$userCourseProgressTable->save($courseProgress)) {
                                        Log::error('Failed to save course progress: ' . json_encode($courseProgress->getErrors()), ['scope' => ['payment', 'access', 'webhook', 'course_progress_error_stage7']]);
                                        error_log('Failed to save course progress: ' . json_encode($courseProgress->getErrors()));
                                        // Decide if this is critical enough to rollback; for now, log and continue
                                    } else {
                                        Log::debug('Course progress created for course ID ' . $savedOrderItem->item_id, ['scope' => ['payment', 'access', 'webhook', 'course_progress_created_stage7']]);
                                        error_log('Course progress created for course ID ' . $savedOrderItem->item_id);
                                    }
                                } else {
                                    Log::info('UserCourseProgress already exists for User ' . $userId . ' Course ' . $savedOrderItem->item_id, ['scope' => ['payment', 'access', 'webhook', 'course_progress_exists_stage7']]);
                                    error_log('UserCourseProgress already exists for User ' . $userId . ' Course ' . $savedOrderItem->item_id);
                                }
                            } elseif ($savedOrderItem->item_type === 'Module') {
                                Log::debug('Attempting UserModuleProgress for Module ID ' . $savedOrderItem->item_id, ['scope' => ['payment', 'access', 'webhook', 'module_progress_attempt_stage7']]);
                                error_log('Attempting UserModuleProgress for Module ID ' . $savedOrderItem->item_id);
                                // Check if progress already exists
                                $existingModuleProgress = $userModuleProgressTable->find()->where(['user_id' => (int)$userId, 'module_id' => $savedOrderItem->item_id])->first();
                                if (!$existingModuleProgress) {
                                    $moduleProgress = $userModuleProgressTable->newEntity(['user_id' => (int)$userId, 'module_id' => $savedOrderItem->item_id, 'status' => 'not_started']);
                                    if (!$userModuleProgressTable->save($moduleProgress)) {
                                        Log::error('Failed to save module progress: ' . json_encode($moduleProgress->getErrors()), ['scope' => ['payment', 'access', 'webhook', 'module_progress_error_stage7']]);
                                        error_log('Failed to save module progress: ' . json_encode($moduleProgress->getErrors()));
                                    } else {
                                        Log::debug('Module progress created for module ID ' . $savedOrderItem->item_id, ['scope' => ['payment', 'access', 'webhook', 'module_progress_created_stage7']]);
                                        error_log('Module progress created for module ID ' . $savedOrderItem->item_id);
                                    }
                                } else {
                                    Log::info('UserModuleProgress already exists for User ' . $userId . ' Module ' . $savedOrderItem->item_id, ['scope' => ['payment', 'access', 'webhook', 'module_progress_exists_stage7']]);
                                    error_log('UserModuleProgress already exists for User ' . $userId . ' Module ' . $savedOrderItem->item_id);
                                }
                            }
                        }
                        Log::info('All purchase and progress records processed for order ID: ' . $order->id, ['scope' => ['payment', 'stripe', 'webhook', 'final_processing_complete_stage7']]);
                        error_log('PaymentsController::webhook() - PI Succeeded: All final steps (purchase/progress) processed for OrderID ' . $order->id);

                        // --- Update User Role from Member to Student ---
                        if ($userId) {
                            $userEntity = $usersTable->get((int)$userId);
                            if ($userEntity && $userEntity->role === 'member') {
                                Log::info('Attempting to upgrade user ' . $userId . ' from member to student after purchase.', ['scope' => ['payment', 'access', 'webhook', 'user_role_update_stage7']]);
                                error_log('PaymentsController::webhook() - Attempting user role upgrade for UserID: ' . $userId);
                                $userEntity->role = 'student';
                                if ($usersTable->save($userEntity)) {
                                    Log::info('User ' . $userId . ' role updated to student in DB.', ['scope' => ['payment', 'access', 'webhook', 'user_role_updated_stage7']]);
                                    error_log('PaymentsController::webhook() - UserID: ' . $userId . ' role updated to student.');
                                } else {
                                    Log::error('Failed to update user ' . $userId . ' role to student. Errors: ' . json_encode($userEntity->getErrors()), ['scope' => ['payment', 'access', 'webhook', 'user_role_update_error_stage7']]);
                                    error_log('PaymentsController::webhook() - FAILED to update role for UserID: ' . $userId . '. Errors: ' . json_encode($userEntity->getErrors()));
                                    // Decide if this is critical enough to rollback; for now, log and continue,
                                    // as the purchase itself was successful.
                                }
                            } else {
                                Log::info('User ' . $userId . ' not found or role is not "member" (' . ($userEntity ? $userEntity->role : 'not found') . '). No role update needed.', ['scope' => ['payment', 'access', 'webhook', 'user_role_no_update_needed_stage7']]);
                                error_log('PaymentsController::webhook() - UserID: ' . $userId . ' role is ' . ($userEntity ? $userEntity->role : 'not found') . '. No update.');
                            }
                        }
                        // --- End Update User Role ---

                        if ($connection && $connection->inTransaction()) {
                            $connection->commit();
                            Log::info('Transaction committed successfully for order ID: ' . $order->id, ['scope' => ['payment', 'stripe', 'webhook', 'transaction_commit_final_stage6']]);
                            error_log('PaymentsController::webhook() - PI Succeeded: Transaction committed (final).');
                        }
                        return $this->response->withStringBody('Webhook payment_intent.succeeded processed successfully. Order ID: ' . $order->id)->withStatus(200);

                    } catch (\Throwable $e) {
                        Log::emergency('CRITICAL ERROR during cart/order processing: ' . $e->getMessage(), ['scope' => ['payment', 'stripe', 'webhook', 'cart_order_critical_error_stage4'], 'exception' => $e]);
                        error_log('PaymentsController::webhook() - PI Succeeded: CRITICAL ERROR during cart/order processing: ' . $e->getMessage());
                        if ($connection && $connection->inTransaction()) {
                            $connection->rollback();
                            Log::info('Transaction rolled back due to cart/order processing error.', ['scope' => ['payment', 'stripe', 'webhook', 'cart_order_rollback_stage4']]);
                            error_log('PaymentsController::webhook() - PI Succeeded: Transaction rolled back (cart/order error).');
                        }
                        return $this->response->withStatus(500, 'Server error during cart/order processing.');
                    }
                    break; // Added break for payment_intent.succeeded

                case 'charge.succeeded':
                case 'charge.updated':
                // Add other event types here that you want to acknowledge but not process
                    Log::info('Webhook event received and acknowledged: ' . $event->type, ['scope' => ['payment', 'stripe', 'webhook', 'event_acknowledged']]);
                    error_log('PaymentsController::webhook() - Acknowledged event type: ' . $event->type);
                    return $this->response->withStringBody('Webhook event ' . $event->type . ' received and acknowledged.')->withStatus(200);
                
                default:
                    Log::info('Received unhandled event type: ' . $event->type, ['scope' => ['payment', 'stripe', 'webhook', 'unhandled_event_stage2']]);
                    error_log('PaymentsController::webhook() - Unhandled event type: ' . $event->type);
                    return $this->response->withStringBody('Webhook unhandled event type received.')->withStatus(200);
            }
        } catch (\Throwable $e) {
            Log::emergency('CRITICAL UNHANDLED EXCEPTION in webhook event handling switch: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine(),
                ['scope' => ['payment', 'stripe', 'webhook', 'event_handling_critical_exception_stage2'], 'exception_trace' => $e->getTraceAsString()]);
            error_log('PaymentsController::webhook() - CRITICAL EXCEPTION in event handling switch: ' . $e->getMessage());
            return $this->response->withStatus(500, 'Critical internal server error during webhook event handling.');
        }
        // Fallback, should ideally be handled by one of the returns above.
        Log::error('Reached end of webhook method without explicit return after event handling.', ['scope' => ['payment', 'webhook', 'unexpected_fallthrough_stage2']]);
        error_log('PaymentsController::webhook() - Reached end of method unexpectedly.');
        return $this->response->withStatus(500, 'Webhook processing ended unexpectedly.');
    }
    
    // ... (rest of the original class, if any, though it was mostly the webhook method)
}