<?php
session_start();
include('db_connection.php');

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generate reset token
        $token = bin2hex(random_bytes(50)); // Secure random token
        $expiry = time() + 3600; // Token expiration time (1 hour)

        // Update database with the reset token and expiry
        $sql = "UPDATE users SET reset_token = '$token', reset_token_expiry = '$expiry' WHERE email = '$email'";
        $conn->query($sql);

        // Send password reset email
        $reset_link = "http://yourdomain.com/reset-password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $reset_link";
        mail($email, $subject, $message);

        $_SESSION['message'] = "A password reset link has been sent to your email address.";
        header('Location: forgot-password.php');
        exit();
    } else {
        $_SESSION['error'] = "No account found with that email address.";
        header('Location: forgot-password.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h1 class="text-3xl font-semibold text-center text-indigo-600 mb-6">Forgot Password</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="text-green-500 text-center mb-4"><?php echo $_SESSION['message']; ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="forgot-password.php">
            <div class="mb-4">
                <label for="email" class="block text-lg font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 focus:outline-none">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
