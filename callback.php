<?php
require_once 'db_connection.php';

// Get the callback data
$callbackData = file_get_contents('php://input');
$data = json_decode($callbackData, true);

if (isset($data['Body']['stkCallback'])) {
    $callback = $data['Body']['stkCallback'];
    $checkoutRequestID = $callback['CheckoutRequestID'];
    $resultCode = $callback['ResultCode'];
    $resultDesc = $callback['ResultDesc'];
    
    if ($resultCode == 0) {
        // Successful payment
        $items = $callback['CallbackMetadata']['Item'];
        $amount = $items[0]['Value'];
        $mpesaReceiptNumber = $items[1]['Value'];
        $phoneNumber = $items[3]['Value'];
        
        // Store in database
        $stmt = $conn->prepare("INSERT INTO mpesa_transactions 
                              (checkout_request_id, mpesa_receipt_number, phone_number, amount, result_code, result_desc) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdds", $checkoutRequestID, $mpesaReceiptNumber, $phoneNumber, $amount, $resultCode, $resultDesc);
        $stmt->execute();
    } else {
        // Failed payment
        $stmt = $conn->prepare("INSERT INTO mpesa_transactions 
                              (checkout_request_id, result_code, result_desc) 
                              VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $checkoutRequestID, $resultCode, $resultDesc);
        $stmt->execute();
    }
    
    // Respond to Safaricom
    header('Content-Type: application/json');
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid request']);
}
?>