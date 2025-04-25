<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission to add invoice
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_invoice'])) {
    $patient_name = $_POST['patient_name'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $total_amount = $quantity * $unit_price;
    $status = $_POST['status'];
    
    // Get the invoice month and year from form
    $invoice_month = $_POST['invoice_month'];
    $invoice_year = $_POST['invoice_year'];
    
    // Create a date string in the format YYYY-MM-DD
    $invoice_date = $invoice_year . '-' . $invoice_month . '-01';
    
    $sql = "INSERT INTO invoices (patient_name, medicine_id, quantity, unit_price, total_amount, status, invoice_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiddss", $patient_name, $medicine_id, $quantity, $unit_price, $total_amount, $status, $invoice_date);
    
    if ($stmt->execute()) {
        echo "<script>alert('Invoice added successfully');</script>";
    } else {
        echo "<script>alert('Error adding invoice: " . $stmt->error . "');</script>";
    }
}

// Handle deleting invoice
if (isset($_GET['delete_id'])) {
    $invoice_id = $_GET['delete_id'];
    $sql = "DELETE FROM invoices WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $invoice_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Invoice deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting invoice');</script>";
    }
}

// Handle editing invoice
if (isset($_POST['edit_invoice'])) {
    $invoice_id = $_POST['invoice_id'];
    $patient_name = $_POST['patient_name'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $total_amount = $quantity * $unit_price;
    $status = $_POST['status'];
    
    // Get the invoice month and year from form
    $invoice_month = $_POST['invoice_month'];
    $invoice_year = $_POST['invoice_year'];
    
    // Create a date string in the format YYYY-MM-DD
    $invoice_date = $invoice_year . '-' . $invoice_month . '-01';
    
    $sql = "UPDATE invoices SET patient_name = ?, medicine_id = ?, quantity = ?, unit_price = ?, total_amount = ?, status = ?, invoice_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiddssi", $patient_name, $medicine_id, $quantity, $unit_price, $total_amount, $status, $invoice_date, $invoice_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Invoice updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating invoice: " . $stmt->error . "');</script>";
    }
}

// Toggle invoice status (quick update)
if (isset($_GET['toggle_status'])) {
    $invoice_id = $_GET['toggle_status'];
    $current_status = $_GET['current_status'];
    $new_status = ($current_status == 'Paid') ? 'Pending' : 'Paid';
    
    $sql = "UPDATE invoices SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $invoice_id);
    
    if ($stmt->execute()) {
        // Refresh the page to show the updated status
        echo "<script>window.location.href = window.location.pathname;</script>";
    } else {
        echo "<script>alert('Error updating status');</script>";
    }
}

// Fetch all invoices
$sql = "SELECT invoices.id, invoices.patient_name, invoices.quantity, invoices.unit_price, 
                invoices.total_amount, invoices.invoice_date, invoices.status, 
                med_drug.name AS medicine_name 
        FROM invoices 
        JOIN med_drug ON invoices.medicine_id = med_drug.id
        ORDER BY invoices.invoice_date DESC";
$result = $conn->query($sql);

// Fetch data for editing
if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];
    $sql = "SELECT * FROM invoices WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $edit_result = $stmt->get_result()->fetch_assoc();
}

// Fetch data for analytics
$analytics_data = [];

// 1. Monthly Sales
$monthly_sales = $conn->query("SELECT 
    DATE_FORMAT(invoice_date, '%Y-%m') AS month, 
    SUM(total_amount) AS total_sales,
    COUNT(id) AS invoice_count
    FROM invoices 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 12");

// 2. Top Medicines
$top_medicines = $conn->query("SELECT 
    med_drug.name AS medicine_name,
    SUM(invoices.quantity) AS total_quantity,
    SUM(invoices.total_amount) AS total_sales
    FROM invoices
    JOIN med_drug ON invoices.medicine_id = med_drug.id
    GROUP BY medicine_id
    ORDER BY total_sales DESC
    LIMIT 5");

// 3. Payment Status
$payment_status = $conn->query("SELECT 
    status,
    COUNT(id) AS count,
    SUM(total_amount) AS amount
    FROM invoices
    GROUP BY status");

// 4. Recent Activity
$recent_activity = $conn->query("SELECT 
    id, patient_name, total_amount, invoice_date, status
    FROM invoices
    ORDER BY invoice_date DESC
    LIMIT 5");

// Prepare data for JavaScript
$monthly_labels = [];
$monthly_sales_data = [];
$monthly_invoice_counts = [];

while ($row = $monthly_sales->fetch_assoc()) {
    $monthly_labels[] = $row['month'];
    $monthly_sales_data[] = $row['total_sales'];
    $monthly_invoice_counts[] = $row['invoice_count'];
}

$top_med_labels = [];
$top_med_sales = [];
$top_med_quantities = [];

while ($row = $top_medicines->fetch_assoc()) {
    $top_med_labels[] = $row['medicine_name'];
    $top_med_sales[] = $row['total_sales'];
    $top_med_quantities[] = $row['total_quantity'];
}

$status_labels = [];
$status_counts = [];
$status_amounts = [];

while ($row = $payment_status->fetch_assoc()) {
    $status_labels[] = $row['status'];
    $status_counts[] = $row['count'];
    $status_amounts[] = $row['amount'];
}

// Get current month and year for default values
$current_month = date('m');
$current_year = date('Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Invoice Management System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c84bb;
            --secondary-color: #34b36e;
            --accent-color: #f5ae23;
            --danger-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #333;
            --border-color: #e0e0e0;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 95%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2rem;
            margin: 0;
        }

        .section-title {
            color: var(--primary-color);
            border-left: 5px solid var(--secondary-color);
            padding-left: 15px;
            margin: 40px 0 20px;
            font-size: 1.8rem;
        }

        /* Form Styles */
        .form-container {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 500;
        }

        input, select, button {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(44, 132, 187, 0.2);
        }

        button {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2d9a5f;
        }

        button.primary {
            background-color: var(--primary-color);
        }

        button.primary:hover {
            background-color: #236d9e;
        }

        /* Table Styles */
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        tr:hover {
            background-color: rgba(44, 132, 187, 0.05);
        }

        .paid {
            color: var(--success-color);
            font-weight: 600;
            background-color: rgba(40, 167, 69, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .pending {
            color: var(--warning-color);
            font-weight: 600;
            background-color: rgba(255, 193, 7, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .btn-edit {
            background-color: var(--info-color);
        }

        .btn-edit:hover {
            background-color: #138496;
        }

        .btn-delete {
            background-color: var(--danger-color);
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-status {
            background-color: var(--primary-color);
        }

        .btn-status:hover {
            background-color: #236d9e;
        }

        /* Analytics Styles */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 10px 0;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
        }

        .chart-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-box {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            font-size: 1.2rem;
            color: var(--dark-color);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .chart-wrapper {
            position: relative;
            height: 350px;
        }

        /* Recent Activity */
        .recent-activity {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .activity-item {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-info {
            display: flex;
            flex-direction: column;
        }

        .activity-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .activity-date {
            color: #666;
            font-size: 0.9rem;
        }

        .activity-details {
            text-align: right;
        }

        .activity-amount {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        /* Footer */
        footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            border-radius: 8px;
        }

        /* Date Input Styles */
        .date-inputs {
            display: flex;
            gap: 15px;
        }

        .date-inputs .form-group {
            flex: 1;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .chart-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .date-inputs {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            th, td {
                padding: 10px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-hospital"></i> Hospital Invoice Management System</h1>
        </header>

        <h2 class="section-title">
            <?php echo isset($edit_result) ? 'Edit Invoice #' . $edit_result['id'] : 'Create New Invoice'; ?>
        </h2>

        <div class="form-container">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="patient_name">Patient Name</label>
                        <input type="text" id="patient_name" name="patient_name" required 
                               value="<?php echo isset($edit_result) ? $edit_result['patient_name'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="medicine_id">Medicine</label>
                        <select id="medicine_id" name="medicine_id" required>
                            <option value="">Select Medicine</option>
                            <?php
                            // Fetch available medicines
                            $result_meds = $conn->query("SELECT id, name FROM med_drug");
                            while ($row = $result_meds->fetch_assoc()) {
                                $selected = (isset($edit_result) && $edit_result['medicine_id'] == $row['id']) ? 'selected' : '';
                                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" required min="1"
                               value="<?php echo isset($edit_result) ? $edit_result['quantity'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="unit_price">Unit Price (KSh)</label>
                        <input type="number" id="unit_price" name="unit_price" required step="0.01" min="0"
                               value="<?php echo isset($edit_result) ? $edit_result['unit_price'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Payment Status</label>
                        <select id="status" name="status" required>
                            <option value="Pending" <?php echo (isset($edit_result) && $edit_result['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Paid" <?php echo (isset($edit_result) && $edit_result['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                        </select>
                    </div>
                </div>
                
                <!-- New Date Inputs -->
                <div class="form-group">
                    <label>Invoice Date</label>
                    <div class="date-inputs">
                        <div class="form-group">
                            <label for="invoice_month">Month</label>
                            <select id="invoice_month" name="invoice_month" required>
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    $month_num = sprintf("%02d", $i);
                                    $month_name = date('F', mktime(0, 0, 0, $i, 1));
                                    $selected = '';
                                    if (isset($edit_result)) {
                                        $invoice_date = new DateTime($edit_result['invoice_date']);
                                        $selected = ($invoice_date->format('m') == $month_num) ? 'selected' : '';
                                    } else {
                                        $selected = ($current_month == $month_num) ? 'selected' : '';
                                    }
                                    echo "<option value='$month_num' $selected>$month_name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="invoice_year">Year</label>
                            <select id="invoice_year" name="invoice_year" required>
                                <?php
                                $start_year = 2020;
                                $end_year = date('Y') + 1;
                                for ($i = $start_year; $i <= $end_year; $i++) {
                                    $selected = '';
                                    if (isset($edit_result)) {
                                        $invoice_date = new DateTime($edit_result['invoice_date']);
                                        $selected = ($invoice_date->format('Y') == $i) ? 'selected' : '';
                                    } else {
                                        $selected = ($current_year == $i) ? 'selected' : '';
                                    }
                                    echo "<option value='$i' $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="invoice_id" value="<?php echo isset($edit_result) ? $edit_result['id'] : ''; ?>">
                
                <?php if (isset($edit_result)): ?>
                    <button type="submit" name="edit_invoice" class="primary">
                        <i class="fas fa-save"></i> Update Invoice
                    </button>
                <?php else: ?>
                    <button type="submit" name="add_invoice">
                        <i class="fas fa-plus-circle"></i> Create Invoice
                    </button>
                <?php endif; ?>
            </form>
        </div>
        
        <h2 class="section-title"><i class="fas fa-chart-line"></i> Sales Analytics Dashboard</h2>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stat-value">
                    <?php 
                    $total_invoices = $conn->query("SELECT COUNT(id) AS count FROM invoices")->fetch_assoc()['count'];
                    echo number_format($total_invoices);
                    ?>
                </div>
                <div class="stat-label">Total Invoices</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">
                    KSh <?php 
                    $total_revenue = $conn->query("SELECT SUM(total_amount) AS total FROM invoices")->fetch_assoc()['total'];
                    echo number_format($total_revenue, 2);
                    ?>
                </div>
                <div class="stat-label">Total Revenue</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-value">
                    KSh <?php 
                    $avg_invoice = $total_invoices > 0 ? $total_revenue / $total_invoices : 0;
                    echo number_format($avg_invoice, 2);
                    ?>
                </div>
                <div class="stat-label">Avg. Invoice Value</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value">
                    <?php 
                    $paid_invoices = $conn->query("SELECT COUNT(id) AS count FROM invoices WHERE status = 'Paid'")->fetch_assoc()['count'];
                    echo number_format($paid_invoices);
                    ?>
                </div>
                <div class="stat-label">Paid Invoices</div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-box">
                <div class="chart-title">Monthly Sales Trend</div>
                <div class="chart-wrapper">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>
            
            <div class="chart-box">
                <div class="chart-title">Top Selling Medicines</div>
                <div class="chart-wrapper">
                    <canvas id="topMedicinesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-box">
                <div class="chart-title">Payment Status Distribution</div>
                <div class="chart-wrapper">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>
            
            <div class="chart-box">
                <div class="chart-title">Recent Invoice Activity</div>
                <div class="recent-activity">
                    <?php 
                    // Reset pointer
                    $recent_activity->data_seek(0);
                    while ($row = $recent_activity->fetch_assoc()): 
                    ?>
                        <div class="activity-item">
                            <div class="activity-info">
                                <div class="activity-name">#<?php echo $row['id']; ?> - <?php echo $row['patient_name']; ?></div>
                                <div class="activity-date"><i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($row['invoice_date'])); ?></div>
                            </div>
                            <div class="activity-details">
                                <div class="activity-amount">KSh <?php echo number_format($row['total_amount'], 2); ?></div>
                                <div class="<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <h2 class="section-title"><i class="fas fa-list"></i> Invoices List</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Medicine</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset result pointer
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['medicine_name']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>KSh <?php echo number_format($row['unit_price'], 2); ?></td>
                            <td>KSh <?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['invoice_date'])); ?></td>
                            <td>
                                <span class="<?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="?toggle_status=<?php echo $row['id']; ?>&current_status=<?php echo $row['status']; ?>" 
                                   class="btn btn-status">
                                    <i class="fas fa-exchange-alt"></i> Toggle Status
                                </a>
                                <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-delete"<a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this invoice?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Hospital Invoice Management System</p>
        </footer>
    </div>

    <script>
        // Set up the charts using Chart.js
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Sales Chart
            const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
            const monthlySalesChart = new Chart(monthlySalesCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_reverse($monthly_labels)); ?>,
                    datasets: [
                        {
                            label: 'Monthly Sales (KSh)',
                            data: <?php echo json_encode(array_reverse($monthly_sales_data)); ?>,
                            borderColor: '#2c84bb',
                            backgroundColor: 'rgba(44, 132, 187, 0.1)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2
                        },
                        {
                            label: 'Invoice Count',
                            data: <?php echo json_encode(array_reverse($monthly_invoice_counts)); ?>,
                            borderColor: '#34b36e',
                            backgroundColor: 'rgba(52, 179, 110, 0.1)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales Amount (KSh)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Number of Invoices'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });

            // Top Medicines Chart
            const topMedicinesCtx = document.getElementById('topMedicinesChart').getContext('2d');
            const topMedicinesChart = new Chart(topMedicinesCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($top_med_labels); ?>,
                    datasets: [
                        {
                            label: 'Sales (KSh)',
                            data: <?php echo json_encode($top_med_sales); ?>,
                            backgroundColor: 'rgba(44, 132, 187, 0.7)'
                        },
                        {
                            label: 'Quantity Sold',
                            data: <?php echo json_encode($top_med_quantities); ?>,
                            backgroundColor: 'rgba(52, 179, 110, 0.7)',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales Amount (KSh)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Quantity'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Payment Status Chart
            const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
            const paymentStatusChart = new Chart(paymentStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($status_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($status_amounts); ?>,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(255, 193, 7, 0.7)'
                        ],
                        borderColor: [
                            'rgb(40, 167, 69)',
                            'rgb(255, 193, 7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: KSh ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>