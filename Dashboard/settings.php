<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Restrict access to Admin users only
if ($_SESSION["user_role"] !== "Admin") { // Ensure you check "user_role" and not "role"
    echo "Access Denied!";
    exit();
}

include '../db_connect.php'; // Ensure DB connection is included after session start

$current_page = 'settings.php'; // Define the current page
include 'includes/sidebar.php'; // Include the sidebar
include 'includes/header.php'; // Include Header

// Initialize message variables
$message = '';
$message_type = '';

// Handle Profile Update
if (isset($_POST["update_profile"])) {
    $name = $conn->real_escape_string(trim($_POST["name"]));
    $email = $conn->real_escape_string(trim($_POST["email"]));
    
    // Validate inputs
    if (empty($name) || empty($email)) {
        $message = "All fields are required";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $message_type = "error";
    } else {
        // Check if email exists for another user
        $check = $conn->query("SELECT id FROM users WHERE email='$email' AND id != {$_SESSION['user_id']}");
        if ($check->num_rows > 0) {
            $message = "Email already exists for another user";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $_SESSION["user_id"]);
            if ($stmt->execute()) {
                $message = "Profile updated successfully";
                $message_type = "success";
                // Update session name
                $_SESSION["user_name"] = $name;
            } else {
                $message = "Error updating profile: " . $conn->error;
                $message_type = "error";
            }
            $stmt->close();
        }
    }
}

// Handle Password Change
if (isset($_POST["change_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All password fields are required";
        $message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match";
        $message_type = "error";
    } elseif (strlen($new_password) < 8) {
        $message = "Password must be at least 8 characters";
        $message_type = "error";
    } else {
        // Verify current password
        $result = $conn->query("SELECT password FROM users WHERE id = {$_SESSION['user_id']}");
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($current_password, $user["password"])) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_password_hash, $_SESSION["user_id"]);
                if ($stmt->execute()) {
                    $message = "Password changed successfully";
                    $message_type = "success";
                } else {
                    $message = "Error changing password: " . $conn->error;
                    $message_type = "error";
                }
                $stmt->close();
            } else {
                $message = "Current password is incorrect";
                $message_type = "error";
            }
        }
    }
}

// Handle System Settings Update (for admin only)
if (isset($_POST["update_settings"]) && $_SESSION["user_role"] === "Admin") {
    $site_name = $conn->real_escape_string(trim($_POST["site_name"]));
    $site_email = $conn->real_escape_string(trim($_POST["site_email"]));
    $items_per_page = (int)$_POST["items_per_page"];
    
    // Validate inputs
    if (empty($site_name) || empty($site_email)) {
        $message = "All fields are required";
        $message_type = "error";
    } elseif (!filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $message_type = "error";
    } elseif ($items_per_page < 5 || $items_per_page > 100) {
        $message = "Items per page must be between 5 and 100";
        $message_type = "error";
    } else {
        // In a real application, you would save these to a settings table
        // For this example, we'll just show a success message
        $message = "System settings updated successfully";
        $message_type = "success";
    }
}

// Get current user data
$user_result = $conn->query("SELECT name, email FROM users WHERE id = {$_SESSION['user_id']}");
$user_data = $user_result->fetch_assoc();

// Default system settings (in a real app, these would come from a database)
$system_settings = [
    'site_name' => 'KEFARM',
    'site_email' => 'info@kefarm.com',
    'items_per_page' => 10,
    'maintenance_mode' => false
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - KEFARM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .message .close-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        .settings-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .settings-section {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        
        .settings-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .settings-section h3 {
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            color: #05480c;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 35px;
            cursor: pointer;
            color: #6c757d;
            background: none;
            border: none;
        }
        
        .form-actions {
            margin-top: 25px;
            text-align: right;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            margin-right: 10px;
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #fd7e14; }
        .strength-strong { color: #28a745; }
        
        .required:after {
            content: " *";
            color: #dc3545;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin-left: 10px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #28a745;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 15px;
            }
            
            .settings-section {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2 style="color: #05480c; margin-bottom: 25px;">
        <i class="fas fa-cog"></i> Settings
    </h2>
    
    <?php if ($message): ?>
        <div class="message <?= $message_type ?>">
            <?= $message ?>
            <button class="close-btn" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="settings-container">
        <!-- Profile Settings -->
        <div class="settings-section">
            <h3><i class="fas fa-user"></i> Profile Settings</h3>
            <form method="POST" action="settings.php">
                <div class="form-group">
                    <label for="name" class="required">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="update_profile">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Password Change -->
        <div class="settings-section">
            <h3><i class="fas fa-lock"></i> Change Password</h3>
            <form method="POST" action="settings.php">
                <div class="form-group">
                    <label for="current_password" class="required">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="form-group">
                    <label for="new_password" class="required">New Password</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8">
                    <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="password-strength" id="passwordStrength">
                        <span>Password strength:</span>
                        <span id="strengthText">-</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="required">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="change_password">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </div>
            </form>
        </div>
        
        <?php if ($_SESSION["user_role"] === "Admin"): ?>
        <!-- System Settings (Admin Only) -->
        <div class="settings-section">
            <h3><i class="fas fa-cog"></i> System Settings</h3>
            <form method="POST" action="settings.php">
                <div class="form-group">
                    <label for="site_name" class="required">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($system_settings['site_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="site_email" class="required">Site Email</label>
                    <input type="email" id="site_email" name="site_email" value="<?= htmlspecialchars($system_settings['site_email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="items_per_page" class="required">Items Per Page</label>
                    <input type="number" id="items_per_page" name="items_per_page" value="<?= htmlspecialchars($system_settings['items_per_page']) ?>" min="5" max="100" required>
                </div>
                <div class="form-group" style="display: flex; align-items: center;">
                    <label for="maintenance_mode">Maintenance Mode</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" <?= $system_settings['maintenance_mode'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" name="update_settings">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword(id) {
        const input = document.getElementById(id);
        const toggle = input.nextElementSibling;
        if (input.type === "password") {
            input.type = "text";
            toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = "password";
            toggle.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }

    // Password strength indicator
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const strengthText = document.getElementById('strengthText');
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        if (password.length === 0) {
            strengthText.textContent = '-';
            strengthText.className = '';
        } else if (strength <= 2) {
            strengthText.textContent = 'Weak';
            strengthText.className = 'strength-weak';
        } else if (strength === 3) {
            strengthText.textContent = 'Medium';
            strengthText.className = 'strength-medium';
        } else {
            strengthText.textContent = 'Strong';
            strengthText.className = 'strength-strong';
        }
    });
    
    // Form validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const passwordInputs = this.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                if (input.id === 'new_password' && input.value.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters long');
                    input.focus();
                }
            });
        });
    });
    
    // Auto-close messages after 5 seconds
    setTimeout(() => {
        const messages = document.querySelectorAll('.message');
        messages.forEach(msg => {
            msg.style.display = 'none';
        });
    }, 5000);
</script>

</body>
</html>

<?php $conn->close(); ?>