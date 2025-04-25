<?php
session_start();
include('db_connection.php');

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch the user's data based on session information
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $email = $user['email'];
    $full_name = $user['full_name'] ?? 'Not set';
    $profile_pic = $user['profile_pic'] ?? 'default-avatar.jpg';
} else {
    die("User not found.");
}

// Handle updating the settings
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_full_name = $_POST['full_name'];

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } else {
        // Check if the new username already exists
        $check_username_sql = "SELECT * FROM users WHERE username = '$new_username' AND username != '$username'";
        $username_result = $conn->query($check_username_sql);

        if ($username_result->num_rows > 0) {
            $error_message = "Username already taken!";
        } else {
            // Handle profile picture upload
            if (!empty($_FILES['profile_pic']['name'])) {
                $target_dir = "uploads/profiles/";
                $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Check if image file is a actual image
                $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
                if ($check !== false) {
                    // Generate unique filename
                    $new_filename = uniqid() . '.' . $imageFileType;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                        $profile_pic = $new_filename;
                    } else {
                        $error_message = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $error_message = "File is not an image.";
                }
            }

            if (!isset($error_message)) {
                $update_sql = "UPDATE users SET username = '$new_username', email = '$new_email', full_name = '$new_full_name', profile_pic = '$profile_pic' WHERE username = '$username'";
                if ($conn->query($update_sql) === TRUE) {
                    $_SESSION['username'] = $new_username;
                    $_SESSION['email'] = $new_email;
                    $success_message = "Settings updated successfully!";
                    header("Location: settings.php");
                    exit();
                } else {
                    $error_message = "Error updating settings: " . $conn->error;
                }
            }
        }
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Get device information
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$device = 'Unknown';
$os_icons = [
    'Windows' => 'fab fa-windows',
    'Mac' => 'fab fa-apple',
    'Linux' => 'fab fa-linux',
    'Android' => 'fab fa-android',
    'iPhone' => 'fab fa-apple',
    'iPad' => 'fab fa-apple'
];

if (strpos($user_agent, 'Windows') !== false) {
    $device = 'Windows';
} elseif (strpos($user_agent, 'Mac') !== false) {
    $device = 'Mac';
} elseif (strpos($user_agent, 'Linux') !== false) {
    $device = 'Linux';
} elseif (strpos($user_agent, 'Android') !== false) {
    $device = 'Android';
} elseif (strpos($user_agent, 'iPhone') !== false) {
    $device = 'iPhone';
} elseif (strpos($user_agent, 'iPad') !== false) {
    $device = 'iPad';
}

$browser = 'Unknown';
$browser_icons = [
    'Chrome' => 'fab fa-chrome',
    'Firefox' => 'fab fa-firefox',
    'Safari' => 'fab fa-safari',
    'Edge' => 'fab fa-edge',
    'Opera' => 'fab fa-opera'
];

if (strpos($user_agent, 'Chrome') !== false) {
    $browser = 'Chrome';
} elseif (strpos($user_agent, 'Firefox') !== false) {
    $browser = 'Firefox';
} elseif (strpos($user_agent, 'Safari') !== false) {
    $browser = 'Safari';
} elseif (strpos($user_agent, 'Edge') !== false) {
    $browser = 'Edge';
} elseif (strpos($user_agent, 'Opera') !== false) {
    $browser = 'Opera';
}

// Calculate time spent
$time_spent = time() - $_SESSION['login_time'];
$hours = floor($time_spent / 3600);
$minutes = floor(($time_spent / 60) % 60);
$seconds = $time_spent % 60;
$formatted_time = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .profile-pic {
            transition: all 0.3s ease;
        }
        
        .profile-pic:hover {
            transform: scale(1.05);
        }
        
        .settings-card {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }
        
        .info-card {
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-indigo-700">Account Settings</h1>
            <p class="text-gray-600 mt-2">Manage your profile and preferences</p>
        </div>

        <div class="bg-white settings-card">
            <div class="md:flex">
                <!-- Left Side - Profile Picture -->
                <div class="md:w-1/3 p-8 bg-gradient-to-b from-indigo-50 to-blue-50 flex flex-col items-center">
                    <div class="relative mb-6">
                        <img src="uploads/profiles/<?php echo $profile_pic; ?>" alt="Profile Picture" class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-lg profile-pic">
                        <label for="profile-upload" class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full cursor-pointer hover:bg-indigo-700">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800"><?php echo htmlspecialchars($full_name); ?></h2>
                    <p class="text-indigo-600">@<?php echo htmlspecialchars($username); ?></p>
                    
                    <div class="mt-6 w-full">
                        <a href="?logout=true" class="block w-full py-2 px-4 bg-red-100 text-red-600 font-medium rounded-lg hover:bg-red-200 text-center transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>

                <!-- Right Side - Settings Form -->
                <div class="md:w-2/3 p-8">
                    <?php if (isset($success_message)): ?>
                        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="file" id="profile-upload" name="profile_pic" class="hidden" accept="image/*">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input type="text" name="username" id="username" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo htmlspecialchars($username); ?>" required>
                            </div>
                            
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" name="full_name" id="full_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo htmlspecialchars($full_name); ?>">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" name="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full py-3 px-6 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </form>
                    
                    <div class="mt-10 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Account Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg info-card">
                                <div class="flex items-center">
                                    <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-desktop text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Device</p>
                                        <p class="font-medium">
                                            <i class="<?php echo $os_icons[$device] ?? 'fas fa-question'; ?> mr-2"></i>
                                            <?php echo $device; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg info-card">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-globe text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Browser</p>
                                        <p class="font-medium">
                                            <i class="<?php echo $browser_icons[$browser] ?? 'fas fa-question'; ?> mr-2"></i>
                                            <?php echo $browser; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg info-card">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-sign-in-alt text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Login Time</p>
                                        <p class="font-medium"><?php echo date('M j, Y g:i A', $_SESSION['login_time']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg info-card">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-3 rounded-full mr-4">
                                        <i class="fas fa-clock text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Session Duration</p>
                                        <p class="font-medium"><?php echo $formatted_time; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview profile picture before upload
        document.getElementById('profile-upload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-pic').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>