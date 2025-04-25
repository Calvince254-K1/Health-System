<?php
include 'db_connection.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $disease_treated = $_POST['disease_treated'];
    $price = $_POST['price'];

    // Prepare the SQL query
    $stmt = $conn->prepare("INSERT INTO drugs (name, description, disease_treated, price) VALUES (?, ?, ?, ?)");
    
    // Check if prepare() failed
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind the parameters to the query
    $stmt->bind_param("sssd", $name, $description, $disease_treated, $price);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect after adding a drug
        header('Location: index.php');
        exit();
    } else {
        // Show error if query fails
        echo 'Error: ' . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drug</title>
</head>
<body>
    <h1>Add New Drug</h1>
    <form method="POST" action="add_drug.php">
        <label for="name">Drug Name:</label>
        <input type="text" name="name" required><br>
        
        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="disease_treated">Disease Treated:</label>
        <input type="text" name="disease_treated" required><br>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" required><br>

        <button type="submit">Add Drug</button>
    </form>
</body>
</html>
