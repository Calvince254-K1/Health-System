<?php
// Ensure the request is made with POST method
$responseMessage = '';
$responseStatus = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get phone and amount from the form
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];

    // Call the function to process the payment request (STK Push)
    $response = json_decode(initiateSTKPush($phone, $amount));

    if (isset($response->ResponseCode) && $response->ResponseCode == "0") {
        $responseMessage = "STK Push sent successfully! Check your phone to complete payment.";
        $responseStatus = 'success';
        $isSuccess = true;
    } else {
        $responseMessage = isset($response->errorMessage) ? $response->errorMessage : 'Failed to send payment request.';
        $responseStatus = 'error';
    }
} else {
    $responseMessage = "Invalid request method.";
    $responseStatus = 'error';
}

// Function to get access token from Safaricom
function getAccessToken() {
    $consumerKey = 'CFQB6geP6GssV302et9wfboGzUj6yqm2KGNwxEevZjrRSWY7'; // Replace with your sandbox consumer key
    $consumerSecret = 'GfC5g9P8bMbgrI1yvP3cl1zq3RADNQRMwl2OL03JXogUEKdX1bhmvuzojrxAXKJX'; // Replace with your sandbox consumer secret
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $result = json_decode($response);
    curl_close($curl);

    return $result->access_token;
}

// Function to initiate the STK Push
function initiateSTKPush($phone, $amount) {
    $accessToken = getAccessToken();
    $BusinessShortCode = '174379';  // Replace with your Paybill shortcode
    $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';  // Replace with your passkey from the sandbox
    $Timestamp = date('YmdHis');
    $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

    // Format phone number to 2547XXXXXXXX
    $phone = '254' . substr($phone, -9);

    $stkPushData = [
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $BusinessShortCode,
        'PhoneNumber' => $phone,
        'CallBackURL' => 'https://sandbox.safaricom.co.ke/mpesa/online/confirmation',  // Use sandbox URL for callback
        'AccountReference' => 'TestPayment123',
        'TransactionDesc' => 'Payment for testing'
    ];

    $curl = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($stkPushData));
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #2D3748;
            --secondary: #4A5568;
            --accent: #4299E1;
            --success: #48BB78;
            --error: #F56565;
        }
        
        body {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        .payment-card {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #E2E8F0;
        }
        
        .input-field:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
        
        .btn-primary {
            background-color: var(--accent);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: #3182CE;
            transform: translateY(-1px);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .payment-method {
            border: 1px solid #E2E8F0;
            transition: all 0.2s ease;
        }
        
        .payment-method:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }
        
        .payment-method.selected {
            border-color: var(--accent);
            background-color: #EBF8FF;
        }
        
        .success-animation {
            animation: bounceIn 0.8s;
        }
        
        .phone-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="py-12 px-4">
    <div class="max-w-md mx-auto">
        <?php if (!$isSuccess): ?>
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-blue-500 text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">M-Pesa Payment</h1>
            <p class="text-gray-600 mt-2">Enter your details to receive payment request</p>
        </div>

        <div class="bg-white rounded-xl payment-card p-8">
            <form method="POST" id="paymentForm">
                <!-- Phone Number -->
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">+254</span>
                        </div>
                        <input type="tel" id="phone" name="phone" 
                               class="pl-16 input-field block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none" 
                               placeholder="700123456" 
                               pattern="[0-9]{9}" 
                               title="Enter 9 digits without the leading 0 (e.g., 700123456)" 
                               required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Format: 7XX XXX XXX (without leading 0)</p>
                </div>

                <!-- Amount -->
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount (KES) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">KSh</span>
                        </div>
                        <input type="number" id="amount" name="amount" min="1" step="1"
                               class="pl-12 input-field block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none" 
                               placeholder="100" 
                               required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Minimum amount: KSh 1</p>
                </div>

                <!-- Payment Methods -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="payment-method border rounded-lg p-3 cursor-pointer selected">
                            <input type="radio" name="payment_method" value="mpesa" class="hidden" checked>
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-mobile-alt text-green-600"></i>
                                </div>
                                <span>M-Pesa</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-6 flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" 
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" required>
                    </div>
                    <div class="ml-3">
                        <label for="terms" class="text-sm text-gray-700">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn"
                        class="w-full py-3 px-4 btn-primary text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-paper-plane mr-2"></i> Send Payment Request
                </button>

                <!-- Error Message -->
                <?php if (!empty($responseMessage) && $responseStatus === 'error'): ?>
                    <div class="mt-4 text-center px-4 py-3 rounded bg-red-50 text-red-800 animate__animated animate__shakeX">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($responseMessage); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        <?php else: ?>
        <!-- Success State -->
        <div class="text-center mb-8 success-animation">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-green-500 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Payment Request Sent!</h1>
            <p class="text-gray-600">Check your phone to complete payment</p>
        </div>

        <div class="bg-white rounded-xl payment-card p-8 text-center">
            <div class="phone-animation mb-6">
                <i class="fas fa-mobile-alt text-blue-500 text-6xl"></i>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-800">Payment Details</h3>
                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between max-w-xs mx-auto">
                        <span>Phone:</span>
                        <span class="font-medium">+254<?php echo htmlspecialchars(substr($_POST['phone'], -9)); ?></span>
                    </div>
                    <div class="flex justify-between max-w-xs mx-auto">
                        <span>Amount:</span>
                        <span class="font-medium">KSh <?php echo htmlspecialchars($_POST['amount']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-blue-800 mb-2"><i class="fas fa-info-circle mr-2"></i>What to do next</h4>
                <ol class="text-sm text-blue-700 text-left list-decimal list-inside space-y-1">
                    <li>Check your phone for M-Pesa prompt</li>
                    <li>Enter your M-Pesa PIN when prompted</li>
                    <li>Wait for confirmation message</li>
                </ol>
            </div>
            
            <div class="mt-6 px-4 py-3 rounded bg-green-50 text-green-800">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo htmlspecialchars($responseMessage); ?>
            </div>
            
            <button onclick="window.location.href = window.location.href.split('?')[0];"
                    class="mt-6 w-full py-3 px-4 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <i class="fas fa-redo mr-2"></i> Make Another Payment
            </button>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Form validation
        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            const amount = document.getElementById('amount').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!/^[0-9]{9}$/.test(phone)) {
                alert('Please enter a valid 9-digit phone number (without leading 0)');
                e.preventDefault();
                return;
            }
            
            if (parseFloat(amount) < 1) {
                alert('Minimum payment amount is KSh 1');
                e.preventDefault();
                return;
            }
            
            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending Request...';
        });

        // Format phone number input
        document.getElementById('phone')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 9);
        });
    </script>
</body>
</html>