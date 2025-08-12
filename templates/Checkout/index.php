<?php
/**
 * @var \App\View\AppView $this
 * @var string $stripePublishableKey // Passed from CheckoutController
 * @var float $cartTotal // Passed from CheckoutController or calculated again
 * @var \App\Model\Entity\CartItem[]|\Cake\Collection\CollectionInterface $cartItems // Optional, for summary
 */

$this->layout = 'jenbury'; // Or your default layout
$this->assign('title', __('Checkout'));

$this->Html->css('checkout.css', ['block' => true]);
// It's crucial to load Stripe.js
$this->Html->script('https://js.stripe.com/v3/', ['block' => true]);
// Also link our custom checkout JS
?>
<script>
    const csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>;
</script>
<?php $this->Html->script('checkout.js', ['block' => true, 'defer' => true]); // Use defer to ensure DOM is ready
?>

<div class="container-fluid mt-5 checkout-page-container"> <!-- Added a general page container for overall padding/margin if needed -->
    <h1><?= __('Checkout') ?></h1>

    <div class="checkout-container">
        <div class="checkout-box order-md-2"> <!-- order-md-2 can be kept if Bootstrap's responsive ordering is still desired, or removed if flex order is handled in checkout.css -->
            <h4 class="d-flex justify-content-between align-items-center mb-3 cart-header">
                <span><?= __('Your cart') ?></span> <!-- Keep text-muted if desired, or style via .cart-header span -->
                <span class="badge bg-secondary rounded-pill"><?= $cart && $cart->cart_items ? count($cart->cart_items) : 0 ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php if ($cart && !empty($cart->cart_items)): ?>
                    <?php foreach ($cart->cart_items as $item):
                        // Access course details if available (controller uses contain(['CartItems.Courses']))
                        $itemName = 'Unknown Item';
                        $itemPrice = 0.00;
                        if ($item->item_type === 'Course' && $item->has('course') && $item->course !== null) {
                            $itemName = $item->course->title;
                            $itemPrice = $item->course->price;
                        } elseif ($item->item_type === 'Module' && $item->has('module') && $item->module !== null) {
                            $itemName = $item->module->title;
                            $itemPrice = $item->module->price;
                        }
                    ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?= h($itemName) ?></h6>
                            <small class="text-muted">Quantity: <?= $item->quantity ?></small>
                        </div>
                        <span class="text-muted"><?= $this->Number->currency($itemPrice * $item->quantity) ?></span>
                    </li>
                    <?php endforeach; ?>
                    <!-- Discount Code Section -->
                    <li class="list-group-item bg-light">
                        <div class="input-group">
                            <input type="text" class="form-control" name="discount_code" id="discount-code-input" placeholder="Discount code" maxlength="5">
                            <button class="btn btn-secondary" type="button" id="apply-discount-button">Apply</button>
                        </div>
                        <div id="discount-feedback" class="mt-2" style="min-height: 20px;"></div> <!-- Placeholder for messages -->
                    </li>
                    <!-- End Discount Code Section -->
                    <li class="list-group-item d-flex justify-content-between" id="summary-subtotal">
                        <span><?= __('Subtotal (AUD)') ?></span>
                        <strong data-original-subtotal="<?= $this->Number->format($totalAmount, ['places' => 2, 'before' => '', 'escape' => false]) ?>"><?= $this->Number->currency($totalAmount) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between text-success" id="summary-discount" style="display: none;">
                        <span><?= __('Discount') ?> (<span id="discount-code-applied"></span>)</span>
                        <strong id="discount-amount"></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between" id="summary-total">
                        <span><?= __('Total (AUD)') ?></span>
                        <strong id="total-amount"><?= $this->Number->currency($totalAmount) ?></strong>
                    </li>
                <?php else: ?>
                     <li class="list-group-item"><?= __('Your cart is empty.') ?></li>
                <?php endif; ?>
            </ul>
        </div> <!-- End first checkout-box (Cart) -->

        <div class="checkout-box order-md-1"> <!-- order-md-1 can be kept or removed -->
            <h4 class="mb-3"><?= __('Payment Details') ?></h4>
            <form id="payment-form">
                <div class="mb-3 form-group"> <!-- Added form-group for consistent spacing -->
                    <label for="card-element"><?= __('Credit or debit card') ?></label>
                    <div id="card-element" class="form-control">
                        <!-- A Stripe Element will be inserted here. -->
                    </div>
                    <!-- Used to display form errors. -->
                    <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                </div>

                <hr class="mb-4">
                <button id="submit-button" class="btn btn-dark btn-lg w-100" type="submit"> <!-- Changed to btn-dark, w-100 for full-width -->
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    <?= __('Pay Now') ?> (<?= $this->Number->currency($totalAmount) ?>)
                </button>
                <p id="payment-message" class="mt-3 text-center"></p>
                <p class="text-muted small mt-3 text-center">Your payment is processed securely. We do not store your credit card details.</p>
            </form>
        </div> <!-- End second checkout-box (Payment) -->
    </div> <!-- End checkout-container -->
<!-- Script moved to the end of the file -->
<!-- Removed duplicated script block that was causing raw JS to render -->
</div> <!-- End checkout-page-container -->

<!-- Pass data to JavaScript -->
<script>
    // Pass the Stripe publishable key and the URL to create the Payment Intent
    const stripePublishableKey = '<?= h($stripePublishableKey) ?>';
    const createPaymentIntentUrl = '<?= $this->Url->build(['controller' => 'Checkout', 'action' => 'createPaymentIntent', '_ext' => 'json']) ?>';
    const orderConfirmationUrlBase = '<?= $this->Url->build(['controller' => 'Orders', 'action' => 'confirmation']) ?>'; // Base URL, ID will be appended
    const checkWebhookStatusUrl = '<?= $this->Url->build(['controller' => 'Checkout', 'action' => 'checkWebhookStatus', '_ext' => 'json']) ?>'; // New: URL for webhook status check
</script>

<?php $this->append('scriptBottom'); // Append the following script to the 'scriptBottom' block ?>
<script>
const applyDiscountUrl = '<?= $this->Url->build(['controller' => 'Checkout', 'action' => 'applyDiscount']) ?>';
    console.log("Checkout page: Document ready. jQuery version:", $.fn.jquery);
    // Function to format currency (basic example, consider a more robust library if needed)
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }

    $(document).ready(function() {
        console.log("Apply discount button clicked.");
        $('#apply-discount-button').on('click', function(event) {
            event.preventDefault(); // Prevent default button action

            const discountCode = $('#discount-code-input').val().trim();
            console.log("Discount code entered:", discountCode);

            const feedbackEl = $('#discount-feedback');
            const discountRow = $('#summary-discount');
            const discountAmountEl = $('#discount-amount');
            const discountCodeAppliedEl = $('#discount-code-applied');
            const totalAmountEl = $('#total-amount');
            const subtotalEl = $('#summary-subtotal strong');
            const payButton = $('#submit-button');
            const originalSubtotal = parseFloat(subtotalEl.data('original-subtotal')); // Get original subtotal

            feedbackEl.text('').removeClass('text-success text-danger'); // Clear previous feedback

            if (!discountCode) {
                console.log("No discount code entered.");
                feedbackEl.text('Please enter a discount code.').addClass('text-danger');
                return;
            }
            console.log("Proceeding with AJAX request.");
            // Disable button during request
            $(this).prop('disabled', true).text('Applying...');

            $.ajax({
                url: applyDiscountUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    discount_code: discountCode
                },
                headers: {
                    'X-CSRF-Token': csrfToken // Defined earlier in the template
                },
                success: function(response) {
                    console.log("AJAX success:", response);
                    feedbackEl.text(response.message);
                    if (response.success) {
                        console.log("Discount applied successfully.");
                        feedbackEl.addClass('text-success');
                        discountCodeAppliedEl.text(discountCode); // Use the code entered or response.code if available
                        discountAmountEl.text('- ' + formatCurrency(response.discountAmount));
                        discountRow.show();
                        totalAmountEl.text(formatCurrency(response.newTotal));
                        // Update pay button text
                        payButton.contents().filter(function() {
                            // Find the text node containing 'Pay Now'
                            return this.nodeType === 3 && this.nodeValue.includes('Pay Now');
                        }).replaceWith('Pay Now (' + formatCurrency(response.newTotal) + ')');

                    } else {
                        console.log("Discount application failed:", response.message);
                        feedbackEl.addClass('text-danger');
                        discountRow.hide();
                        discountAmountEl.text('');
                        discountCodeAppliedEl.text('');
                        totalAmountEl.text(formatCurrency(originalSubtotal)); // Reset to original subtotal
                         // Reset pay button text
                        payButton.contents().filter(function() {
                            return this.nodeType === 3 && this.nodeValue.includes('Pay Now');
                        }).replaceWith('Pay Now (' + formatCurrency(originalSubtotal) + ')');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                    feedbackEl.text('An error occurred while applying the discount. Please try again.').addClass('text-danger');
                    discountRow.hide();
                    discountAmountEl.text('');
                    discountCodeAppliedEl.text('');
                    totalAmountEl.text(formatCurrency(originalSubtotal)); // Reset to original subtotal
                     // Reset pay button text
                    payButton.contents().filter(function() {
                        return this.nodeType === 3 && this.nodeValue.includes('Pay Now');
                    }).replaceWith('Pay Now (' + formatCurrency(originalSubtotal) + ')');
                },
                complete: function() {
                    // Re-enable button
                    $('#apply-discount-button').prop('disabled', false).text('Apply');
                }
            });
        });
    });
</script>
<?php $this->end(); ?>