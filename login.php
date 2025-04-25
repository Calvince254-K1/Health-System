<?php
session_start();
include('db_connection.php');

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    // Check session timeout (10 minutes)
    $inactive_time = 600; // 10 minutes in seconds
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $inactive_time) {
        session_unset(); // Destroy session variables
        session_destroy(); // Destroy the session
        header('Location: login.php'); // Redirect to login page with timeout message
        exit();
    }
    $_SESSION['login_time'] = time(); // Update the session time to prevent automatic logout
    header('Location: home.php'); // Redirect to home page if already logged in
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role']; // Store role in session
        $_SESSION['email'] = $user['email']; // Store email in session
        $_SESSION['login_time'] = time(); // Store login timestamp
        
        header('Location: home.php');
    } else {
        $error_message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Pharmacy Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'spin-slow': 'spin 8s linear infinite',
                        'fade-in': 'fadeIn 1.2s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                        'slide-right': 'slideRight 0.8s ease-out forwards',
                        'zoom-in': 'zoomIn 0.8s ease-out forwards',
                        'wave': 'wave 2.5s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(30px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideRight: {
                            '0%': { transform: 'translateX(-30px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        zoomIn: {
                            '0%': { transform: 'scale(0.8)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        wave: {
                            '0%': { transform: 'rotate(0deg)' },
                            '10%': { transform: 'rotate(14deg)' },
                            '20%': { transform: 'rotate(-8deg)' },
                            '30%': { transform: 'rotate(14deg)' },
                            '40%': { transform: 'rotate(-4deg)' },
                            '50%': { transform: 'rotate(10deg)' },
                            '60%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(0deg)' },
                        },
                    },
                    colors: {
                        'medical-blue': '#1a76d2',
                        'medical-teal': '#20c997',
                        'medical-green': '#28a745',
                        'medical-dark': '#033c73',
                    },
                    boxShadow: {
                        'inner-glow': 'inset 0 0 20px rgba(255, 255, 255, 0.5)',
                        'outer-glow': '0 0 20px rgba(26, 118, 210, 0.5)',
                    },
                }
            },
        }
    </script>
    <style>
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animated-gradient {
            background: linear-gradient(-45deg, #1a76d2, #20c997, #033c73, #28a745);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        .card-shadow {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .card-shadow:hover {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            transform: translateY(-5px);
        }

        .input-focus-effect {
            transition: all 0.3s ease;
        }
        
        .input-focus-effect:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 3px rgba(26, 118, 210, 0.3);
        }

        .btn-pulse {
            position: relative;
        }
        
        .btn-pulse::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 0.375rem;
            background: rgba(26, 118, 210, 0.4);
            transform: scale(1);
            animation: pulse 2s infinite;
            z-index: -1;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        .image-container {
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .image-container img {
            transition: transform 0.5s ease;
        }

        .image-container:hover img {
            transform: scale(1.1);
        }

        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center animated-gradient">
    <div class="relative flex w-full max-w-6xl p-4">
        <!-- Animated floating pills and icons -->
        <div class="absolute top-10 left-10 animate-float opacity-70">
            <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div class="absolute bottom-20 right-20 animate-spin-slow opacity-70">
            <svg class="w-16 h-16 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </div>
        <div class="absolute top-20 right-20 animate-pulse-slow opacity-70">
            <svg class="w-12 h-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>
        <div class="absolute bottom-10 left-24 animate-bounce-slow opacity-70">
            <svg class="w-12 h-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>

        <!-- Left side with images -->
        <div class="hidden lg:flex flex-col w-1/2 space-y-6 p-6">
            <div class="flex items-center space-x-4 mb-6 opacity-0 animate-slide-right" style="animation-delay: 0.3s;">
                <svg class="w-14 h-14 text-white animate-pulse-slow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <h1 class="text-3xl font-bold text-white">Thika Pharmacy System</h1>
            </div>
            
            <div class="image-container opacity-0 animate-fade-in" style="animation-delay: 0.6s;">
                <img src="images/nurse.jpg" alt="Hospital Pharmacy" class="w-full h-64 object-cover rounded-lg shadow-lg" />
                <div class="mt-2 text-white text-center font-medium text-lg">Thika Pharmacy Management</div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="image-container opacity-0 animate-zoom-in" style="animation-delay: 0.9s;">
                    <img src="images/login1.jpg" alt="Medication Management" class="w-full h-40 object-cover rounded-lg shadow-lg" />
                    <div class="mt-1 text-white text-center text-sm">Medication Tracking</div>
                </div>
                <div class="image-container opacity-0 animate-zoom-in" style="animation-delay: 1.2s;">
                    <img src="images/drugs.jpg" alt="Patient Care" class="w-full h-40 object-cover rounded-lg shadow-lg" />
                    <div class="mt-1 text-white text-center text-sm">Patient Care</div>
                </div>
            </div>
            
            <div class="p-4 rounded-lg glass-effect opacity-0 animate-fade-in" style="animation-delay: 1.5s;">
                <p class="text-white text-lg font-medium">"Streamlining medication management for better patient outcomes."</p>
                <p class="text-white text-sm mt-2">Access our advanced pharmacy system to manage prescriptions, inventory, and patient records efficiently.</p>
            </div>
        </div>

        <!-- Right side with login form -->
        <div class="w-full lg:w-1/2 px-6 py-8 opacity-0 animate-slide-up" style="animation-delay: 0.3s;">
            <div class="bg-white rounded-xl card-shadow p-8 max-w-md mx-auto">
                <div class="flex justify-center mb-6">
                    <svg class="w-16 h-16 text-medical-blue animate-pulse-slow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 4v16m8-8a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                </div><h1 class="text-3xl font-bold text-center text-medical-blue mb-2">Welcome Back</h1>
                <p class="text-center text-gray-600 mb-6">Log in to the Hospital Pharmacy System</p>
                
                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-shake">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p><?php echo $error_message; ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" name="username" id="username" placeholder="Username" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-medical-blue input-focus-effect" required>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" placeholder="Password" class="pl-10 w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-medical-blue input-focus-effect" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-medical-blue focus:ring-medical-blue border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-gray-700">Remember me</label>
                        </div>
                        <a href="forgot-password.php" class="text-medical-blue hover:text-medical-dark transition-colors duration-300">Forgot Password?</a>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-medical-blue text-white font-semibold rounded-lg hover:bg-medical-dark transition-all duration-300 btn-pulse shadow-lg hover:shadow-xl">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login to Dashboard
                        </div>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-center">
                        <a href="signup.php" class="text-medical-blue hover:text-medical-dark transition-colors duration-300">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Create New Account
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Show user details after login (if logged in) -->
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="mt-6 bg-gray-100 p-4 rounded-lg">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-medical-blue mr-3 animate-wave" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-xl font-semibold text-medical-blue">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                        </div>
                        <div class="space-y-2 mb-4">
                            <p class="text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-medical-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email: <?php echo htmlspecialchars($_SESSION['email']); ?>
                            </p>
                            <p class="text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-medical-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Role: <?php echo htmlspecialchars($_SESSION['role']); ?>
                            </p>
                            <p class="text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-medical-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Login Time: <?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?>
                            </p>
                            <p class="text-gray-700 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-medical-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Time Spent: <?php echo gmdate("H:i:s", time() - $_SESSION['login_time']); ?>
                            </p>
                        </div>
                        <form method="POST" action="logout.php">
                            <button type="submit" class="w-full py-3 px-4 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-lg hover:shadow-xl">
                                <div class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </div>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-8 text-center text-white text-sm opacity-0 animate-fade-in" style="animation-delay: 1.5s;">
                Hospital Pharmacy Management System &copy; 2025 | <a href="#" class="text-blue-200 hover:text-white">Privacy Policy</a> | <a href="#" class="text-blue-200 hover:text-white">Terms of Service</a>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification" class="fixed bottom-5 right-5 bg-white rounded-lg shadow-lg p-4 hidden transform transition-all duration-500 opacity-0 translate-y-10 max-w-sm glass-effect">
        <div class="flex items-center">
            <svg class="w-8 h-8 text-medical-blue mr-3 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Welcome to Hospital Pharmacy</h3>
                <p class="text-sm text-gray-600">Please login to access the pharmacy management system.</p>
            </div>
        </div>
    </div>

    <script>
     // Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // Toggle the eye icon
    this.innerHTML = type === 'password' ? 
        '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>' : 
        '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>';
});

// Show notification toast
window.addEventListener('load', function() {
    const notification = document.getElementById('notification');
    setTimeout(function() {
        notification.classList.remove('hidden', 'opacity-0', 'translate-y-10');
        notification.classList.add('opacity-100', 'translate-y-0');
        
        // Hide notification after 5 seconds
        setTimeout(function() {
            notification.classList.add('opacity-0', 'translate-y-10');
            notification.classList.remove('opacity-100', 'translate-y-0');
        }, 5000);
    }, 1000);
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    if (username === '' || password === '') {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});

// Add input animation effects
const inputs = document.querySelectorAll('input');
inputs.forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('scale-105');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('scale-105');
    });
});