<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>

    <!-- Fonts -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

<body class="antialiased">

    <div class="container">
        <h2 class="mt-5">Payment Page</h2>
        <form id="payment-form">
            @csrf
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" class="form-control" id="customer_name" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount (INR)</label>
                <input type="number" class="form-control" id="amount" required>
            </div>
            <button type="button" class="btn btn-primary" id="pay-button">Pay</button>
        </form>
    </div>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="success-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">Payment successfully completed.</div>
        </div>
    </div>

    <!-- Toast Message for Error -->
    <div id="error-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">Payment verification failed.</div>
    </div>
    <script>
        document.getElementById('pay-button').onclick = function() {
            var customer_name = document.getElementById('customer_name').value;
            var amount = document.getElementById('amount').value;
            if (customer_name && amount) {

                console.log(customer_name, amount)
                fetch('/payment/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            amount: amount
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // console.log(amount)
                        var options = {
                            key: '{{ env('
                            RAZORPAY_KEY_ID ') }}', // Get the Razorpay key from the env file
                            amount: amount * 100,
                            currency: data.order.currency,
                            name: customer_name,
                            description: "Payment",
                            order_id: data.order.id,
                            handler: function(response) {
                                fetch('/payment/success', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({
                                            customer_name: customer_name,
                                            razorpay_payment_id: response.razorpay_payment_id,
                                            amount: data.order.amount
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(res => {
                                        if (res.status == 'success') {
                                            document.getElementById('success-toast').classList.add('show');
                                        } else {
                                            document.getElementById('error-toast').classList.add('show');
                                        }
                                    });
                            },
                            theme: {
                                color: "#3399cc"
                            }
                        };

                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    })
                    .catch(error => alert('Payment initialization failed.'));
            } else {
                alert('Please fill all fields.');
            }
        };
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>