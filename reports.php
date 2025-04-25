<?php
// Database connection
$host = "localhost";
$dbname = "hospital_inventory";
$username = "root"; // Change this if necessary
$password = ""; // Change this if necessary

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Fetch data for report (e.g., suppliers)
try {
    $suppliers = $conn->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit;
}

// Data for pie chart
$license_data = [];
foreach ($suppliers as $supplier) {
    $license_data[$supplier['license_number']] = (isset($license_data[$supplier['license_number']])) ? $license_data[$supplier['license_number']] + 1 : 1;
}

// Data for bar graph (suppliers by name)
$supplier_names = [];
$supplier_counts = [];
foreach ($suppliers as $supplier) {
    $supplier_names[] = $supplier['name'];
    $supplier_counts[] = 1; // Each supplier counts as 1
}

// Data for pie chart labels and values
$labels = array_keys($license_data);
$values = array_values($license_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px 0;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        h1 {
            font-weight: 700;
            margin: 0;
            font-size: 2.5rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            padding: 15px 20px;
            border-bottom: none;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .summary-card {
            text-align: center;
            padding: 20px;
        }
        
        .summary-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .summary-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
            color: var(--dark-color);
        }
        
        .summary-card p {
            color: #7f8c8d;
            font-size: 1rem;
        }
        
        .btn-custom {
            background-color: var(--primary-color);
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn-custom:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-danger {
            background-color: var(--accent-color);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        table th {
            background-color: #f8f9fa;
            color: var(--dark-color);
            font-weight: 600;
            padding: 12px 15px;
        }
        
        table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #eee;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .chart-container {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }
        
        .chart-title {
            text-align: center;
            margin-bottom: 20px;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 25px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px;
        }
        
        .footer p {
            margin: 0;
            font-size: 1rem;
        }
        
        .action-buttons .btn {
            margin-right: 5px;
            min-width: 70px;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 15px;
            }
            
            table td, table th {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
            
            .action-buttons .btn {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <div class="header">
            <h1><i class="fas fa-clipboard-list me-2"></i> Supplier Report</h1>
            <p class="lead mt-2">Comprehensive overview of hospital suppliers</p>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card summary-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo count($suppliers); ?></h3>
                    <p>Total Suppliers</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card">
                    <i class="fas fa-id-card"></i>
                    <h3><?php echo count($license_data); ?></h3>
                    <p>Unique Licenses</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card">
                    <i class="fas fa-chart-line"></i>
                    <h3><?php echo array_sum($values); ?></h3>
                    <p>Total Records</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-pie me-2"></i>License Distribution
                    </div>
                    <canvas id="licenseChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-bar me-2"></i>Suppliers Overview
                    </div>
                    <canvas id="supplierChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Supplier Table -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-2"></i>
                    <strong>Supplier List</strong>
                </div>
                <a href="#" class="btn btn-sm btn-light">
                    <i class="fas fa-download me-1"></i> Export
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact Info</th>
                                <th>License Number</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suppliers as $supplier): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($supplier['id']); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['contact_info']); ?></td>
                                    <td><?php echo htmlspecialchars($supplier['license_number']); ?></td>
                                    <td class="action-buttons text-center">
                                        <a href="?edit_id=<?php echo $supplier['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete_id=<?php echo $supplier['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this supplier?');">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><i class="far fa-copyright me-1"></i> 2025 Hospital Inventory Management System. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Pie chart setup
        const ctx = document.getElementById('licenseChart').getContext('2d');
        const licenseChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'License Distribution',
                    data: <?php echo json_encode($values); ?>,
                    backgroundColor: [
                        '#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6', '#1abc9c', '#34495e',
                        '#e67e22', '#95a5a6', '#d35400', '#16a085', '#c0392b'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ": " + tooltipItem.raw + " suppliers";
                            }
                        }
                    }
                }
            }
        });

        // Bar chart setup (new graph)
        const supplierCtx = document.getElementById('supplierChart').getContext('2d');
        const supplierChart = new Chart(supplierCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($supplier_names); ?>,
                datasets: [{
                    label: 'Suppliers',
                    data: <?php echo json_encode($supplier_counts); ?>,
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.raw + " record(s)";
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>