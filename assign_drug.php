<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $drug_id = $_POST['drug_id'];
    $patient_name = $_POST['patient_name'];
    $patient_contact = $_POST['patient_contact'];

    // Insert assignment record into the database
    $stmt = $conn->prepare("INSERT INTO assignments (drug_id, patient_name, patient_contact) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $drug_id, $patient_name, $patient_contact);
    $stmt->execute();

    // Redirect to this page to refresh the assigned drugs list
    header('Location: assign_drug.php?id=' . $drug_id); 
    exit();
}

// Get the drug details for the assignment
$drug_id = $_GET['id'];
$result = $conn->query("SELECT * FROM drugs WHERE id = $drug_id");
$drug = $result->fetch_assoc();

// Fetch all assigned drugs for this particular drug
$assignments_result = $conn->query("SELECT * FROM assignments WHERE drug_id = $drug_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Drug</title>
</head>
<body>
    <h1>Assign Drug: <?php echo htmlspecialchars($drug['name']); ?></h1>
    
    <form method="POST" action="assign_drug.php">
        <input type="hidden" name="drug_id" value="<?php echo $drug['id']; ?>">

        <label for="patient_name">Patient's Name:</label>
        <input type="text" name="patient_name" required><br>

        <label for="patient_contact">Patient's Contact:</label>
        <input type="text" name="patient_contact" required><br>

        <button type="submit">Assign Drug</button>
    </form>

    <h2>Assigned Patients for <?php echo htmlspecialchars($drug['name']); ?></h2>

    <?php if ($assignments_result->num_rows > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Patient Contact</th>
                    <th>Assigned At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = $assignments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['patient_contact']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['assigned_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No patients assigned to this drug yet.</p>
    <?php endif; ?>
</body>
</html>
