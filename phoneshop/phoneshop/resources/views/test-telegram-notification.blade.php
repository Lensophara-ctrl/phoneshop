<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Telegram Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fa-brands fa-telegram me-2"></i>Telegram Notification Test
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>Note:</strong> Make sure you have configured your Telegram Bot Token and Chat ID in the Settings page.
                        </div>

                        <h5 class="mb-3">Test Different Notification Types:</h5>

                        <div class="d-grid gap-3">
                            <form action="{{ route('test.telegram') }}" method="GET" target="_blank">
                                <button type="submit" class="btn btn-primary w-100 py-3">
                                    <i class="fa-solid fa-paper-plane me-2"></i>
                                    Test Basic Notification
                                </button>
                            </form>

                            <button type="button" class="btn btn-success w-100 py-3" onclick="testPaymentNotification()">
                                <i class="fa-solid fa-money-bill-wave me-2"></i>
                                Test Payment Confirmation
                            </button>

                            <button type="button" class="btn btn-warning w-100 py-3" onclick="testOrderNotification()">
                                <i class="fa-solid fa-shopping-cart me-2"></i>
                                Test New Order Notification
                            </button>

                            <a href="{{ route('settings.index') }}" class="btn btn-secondary w-100 py-3">
                                <i class="fa-solid fa-cog me-2"></i>
                                Go to Settings
                            </a>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">How Telegram Notifications Work:</h5>
                        <ol class="text-muted">
                            <li class="mb-2">When a customer creates an order, you receive a notification with order details (Pending Payment)</li>
                            <li class="mb-2">When payment is completed, you receive another notification confirming the payment</li>
                            <li class="mb-2">All notifications include: Bill Number, Customer Name, Email, Items, Total Amount, and Timestamp</li>
                            <li class="mb-2">Notifications work for all payment methods: Bakong, Card, and ABA</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function testPaymentNotification() {
            alert('To test payment notifications, create a real order and complete the payment. The notification will be sent automatically.');
        }

        function testOrderNotification() {
            alert('To test order notifications, add items to cart and proceed to checkout. A notification will be sent when the order is created.');
        }
    </script>
</body>
</html>
