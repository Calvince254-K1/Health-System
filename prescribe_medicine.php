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

// Message handling
$message = "";
$messageType = "";

// Add a prescription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prescribe_medicine'])) {
    // Ensure all required fields are set
    $client_name = isset($_POST['client_name']) ? $_POST['client_name'] : null;
    $client_email = isset($_POST['client_email']) ? $_POST['client_email'] : null;
    $medicine_id = isset($_POST['medicine_id']) ? $_POST['medicine_id'] : null;
    $dose = isset($_POST['dose']) ? $_POST['dose'] : null;
    $duration = isset($_POST['duration']) ? $_POST['duration'] : null;
    $instructions = isset($_POST['instructions']) ? $_POST['instructions'] : null;
    $age = isset($_POST['age']) ? $_POST['age'] : null;
    $disease_type = isset($_POST['disease_type']) ? $_POST['disease_type'] : null;

    // Validate form input (simple validation)
    if ($client_name && $medicine_id && $dose && $duration && $instructions && $age && $disease_type) {
        // Query to insert the prescription
        $query = "INSERT INTO prescriptions (client_name, client_email, medicine_id, dose, duration, instructions, age, disease_type)
                  VALUES (:client_name, :client_email, :medicine_id, :dose, :duration, :instructions, :age, :disease_type)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':client_name' => $client_name,
            ':client_email' => $client_email,
            ':medicine_id' => $medicine_id,
            ':dose' => $dose,
            ':duration' => $duration,
            ':instructions' => $instructions,
            ':age' => $age,
            ':disease_type' => $disease_type
        ]);
        
        // Get the medicine name for email
        $query = "SELECT medicine_name FROM medicines_drugs WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $medicine_id]);
        $medicine = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Send email if email is provided
        if ($client_email) {
            $to = $client_email;
            $subject = "Your Prescription from Thika Level 5 Hospital";
            
            $emailBody = "
            <html>
            <head>
                <title>Your Prescription</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .prescription { border: 1px solid #ccc; padding: 20px; }
                    h2 { color: #2b5797; }
                </style>
            </head>
            <body>
                <div class='prescription'>
                    <h2>Thika Level 5 Hospital - Prescription</h2>
                    <p><strong>Dear $client_name,</strong></p>
                    <p>Here are the details of your prescription:</p>
                    
                    <p><strong>Medicine:</strong> {$medicine['medicine_name']}</p>
                    <p><strong>Dose:</strong> $dose</p>
                    <p><strong>Duration:</strong> $duration</p>
                    <p><strong>Instructions:</strong> $instructions</p>
                    <p><strong>Disease:</strong> $disease_type</p>
                    
                    <p>Please follow these instructions carefully. If you have any questions, contact our pharmacy department.</p>
                    
                    <p>Regards,<br>Thika Level 5 Hospital</p>
                </div>
            </body>
            </html>
            ";
            
            // Headers for sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: pharmacy@thikalevel5.com" . "\r\n";
            
            // Send email
            if(mail($to, $subject, $emailBody, $headers)) {
                $message = "Prescription added successfully and email sent to patient.";
            } else {
                $message = "Prescription added successfully but failed to send email.";
            }
            $messageType = "success";
        } else {
            $message = "Prescription added successfully. No email sent (email not provided).";
            $messageType = "success";
        }
    } else {
        $message = "All fields are required.";
        $messageType = "danger";
    }
}

// Update an existing prescription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_prescription'])) {
    $id = $_POST['prescription_id'];
    $client_name = $_POST['client_name'];
    $client_email = $_POST['client_email'];
    $medicine_id = $_POST['medicine_id'];
    $dose = $_POST['dose'];
    $duration = $_POST['duration'];
    $instructions = $_POST['instructions'];
    $age = $_POST['age'];
    $disease_type = $_POST['disease_type'];
    
    $query = "UPDATE prescriptions SET 
              client_name = :client_name,
              client_email = :client_email,
              medicine_id = :medicine_id,
              dose = :dose,
              duration = :duration,
              instructions = :instructions,
              age = :age,
              disease_type = :disease_type
              WHERE id = :id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':client_name' => $client_name,
        ':client_email' => $client_email,
        ':medicine_id' => $medicine_id,
        ':dose' => $dose,
        ':duration' => $duration,
        ':instructions' => $instructions,
        ':age' => $age,
        ':disease_type' => $disease_type,
        ':id' => $id
    ]);
    
    // Send updated prescription email if requested
    if (isset($_POST['send_email']) && $_POST['send_email'] == 'yes' && !empty($client_email)) {
        // Get medicine name
        $query = "SELECT medicine_name FROM medicines_drugs WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $medicine_id]);
        $medicine = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $to = $client_email;
        $subject = "Updated Prescription from Thika Level 5 Hospital";
        
        $emailBody = "
        <html>
        <head>
            <title>Updated Prescription</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .prescription { border: 1px solid #ccc; padding: 20px; }
                h2 { color: #2b5797; }
            </style>
        </head>
        <body>
            <div class='prescription'>
                <h2>Thika Level 5 Hospital - Updated Prescription</h2>
                <p><strong>Dear $client_name,</strong></p>
                <p>Your prescription has been updated. Here are the new details:</p>
                
                <p><strong>Medicine:</strong> {$medicine['medicine_name']}</p>
                <p><strong>Dose:</strong> $dose</p>
                <p><strong>Duration:</strong> $duration</p>
                <p><strong>Instructions:</strong> $instructions</p>
                <p><strong>Disease:</strong> $disease_type</p>
                
                <p>Please follow these instructions carefully. If you have any questions, contact our pharmacy department.</p>
                
                <p>Regards,<br>Thika Level 5 Hospital</p>
            </div>
        </body>
        </html>";
        
        // Headers for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: pharmacy@thikalevel5.com" . "\r\n";
        
        // Send email
        if(mail($to, $subject, $emailBody, $headers)) {
            $message = "Prescription updated successfully and email sent to patient.";
        } else {
            $message = "Prescription updated successfully but failed to send email.";
        }
        $messageType = "success";
    } else {
        $message = "Prescription updated successfully. No email sent.";
        $messageType = "success";
    }
}

// Delete prescription
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM prescriptions WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    
    $message = "Prescription deleted successfully.";
    $messageType = "success";
}

// Get prescription for editing
$edit_prescription = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = $_GET['edit'];
    $query = "SELECT * FROM prescriptions WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $edit_prescription = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all medicines to display in the prescription form
$query = "SELECT * FROM medicines_drugs";
$stmt = $pdo->prepare($query);
$stmt->execute();
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all prescriptions to display in a table
$query = "SELECT prescriptions.*, medicines_drugs.medicine_name FROM prescriptions 
          JOIN medicines_drugs ON prescriptions.medicine_id = medicines_drugs.id
          ORDER BY prescriptions.prescription_datetime DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Send individual email
if (isset($_GET['email']) && is_numeric($_GET['email'])) {
    $id = $_GET['email'];
    $query = "SELECT p.*, m.medicine_name FROM prescriptions p
              JOIN medicines_drugs m ON p.medicine_id = m.id
              WHERE p.id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $prescription = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($prescription && !empty($prescription['client_email'])) {
        $to = $prescription['client_email'];
        $subject = "Your Prescription from Thika Level 5 Hospital";
        
        $emailBody = "
        <html>
        <head>
            <title>Your Prescription</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .prescription { border: 1px solid #ccc; padding: 20px; }
                h2 { color: #2b5797; }
            </style>
        </head>
        <body>
            <div class='prescription'>
                <h2>Thika Level 5 Hospital - Prescription</h2>
                <p><strong>Dear {$prescription['client_name']},</strong></p>
                <p>Here are the details of your prescription:</p>
                
                <p><strong>Medicine:</strong> {$prescription['medicine_name']}</p>
                <p><strong>Dose:</strong> {$prescription['dose']}</p>
                <p><strong>Duration:</strong> {$prescription['duration']}</p>
                <p><strong>Instructions:</strong> {$prescription['instructions']}</p>
                <p><strong>Disease:</strong> {$prescription['disease_type']}</p>
                
                <p>Please follow these instructions carefully. If you have any questions, contact our pharmacy department.</p>
                
                <p>Regards,<br>Thika Level 5 Hospital</p>
            </div>
        </body>
        </html>";
        
        // Headers for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: pharmacy@thikalevel5.com" . "\r\n";
        
        // Send email
        if(mail($to, $subject, $emailBody, $headers)) {
            $message = "Email sent successfully to {$prescription['client_name']}.";
            $messageType = "success";
        } else {
            $message = "Failed to send email.";
            $messageType = "danger";
        }
    } else {
        $message = "Cannot send email. Either prescription not found or email address is missing.";
        $messageType = "danger";
    }
}

// View prescription details in a modal
$view_prescription = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $id = $_GET['view'];
    $query = "SELECT p.*, m.medicine_name FROM prescriptions p
              JOIN medicines_drugs m ON p.medicine_id = m.id
              WHERE p.id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $view_prescription = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - Thika Level 5</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .action-buttons a {
            margin-right: 5px;
        }
        .badge-prescription {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center mb-4">Thika Level 5 Hospital - Prescription System</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <?= $edit_prescription ? 'Edit Prescription' : 'Prescribe Medicine' ?>
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Prescription Form -->
                    <form method="POST" class="mb-4">
                        <?php if ($edit_prescription): ?>
                            <input type="hidden" name="prescription_id" value="<?= $edit_prescription['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="client_name" class="form-label">Client Name</label>
                                <input type="text" id="client_name" name="client_name" class="form-control" value="<?= $edit_prescription ? htmlspecialchars($edit_prescription['client_name']) : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="client_email" class="form-label">Client Email</label>
                                <input type="email" id="client_email" name="client_email" class="form-control" value="<?= $edit_prescription && isset($edit_prescription['client_email']) ? htmlspecialchars($edit_prescription['client_email']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="medicine_id" class="form-label">Medicine</label>
                                <select id="medicine_id" name="medicine_id" class="form-select" required>
                                    <option value="">Select Medicine</option>
                                    <?php foreach ($medicines as $medicine): ?>
                                        <option value="<?= $medicine['id'] ?>" <?= $edit_prescription && $edit_prescription['medicine_id'] == $medicine['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($medicine['medicine_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" id="age" name="age" class="form-control" value="<?= $edit_prescription ? htmlspecialchars($edit_prescription['age']) : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="dose" class="form-label">Dose</label>
                                <input type="text" id="dose" name="dose" class="form-control" value="<?= $edit_prescription ? htmlspecialchars($edit_prescription['dose']) : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" id="duration" name="duration" class="form-control" value="<?= $edit_prescription ? htmlspecialchars($edit_prescription['duration']) : '' ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label for="disease_type" class="form-label">Disease Type</label>
                                <input type="text" id="disease_type" name="disease_type" class="form-control" value="<?= $edit_prescription ? htmlspecialchars($edit_prescription['disease_type']) : '' ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label for="instructions" class="form-label">Instructions</label>
                                <textarea id="instructions" name="instructions" class="form-control" rows="3" required><?= $edit_prescription ? htmlspecialchars($edit_prescription['instructions']) : '' ?></textarea>
                            </div>
                            
                            <?php if ($edit_prescription): ?>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="send_email" value="yes" id="send_email">
                                    <label class="form-check-label" for="send_email">
                                        Send updated prescription to client via email
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-md-12 mt-3">
                                <?php if ($edit_prescription): ?>
                                    <button type="submit" name="update_prescription" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Prescription
                                    </button>
                                    <a href="prescription.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                <?php else: ?>
                                    <button type="submit" name="prescribe_medicine" class="btn btn-success">
                                        <i class="fas fa-prescription-bottle-medical"></i> Prescribe Medicine
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Display Prescriptions Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Prescription List</h3>
                    <a href="prescription_report.php" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar"></i> See Prescriptions Report
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Client Name</th>
                                <th>Medicine</th>
                                <th>Dose</th>
                                <th>Duration</th>
                                <th>Age</th>
                                <th>Disease</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($prescriptions) > 0): ?>
                                <?php foreach ($prescriptions as $prescription): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($prescription['client_name']) ?></td>
                                        <td><?= htmlspecialchars($prescription['medicine_name']) ?></td>
                                        <td><?= htmlspecialchars($prescription['dose']) ?></td>
                                        <td><?= htmlspecialchars($prescription['duration']) ?></td>
                                        <td><?= htmlspecialchars($prescription['age']) ?></td>
                                        <td><?= htmlspecialchars($prescription['disease_type']) ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime($prescription['prescription_datetime'])) ?></td>
                                        <td class="action-buttons">
                                            <a href="?view=<?= $prescription['id'] ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?edit=<?= $prescription['id'] ?>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (!empty($prescription['client_email'])): ?>
                                            <a href="?email=<?= $prescription['id'] ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Send Email">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="?delete=<?= $prescription['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this prescription?')" data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No prescriptions found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Prescription Modal -->
<?php if ($view_prescription): ?>
<div class="modal fade" id="viewPrescriptionModal" tabindex="-1" aria-labelledby="viewPrescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewPrescriptionModalLabel">Prescription Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Client:</strong> <?= htmlspecialchars($view_prescription['client_name']) ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($view_prescription['age']) ?></p>
                        <p><strong>Email:</strong> <?= !empty($view_prescription['client_email']) ? htmlspecialchars($view_prescription['client_email']) : 'Not provided' ?></p>
                        <p><strong>Date:</strong> <?= date('d-m-Y H:i', strtotime($view_prescription['prescription_datetime'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Medicine:</strong> <?= htmlspecialchars($view_prescription['medicine_name']) ?></p>
                        <p><strong>Dose:</strong> <?= htmlspecialchars($view_prescription['dose']) ?></p>
                        <p><strong>Duration:</strong> <?= htmlspecialchars($view_prescription['duration']) ?></p>
                        <p><strong>Disease:</strong> <?= htmlspecialchars($view_prescription['disease_type']) ?></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Instructions
                            </div>
                            <div class="card-body">
                                <?= nl2br(htmlspecialchars($view_prescription['instructions'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="?edit=<?= $view_prescription['id'] ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <?php if (!empty($view_prescription['client_email'])): ?>
                <a href="?email=<?= $view_prescription['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Send Email
                </a>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var viewModal = new bootstrap.Modal(document.getElementById('viewPrescriptionModal'));
        viewModal.show();
    });
</script>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
</body>
</html>