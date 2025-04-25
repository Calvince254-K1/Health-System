<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    if ($_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename with extension
        $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('profile_') . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;

        // Validate image
        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        if ($check !== false) {
            // Resize and save image
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Delete old profile picture if exists
                if (isset($_SESSION['profile_picture']) && file_exists('uploads/' . $_SESSION['profile_picture'])) {
                    unlink('uploads/' . $_SESSION['profile_picture']);
                }
                
                $_SESSION['profile_picture'] = $file_name;
                $success_message = "Profile picture updated successfully!";
            } else {
                $error_message = "Error uploading file. Please try again.";
            }
        } else {
            $error_message = "File is not a valid image.";
        }
    } else {
        $error_message = "Error uploading file. Code: " . $_FILES['profile_picture']['error'];
    }
}

// Handle profile picture removal
if (isset($_POST['remove_picture'])) {
    if (isset($_SESSION['profile_picture'])) {
        $file_to_delete = 'uploads/' . $_SESSION['profile_picture'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
        unset($_SESSION['profile_picture']);
        $success_message = "Profile picture removed successfully!";
    } else {
        $error_message = "No profile picture to remove.";
    }
}

// Get profile picture path
$profile_picture = isset($_SESSION['profile_picture']) ? 'uploads/' . $_SESSION['profile_picture'] : 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=4f46e5&color=fff&size=200';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
        }
        
        .profile-container {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .profile-img:hover {
            transform: scale(1.05);
        }
        
        .file-input {
            opacity: 0;
            width: 0.1px;
            height: 0.1px;
            position: absolute;
        }
        
        .file-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-label:hover {
            background-color: #4f46e5;
        }
        
        .upload-section {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .upload-section:hover {
            border-color: #818cf8;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="py-12 px-4">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
            <p class="text-gray-600 mt-2">Manage your account settings</p>
        </div>
        
        <div class="bg-white profile-container">
            <!-- Profile Header -->
            <div class="bg-indigo-600 py-6 text-center">
                <div class="relative inline-block">
                    <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-img mx-auto">
                    <label for="profile_picture" class="absolute bottom-2 right-2 bg-white p-2 rounded-full shadow-md cursor-pointer hover:bg-gray-100">
                        <i class="fas fa-camera text-indigo-600"></i>
                    </label>
                </div>
                <h2 class="text-xl font-semibold text-white mt-4"><?php echo htmlspecialchars($username); ?></h2>
            </div>
            
            <!-- Main Content -->
            <div class="p-6">
                <!-- Success/Error Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php elseif (isset($error_message)): ?>
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Upload Form -->
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_picture" id="profile_picture" class="file-input" accept="image/*">
                    
                    <div class="upload-section p-8 mb-4 text-center">
                        <label for="profile_picture" class="file-label">
                            <i class="fas fa-cloud-upload-alt text-indigo-500 text-2xl"></i>
                            <span class="text-indigo-600 font-medium">Click to upload a new profile picture</span>
                        </label>
                        <p class="text-gray-500 text-sm mt-2">JPG, PNG or GIF (Max 2MB)</p>
                    </div>
                    
                    <button type="submit" class="w-full py-3 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </form>
                
                <!-- Remove Picture Button (if picture exists) -->
                <?php if (isset($_SESSION['profile_picture'])): ?>
                    <form action="profile.php" method="POST" class="mt-4">
                        <button type="submit" name="remove_picture" class="w-full py-3 px-4 bg-red-100 text-red-600 font-medium rounded-lg hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <i class="fas fa-trash-alt mr-2"></i> Remove Profile Picture
                        </button>
                    </form>
                <?php endif; ?>
                
                <!-- Account Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Account Information</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Username:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($username); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Member since:</span>
                            <span class="font-medium"><?php echo date('M Y', $_SESSION['login_time'] ?? time()); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last login:</span>
                            <span class="font-medium"><?php echo date('M j, Y g:i A', $_SESSION['login_time'] ?? time()); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <div class="mt-8 text-center">
                    <a href="logout.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview image before upload
        document.getElementById('profile_picture').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>