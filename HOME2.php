
!<DOCTYPE html> -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thika Hospital Pharmacy - Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        secondary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        warning: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.5s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'spin-slow': 'spin 3s linear infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-20px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>

    <!-- Top Navigation Bar -->
    <div class="bg-primary-800 text-white px-4 py-2 sm:px-6 flex justify-between items-center shadow-md">
        <div class="flex items-center space-x-2">
            <i class="fas fa-hospital-alt text-xl"></i>
            <span class="font-semibold text-lg hidden sm:inline">Thika Hospital Pharmacy</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="tel:+254700000000" class="hover:text-primary-200 transition-colors hidden sm:flex items-center space-x-1">
                <i class="fas fa-phone-alt"></i>
                <span>+254 700 000 000</span>
            </a>
            <a href="mailto:info@thikahospital.com" class="hover:text-primary-200 transition-colors hidden md:flex items-center space-x-1">
                <i class="fas fa-envelope"></i>
                <span>info@thikahospital.com</span>
            </a>
            <div x-data="{ profileOpen: false }" class="relative">
    <button @click="profileOpen = !profileOpen" class="flex items-center space-x-1 focus:outline-none">
        <img src="images/photo1.jpg" alt="Profile" class="w-8 h-8 rounded-full border-2 border-white">
        <span class="hidden sm:inline">
         
        </span>
        <i class="fas fa-chevron-down text-xs"></i>
    </button>
</div>

                <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak 
                    class="absolute right-0 mt-2 w-48 bg-white text-gray-700 rounded-md shadow-lg py-1 z-50">
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-user mr-2"></i>Profile</a>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-cog mr-2"></i>Settings</a>
                    <hr class="my-1">
                    <a href="#" class="block px-4 py-2 text-red-500 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Layout -->
    <div class="flex flex-1 overflow-hidden" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <div :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}" 
            class="bg-primary-900 text-white transition-all duration-300 ease-in-out flex flex-col">
            
            <!-- Sidebar Header -->
            <div class="p-4 flex items-center justify-between border-b border-primary-700">
                <div class="flex items-center space-x-2" :class="{'opacity-0': !sidebarOpen}">
                    <i class="fas fa-pills text-xl"></i>
                    <span class="font-semibold truncate">Pharmacy Inventory</span>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-primary-300 hover:text-white">
                    <i :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'" class="fas"></i>
                </button>
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto scrollbar-hide py-4">
                <ul>
                    <li class="mb-1">
                        <a href="#dashboard" class="flex items-center py-2 px-4 text-white bg-primary-800 bg-opacity-70 rounded-md mx-2">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300">Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#inventory" class="flex items-center py-2 px-4 text-white hover:bg-primary-800 hover:bg-opacity-50 rounded-md mx-2">
                            <i class="fas fa-boxes w-5"></i>
                            <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300">Inventory</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#prescriptions" class="flex items-center py-2 px-4 text-white hover:bg-primary-800 hover:bg-opacity-50 rounded-md mx-2">
                            <i class="fas fa-prescription w-5"></i>
                            <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300">Prescriptions</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#orders" class="flex items-center py-2 px-4 text-white hover:bg-primary-800 hover:bg-opacity-50 rounded-md mx-2">
                            <i class="fas fa-shopping-cart w-5"></i>
                            <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300">Purchase Orders</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#suppliers" class="flex items-center py-2 px-4 text-white hover:bg-primary-800 hover:bg-opacity-50 rounded-md mx-2">
                            <i class="fas fa-truck w-5"></i>
                            <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300">Suppliers</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="#reports" class="flex items-center py-2 px-4 text-white hover:bg-primary-800 hover:bg-opacity-50 rounded-md mx-2">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300">Reports</span>
                        </a>
                    </li>
                    <li class="mb-1">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="w-full flex items-center py-2 px-4 text-white hover:bg-primary-800 hover:bg-opacity-50 rounded-md mx-2">
                                <i class="fas fa-cog w-5"></i>
                                <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300 flex-1 text-left">Settings</span>
                                <i :class="{'opacity-0': !sidebarOpen, 'fa-chevron-down': !open, 'fa-chevron-up': open}" class="fas transition-transform duration-200 ml-2"></i>
                            </button>
                            <div x-show="open" x-cloak class="pl-10" :class="{'hidden': !sidebarOpen}">
                                <a href="#profile" class="block py-2 hover:text-primary-300">User Profile</a>
                                <a href="#system" class="block py-2 hover:text-primary-300">System Settings</a>
                                <a href="#backup" class="block py-2 hover:text-primary-300">Backup & Restore</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-primary-700">
                <div :class="{'hidden': !sidebarOpen}" class="text-xs text-primary-300 mb-2">
                    <p>System Version: 2.5.3</p>
                    <p>Last Updated: 12 Mar 2025</p>
                </div>
                <a href="#" class="flex items-center text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span :class="{'opacity-0': !sidebarOpen, 'ml-3': sidebarOpen}" class="transition-opacity duration-300">Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
            <!-- Top Header with Page Title -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" placeholder="Search inventory..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 w-64">
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="p-2 text-gray-500 hover:text-primary-700 relative">
                                <i class="fas fa-bell"></i>
                                <span class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
                            </button>
                            <button class="p-2 text-gray-500 hover:text-primary-700">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Alert for expiring medications -->
                <div class="bg-<!-- Alert for expiring medications -->
                    <div class="bg-warning-100 border-l-4 border-warning-500 text-warning-700 p-4 rounded-md animate-fade-in">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-warning-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium">Alert: 15 medications are expiring within 30 days.</p>
                                <p class="text-sm">Please review the <a href="#expiring" class="underline font-medium">expiring medications list</a> and take necessary action.</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button class="text-warning-500 hover:text-warning-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
    
                    <!-- Dashboard Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Inventory Value Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-lg">
                            <div class="p-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium">Total Inventory Value</p>
                                        <h3 class="text-2xl font-bold text-gray-800">KSh 3,549,820</h3>
                                        <p class="text-green-600 text-sm mt-1 flex items-center">
                                            <i class="fas fa-arrow-up mr-1"></i>
                                            <span>4.2% from last month</span>
                                        </p>
                                    </div>
                                    <div class="bg-primary-100 p-3 rounded-full">
                                        <i class="fas fa-money-bill-wave text-primary-600 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3">
                                <a href="#inventory-value" class="text-sm text-primary-600 font-medium hover:text-primary-800">View details</a>
                            </div>
                        </div>
    
                        <!-- Low Stock Items Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-lg">
                            <div class="p-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium">Low Stock Items</p>
                                        <h3 class="text-2xl font-bold text-gray-800">28</h3>
                                        <p class="text-red-600 text-sm mt-1 flex items-center">
                                            <i class="fas fa-arrow-up mr-1"></i>
                                            <span>12% from last week</span>
                                        </p>
                                    </div>
                                    <div class="bg-red-100 p-3 rounded-full">
                                        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3">
                                <a href="#low-stock" class="text-sm text-primary-600 font-medium hover:text-primary-800">View details</a>
                            </div>
                        </div>
    
                        <!-- Prescriptions Filled Today Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-lg">
                            <div class="p-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium">Prescriptions Filled Today</p>
                                        <h3 class="text-2xl font-bold text-gray-800">124</h3>
                                        <p class="text-green-600 text-sm mt-1 flex items-center">
                                            <i class="fas fa-arrow-up mr-1"></i>
                                            <span>8.7% from yesterday</span>
                                        </p>
                                    </div>
                                    <div class="bg-secondary-100 p-3 rounded-full">
                                        <i class="fas fa-prescription text-secondary-600 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3">
                                <a href="#prescriptions" class="text-sm text-primary-600 font-medium hover:text-primary-800">View details</a>
                            </div>
                        </div>
    
                        <!-- Pending Orders Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-lg">
                            <div class="p-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-500 text-sm font-medium">Pending Orders</p>
                                        <h3 class="text-2xl font-bold text-gray-800">12</h3>
                                        <p class="text-yellow-600 text-sm mt-1 flex items-center">
                                            <i class="fas fa-arrow-down mr-1"></i>
                                            <span>3 orders awaiting approval</span>
                                        </p>
                                    </div>
                                    <div class="bg-yellow-100 p-3 rounded-full">
                                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3">
                                <a href="#pending-orders" class="text-sm text-primary-600 font-medium hover:text-primary-800">View details</a>
                            </div>
                        </div>
                    </div>
    
                    <!-- Charts and Graphs Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Inventory Consumption Chart -->
                        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">Monthly Inventory Consumption</h2>
                                <div class="flex items-center space-x-2">
                                    <select class="text-sm border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <option>Last 6 Months</option>
                                        <option>Last 12 Months</option>
                                        <option>YTD</option>
                                    </select>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="relative h-64">
                                <canvas id="consumptionChart"></canvas>
                            </div>
                        </div>
    
                        <!-- Inventory Categories Chart -->
                        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">Inventory by Category</h2>
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                            <div class="relative h-64">
                                <canvas id="categoriesChart"></canvas>
                            </div>
                        </div>
                    </div>
    
                    <!-- Recent Activities & Calendar Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Recent Activities -->
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">Recent Activities</h2>
                                <a href="#" class="text-sm text-primary-600 hover:text-primary-800">View all</a>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-md transition-colors">
                                    <div class="flex-shrink-0 bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-plus text-green-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">New stock received</p>
                                        <p class="text-sm text-gray-600">500 units of Paracetamol 500mg added to inventory</p>
                                        <p class="text-xs text-gray-400 mt-1">Today, 10:30 AM</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-1 rounded-full">Stock In</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-md transition-colors">
                                    <div class="flex-shrink-0 bg-blue-100 p-2 rounded-full">
                                        <i class="fas fa-prescription-bottle-alt text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Prescription filled</p>
                                        <p class="text-sm text-gray-600">Prescription #2345 for patient P-10042 filled</p>
                                        <p class="text-xs text-gray-400 mt-1">Today, 9:45 AM</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Outpatient</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-md transition-colors">
                                    <div class="flex-shrink-0 bg-yellow-100 p-2 rounded-full">
                                        <i class="fas fa-bell text-yellow-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Low stock alert</p>
                                        <p class="text-sm text-gray-600">Insulin Glargine 100units/ml is running low (5 units left)</p>
                                        <p class="text-xs text-gray-400 mt-1">Today, 8:15 AM</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text-xs font-medium bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Alert</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-md transition-colors">
                                    <div class="flex-shrink-0 bg-red-100 p-2 rounded-full">
                                        <i class="fas fa-trash-alt text-red-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">Expired medication disposed</p>
                                        <p class="text-sm text-gray-600">25 units of Amoxicillin 250mg capsules disposed</p>
                                        <p class="text-xs text-gray-400 mt-1">Yesterday, 4:30 PM</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text-xs font-medium bg-red-100 text-red-800 px-2 py-1 rounded-full">Disposal</span>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Calendar -->
                        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">Calendar</h2>
                                <div class="flex items-center space-x-2">
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <span class="text-sm font-medium">March 2025</span>
                                    <button class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center">
                                <div class="text-xs font-medium text-gray-500">Sun</div>
                                <div class="text-xs font-medium text-gray-500">Mon</div>
                                <div class="text-xs font-medium text-gray-500">Tue</div>
                                <div class="text-xs font-medium text-gray-500">Wed</div>
                                <div class="text-xs font-medium text-gray-500">Thu</div>
                                <div class="text-xs font-medium text-gray-500">Fri</div>
                                <div class="text-xs font-medium text-gray-500">Sat</div>
                                
                                <!-- Days from previous month -->
                                <div class="py-1 text-xs text-gray-400">23</div>
                                <div class="py-1 text-xs text-gray-400">24</div>
                                <div class="py-1 text-xs text-gray-400">25</div>
                                <div class="py-1 text-xs text-gray-400">26</div>
                                <div class="py-1 text-xs text-gray-400">27</div>
                                <div class="py-1 text-xs text-gray-400"><!-- Days from previous month -->
                                    <div class="py-1 text-xs text-gray-400">23</div>
                                    <div class="py-1 text-xs text-gray-400">24</div>
                                    <div class="py-1 text-xs text-gray-400">25</div>
                                    <div class="py-1 text-xs text-gray-400">26</div>
                                    <div class="py-1 text-xs text-gray-400">27</div>
                                    <div class="py-1 text-xs text-gray-400">28</div>
                                    <div class="py-1 text-xs text-gray-400">29</div>
                                    
                                    <!-- Days in current month -->
                                    <div class="py-1 text-xs">1</div>
                                    <div class="py-1 text-xs">2</div>
                                    <div class="py-1 text-xs">3</div>
                                    <div class="py-1 text-xs">4</div>
                                    <div class="py-1 text-xs">5</div>
                                    <div class="py-1 text-xs">6</div>
                                    <div class="py-1 text-xs">7</div>
                                    <div class="py-1 text-xs">8</div>
                                    <div class="py-1 text-xs">9</div>
                                    <div class="py-1 text-xs">10</div>
                                    <div class="py-1 text-xs">11</div>
                                    <div class="py-1 text-xs">12</div>
                                    <div class="py-1 text-xs">13</div>
                                    <div class="py-1 text-xs">14</div>
                                    <div class="py-1 text-xs">15</div>
                                    <div class="py-1 text-xs bg-primary-100 text-primary-700 rounded-full font-medium">16</div>
                                    <div class="py-1 text-xs relative">
                                        17
                                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-red-500 rounded-full"></div>
                                    </div>
                                    <div class="py-1 text-xs relative">
                                        18
                                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full"></div>
                                    </div>
                                    <div class="py-1 text-xs">19</div>
                                    <div class="py-1 text-xs">20</div>
                                    <div class="py-1 text-xs">21</div>
                                    <div class="py-1 text-xs relative">
                                        22
                                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-green-500 rounded-full"></div>
                                    </div>
                                    <div class="py-1 text-xs">23</div>
                                    <div class="py-1 text-xs">24</div>
                                    <div class="py-1 text-xs">25</div>
                                    <div class="py-1 text-xs">26</div>
                                    <div class="py-1 text-xs">27</div>
                                    <div class="py-1 text-xs">28</div>
                                    <div class="py-1 text-xs">29</div>
                                    <div class="py-1 text-xs">30</div>
                                    <div class="py-1 text-xs">31</div>
                                    
                                    <!-- Days from next month -->
                                    <div class="py-1 text-xs text-gray-400">1</div>
                                    <div class="py-1 text-xs text-gray-400">2</div>
                                    <div class="py-1 text-xs text-gray-400">3</div>
                                    <div class="py-1 text-xs text-gray-400">4</div>
                                    <div class="py-1 text-xs text-gray-400">5</div>
                                </div>
                                
                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600">Stock Delivery</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600">Inventory Audit</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600">Staff Training</span>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <!-- Inventory Table Section -->
                        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-800">Low Stock Medications</h2>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </span>
                                        <input type="text" placeholder="Filter medications..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm w-64">
                                    </div>
                                    <button class="flex items-center space-x-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors text-sm">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Create Order</span>
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Medication Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Category
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Current Stock
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Reorder Level
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Supplier
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Insulin Glargine 100units/ml</p>
                                                        <p class="text-xs text-gray-500">SKU: MED-INS-123</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">Endocrine</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">5 vials</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">20 vials</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Critical</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">Pharma Kenya Ltd</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <button class="text-primary-600 hover:text-primary-900 mx-1">Order</button>
                                                <button class="text-gray-600 hover:text-gray-900 mx-1">Details</button>
                                            </td>
                                        </tr>
                                        
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Amoxicillin 500mg capsules</p>
                                                        <p class="text-xs text-gray-500">SKU: MED-AMOX-500</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">Antibiotics</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">150 caps</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">500 caps</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Low</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">Quality Pharmaceuticals</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <button class="text-primary-600 hover:text-primary-900 mx-1">Order</button>
                                                <button class="text-gray-600 hover:text-gray-900 mx-1">Details</button>
                                            </td>
                                        </tr>
                                        
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Salbutamol Inhaler 100mcg</p>
                                                        <p class="text-xs text-gray-500">SKU: MED-SAL-100</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">Respiratory</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">12 units</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">30 units</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Low</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">GlaxoSmithKline</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <button class="text-primary-600 hover:text-primary-900 mx-1">Order</button>
                                                <button class="text-gray-600 hover:text-gray-900 mx-1">Details</button>
                                            </td>
                                        </tr>
                                        
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Metformin 500mg tablets</p>
                                                        <p class="text-xs text-gray-500">SKU: MED-MET-500</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">Endocrine</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">80 tabs</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">200 tabs</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Low</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">Sun Pharma</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <button class="text-primary-600 hover:text-primary-900 mx-1">Order</button>
                                                <button class="text-gray-600 hover:text-gray-900 mx-1">Details</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
 
