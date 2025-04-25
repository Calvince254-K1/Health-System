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

// Fetch all medicines for the report
$query = "SELECT * FROM medicines_drugs";
$stmt = $pdo->prepare($query);
$stmt->execute();
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the chart
$medicine_names = [];
$quantities = [];
foreach ($medicines as $medicine) {
    $medicine_names[] = $medicine['medicine_name'];
    $quantities[] = $medicine['quantity'];
}

// CSV Download Functionality
if (isset($_POST['download'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=medicine_report.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['#', 'Medicine Name', 'Packing', 'Generic Name', 'Batch ID', 'Expiry Date', 'Supplier', 'Quantity', 'MRP', 'Rate']);
    
    foreach ($medicines as $index => $medicine) {
        fputcsv($output, [
            $index + 1,
            $medicine['medicine_name'],
            $medicine['packing'],
            $medicine['generic_name'],
            $medicine['batch_id'],
            $medicine['expiry_date'],
            $medicine['supplier'],
            $medicine['quantity'],
            $medicine['mrp'],
            $medicine['rate']
        ]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Report - Thika Level 5</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Page Styling */
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .text-center {
            text-align: center;
        }

        /* Table Styling */
        table {
            margin-top: 20px;
            width: 100%;
        }

        th, td {
            text-align: center;
            padding: 12px 15px;
        }

        th {
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }

        td {
            background-color: #f8f9fa;
        }

        tr:nth-child(even) td {
            background-color: #e9ecef;
        }

        tr:hover td {
            background-color: #ddd;
        }

        /* Graph Styling */
        .chart-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        #quantityChart {
            max-width: 100%;
            height: 400px;
        }

        /* Button Styling */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* Print-Friendly Styles */
        @media print {
            .no-print {
                display: none;
            }
            .container {
                margin-top: 0;
                padding: 0;
            }
        }

        /* Footer Styling */
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            margin-top: 50px;
        }

        footer a {
            color: #f8f9fa;
            text-decoration: none;
            padding: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Medicine Report - Thika Level 5</h1>

    <!-- Report Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="report-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Medicine Name</th>
                <th>Packing</th>
                <th>Generic Name</th>
                <th>Batch ID</th>
                <th>Expiry Date</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>MRP</th>
                <th>Rate</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($medicines as $index => $medicine): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($medicine['medicine_name']) ?></td>
                    <td><?= htmlspecialchars($medicine['packing']) ?></td>
                    <td><?= htmlspecialchars($medicine['generic_name']) ?></td>
                    <td><?= htmlspecialchars($medicine['batch_id']) ?></td>
                    <td><?= htmlspecialchars($medicine['expiry_date']) ?></td>
                    <td><?= htmlspecialchars($medicine['supplier']) ?></td>
                    <td><?= htmlspecialchars($medicine['quantity']) ?></td>
                    <td><?= htmlspecialchars($medicine['mrp']) ?></td>
                    <td><?= htmlspecialchars($medicine['rate']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Graph Section: Medicine Quantities -->
    <div class="chart-container">
        <h3 class="text-center">Medicine Quantities</h3>
        <canvas id="quantityChart"></canvas>
    </div>

    <!-- Buttons: Print and Download -->
    <div class="text-center mt-4">
        <button class="btn btn-success no-print" onclick="window.print();">Print Report</button>
        <form method="POST" class="d-inline-block">
            <button type="submit" name="download" class="btn btn-success">Download Report</button>
        </form>
    </div>
</div>

<!-- Footer Section -->
<footer>
    <p>&copy; 2025 Thika Level 5 Management System</p>
    <div>
        <a href="https://www.facebook.com" target="_blank">Facebook</a>
        <a href="https://www.instagram.com" target="_blank">Instagram</a>
        <a href="https://www.twitter.com" target="_blank">Twitter</a>
        <a href="https://www.linkedin.com" target="_blank">LinkedIn</a>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Prepare data for the chart
    var medicineNames = <?php echo json_encode($medicine_names); ?>;
    var quantities = <?php echo json_encode($quantities); ?>;

    // Chart.js code for displaying a bar chart of medicine quantities
    var ctx = document.getElementById('quantityChart').getContext('2d');
    var quantityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: medicineNames,
            datasets: [{
                label: 'Quantity',
                data: quantities,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>
