<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pay with Apple Pay, Google Pay, and PayPal</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}"></script>
    <style>
        #payment-request-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Checkout</h1>

    <h2>Apple Pay / Google Pay (Stripe Payment Request Button)</h2>
    <div id="payment-request-button"></div>
    <div id="stripe-message"></div>

    <hr />

    <h2>PayPal</h2>
    <div id="paypal-button-container"></div>
    <div id="paypal-message"></div>

    <script>
        const stripe = Stripe('{{ config('services.stripe.key') }}');

        // Stripe Payment Request Button (Apple Pay + Google Pay)
        async function setupStripePaymentRequest() {
            const paymentRequest = stripe.paymentRequest({
                country: 'US',
                currency: 'usd',
                total: {
                    label: 'Demo Product',
                    amount: 2000,
                },
                requestPayerName: true,
                requestPayerEmail: true,
            });

            const elements = stripe.elements();
            const prButton = elements.create('paymentRequestButton', {
                paymentRequest,
            });

            const result = await paymentRequest.canMakePayment();

            if (result) {
                prButton.mount('#payment-request-button');
            } else {
                document.getElementById('payment-request-button').style.display = 'none';
                document.getElementById('stripe-message').textContent = 'Apple Pay / Google Pay not available on this device/browser.';
            }

            paymentRequest.on('paymentmethod', async (ev) => {
                try {
                    const response = await fetch('{{ route("stripe.pay") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            payment_method: ev.paymentMethod.id,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        ev.complete('success');
                        alert('Payment successful!');
                    } else {
                        ev.complete('fail');
                        alert('Payment failed: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    ev.complete('fail');
                    alert('Payment failed: ' + error.message);
                }
            });
        }

        setupStripePaymentRequest();

        // PayPal Buttons
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '20.00' // Amount in USD
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return fetch('{{ route("paypal.capture") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        orderID: data.orderID,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert('PayPal payment successful!');
                    } else {
                        alert('PayPal payment failed.');
                    }
                })
                .catch(err => alert('Error capturing PayPal payment: ' + err.message));
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
