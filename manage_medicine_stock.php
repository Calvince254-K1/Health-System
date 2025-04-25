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

// Add a new medicine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medicine'])) {
    $medicine_name = $_POST['medicine_name'];
    $packing = $_POST['packing'];
    $generic_name = $_POST['generic_name'];
    $batch_id = $_POST['batch_id'];
    $expiry_date = $_POST['expiry_date'];
    $supplier = $_POST['supplier'];
    $quantity = $_POST['quantity'];
    $mrp = $_POST['mrp'];
    $rate = $_POST['rate'];
    $stock_rate = $_POST['stock_rate'];

    $query = "INSERT INTO medicines_drugs (medicine_name, packing, generic_name, batch_id, expiry_date, supplier, quantity, mrp, rate, stock_rate)
              VALUES (:medicine_name, :packing, :generic_name, :batch_id, :expiry_date, :supplier, :quantity, :mrp, :rate, :stock_rate)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':medicine_name' => $medicine_name,
        ':packing' => $packing,
        ':generic_name' => $generic_name,
        ':batch_id' => $batch_id,
        ':expiry_date' => $expiry_date,
        ':supplier' => $supplier,
        ':quantity' => $quantity,
        ':mrp' => $mrp,
        ':rate' => $rate,
        ':stock_rate' => $stock_rate
    ]);
}

// Edit a medicine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_medicine'])) {
    $medicine_id = $_POST['medicine_id'];
    $medicine_name = $_POST['medicine_name'];
    $packing = $_POST['packing'];
    $generic_name = $_POST['generic_name'];
    $batch_id = $_POST['batch_id'];
    $expiry_date = $_POST['expiry_date'];
    $supplier = $_POST['supplier'];
    $quantity = $_POST['quantity'];
    $mrp = $_POST['mrp'];
    $rate = $_POST['rate'];
    $stock_rate = $_POST['stock_rate'];

    $query = "UPDATE medicines_drugs SET 
                medicine_name = :medicine_name, 
                packing = :packing, 
                generic_name = :generic_name, 
                batch_id = :batch_id, 
                expiry_date = :expiry_date, 
                supplier = :supplier, 
                quantity = :quantity, 
                mrp = :mrp, 
                rate = :rate,
                stock_rate = :stock_rate
              WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':medicine_name' => $medicine_name,
        ':packing' => $packing,
        ':generic_name' => $generic_name,
        ':batch_id' => $batch_id,
        ':expiry_date' => $expiry_date,
        ':supplier' => $supplier,
        ':quantity' => $quantity,
        ':mrp' => $mrp,
        ':rate' => $rate,
        ':stock_rate' => $stock_rate,
        ':id' => $medicine_id
    ]);
}

// Delete a medicine
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $query = "DELETE FROM medicines_drugs WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $delete_id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all medicines
$query = "SELECT * FROM medicines_drugs";
$stmt = $pdo->prepare($query);
$stmt->execute();
$medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the medicine to edit
$medicine_to_edit = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];

    $query = "SELECT * FROM medicines_drugs WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $edit_id]);
    $medicine_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thika Level 5 Medicine Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-indigo-800">Medicine Management System</h1>
            <p class="text-gray-600 mt-2">Thika Level 5 Hospital - Inventory Control</p>
        </div>

        <!-- Add Medicine Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <?= $medicine_to_edit ? 'Update Medicine' : 'Add New Medicine' ?>
            </h2>
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Name*</label>
                        <input type="text" name="medicine_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['medicine_name']) : '' ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Packing</label>
                        <input type="text" name="packing" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['packing']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Generic Name</label>
                        <input type="text" name="generic_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['generic_name']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch ID</label>
                        <input type="text" name="batch_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['batch_id']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <input type="date" name="expiry_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['expiry_date']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <input type="text" name="supplier" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['supplier']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['quantity']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">MRP (KES)</label>
                        <input type="number" step="0.01" name="mrp" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['mrp']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rate (KES)</label>
                        <input type="number" step="0.01" name="rate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['rate']) : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Rate (KES)</label>
                        <input type="number" step="0.01" name="stock_rate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                               value="<?= $medicine_to_edit ? htmlspecialchars($medicine_to_edit['stock_rate']) : '' ?>">
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" name="<?= $medicine_to_edit ? 'edit_medicine' : 'add_medicine' ?>" 
                            class="w-full md:w-auto px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <?= $medicine_to_edit ? 'Update Medicine' : 'Add Medicine' ?>
                    </button>
                    <?php if ($medicine_to_edit): ?>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="ml-2 px-4 py-2 bg-gray-300 text-gray-800 font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Cancel
                        </a>
                    <?php endif; ?>
                </div>
                <?= $medicine_to_edit ? '<input type="hidden" name="medicine_id" value="' . $medicine_to_edit['id'] . '">' : '' ?>
            </form>
        </div>

        <!-- Medicines Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Medicine Inventory</h2>
                <p class="text-sm text-gray-600 mt-1">Total <?= count($medicines) ?> medicines in stock</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generic</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MRP (KES)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate (KES)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Rate (KES)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($medicines as $index => $medicine): ?>
                            <tr class="<?= $medicine['quantity'] < 10 ? 'bg-red-50' : '' ?> hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $index + 1 ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($medicine['medicine_name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($medicine['packing']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($medicine['generic_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($medicine['batch_id']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($medicine['expiry_date']) ?>
                                    <?php if (strtotime($medicine['expiry_date']) < strtotime('+3 months')): ?>
                                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expiring</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?= $medicine['quantity'] < 10 ? 'font-bold text-red-600' : 'text-gray-500' ?>">
                                    <?= htmlspecialchars($medicine['quantity']) ?>
                                    <?php if ($medicine['quantity'] < 10): ?>
                                        <i class="fas fa-exclamation-circle ml-1 text-red-500"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($medicine['mrp'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($medicine['rate'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">
                                    <?= number_format($medicine['stock_rate'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="?edit_id=<?= $medicine['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete_id=<?= $medicine['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this medicine?');">
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
</div>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex justify-center md:order-2 space-x-6">
                <a href="https://www.facebook.com" class="text-gray-400 hover:text-white" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com" class="text-gray-400 hover:text-white" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.twitter.com" class="text-gray-400 hover:text-white" target="_blank">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com" class="text-gray-400 hover:text-white" target="_blank">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
            <div class="mt-8 md:mt-0 md:order-1">
                <p class="text-center text-base text-gray-400">
                    &copy; 2025 Thika Level 5 Hospital Management System. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
    // Add any JavaScript functionality here if needed
</script>
</body>
</html>