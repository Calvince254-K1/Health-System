<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $disease_treated = $_POST['disease_treated'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE drugs SET name=?, description=?, disease_treated=?, price=? WHERE id=?");
    $stmt->bind_param("sssdi", $name, $description, $disease_treated, $price, $id);
    $stmt->execute();

    // Redirect after editing the drug
    header('Location: index.php');
    exit();
}

// Get drug details for editing
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM drugs WHERE id=$id");
$drug = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Drug</title>
</head>
<body>
    <h1>Edit Drug</h1>
    <form method="POST" action="edit_drug.php">
        <input type="hidden" name="id" value="<?php echo $drug['id']; ?>">

        <label for="name">Drug Name:</label>
        <input type="text" name="name" value="<?php echo $drug['name']; ?>" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required><?php echo $drug['description']; ?></textarea><br>

        <label for="disease_treated">Disease Treated:</label>
        <input type="text" name="disease_treated" value="<?php echo $drug['disease_treated']; ?>" required><br>

        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" value="<?php echo $drug['price']; ?>" required><br>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
