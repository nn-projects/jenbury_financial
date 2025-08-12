document.addEventListener('DOMContentLoaded', async () => {
    // Check if Stripe related variables are defined (passed from the PHP template)
    if (typeof stripePublishableKey === 'undefined' || typeof createPaymentIntentUrl === 'undefined' || typeof orderConfirmationUrlBase === 'undefined') {
        console.error('Stripe configuration variables are missing.');
        // Display error to user?
        const paymentMessage = document.getElementById('payment-message');
        if(paymentMessage) {
            paymentMessage.textContent = 'Payment system configuration error. Please contact support.';
        }
        return;
    }

    // 1. Initialize Stripe
    const stripe = Stripe(stripePublishableKey);
    let elements; // Declare elements here to be accessible in updatePaymentIntent
    let cardElement; // Declare cardElement here
    let clientSecret; // Declare clientSecret here

    // Function to fetch/update Payment Intent and re-initialize Stripe elements
    async function initializeOrUpdatePaymentIntent() {
        const paymentMessage = document.getElementById('payment-message');
        const submitButton = document.getElementById('submit-button');
        try {
            const response = await fetch(createPaymentIntentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': csrfToken
                },
            });
            const data = await response.json();
            if (data.clientSecret) {
                clientSecret = data.clientSecret;

                // If elements already exist, update them. Otherwise, create them.
                if (elements) {
                    // Stripe.js v3 doesn't have a direct 'elements.update' for clientSecret.
                    // The recommended way is to re-create elements or the payment intent.
                    // For simplicity here, we'll re-mount the card element if it exists,
                    // assuming the clientSecret is the primary thing that might change
                    // for the *same* elements instance tied to an intent.
                    // A more robust solution for Stripe might involve creating a new PaymentIntent
                    // and then re-creating the elements object if the amount changes significantly.
                    // However, Stripe's confirmCardPayment uses the clientSecret, so updating it is key.
                    // If cardElement exists, unmount and remount.
                    // This is a simplified re-initialization.
                    // A full re-creation of `elements = stripe.elements({ clientSecret });`
                    // and `cardElement = elements.create('card', ...); cardElement.mount(...)`
                    // might be needed if Stripe's internal state for `elements` becomes stale.
                    if (cardElement) {
                        cardElement.unmount();
                    }
                    elements = stripe.elements({ clientSecret }); // Re-init elements with new secret
                    cardElement = elements.create('card', cardStyle);
                    cardElement.mount('#card-element');
                    cardElement.on('change', handleCardElementChange); // Re-attach listener
                    console.log("Payment Intent updated with new clientSecret.");

                } else {
                    elements = stripe.elements({ clientSecret });
                    cardElement = elements.create('card', cardStyle);
                    cardElement.mount('#card-element');
                    cardElement.on('change', handleCardElementChange); // Attach listener
                }
                 if(paymentMessage) paymentMessage.textContent = ''; // Clear init errors
                 if(submitButton) submitButton.disabled = false; // Re-enable button

            } else {
                throw new Error(data.error || 'Could not retrieve payment intent.');
            }
        } catch (error) {
            console.error('Error fetching/updating payment intent:', error);
            if(paymentMessage) {
                paymentMessage.textContent = 'Error initializing payment: ' + error.message;
            }
            if(submitButton) submitButton.disabled = true;
            throw error; // Re-throw to stop further execution if critical
        }
    }

    const cardStyle = {
        // Add style options if needed: https://stripe.com/docs/js/elements_object/create_element?type=card#elements_create-options-style
        style: {
            base: {
                iconColor: '#666EE8',
                color: '#31325F',
                fontWeight: '400',
                fontFamily: 'Arial, sans-serif',
                fontSize: '16px',
                '::placeholder': {
                    color: '#CFD7E0',
                },
            },
        }
    }; // End of cardStyle object

    // cardElement.mount('#card-element'); // REMOVED: This was misplaced. cardElement is created and mounted within initializeOrUpdatePaymentIntent

    // Handle real-time validation errors from the card Element.
    const cardErrors = document.getElementById('card-errors');
    function handleCardElementChange(event) {
        if (event.error) {
            cardErrors.textContent = event.error.message;
        } else {
            cardErrors.textContent = '';
        }
    }

    // Initial fetch of Payment Intent
    await initializeOrUpdatePaymentIntent();

    // --- Discount Code Application ---
    const applyDiscountButton = document.getElementById('apply-discount-button');
    if (applyDiscountButton) {
        applyDiscountButton.addEventListener('click', async (event) => {
            event.preventDefault();
            const discountCodeInput = document.getElementById('discount-code-input');
            const discountCode = discountCodeInput ? discountCodeInput.value.trim() : '';
            const feedbackEl = document.getElementById('discount-feedback');
            const discountRow = document.getElementById('summary-discount');
            const discountAmountEl = document.getElementById('discount-amount');
            const discountCodeAppliedEl = document.getElementById('discount-code-applied');
            const totalAmountEl = document.getElementById('total-amount');
            const subtotalElStrong = document.querySelector('#summary-subtotal strong'); // Corrected selector
            const payButton = document.getElementById('submit-button'); // submitButton already defined globally
            const originalSubtotal = subtotalElStrong ? parseFloat(subtotalElStrong.dataset.originalSubtotal) : 0;

            if (feedbackEl) feedbackEl.textContent = '';
            if (feedbackEl) feedbackEl.classList.remove('text-success', 'text-danger');

            if (!discountCode) {
                if (feedbackEl) {
                    feedbackEl.textContent = 'Please enter a discount code.';
                    feedbackEl.classList.add('text-danger');
                }
                return;
            }

            applyDiscountButton.disabled = true;
            applyDiscountButton.textContent = 'Applying...';

            try {
                const response = await fetch(applyDiscountUrl, { // Ensure applyDiscountUrl is defined globally
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({ discount_code: discountCode })
                });
                const data = await response.json();

                if (feedbackEl) feedbackEl.textContent = data.message;

                if (data.success) {
                    if (feedbackEl) feedbackEl.classList.add('text-success');
                    if (discountCodeAppliedEl) discountCodeAppliedEl.textContent = discountCode;
                    if (discountAmountEl) discountAmountEl.textContent = '- ' + formatCurrency(data.discountAmount);
                    if (discountRow) discountRow.style.display = 'flex'; // Assuming it's a flex container
                    if (totalAmountEl) totalAmountEl.textContent = formatCurrency(data.newTotal);
                    
                    const payButtonTextNode = Array.from(payButton.childNodes).find(node => node.nodeType === Node.TEXT_NODE && node.nodeValue.includes('Pay Now'));
                    if(payButtonTextNode) payButtonTextNode.nodeValue = 'Pay Now (' + formatCurrency(data.newTotal) + ')';


                    // IMPORTANT: Re-fetch/update the Payment Intent
                    await initializeOrUpdatePaymentIntent();
                    console.log("Payment Intent updated after discount application.");

                } else {
                    if (feedbackEl) feedbackEl.classList.add('text-danger');
                    if (discountRow) discountRow.style.display = 'none';
                    if (discountAmountEl) discountAmountEl.textContent = '';
                    if (discountCodeAppliedEl) discountCodeAppliedEl.textContent = '';
                    if (totalAmountEl) totalAmountEl.textContent = formatCurrency(originalSubtotal);

                    const payButtonTextNode = Array.from(payButton.childNodes).find(node => node.nodeType === Node.TEXT_NODE && node.nodeValue.includes('Pay Now'));
                    if(payButtonTextNode) payButtonTextNode.nodeValue = 'Pay Now (' + formatCurrency(originalSubtotal) + ')';
                    
                    // Optionally, re-initialize PI to original amount if discount fails server-side
                    // await initializeOrUpdatePaymentIntent();
                }
            } catch (error) {
                console.error("AJAX Error applying discount:", error);
                if (feedbackEl) {
                    feedbackEl.textContent = 'An error occurred while applying the discount. Please try again.';
                    feedbackEl.classList.add('text-danger');
                }
                if (discountRow) discountRow.style.display = 'none';
                if (totalAmountEl) totalAmountEl.textContent = formatCurrency(originalSubtotal);
                const payButtonTextNode = Array.from(payButton.childNodes).find(node => node.nodeType === Node.TEXT_NODE && node.nodeValue.includes('Pay Now'));
                if(payButtonTextNode) payButtonTextNode.nodeValue = 'Pay Now (' + formatCurrency(originalSubtotal) + ')';
            } finally {
                applyDiscountButton.disabled = false;
                applyDiscountButton.textContent = 'Apply';
            }
        });
    }
    // Helper function for currency formatting (if not already present)
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }


    // 4. Handle Payment Submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const spinner = submitButton.querySelector('.spinner-border');
    const paymentMessage = document.getElementById('payment-message');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        // Disable button and show spinner
        submitButton.disabled = true;
        if(spinner) spinner.style.display = 'inline-block';
        paymentMessage.textContent = ''; // Clear previous messages
        cardErrors.textContent = ''; // Clear previous card errors


        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
                // billing_details: { // Optional: Add billing details if needed/collected
                //     name: 'Jenny Rosen',
                // },
            },
            // return_url: 'http://localhost:8765/orders/confirmation' // Not strictly needed if handling redirect manually
        });

        if (error) {
            // Show error to your customer (e.g., insufficient funds, card declined).
            console.error('Payment failed:', error);
            paymentMessage.textContent = error.message;
            // Re-enable button and hide spinner
            submitButton.disabled = false;
            if(spinner) spinner.style.display = 'none';
        } else {
            // The payment has been processed!
            console.log('Payment successful:', paymentIntent);
            paymentMessage.textContent = 'Processing payment...';

            // Check the PaymentIntent status. The webhook is the primary source of truth for order fulfillment.
            // The frontend redirect happens after confirmation.
            if (paymentIntent.status === 'succeeded') {
                paymentMessage.textContent = 'Payment successful. Waiting for order confirmation...';
                // Start polling the backend to check webhook status
                pollWebhookStatus(paymentIntent.id, paymentMessage, submitButton, spinner);

            } else {
                 // Handle other successful statuses if necessary (e.g., requires_action)
                 // For now, assume 'succeeded' is the only one leading to confirmation page
                 paymentMessage.textContent = `Payment status: ${paymentIntent.status}. Please wait or contact support.`;
                 // Re-enable button and hide spinner (as payment is not fully 'succeeded' yet)
                 submitButton.disabled = false;
                 if(spinner) spinner.style.display = 'none';
            }
        }
    });

    // --- Polling Function to Check Webhook Status ---
    async function pollWebhookStatus(paymentIntentId, messageElement, buttonElement, spinnerElement, attempts = 0) {
        const maxAttempts = 20; // Poll for up to 20 seconds (20 * 1000ms interval)
        const interval = 1000; // Poll every 1 second

        if (attempts >= maxAttempts) {
            console.error('Webhook status check timed out.');
            messageElement.textContent = 'Payment confirmed, but order details are not yet available. Please check your dashboard or contact support.';
            // Re-enable button and hide spinner
            buttonElement.disabled = false;
            if(spinnerElement) spinnerElement.style.display = 'none';
            // Optionally redirect to dashboard or a generic error page after timeout
            // window.location.href = '/dashboard';
            return;
        }

        try {
            const response = await fetch(checkWebhookStatusUrl, { // Use the new URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({ payment_intent_id: paymentIntentId })
            });

            const data = await response.json();

            if (data.is_confirmed) {
                console.log('Webhook confirmed order completion.');
                messageElement.textContent = 'Order confirmed! Redirecting...';
                // Redirect to the confirmation page with the order ID
                if (data.order_id) {
                    window.location.href = `${orderConfirmationUrlBase}/${data.order_id}`;
                } else {
                    // Fallback if order_id is not returned (though backend should provide it)
                    console.warn("Order ID not returned by webhook status check. Redirecting to base confirmation URL.");
                    window.location.href = orderConfirmationUrlBase;
                }
            } else {
                // Webhook not yet completed, poll again
                console.log(`Webhook not yet confirmed. Attempt ${attempts + 1}/${maxAttempts}. Polling again in ${interval}ms.`);
                setTimeout(() => {
                    pollWebhookStatus(paymentIntentId, messageElement, buttonElement, spinnerElement, attempts + 1);
                }, interval);
            }

        } catch (error) {
            console.error('Error polling webhook status:', error);
            messageElement.textContent = 'An error occurred while confirming your order status. Please check your dashboard or contact support.';
            // Re-enable button and hide spinner
            buttonElement.disabled = false;
            if(spinnerElement) spinnerElement.style.display = 'none';
        }
    }
});