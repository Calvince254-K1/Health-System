<?php
include('db_connection.php');

// Function to check if password is more than 8 characters
function is_valid_password($password) {
    return strlen($password) >= 8;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validate password length
    if (!is_valid_password($password)) {
        $error_message = "Password must be at least 8 characters long!";
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
        
        if ($conn->query($sql) === TRUE) {
            // Redirect to login page after successful signup
            header('Location: login.php');
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include Typed.js -->
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <style>
        /* Dark mode styles */
        body.dark {
            background-color: #1f2937;
            color: #e5e7eb;
        }
        .dark .bg-white {
            background-color: #374151;
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 1s ease-out;
        }

        /* Button hover effect */
        .button-hover:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
        }

        /* Placeholder fade-out effect */
        input::placeholder {
            transition: opacity 0.5s ease-in-out, transform 0.3s ease-in-out;
            opacity: 1;
            color: transparent; /* Make the placeholder text color transparent initially */
            background: linear-gradient(90deg, #6ee7b7, #3b82f6); /* Gradient color */
            -webkit-background-clip: text; /* Use the gradient as text color */
        }

        input:focus::placeholder {
            opacity: 0;
            transform: translateX(10px);
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center transition-all">

    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg max-w-sm w-full fade-in">
        <h1 class="text-2xl font-semibold mb-4 text-center text-indigo-600 dark:text-indigo-400">Create a New Account</h1>

        <?php if (isset($error_message)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
            <input type="text" name="username" id="username" placeholder="Enter your username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 transition duration-300 ease-in-out" required>

            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 transition duration-300 ease-in-out" required>

            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 transition duration-300 ease-in-out" required>

            <button type="submit" class="w-full mt-4 py-2 px-4 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 button-hover mb-4">Sign Up</button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">Already have an account? <a href="login.php" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-600">Login here</a></p>

        <!-- Dark Mode Toggle -->
        <button onclick="toggleDarkMode()" class="mt-4 text-sm text-gray-500 dark:text-gray-300 w-full py-2 text-center hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">Toggle Dark Mode</button>
    </div>

    <script>
        // Function to toggle dark mode
        function toggleDarkMode() {
            document.body.classList.toggle('dark');
        }

        // Typed.js configuration for placeholder typing effect
        document.addEventListener("DOMContentLoaded", function () {
            var options = {
                strings: ["Enter your username", "Enter your password", "Enter your email"], // Placeholder texts
                typeSpeed: 60,  // Speed at which each character is typed
                backSpeed: 30,  // Speed at which characters are deleted
                backDelay: 500,  // Delay before backspacing
                startDelay: 100, // Delay before typing starts
                showCursor: false, // Hide cursor
                fadeOut: true,  // Fade out when the text is finished typing
            };

            var typed = new Typed("input::placeholder", options);  // Apply Typed.js to placeholder
        });
    </script>
</body>
</html>
