<?php
session_start();
if (!isset($_SESSION['checkout_request_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <div class="text-center mb-6">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-indigo-500 mx-auto mb-4"></div>
            <h2 class="text-2xl font-bold text-gray-800">Processing Your Payment</h2>
            <p class="text-gray-600 mt-2">Please check your phone to complete the M-Pesa payment</p>
        </div>
        
        <div class="bg-blue-50 p-4 rounded-lg mb-6">
            <div class="flex justify-between mb-2">
                <span class="text-gray-700">Amount:</span>
                <span class="font-semibold">KES <?php echo number_format($_SESSION['payment_amount'], 2); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-700">Phone:</span>
                <span class="font-semibold"><?php echo $_SESSION['payment_phone']; ?></span>
            </div>
        </div>
        
        <div class="text-center text-sm text-gray-500">
            <p>This page will automatically update when payment is received</p>
            <p class="mt-2">Transaction ID: <?php echo substr($_SESSION['checkout_request_id'], 0, 8) . '...'; ?></p>
        </div>
        
        <div class="mt-6 text-center">
            <a href="index.php" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Back to Home
            </a>
        </div>
    </div>

    <script>
        // Check payment status every 5 seconds
        function checkPaymentStatus() {
            fetch('check_payment.php?checkout_request_id=<?php echo $_SESSION['checkout_request_id']; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = 'payment_success.php?transaction_id=' + data.transaction_id;
                    } else if (data.status === 'failed') {
                        window.location.href = 'payment_error.php?message=Payment failed or was cancelled';
                    }
                });
        }
        
        // Check every 5 seconds
        setInterval(checkPaymentStatus, 5000);
    </script>
</body>
</html>