<?php
// Database connection
$host = "localhost";
$dbname = "hospital_inventory";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Fetch all suppliers
try {
    $suppliers = $conn->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit;
}

// Create CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=supplier_report.csv');

// Open the output stream
$output = fopen('php://output', 'w');

// Add column headers
fputcsv($output, ['ID', 'Name', 'Contact Info', 'License Number']);

// Add supplier data rows
foreach ($suppliers as $supplier) {
    fputcsv($output, $supplier);
}

// Close the output stream
fclose($output);
exit;
?>
