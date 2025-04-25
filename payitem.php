<?php
// Connect to the database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hospital_inventory";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";
$total_amount = 0;
$items = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save_record'])) {
        $item_name = $_POST['item_name'];
        $quantity = $_POST['quantity'];
        $amount = $_POST['amount'];
        $receipt = $_POST['mpesa_receipt'];
        $person = $_POST['person_name'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        $sql = "INSERT INTO item_records (item_name, quantity, amount, mpesa_receipt, person_name, date, time)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidssss", $item_name, $quantity, $amount, $receipt, $person, $date, $time);

        if ($stmt->execute()) {
            $msg = "<div class='alert success'>Data saved successfully!</div>";
        } else {
            $msg = "<div class='alert error'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } elseif (isset($_POST['update_record'])) {
        $id = $_POST['record_id'];
        $item_name = $_POST['item_name'];
        $quantity = $_POST['quantity'];
        $amount = $_POST['amount'];
        $receipt = $_POST['mpesa_receipt'];
        $person = $_POST['person_name'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        $sql = "UPDATE item_records SET item_name=?, quantity=?, amount=?, mpesa_receipt=?, person_name=?, date=?, time=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidssssi", $item_name, $quantity, $amount, $receipt, $person, $date, $time, $id);

        if ($stmt->execute()) {
            $msg = "<div class='alert success'>Record updated successfully!</div>";
        } else {
            $msg = "<div class='alert error'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Handle record deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM item_records WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $msg = "<div class='alert success'>Record deleted successfully!</div>";
    } else {
        $msg = "<div class='alert error'>Error deleting record: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Get total amount paid
$result = $conn->query("SELECT SUM(amount) as total FROM item_records");
if ($result) {
    $row = $result->fetch_assoc();
    $total_amount = $row['total'] ?? 0;
}

// Get all items
$items_result = $conn->query("SELECT * FROM item_records ORDER BY date DESC, time DESC");
if ($items_result) {
    $items = $items_result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Hospital Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            transition: all 0.3s;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert.success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .alert.error {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #ef4444;
        }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        .form-input {
            transition: border-color 0.3s;
        }
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar bg-blue-800 text-white w-64 space-y-6 py-7 px-2 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition duration-200 ease-in-out">
            <div class="text-white flex items-center space-x-2 px-4">
                <i class="fas fa-hospital text-2xl"></i>
                <span class="text-xl font-bold">Hospital Admin</span>
            </div>
            <nav>
                <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 hover:text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="#records" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 hover:text-white">
                    <i class="fas fa-list mr-2"></i>Item Records
                </a>
                <a href="#add" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 hover:text-white">
                    <i class="fas fa-plus-circle mr-2"></i>Add New
                </a>
                <a href="#reports" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 hover:text-white">
                    <i class="fas fa-chart-bar mr-2"></i>Reports
                </a>
                <a href="process_payment.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 hover:text-white">
                    <i class="fas fa-chart-bar mr-2"></i>PayItems
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64">
            <div class="p-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><i class="fas fa-user-circle mr-1"></i> Admin</span>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </div>
                </div>

                <?php echo $msg; ?>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Payments</p>
                                <h3 class="text-2xl font-bold" id="totalAmount">KES <?php echo number_format($total_amount, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-boxes text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Items</p>
                                <h3 class="text-2xl font-bold"><?php echo count($items); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                <i class="fas fa-receipt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Transactions</p>
                                <h3 class="text-2xl font-bold"><?php echo count($items); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Records Section -->
                <div id="records" class="bg-white p-6 rounded-lg shadow mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-list mr-2"></i>Item Records
                        </h2>
                        
                        <div class="flex space-x-2">
                            <input type="text" id="searchInput" placeholder="Search records..." class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button onclick="exportToExcel()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-file-excel mr-1"></i> Export
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Person</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $item['quantity']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">KES <?php echo number_format($item['amount'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['mpesa_receipt']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['person_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M j, Y', strtotime($item['date'])); ?><br><?php echo $item['time']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="editRecord(<?php echo $item['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?')" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add/Edit Form Section -->
                <div id="add" class="bg-white p-6 rounded-lg shadow mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-plus-circle mr-2"></i>
                        <span id="formTitle">Add New Item Record</span>
                    </h2>
                    <form method="POST" id="recordForm">
                        <input type="hidden" name="record_id" id="record_id">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="item_name" class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                                <input type="text" id="item_name" name="item_name" required class="form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input type="number" id="quantity" name="quantity" required class="form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (KES)</label>
                                <input type="number" step="0.01" id="amount" name="amount" required class="form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="mpesa_receipt" class="block text-sm font-medium text-gray-700 mb-1">MPESA Receipt No.</label>
                                <input type="text" id="mpesa_receipt" name="mpesa_receipt" required class="form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="person_name" class="block text-sm font-medium text-gray-700 mb-1">Name of Person</label>
                                <input type="text" id="person_name" name="person_name" required class="form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="date" name="date" required class="form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                <input type="time" id="time" name="time" required class="form-input w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="resetForm()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </button>
                            <button type="submit" name="save_record" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-save mr-1"></i> Save Record
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Reports Section -->
                <div id="reports" class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-chart-bar mr-2"></i>Reports & Analytics
                    </h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white p-4 rounded-lg border">
                            <h3 class="text-lg font-semibold mb-4">Payments by Date</h3>
                            <canvas id="paymentsChart" height="250"></canvas>
                        </div>
                        <div class="bg-white p-4 rounded-lg border">
                            <h3 class="text-lg font-semibold mb-4">Top Items</h3>
                            <canvas id="itemsChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button onclick="printReport()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-print mr-1"></i> Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Edit record function
        function editRecord(id) {
            fetch('get_record.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('record_id').value = data.id;
                    document.getElementById('item_name').value = data.item_name;
                    document.getElementById('quantity').value = data.quantity;
                    document.getElementById('amount').value = data.amount;
                    document.getElementById('mpesa_receipt').value = data.mpesa_receipt;
                    document.getElementById('person_name').value = data.person_name;
                    document.getElementById('date').value = data.date;
                    document.getElementById('time').value = data.time;
                    
                    document.getElementById('formTitle').textContent = 'Edit Item Record';
                    document.getElementById('submitBtn').name = 'update_record';
                    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save mr-1"></i> Update Record';
                    
                    document.getElementById('add').scrollIntoView({ behavior: 'smooth' });
                });
        }

        // Reset form function
        function resetForm() {
            document.getElementById('recordForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('formTitle').textContent = 'Add New Item Record';
            document.getElementById('submitBtn').name = 'save_record';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save mr-1"></i> Save Record';
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const input = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        });

        // Export to Excel
        function exportToExcel() {
            const html = document.querySelector('table').outerHTML;
            const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'item_records.xls';
            a.click();
        }

        // Print report
        function printReport() {
            window.print();
        }

        // Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Payments by date chart
            const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
            const paymentsChart = new Chart(paymentsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Payments (KES)',
                        data: [12000, 19000, 15000, 20000, 25000, 22000],
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Top items chart
            const itemsCtx = document.getElementById('itemsChart').getContext('2d');
            const itemsChart = new Chart(itemsCtx, {
                type: 'bar',
                data: {
                    labels: ['Medication A', 'Equipment B', 'Supplies C', 'Test Kits D', 'Other E'],
                    datasets: [{
                        label: 'Total Amount (KES)',
                        data: [45000, 38000, 29000, 23000, 15000],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(139, 92, 246, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>