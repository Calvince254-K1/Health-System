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
    echo "<div class='alert alert-danger'>Connection failed: " . $e->getMessage() . "</div>";
    exit;
}

// Function to sanitize and validate inputs
function sanitize_and_validate($data, $type) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if ($type == 'text') {
        // Updated regex to allow letters, numbers, spaces, hyphens, and periods
        if (!preg_match("/^[a-zA-Z0-9 .-]*$/", $data)) {
            throw new Exception("Only letters, numbers, spaces, hyphens, and periods are allowed");
        }
    } elseif ($type == 'number') {
        if (!is_numeric($data)) {
            throw new Exception("Only numbers are allowed");
        }
    } elseif ($type == 'license') {
        if (!preg_match("/^[A-Za-z0-9]+$/", $data)) {
            throw new Exception("Invalid license number format");
        }
    }

    return $data;
}

// Message handling
$message = '';
$message_type = '';

// Load supplier for editing
$edit_supplier = null;
if (isset($_GET['edit_id'])) {
    try {
        $id = sanitize_and_validate($_GET['edit_id'], 'number');
        $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $edit_supplier = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Insert Supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_supplier'])) {
    try {
        $name = sanitize_and_validate($_POST['name'], 'text');
        $address = sanitize_and_validate($_POST['address'], 'text');
        $contact_info = sanitize_and_validate($_POST['contact_info'], 'text');
        $license_number = sanitize_and_validate($_POST['license_number'], 'license');

        $sql = "INSERT INTO suppliers (name, address, contact_info, license_number) 
                VALUES (:name, :address, :contact_info, :license_number)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['name' => $name, 'address' => $address, 'contact_info' => $contact_info, 'license_number' => $license_number]);

        $message = "Supplier added successfully!";
        $message_type = 'success';
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Update Supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_supplier'])) {
    try {
        $id = sanitize_and_validate($_POST['id'], 'number');
        $name = sanitize_and_validate($_POST['name'], 'text');
        $address = sanitize_and_validate($_POST['address'], 'text');
        $contact_info = sanitize_and_validate($_POST['contact_info'], 'text');
        $license_number = sanitize_and_validate($_POST['license_number'], 'license');

        $sql = "UPDATE suppliers SET name = :name, address = :address, contact_info = :contact_info, license_number = :license_number WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['name' => $name, 'address' => $address, 'contact_info' => $contact_info, 'license_number' => $license_number, 'id' => $id]);

        $message = "Supplier updated successfully!";
        $message_type = 'success';
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Delete Supplier
if (isset($_GET['delete_id'])) {
    try {
        $id = sanitize_and_validate($_GET['delete_id'], 'number');
        $sql = "DELETE FROM suppliers WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        $message = "Supplier deleted successfully!";
        $message_type = 'success';
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Fetch all suppliers
try {
    $suppliers = $conn->query("SELECT * FROM suppliers ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching data: " . $e->getMessage();
    $message_type = 'danger';
    $suppliers = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Supplier Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --light-text: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ecf0f1;
            margin: 0;
            padding: 0;
            color: var(--dark-text);
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            font-weight: 300;
            margin-bottom: 0;
            opacity: 0.9;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: none;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: none;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 0.75rem;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }
        
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-danger {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        .btn-success {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-success:hover {
            background-color: #219653;
            border-color: #219653;
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .table th {
            background-color: #f1f1f1;
            color: var(--dark-text);
            font-weight: 600;
            padding: 1rem;
            border-bottom: 2px solid #ddd;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-around;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        
        .alert {
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .footer {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            text-align: center;
            margin-top: 3rem;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .footer-content {
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .badge-license {
            background-color: #3498db;
            color: white;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
        }
        
        .report-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 0;
            color: #777;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #ddd;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="page-header">
        <div class="container text-center">
            <h1 class="page-title">Drug Supplier Management System</h1>
            <p class="page-subtitle">Efficiently manage your hospital's drug suppliers</p>
        </div>
    </header>

    <div class="container">
        <!-- Message Alert -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Supplier Form Card -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-<?php echo $edit_supplier ? 'edit' : 'plus-circle'; ?> me-2"></i>
                        <strong><?php echo $edit_supplier ? 'Update Supplier' : 'Add New Supplier'; ?></strong>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" id="supplier-form">
                            <?php if ($edit_supplier): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_supplier['id']; ?>" />
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Supplier Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" 
                                        value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['name']) : ''; ?>" 
                                        placeholder="Enter supplier name" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control" id="address" name="address" 
                                        value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['address']) : ''; ?>" 
                                        placeholder="Enter supplier address" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Contact Info</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" id="contact_info" name="contact_info" 
                                        value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['contact_info']) : ''; ?>" 
                                        placeholder="Enter contact information" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="license_number" class="form-label">License Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control" id="license_number" name="license_number" 
                                        value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['license_number']) : ''; ?>" 
                                        placeholder="Enter license number" required>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" name="<?php echo $edit_supplier ? 'update_supplier' : 'save_supplier'; ?>">
                                    <i class="fas fa-<?php echo $edit_supplier ? 'save' : 'plus-circle'; ?> me-2"></i>
                                    <?php echo $edit_supplier ? 'Update Supplier' : 'Save Supplier'; ?>
                                </button>
                                
                                <?php if ($edit_supplier): ?>
                                    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Supplier List Card -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-list me-2"></i>
                            <strong>Supplier List</strong>
                        </div>
                        <div>
                            <span class="badge bg-info">
                                <i class="fas fa-users me-1"></i>
                                <?php echo count($suppliers); ?> Suppliers
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (count($suppliers) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-hashtag me-2"></i>ID</th>
                                            <th><i class="fas fa-building me-2"></i>Name</th>
                                            <th><i class="fas fa-map-marker-alt me-2"></i>Address</th>
                                            <th><i class="fas fa-phone me-2"></i>Contact</th>
                                            <th><i class="fas fa-id-card me-2"></i>License</th>
                                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($supplier['id']); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($supplier['name']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($supplier['address']); ?></td>
                                                <td><?php echo htmlspecialchars($supplier['contact_info']); ?></td>
                                                <td>
                                                    <span class="badge badge-license">
                                                        <?php echo htmlspecialchars($supplier['license_number']); ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="?edit_id=<?php echo $supplier['id']; ?>" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete_id=<?php echo $supplier['id']; ?>" class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Are you sure you want to delete this supplier?');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-database"></i>
                                <h5>No suppliers found</h5>
                                <p>Add your first supplier to get started</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="report-section">
                            <a href="supplier_report.php" class="btn btn-success">
                                <i class="fas fa-file-download me-2"></i>Download Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p><i class="fas fa-hospital me-2"></i> &copy; 2025 Hospital Inventory Management System</p>
                <p class="mb-0">All rights reserved</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>