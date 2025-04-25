<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard - Thika Level 5 Hospital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #00509E;
            --primary-dark: #002244;
            --primary-light: #87CEFA;
            --secondary-color: #4CAF50;
            --accent-color: #FF6B6B;
            --text-color: #333333;
            --text-light: #f0f0f0;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --sidebar-bg: #002244;
            --sidebar-width: 280px;
            --header-height: 80px;
            --transition-speed: 0.3s;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--text-light);
            padding: 1.5rem 0;
            transition: all var(--transition-speed) ease;
            position: fixed;
            height: 100vh;
            z-index: 100;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-header h2 img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        .sidebar-menu {
            padding: 0 1rem;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
            position: relative;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all var(--transition-speed) ease;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--primary-light);
        }

        .sidebar-menu a i {
            font-size: 1.1rem;
            margin-right: 0.75rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-menu .badge {
            margin-left: auto;
            background-color: var(--accent-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: all var(--transition-speed) ease;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--card-bg);
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 1.5rem;
            height: var(--header-height);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .header-title p {
            font-size: 0.85rem;
            color: var(--text-color);
            opacity: 0.7;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-widget {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-widget i {
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .header-widget .time {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-profile {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            border: none;
            font-family: inherit;
            font-weight: 500;
        }

        .profile-btn:hover {
            background: var(--primary-dark);
        }

        .profile-img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 220px;
            padding: 0.75rem 0;
            z-index: 10;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all var(--transition-speed) ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            transition: all var(--transition-speed) ease;
        }

        .dropdown-menu a:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--primary-color);
        }

        .dropdown-menu a i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
            margin: 0.5rem 0;
        }

        /* Dashboard Content */
        .dashboard-content {
            margin-top: 1.5rem;
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .welcome-banner p {
            max-width: 600px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .welcome-banner::after {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }

        .welcome-banner::before {
            content: "";
            position: absolute;
            bottom: -80px;
            right: 20px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            z-index: 1;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            transition: all var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .card-icon.blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .card-icon.green {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .card-icon.orange {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
        }

        .card-icon.purple {
            background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
        }

        .card-icon.red {
            background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%);
        }

        .card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--primary-dark);
        }

        .card p {
            color: var(--text-color);
            opacity: 0.8;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .card-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
        }

        .card-link:hover {
            color: var(--primary-dark);
        }

        .card-link i {
            margin-left: 0.5rem;
            transition: transform var(--transition-speed) ease;
        }

        .card-link:hover i {
            transform: translateX(3px);
        }

        /* Quick Stats Section */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            text-align: center;
        }

        .stat-card h4 {
            font-size: 0.9rem;
            color: var(--text-color);
            opacity: 0.7;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-change {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .stat-card .stat-change.positive {
            color: var(--secondary-color);
        }

        .stat-card .stat-change.negative {
            color: var(--accent-color);
        }

        /* Footer Styles */
        .footer {
            background: var(--primary-dark);
            color: var(--text-light);
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .footer-logo img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .footer-logo h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .footer-about p {
            opacity: 0.8;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .footer-social {
            display: flex;
            gap: 1rem;
        }

        .footer-social a {
            color: var(--text-light);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-speed) ease;
        }

        .footer-social a:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .footer-links h4 {
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-links h4::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background: var(--primary-light);
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: var(--text-light);
            opacity: 0.8;
            text-decoration: none;
            transition: all var(--transition-speed) ease;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-links a:hover {
            opacity: 1;
            color: var(--primary-light);
            transform: translateX(5px);
        }

        .footer-links a i {
            font-size: 0.7rem;
        }

        .footer-contact p {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .footer-contact i {
            margin-top: 0.2rem;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            opacity: 0.7;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                height: auto;
                gap: 1rem;
                padding: 1rem;
            }

            .header-left, .header-right {
                width: 100%;
                justify-content: center;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .quick-stats {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 576px) {
            .quick-stats {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>
                    <img src="https://via.placeholder.com/36" alt="Hospital Logo">
                    Thika Level 5
                </h2>
            </div>
            <nav class="sidebar-menu">
                <ul>
                    <li>
                        <a href="dashboard.php" class="active">
                            <i class="fas fa-home"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="manage_medicine_stock.php">
                            <i class="fas fa-pills"></i>
                            Drugs/Medicine
                            <span class="badge">5</span>
                        </a>
                    </li>
                    <li>
                        <a href="customer_manage.php">
                            <i class="fas fa-users"></i>
                            Customer Management
                        </a>
                    </li>
                    <li>
                        <a href="supplier_management.php">
                            <i class="fas fa-truck"></i>
                            Supplier Management
                        </a>
                    </li>
                    <li>
                        <a href="manage_invoice.php">
                            <i class="fas fa-file-invoice"></i>
                            Invoices
                            <span class="badge">3</span>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php">
                            <i class="fas fa-chart-bar"></i>
                            Reports
                        </a>
                        <a href="payitem.php">
                            <i class="fas fa-chart-bar"></i>
                            Payments
                        </a>
                        <a href="Email.html">
                            <i class="fas fa-chart-bar"></i>
                            Talk to Someone
                        </a>
                    </li>
                    <li>
                        <a href="settings.php">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <img src="https://via.placeholder.com/50" alt="Pharmacy Logo" class="header-logo">
                    <div class="header-title">
                        <h1>Pharmacy Dashboard</h1>
                        <p>Manage medications, customers, and suppliers</p>
                    </div>
                </div>
                <div class="header-right">
                    <div class="header-widget">
                        <i class="far fa-clock"></i>
                        <div class="time" id="current-time"></div>
                    </div>
                    <div class="user-profile">
                        <button class="profile-btn" id="profile-btn">
                            <img src="https://via.placeholder.com/36" alt="Admin" class="profile-img">
                            <span>Admin</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" id="dropdown-menu">
                            <a href="profile.php">
                                <i class="far fa-user"></i>
                                My Profile
                            </a>
                            <a href="settings.php">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <h1>Welcome back, Admin!</h1>
                    <p>You have 5 new prescriptions to review and 3 low stock alerts. The system was last updated today at 08:45 AM.</p>
                </div>

                <!-- Quick Stats -->
                <div class="quick-stats">
                    <div class="stat-card">
                        <h4>Total Medications</h4>
                        <div class="stat-value">248</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            12% from last week
                        </div>
                    </div>
                    <div class="stat-card">
                        <h4>Today's Prescriptions</h4>
                        <div class="stat-value">17</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            5% from yesterday
                        </div>
                    </div>
                    <div class="stat-card">
                        <h4>Low Stock Items</h4>
                        <div class="stat-value">5</div>
                        <div class="stat-change negative">
                            <i class="fas fa-arrow-down"></i>
                            2 new this week
                        </div>
                    </div>
                    <div class="stat-card">
                        <h4>Pending Orders</h4>
                        <div class="stat-value">3</div>
                        <div class="stat-change">
                            <i class="fas fa-minus"></i>
                            No change
                        </div>
                    </div>
                </div>

                <!-- Main Cards Grid -->
                <div class="cards-grid">
                    <div class="card">
                        <div class="card-icon blue">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h3>Medicine Management</h3>
                        <p>Track and manage all medications in stock with detailed information including batch numbers and expiration dates.</p>
                        <a href="manage_medicine_stock.php" class="card-link">
                            Go to Drugs
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card">
                        <div class="card-icon green">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Customer Management</h3>
                        <p>View and manage customer information, prescription history, and contact details in one place.</p>
                        <a href="customer_manage.php" class="card-link">
                            Go to Customers
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card">
                        <div class="card-icon orange">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3>Supplier Management</h3>
                        <p>Oversee supplier interactions, track orders, and manage contracts with pharmaceutical companies.</p>
                        <a href="supplier_management.php" class="card-link">
                            Go to Suppliers
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card">
                        <div class="card-icon purple">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h3>Invoices & Billing</h3>
                        <p>Generate and manage invoices, track payments, and view billing history for all transactions.</p>
                        <a href="manage_invoice.php" class="card-link">
                            Go to Invoices
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card">
                        <div class="card-icon red">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Reports & Analytics</h3>
                        <p>Generate comprehensive reports on sales, inventory, and other key pharmacy metrics.</p>
                        <a href="reports.php" class="card-link">
                            View Reports
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card">
                        <div class="card-icon blue">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3>System Settings</h3>
                        <p>Configure system preferences, user permissions, and other administrative settings.</p>
                        <a href="settings.php" class="card-link">
                            Go to Settings
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-logo">
                        <img src="https://via.placeholder.com/40" alt="Hospital Logo">
                        <h3>Thika Level 5 Hospital</h3>
                    </div>
                    <p>Providing quality healthcare services with state-of-the-art facilities and professional medical staff.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Services</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Doctors</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Departments</h4>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Emergency</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Outpatient</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Inpatient</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Surgery</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Pharmacy</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Hospital Road, Thika Town, Kenya</p>
                    <p><i class="fas fa-phone-alt"></i> +254 700 000 000</p>
                    <p><i class="fas fa-envelope"></i> info@thikahospital.com</p>
                    <p><i class="fas fa-clock"></i> Open 24/7</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Thika Level 5 Hospital. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Update time
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('current-time').innerText = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateTime, 1000);
        window.onload = updateTime;

        // Toggle dropdown menu
        const profileBtn = document.getElementById('profile-btn');
        const dropdownMenu = document.getElementById('dropdown-menu');

        profileBtn.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!profileBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // For mobile menu toggle (would need additional HTML button)
        // const menuToggle = document.querySelector('.menu-toggle');
        // const sidebar = document.querySelector('.sidebar');
        
        // menuToggle.addEventListener('click', function() {
        //     sidebar.classList.toggle('active');
        // });
    </script>
</body>

</html>