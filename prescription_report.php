<?php
// Database connection
$host = "localhost";
$dbname = "hospital_inventory";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch all prescriptions with prescription_date
$query = "SELECT p.*, m.medicine_name FROM prescriptions p
          INNER JOIN medicines_drugs m ON p.medicine_id = m.id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Report - Thika Level 5</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Prescriptions Report</h1>

    <!-- Prescriptions Table -->
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Client Name</th>
            <th>Medicine Name</th>
            <th>Dose</th>
            <th>Duration</th>
            <th>Instructions</th>
            <th>Prescription Date</th> <!-- New column -->
        </tr>
        </thead>
        <tbody>
        <?php foreach ($prescriptions as $index => $prescription): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($prescription['client_name']) ?></td>
                <td><?= htmlspecialchars($prescription['medicine_name']) ?></td>
                <td><?= htmlspecialchars($prescription['dose']) ?></td>
                <td><?= htmlspecialchars($prescription['duration']) ?></td>
                <td><?= htmlspecialchars($prescription['instructions']) ?></td>
                <td><?= htmlspecialchars($prescription['prescription_date']) ?></td> <!-- Display date -->
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center no-print">
        <!-- Button to print the report -->
        <button onclick="window.print()" class="btn btn-info">Print Report</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
