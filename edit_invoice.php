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

// Fetch invoice details
if (isset($_GET['id'])) {
    $invoice_id = $_GET['id'];
    $sql = "SELECT * FROM invoices WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();
} else {
    die("Invoice ID not provided.");
}

// Handle form submission to update invoice
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_invoice'])) {
    $patient_name = $_POST['patient_name'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $total_amount = $quantity * $unit_price;

    $sql = "UPDATE invoices SET patient_name = ?, medicine_id = ?, quantity = ?, unit_price = ?, total_amount = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiddi", $patient_name, $medicine_id, $quantity, $unit_price, $total_amount, $invoice_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Invoice updated successfully'); window.location.href = 'manage_invoice.php';</script>";
    } else {
        echo "<script>alert('Error updating invoice');</script>";
    }
}

// Fetch available medicines
$medicines = [];
$sql_meds = "SELECT id, name FROM med_drug";
$result_meds = $conn->query($sql_meds);
while ($row = $result_meds->fetch_assoc()) {
    $medicines[] = $row;
}

// Calculate total (for displaying in the UI)
$current_total = $invoice['quantity'] * $invoice['unit_price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice | Hospital Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        /* Custom gradient for headings and accents */
        .gradient-text {
            background: linear-gradient(90deg, #3B82F6, #2DD4BF);
            -webkit-background-clip: text;
            color: transparent;
        }
        
        .gradient-bg {
            background: linear-gradient(90deg, #3B82F6, #2DD4BF);
        }
        
        /* Smooth animations */
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Custom focus states */
        .custom-focus:focus {
            border-color:rgb(59, 246, 62);
            box-shadow: 0 0 0 3px rgba(187, 246, 59, 0.3);
            outline: none;
        }
        
        /* Card hover effect */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom number input styling */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 1;
            height: 30px;
            margin-right: 5px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Top navigation bar -->
    <nav class="bg-white shadow-md px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-hospital text-blue-500 text-2xl"></i>
                <span class="font-bold text-gray-800 text-xl">Hospital Inventory</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="manage_invoice.php" class="text-gray-600 hover:text-blue-500 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Invoices
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-10">
        <div class="flex justify-center">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg card-hover fade-in p-6 md:p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold gradient-text mb-2">Edit Invoice #<?php echo $invoice_id; ?></h1>
                    <p class="text-gray-500">Update invoice information below</p>
                </div>
                
                <!-- Form -->
                <form method="POST" class="space-y-6">
                    <!-- Patient Name -->
                    <div class="fade-in" style="animation-delay: 0.1s">
                        <label for="patient_name" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-user text-blue-500 mr-2"></i>Patient Name
                        </label>
                        <input 
                            type="text" 
                            name="patient_name" 
                            id="patient_name" 
                            value="<?php echo htmlspecialchars($invoice['patient_name']); ?>" 
                            class="w-full p-3 border border-gray-300 rounded-lg custom-focus transition-all duration-300"
                            required
                        >
                    </div>
                    
                    <!-- Medicine -->
                    <div class="fade-in" style="animation-delay: 0.2s">
                        <label for="medicine_id" class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-pills text-blue-500 mr-2"></i>Medicine
                        </label>
                        <select 
                            name="medicine_id" 
                            id="medicine_id" 
                            class="w-full p-3 border border-gray-300 rounded-lg custom-focus transition-all duration-300 bg-white"
                            required
                        >
                            <option value="">Select Medicine</option>
                            <?php foreach ($medicines as $medicine): ?>
                                <option value="<?php echo $medicine['id']; ?>" <?php echo ($medicine['id'] == $invoice['medicine_id']) ? 'selected' : ''; ?>>
                                    <?php echo $medicine['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Quantity and Price (side by side on larger screens) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 fade-in" style="animation-delay: 0.3s">
                        <div>
                            <label for="quantity" class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-calculator text-blue-500 mr-2"></i>Quantity
                            </label>
                            <input 
                                type="number" 
                                name="quantity" 
                                id="quantity" 
                                value="<?php echo $invoice['quantity']; ?>" 
                                min="1"
                                class="w-full p-3 border border-gray-300 rounded-lg custom-focus transition-all duration-300"
                                required
                                onchange="updateTotal()"
                            >
                        </div>
                        <div>
                            <label for="unit_price" class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-tag text-blue-500 mr-2"></i>Unit Price
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">$</span>
                                <input 
                                    type="number" 
                                    name="unit_price" 
                                    id="unit_price" 
                                    value="<?php echo $invoice['unit_price']; ?>"
                                    min="0.01"
                                    step="0.01"
                                    class="w-full p-3 pl-8 border border-gray-300 rounded-lg custom-focus transition-all duration-300"
                                    required
                                    onchange="updateTotal()"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Amount (calculated field) -->
                    <div class="bg-gray-50 rounded-lg p-4 fade-in" style="animation-delay: 0.4s">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Total Amount:</span>
                            <span class="text-2xl font-bold text-blue-600" id="total_display">$<?php echo number_format($current_total, 2); ?></span>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-4 fade-in" style="animation-delay: 0.5s">
                        <button 
                            type="submit" 
                            name="update_invoice" 
                            class="w-full gradient-bg text-white py-3 px-4 rounded-lg hover:opacity-90 transition-all duration-300 flex items-center justify-center font-medium"
                        >
                            <i class="fas fa-save mr-2"></i> Update Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to update total amount displayed
        function updateTotal() {
            const quantity = document.getElementById('quantity').value;
            const unitPrice = document.getElementById('unit_price').value;
            const total = quantity * unitPrice;
            document.getElementById('total_display').textContent = '$' + total.toFixed(2);
        }
        
        // Add smooth transitions when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const elements = document.querySelectorAll('.fade-in');
                elements.forEach(el => {
                    el.style.opacity = '1';
                });
            }, 100);
        });
    </script>

</body>
</html>

<?php $conn->close(); ?>