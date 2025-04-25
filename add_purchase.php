<?php
// Include the DB connection
include('db_connection.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medicine_id = $_POST['medicine_id'];
    $purchase_quantity = $_POST['purchase_quantity'];
    $purchase_date = date("Y-m-d H:i:s");

    // SQL query to insert the purchase into the database
    $sql = "INSERT INTO purchases (medicine_id, quantity, purchase_date) VALUES ('$medicine_id', '$purchase_quantity', '$purchase_date')";

    if ($conn->query($sql) === TRUE) {
        echo "Purchase added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Purchase</title>
</head>
<body>
    <h1>Add Purchase</h1>
    <form method="POST" action="">
        <label>Medicine ID: </label><input type="number" name="medicine_id" required><br>
        <label>Quantity: </label><input type="number" name="purchase_quantity" required><br>
        <button type="submit">Add Purchase</button>
    </form>
</body>
</html>
