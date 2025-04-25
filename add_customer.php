<?php
// Include the DB connection
include('db_connection.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];

    $sql = "INSERT INTO customers (name, email, phone) VALUES ('$customer_name', '$customer_email', '$customer_phone')";
    if ($conn->query($sql) === TRUE) {
        echo "New customer added successfully!";
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
    <title>Add Customer</title>
</head>
<body>
    <h1>Add Customer</h1>
    <form method="POST" action="">
        <label>Name: </label><input type="text" name="customer_name" required><br>
        <label>Email: </label><input type="email" name="customer_email" required><br>
        <label>Phone: </label><input type="text" name="customer_phone" required><br>
        <button type="submit">Add Customer</button>
    </form>
</body>
</html>
