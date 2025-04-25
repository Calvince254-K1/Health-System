<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Customer Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #74ebd5, #9face6);
            color: #333;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .section-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #007bff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            border-radius: 25px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5">Customer Management Section</h1>

        <!-- Customer Information Management -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="section-title">Customer Information Management</h2>
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" placeholder="Enter full name">
                        </div>
                        <div class="col-md-6">
                            <label for="contact" class="form-label">Contact Information</label>
                            <input type="email" class="form-control" id="contact" placeholder="Enter email or phone">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" placeholder="Enter address">
                        </div>
                        <div class="col-md-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob">
                        </div>
                        <div class="col-md-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender">
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-custom">Save Information</button>
                </form>
            </div>
        </div>

        <!-- Purchase History -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="section-title">Purchase History</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Medication</th>
                            <th>Date of Purchase</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Paracetamol</td>
                            <td>2024-01-25</td>
                            <td>500mg</td>
                            <td>Twice a day</td>
                        </tr>
                        <!-- Add dynamic rows here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Prescription Management -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="section-title">Prescription Management</h2>
                <form>
                    <div class="mb-3">
                        <label for="prescriptionUpload" class="form-label">Upload Prescription</label>
                        <input type="file" class="form-control" id="prescriptionUpload">
                    </div>
                    <button type="submit" class="btn btn-custom">Upload</button>
                </form>
            </div>
        </div>

        <!-- Notifications and Communication -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="section-title">Communication & Notifications</h2>
                <p>Send refill reminders, medication alerts, and promotional offers to customers seamlessly.</p>
                <button class="btn btn-custom">Send Notification</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
