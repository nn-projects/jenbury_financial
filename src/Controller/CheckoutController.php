<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

/**
 * Checkout Controller
 *
 * Handles the checkout process and payment intent creation.
 */
class CheckoutController extends AppController
{
    /**
     * Initialization hook method.
     *
     * Overrides parent to unload FormProtectionComponent immediately after it's loaded.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize(); // Load components from AppController, including FormProtection

        // Unload FormProtection immediately
        if ($this->components()->has('FormProtection')) {
            $this->components()->unload('FormProtection');
            \Cake\Log\Log::debug('FormProtectionComponent unloaded in CheckoutController::initialize().', ['scope' => ['payment', 'debug']]);
        }

        // Ensure Authentication component is still loaded if needed
        if (!$this->components()->has('Authentication')) {
             $this->loadComponent('Authentication.Authentication');
        }

        // $this->loadModel('Carts'); // Keep commented out unless needed
    }

    // Removed beforeFilter method

    /**
     * Checkout method
     *
     * Initiates the checkout process, displays summary, and collects details.
     *
     * @return \Cake\Http\Response|null|void Renders view.
     */
    public function index()
    {
        $userId = $this->Authentication->getIdentity() ? $this->Authentication->getIdentity()->getIdentifier() : null;

        if (!$userId) {
            // User is not logged in, handle gracefully
            $this->Flash->info(__('Please log in or register to proceed to checkout.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']); // Redirect to login page
        }

        // Fetch the publishable key from configuration
        $stripePublishableKey = Configure::read('Stripe.publishableKey');
        if (!$stripePublishableKey) {
            Log::error('Stripe publishable key is not configured.', ['scope' => ['payment', 'stripe']]);
            $this->Flash->error(__('Payment system configuration error. Please contact support.'));
            // Optionally redirect or handle the error more gracefully
            // return $this->redirect(['controller' => 'Cart', 'action' => 'view']);
            throw new InternalErrorException('Stripe publishable key missing.');
        }

        // Fetch cart details (example) - Adapt as per your actual cart logic
        /** @var \App\Model\Table\CartsTable $cartsTable */
        $cartsTable = TableRegistry::getTableLocator()->get('Carts');
        $cart = $cartsTable->find()
            ->where(['user_id' => $userId])
            ->contain(['CartItems.Courses', 'CartItems.Modules']) // Load both Courses and Modules
            ->first();

        if (!$cart || empty($cart->cart_items)) {
            $this->Flash->warning(__('Your cart is empty.'));
            return $this->redirect(['controller' => 'Courses', 'action' => 'index']); // Redirect to courses or cart view
        }

        // Check if cart contains both courses and modules
        $hasCourses = false;
        $hasModules = false;
        foreach ($cart->cart_items as $item) {
            if (!empty($item->course_id) || !empty($item->course)) {
                $hasCourses = true;
            }
            if (!empty($item->module_id) || !empty($item->module)) {
                $hasModules = true;
            }
        }

        if ($hasCourses && $hasModules) {
            $this->Flash->error(__('You cannot purchase courses and modules in the same transaction. Please create separate orders.'));
            // Redirect to cart view or another appropriate page
            return $this->redirect(['controller' => 'Carts', 'action' => 'view']);
        }

        $totalAmount = $cartsTable->calculateTotal($cart->id);

        // Pass the key and other necessary data to the view
        $this->set(compact('stripePublishableKey', 'cart', 'totalAmount'));

        // Set URLs needed by checkout.js (ensure these routes exist in config/routes.php)
        $this->set('createPaymentIntentUrl', \Cake\Routing\Router::url(['controller' => 'Checkout', 'action' => 'createPaymentIntent', '_ext' => 'json']));
        $this->set('orderConfirmationUrlBase', \Cake\Routing\Router::url(['controller' => 'Orders', 'action' => 'confirmation'], true)); // Base URL for confirmation

        // No longer just a placeholder
        // $this->Flash->info(__('Checkout action placeholder.'));
    }

    /**
     * Create Payment Intent method
     *
     * Creates a Stripe Payment Intent. Usually called via AJAX from the checkout page.
     *
     * @return \Cake\Http\Response|null|void Renders JSON response.
     */
    public function createPaymentIntent()
    {
        Log::debug('Entered createPaymentIntent action.', ['scope' => ['payment', 'debug', 'entry']]); // ADDED ENTRY LOG
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setClassName('Json');

        $userId = $this->Authentication->getIdentity()->getIdentifier();
        if (!$userId) {
            throw new BadRequestException('User not authenticated.');
        }

        /** @var \App\Model\Table\CartsTable $cartsTable */
        $cartsTable = TableRegistry::getTableLocator()->get('Carts');
        $cart = $cartsTable->find()
            ->where(['user_id' => $userId])
            ->contain(['CartItems']) // Load items to ensure cart isn't empty conceptually
            ->first();

        if (!$cart || empty($cart->cart_items)) {
             Log::warning(sprintf('Attempt to create payment intent for empty or non-existent cart for user ID %d', $userId), ['scope' => ['payment', 'stripe']]);
             throw new BadRequestException('Cannot create payment intent for an empty cart.');
        }

        try {
            // Calculate original subtotal
            $subtotalAmount = $cartsTable->calculateTotal($cart->id);
            Log::debug(sprintf('Calculated cart subtotal for cart ID %d: %.2f', $cart->id, $subtotalAmount), ['scope' => ['payment', 'stripe', 'debug']]);

            // Check for discount in session
            $session = $this->request->getSession();
            $discountInfo = $session->read('Discount');
            $finalAmount = $subtotalAmount;
            $discountCode = null;
            $discountAmount = 0.0;

            // Apply discount if valid and matches the cart
            if ($discountInfo && isset($discountInfo['amount'], $discountInfo['code']) && ($discountInfo['applied_to_cart_id'] ?? null) === $cart->id) {
                $discountAmount = (float)$discountInfo['amount'];
                $discountCode = (string)$discountInfo['code'];
                $finalAmount = round($subtotalAmount - $discountAmount, 2);
                Log::debug(sprintf('Discount "%s" (%.2f) applied. Final amount: %.2f', $discountCode, $discountAmount, $finalAmount), ['scope' => ['payment', 'stripe', 'discount', 'debug']]);
            } else {
                 Log::debug('No valid discount found in session or cart ID mismatch.', ['scope' => ['payment', 'stripe', 'discount', 'debug']]);
                 // Ensure any stale discount info is cleared if it doesn't match the cart
                 if ($discountInfo) $session->delete('Discount');
            }

            if ($finalAmount <= 0 && $subtotalAmount > 0) { // Allow free checkouts if subtotal was > 0 but discount made it free
                Log::info(sprintf('Final amount is zero or less after discount for cart ID %d, user ID %d. Subtotal: %.2f', $cart->id, $userId, $subtotalAmount), ['scope' => ['payment', 'stripe', 'discount']]);
            } elseif ($finalAmount <= 0) {
                 Log::warning(sprintf('Calculated subtotal amount is zero or less for cart ID %d, user ID %d', $cart->id, $userId), ['scope' => ['payment', 'stripe', 'webhook']]);
                 throw new BadRequestException('Cart total must be positive.');
            }
            $amountInCents = (int)round($finalAmount * 100); // Convert final amount to cents, use round() before int cast
            Log::debug(sprintf('Amount in cents: %d', $amountInCents), ['scope' => ['payment', 'stripe', 'debug']]); // Log cents

            // Initialize Stripe
            $secretKey = Configure::read('Stripe.secretKey');
            if (empty($secretKey)) {
                Log::error('Stripe secret key is missing or empty in configuration.', ['scope' => ['payment', 'stripe', 'config']]);
                throw new InternalErrorException('Stripe configuration error.');
            }
            Log::debug('Stripe secret key read successfully (not logging the key itself).', ['scope' => ['payment', 'stripe', 'debug']]); // Log key presence
            Stripe::setApiKey($secretKey);
            Stripe::setApiVersion('2024-04-10'); // Optional: Pin API version

            // Create PaymentIntent
            Log::debug('Attempting to create Stripe PaymentIntent...', ['scope' => ['payment', 'stripe', 'debug']]);
            $metadata = [
                'user_id' => $userId,
                'cart_id' => $cart->id,
                // Add other relevant metadata if needed
            ];
            if ($discountCode && $discountAmount > 0) {
                $metadata['discount_code'] = $discountCode;
                $metadata['discount_amount'] = sprintf('%.2f', $discountAmount); // Store as string for consistency
            }
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'aud', // Or get from config/request if dynamic
                'automatic_payment_methods' => ['enabled' => true], // Recommended by Stripe
                'metadata' => $metadata,
                // 'customer' => $stripeCustomerId, // Optional: If you manage Stripe Customers
            ]);

            $clientSecret = $paymentIntent->client_secret;
            $this->set(compact('clientSecret'));
            // Explicitly set serialize option on viewBuilder instead of using set()
            $this->viewBuilder()->setOption('serialize', ['clientSecret']);
            // $this->set('_serialize', ['clientSecret']); // Remove this line
            Log::debug(sprintf('Successfully created PaymentIntent %s and set clientSecret for JSON response.', $paymentIntent->id), ['scope' => ['payment', 'stripe', 'debug']]);

        } catch (ApiErrorException $e) {
            // Log detailed Stripe API error
            Log::error(sprintf(
                'Stripe API error during PaymentIntent creation for user %d, cart %d: [%s] %s. Stripe Request ID: %s',
                $userId,
                $cart->id,
                $e->getStripeCode() ?? 'N/A', // Get Stripe-specific error code if available
                $e->getMessage(),
                $e->getRequestId() ?? 'N/A' // Get Stripe request ID for tracing
            ), ['scope' => ['payment', 'stripe', 'error']]);
            throw new InternalErrorException('Could not initiate payment. Please try again.');
        } catch (\Exception $e) {
            // Log generic error, including type
            Log::error(sprintf(
                'Generic error (%s) during PaymentIntent creation for user %d, cart %d: %s',
                get_class($e),
                $userId,
                $cart->id ?? 'N/A',
                $e->getMessage()
            ), ['scope' => ['payment', 'error']]);
            // Optionally log stack trace for generic errors
            // Log::error($e->getTraceAsString(), ['scope' => ['payment', 'error', 'trace']]);
            throw new InternalErrorException('An unexpected error occurred.');
        }
    }
/**
     * Apply Discount Code method
     *
     * Applies a discount code to the user's cart via AJAX.
     *
     * @return \Cake\Http\Response|null Renders JSON response.
     */
    public function applyDiscount()
    {
        $this->request->allowMethod(['post']);
        // Ensure this is an AJAX request
        if (!$this->request->is('ajax')) {
            throw new BadRequestException('AJAX request expected.');
        }

        // Disable layout and view rendering for JSON response
        $this->viewBuilder()->disableAutoLayout();
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');

        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $discountCodeInput = $this->request->getData('discount_code');
        $response = ['success' => false, 'message' => 'An error occurred.']; // Default response

        if (empty($discountCodeInput)) {
            $response['message'] = 'Please enter a discount code.';
            return $this->response->withStringBody(json_encode($response));
        }

        try {
            // Load necessary tables
            /** @var \App\Model\Table\DiscountCodesTable $discountCodesTable */
            $discountCodesTable = TableRegistry::getTableLocator()->get('DiscountCodes');
            /** @var \App\Model\Table\CartsTable $cartsTable */
            $cartsTable = TableRegistry::getTableLocator()->get('Carts');

            // Find the user's cart
            $cart = $cartsTable->find()
                ->where(['user_id' => $userId])
                ->contain(['CartItems']) // Need items to calculate total
                ->first();

            if (!$cart || empty($cart->cart_items)) {
                $response['message'] = 'Your cart is empty.';
                return $this->response->withStringBody(json_encode($response));
            }

            // Find the discount code
            /** @var \App\Model\Entity\DiscountCode|null $discountCode */
            $discountCode = $discountCodesTable->find()
                ->where([
                    'code' => $discountCodeInput,
                    'is_active' => true, // Only active codes
                    // Optional: Add date validation if your table has start/end dates
                    // 'valid_from <=' => new \DateTime(),
                    // 'valid_to >=' => new \DateTime(),
                ])
                ->first();

            if ($discountCode) {
                // Calculate original total
                $subtotal = $cartsTable->calculateTotal($cart->id);
                $discountPercentage = $discountCode->percentage;
                $discountAmount = round(($subtotal * $discountPercentage) / 100, 2);
                $newTotal = round($subtotal - $discountAmount, 2);

                // Store discount details in session
                $session = $this->request->getSession();
                $session->write('Discount', [
                    'code' => $discountCode->code,
                    'percentage' => $discountPercentage,
                    'amount' => $discountAmount,
                    'applied_to_cart_id' => $cart->id // Optional: Link discount to specific cart state
                ]);

                $response = [
                    'success' => true,
                    'message' => sprintf('Discount "%s" applied!', $discountCode->code),
                    'discountAmount' => $discountAmount,
                    'newTotal' => $newTotal,
                    'originalSubtotal' => $subtotal // Send original subtotal for display consistency
                ];
                Log::debug(sprintf('Discount %s applied for user %d, cart %d. Amount: %.2f, New Total: %.2f', $discountCode->code, $userId, $cart->id, $discountAmount, $newTotal), ['scope' => ['discount', 'checkout']]);

            } else {
                // Invalid or inactive code
                $response['message'] = 'Invalid or expired discount code.';
                // Clear any previous discount from session if invalid code is entered
                $this->request->getSession()->delete('Discount');
                Log::warning(sprintf('Invalid discount code "%s" attempted by user %d for cart %d.', $discountCodeInput, $userId, $cart->id ?? 'N/A'), ['scope' => ['discount', 'checkout', 'warning']]);
            }

        } catch (\Exception $e) {
            Log::error(sprintf('Error applying discount code for user %d: %s', $userId, $e->getMessage()), ['scope' => ['discount', 'checkout', 'error']]);
            // Keep the generic error message for the user
             $response['message'] = 'Could not apply discount code due to a system error.';
             // Ensure potentially sensitive session data is cleared on error
             $this->request->getSession()->delete('Discount');
        }

        return $this->response->withStringBody(json_encode($response));
    }

    /**
     * Check Webhook Status method
     *
     * Checks if the webhook for a given Payment Intent has completed
     * and marked the corresponding order as 'completed'.
     * Called via AJAX from the checkout page after payment confirmation.
     *
     * @return \Cake\Http\Response|null Renders JSON response.
     */
    public function checkWebhookStatus()
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setClassName('Json');

        $userId = $this->Authentication->getIdentity() ? $this->Authentication->getIdentity()->getIdentifier() : null;
        if (!$userId) {
            // Return false if user is not authenticated, though client-side should prevent this
            return $this->response->withStringBody(json_encode(['is_confirmed' => false, 'message' => 'User not authenticated.']))->withStatus(401);
        }

        $paymentIntentId = $this->request->getData('payment_intent_id');

        if (empty($paymentIntentId)) {
            return $this->response->withStringBody(json_encode(['is_confirmed' => false, 'message' => 'Missing Payment Intent ID.']))->withStatus(400);
        }

        try {
            /** @var \App\Model\Table\OrdersTable $ordersTable */
            $ordersTable = TableRegistry::getTableLocator()->get('Orders');

            // Find the order associated with this payment intent and user
            $order = $ordersTable->find()
                ->where([
                    'Orders.transaction_id' => $paymentIntentId,
                    'Orders.user_id' => $userId,
                    'Orders.payment_status' => 'completed', // Check for completed status set by webhook
                ])
                ->first();

            if ($order) {
                // Order found and completed by webhook
                Log::debug(sprintf('Webhook status check: Order %d found and completed for PI %s, user %d.', $order->id, $paymentIntentId, $userId), ['scope' => ['payment', 'webhook_check']]);
                return $this->response->withStringBody(json_encode(['is_confirmed' => true, 'order_id' => $order->id]));
            } else {
                // Order not yet found or not completed
                 Log::debug(sprintf('Webhook status check: Order not yet completed for PI %s, user %d.', $paymentIntentId, $userId), ['scope' => ['payment', 'webhook_check']]);
                return $this->response->withStringBody(json_encode(['is_confirmed' => false, 'message' => 'Order not yet confirmed.']));
            }

        } catch (\Throwable $e) {
            Log::error(sprintf('Error checking webhook status for PI %s, user %d: %s', $paymentIntentId, $userId, $e->getMessage()), ['scope' => ['payment', 'webhook_check', 'error']]);
            return $this->response->withStringBody(json_encode(['is_confirmed' => false, 'message' => 'An error occurred checking status.']))->withStatus(500);
        }
    }
}