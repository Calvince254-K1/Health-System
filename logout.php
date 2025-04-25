<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                        'slide-down': 'slideDown 0.5s ease-out forwards',
                        'fade-in': 'fadeIn 0.7s ease-out forwards',
                        'scale-up': 'scaleUp 0.5s ease-out forwards',
                        'spin-slow': 'spin 3s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(50px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        scaleUp: {
                            '0%': { transform: 'scale(0.8)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                    },
                }
            }
        }
    </script>
    <style>
        /* Dark mode styles */
        body.dark {
            background-color: #1f2937;
            color: #e5e7eb;
        }
        .dark .bg-white {
            background-color: #374151;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal.active {
            display: flex;
            opacity: 1;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        .modal.active .modal-content {
            transform: scale(1);
        }
        
        /* Blur effect */
        .blur-bg {
            backdrop-filter: blur(5px);
        }
        
        /* Button hover effects */
        .btn-hover-effect {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-hover-effect:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 min-h-screen flex items-center justify-center transition-all duration-300">

    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg max-w-sm w-full opacity-0 animate-slide-up">
        <div class="text-center mb-6 animate-float">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-indigo-600 dark:text-indigo-400 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
        </div>
        
        <h1 class="text-2xl font-semibold mb-4 text-center text-indigo-600 dark:text-indigo-400 opacity-0 animate-fade-in" style="animation-delay: 0.3s;">You have successfully logged out!</h1>

        <p class="text-center text-lg text-gray-700 dark:text-gray-300 mb-4 opacity-0 animate-slide-down" style="animation-delay: 0.5s;">See you next time! When you're back, we'll be here to welcome you.</p>
        
        <p class="text-center text-sm text-gray-500 dark:text-gray-400 opacity-0 animate-fade-in" style="animation-delay: 0.7s;">You can <a href="login.php" id="logoutButton" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-600 transition-colors duration-300">log in again</a> when you're ready.</p>

        <div class="mt-6 text-center opacity-0 animate-scale-up" style="animation-delay: 0.9s;">
            <a href="index.html" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-300 btn-hover-effect">Back to Home</a>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="logoutModal" class="modal blur-bg">
        <div class="modal-content dark:bg-gray-800 dark:text-white">
            <div class="animate-pulse-slow mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-indigo-600 dark:text-indigo-400">Ready to log in?</h2>
            <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">Click "Yes" to log in, or "No" to stay logged out.</p>
            <div class="mt-6 flex justify-center space-x-3">
                <button id="confirmLogout" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-all duration-300 btn-hover-effect">Yes</button>
                <button id="cancelLogout" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition-all duration-300 btn-hover-effect">No</button>
            </div>
        </div>
    </div>

    <script>
        // Apply animations when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Automatically detect dark mode preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.classList.add('dark');
            }
            
            // Listen for changes in color scheme preference
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                if (event.matches) {
                    document.body.classList.add('dark');
                } else {
                    document.body.classList.remove('dark');
                }
            });
        });

        // Show logout confirmation modal when the logout button is clicked
        document.getElementById('logoutButton').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent immediate redirection
            document.getElementById('logoutModal').classList.add('active');
        });

        // If the user confirms logout, redirect to login page
        document.getElementById('confirmLogout').addEventListener('click', function () {
            window.location.href = 'login.php'; // Redirect to login page
        });

        // If the user cancels, close the modal
        document.getElementById('cancelLogout').addEventListener('click', function () {
            document.getElementById('logoutModal').classList.remove('active');
        });
        
        // Close modal when clicking outside the modal content
        document.getElementById('logoutModal').addEventListener('click', function (event) {
            if (event.target === this) {
                this.classList.remove('active');
            }
        });
    </script>

</body>
</html>