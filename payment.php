<?php
// Sample product data - in a real system this would come from your database
$products = [
    'P001' => ['name' => 'Panadol Extra', 'price' => 150, 'category' => 'Pain Relief'],
    'P002' => ['name' => 'Amoxicillin 500mg', 'price' => 800, 'category' => 'Antibiotics'],
    'P003' => ['name' => 'Ventolin Inhaler', 'price' => 1200, 'category' => 'Asthma'],
    'P004' => ['name' => 'Diazepam 5mg', 'price' => 250, 'category' => 'Anxiety'],
    'P005' => ['name' => 'Vitamin C 1000mg', 'price' => 300, 'category' => 'Supplements'],
];

// Process form submission
$responseMessage = '';
$responseStatus = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $productId = $_POST['product'];
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
    
    if (!array_key_exists($productId, $products)) {
        $responseMessage = "Invalid product selected";
        $responseStatus = 'error';
    } else {
        $product = $products[$productId];
        $amount = $product['price'] * $quantity;
        
        // Call the function to process the payment request (STK Push)
        $response = json_decode(initiateSTKPush($phone, $amount));

        if (isset($response->ResponseCode) && $response->ResponseCode == "0") {
            $responseMessage = "Payment request sent for {$product['name']} (Qty: $quantity). Check your phone to complete payment.";
            $responseStatus = 'success';
            $isSuccess = true;
            
            // Here you would typically:
            // 1. Save the transaction to your database
            // 2. Update inventory
            // 3. Send confirmation email/SMS
        } else {
            $responseMessage = isset($response->errorMessage) ? $response->errorMessage : 'Failed to send payment request.';
            $responseStatus = 'error';
        }
    }
}

// Function to get access token from Safaricom
function getAccessToken() {
    $consumerKey = 'YOUR_CONSUMER_KEY';
    $consumerSecret = 'YOUR_CONSUMER_SECRET';
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
    $BusinessShortCode = 'YOUR_BUSINESS_SHORTCODE';
    $Passkey = 'YOUR_PASSKEY';
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
        'CallBackURL' => 'YOUR_CALLBACK_URL',
        'AccountReference' => 'PHARMAPAY',
        'TransactionDesc' => 'Pharmacy Product Payment'
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
    <title>Pharmacy Payment System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Inter', sans-serif;
        }
        .pharmacy-header {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        }
        .product-card {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left-color: #4299e1;
        }
        .product-card.selected {
            border-left-color: #4299e1;
            background-color: #ebf8ff;
        }
        .receipt-item {
            border-bottom: 1px dashed #e2e8f0;
        }
        .receipt-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body class="min-h-screen">
    <header class="pharmacy-header text-white py-6 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-prescription-bottle-alt text-2xl"></i>
                    <h1 class="text-2xl font-bold">MediCare Pharmacy</h1>
                </div>
                <div class="text-sm">
                    <i class="fas fa-phone-alt mr-1"></i> Customer Support: 0700 123 456
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <?php if (!$isSuccess): ?>
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-shopping-cart mr-2 text-blue-500"></i> Product Payment
                    </h2>
                    <p class="text-gray-600 mt-1">Select products and complete payment via M-Pesa</p>
                </div>
                
                <form method="POST" id="paymentForm" class="p-6">
                    <!-- Product Selection -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Select Product</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($products as $id => $product): ?>
                            <label class="product-card border rounded-lg p-4 cursor-pointer">
                                <input type="radio" name="product" value="<?php echo $id; ?>" class="hidden" required>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-800"><?php echo $product['name']; ?></h4>
                                        <p class="text-sm text-gray-500 mt-1"><?php echo $product['category']; ?></p>
                                    </div>
                                    <span class="font-bold text-blue-600">KSh <?php echo number_format($product['price']); ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Quantity -->
                    <div class="mb-6">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity
                        </label>
                        <div class="flex items-center">
                            <button type="button" id="decreaseQty" class="bg-gray-200 px-3 py-1 rounded-l-lg">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                   class="w-16 text-center border-t border-b border-gray-300 py-1">
                            <button type="button" id="increaseQty" class="bg-gray-200 px-3 py-1 rounded-r-lg">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Phone Number -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            M-Pesa Phone Number <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">+254</span>
                            </div>
                            <input type="tel" id="phone" name="phone" 
                                   class="pl-16 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500" 
                                   placeholder="700123456" 
                                   pattern="[0-9]{9}" 
                                   title="Enter 9 digits without the leading 0 (e.g., 700123456)" 
                                   required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Format: 7XX XXX XXX (without leading 0)</p>
                    </div>
                    
                    <!-- Summary -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-blue-800 mb-3">Order Summary</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Product:</span>
                                <span id="selectedProduct" class="font-medium">None selected</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Quantity:</span>
                                <span id="displayQuantity" class="font-medium">1</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Unit Price:</span>
                                <span id="unitPrice" class="font-medium">KSh 0</span>
                            </div>
                            <div class="border-t border-blue-200 my-2"></div>
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-800 font-semibold">Total:</span>
                                <span id="totalAmount" class="text-blue-600 font-bold">KSh 0</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms and Submit -->
                    <div class="flex items-start mb-6">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" 
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" required>
                        </div>
                        <div class="ml-3">
                            <label for="terms" class="text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">terms and conditions</a>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" id="submitBtn"
                            class="w-full py-3 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-paper-plane mr-2"></i> Pay via M-Pesa
                    </button>
                    
                    <?php if (!empty($responseMessage) && $responseStatus === 'error'): ?>
                        <div class="mt-4 text-center px-4 py-3 rounded bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo htmlspecialchars($responseMessage); ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Success State -->
        <div class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Request Sent!</h1>
                <p class="text-gray-600">Check your phone to complete payment</p>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-receipt mr-2 text-blue-500"></i> Order Receipt
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="receipt-item py-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Product:</span>
                            <span class="font-medium"><?php echo $products[$productId]['name']; ?></span>
                        </div>
                    </div>
                    
                    <div class="receipt-item py-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Quantity:</span>
                            <span class="font-medium"><?php echo $quantity; ?></span>
                        </div>
                    </div>
                    
                    <div class="receipt-item py-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Unit Price:</span>
                            <span class="font-medium">KSh <?php echo number_format($products[$productId]['price']); ?></span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 my-3"></div>
                    
                    <div class="receipt-item py-3">
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-800 font-semibold">Total Paid:</span>
                            <span class="text-blue-600 font-bold">KSh <?php echo number_format($amount); ?></span>
                        </div>
                    </div>
                    
                    <div class="receipt-item py-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone:</span>
                            <span class="font-medium">+254<?php echo htmlspecialchars(substr($_POST['phone'], -9)); ?></span>
                        </div>
                    </div>
                    
                    <div class="receipt-item py-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-green-600">Pending Payment</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4">
                    <h4 class="font-medium text-blue-800 mb-2"><i class="fas fa-info-circle mr-2"></i>What to do next</h4>
                    <ol class="text-sm text-blue-700 list-decimal list-inside space-y-1">
                        <li>Check your phone for M-Pesa prompt</li>
                        <li>Enter your M-Pesa PIN when prompted</li>
                        <li>You'll receive an SMS confirmation</li>
                        <li>Present this receipt at our pharmacy</li>
                    </ol>
                </div>
                
                <div class="p-6">
                    <button onclick="window.location.href = window.location.href;"
                            class="w-full py-3 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-redo mr-2"></i> New Payment
                    </button>
                    
                    <button class="w-full mt-3 py-3 px-4 bg-white border border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-print mr-2"></i> Print Receipt
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-100 py-6 mt-12">
        <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
            <p>© <?php echo date('Y'); ?> MediCare Pharmacy. All rights reserved.</p>
            <p class="mt-2">
                <i class="fas fa-map-marker-alt mr-1"></i> Nairobi, Kenya | 
                <i class="fas fa-phone-alt ml-2 mr-1"></i> 0700 123 456 | 
                <i class="fas fa-envelope ml-2 mr-1"></i> info@medicare.com
            </p>
        </div>
    </footer>

    <script>
        // Update order summary when product or quantity changes
        document.querySelectorAll('input[name="product"]').forEach(radio => {
            radio.addEventListener('change', updateSummary);
        });
        
        document.getElementById('quantity').addEventListener('change', updateSummary);
        
        // Quantity buttons
        document.getElementById('increaseQty').addEventListener('click', function() {
            const qtyInput = document.getElementById('quantity');
            qtyInput.value = parseInt(qtyInput.value) + 1;
            updateSummary();
        });
        
        document.getElementById('decreaseQty').addEventListener('click', function() {
            const qtyInput = document.getElementById('quantity');
            if (parseInt(qtyInput.value) > 1) {
                qtyInput.value = parseInt(qtyInput.value) - 1;
                updateSummary();
            }
        });
        
        // Format phone number input
        document.getElementById('phone')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 9);
        });
        
        // Form submission
        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!/^[0-9]{9}$/.test(phone)) {
                alert('Please enter a valid 9-digit phone number (without leading 0)');
                e.preventDefault();
                return;
            }
            
            if (!document.querySelector('input[name="product"]:checked')) {
                alert('Please select a product');
                e.preventDefault();
                return;
            }
            
            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
        });
        
        // Update order summary
        function updateSummary() {
            const selectedProduct = document.querySelector('input[name="product"]:checked');
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            
            if (selectedProduct) {
                const productId = selectedProduct.value;
                const productName = selectedProduct.parentElement.querySelector('h4').textContent;
                const productPrice = parseFloat(selectedProduct.parentElement.querySelector('span').textContent.replace('KSh ', '').replace(',', ''));
                
                document.getElementById('selectedProduct').textContent = productName;
                document.getElementById('displayQuantity').textContent = quantity;
                document.getElementById('unitPrice').textContent = 'KSh ' + productPrice.toLocaleString();
                document.getElementById('totalAmount').textContent = 'KSh ' + (productPrice * quantity).toLocaleString();
            }
        }
        
        // Initialize summary
        updateSummary();
    </script>
</body>
</html>