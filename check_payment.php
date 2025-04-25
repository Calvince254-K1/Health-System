<?php
session_start();
require_once 'db_connection.php'; // Your database connection file

if (!isset($_GET['checkout_request_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request']));
}

$checkoutRequestID = $_GET['checkout_request_id'];

// In a real application, you would check your database for the transaction status
// For this example, we'll simulate checking the Safaricom API

// Simulate checking database (replace with actual DB check)
$stmt = $conn->prepare("SELECT * FROM mpesa_transactions WHERE checkout_request_id = ?");
$stmt->bind_param("s", $checkoutRequestID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $transaction = $result->fetch_assoc();
    if ($transaction['result_code'] == 0) {
        echo json_encode([
            'status' => 'success',
            'transaction_id' => $transaction['mpesa_receipt_number']
        ]);
    } else {
        echo json_encode(['status' => 'failed']);
    }
} else {
    echo json_encode(['status' => 'pending']);
}
?>