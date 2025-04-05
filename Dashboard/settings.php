<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
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
                $_SESSION["user_email"] = $email;
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
    $maintenance_mode = isset($_POST["maintenance_mode"]) ? 1 : 0;
    
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
        // Update system settings in the database
        $stmt = $conn->prepare("UPDATE system_settings SET 
                              site_name = ?, 
                              site_email = ?, 
                              items_per_page = ?, 
                              maintenance_mode = ? 
                              WHERE id = 1");
        $stmt->bind_param("ssii", $site_name, $site_email, $items_per_page, $maintenance_mode);
        
        if ($stmt->execute()) {
            $message = "System settings updated successfully";
            $message_type = "success";
        } else {
            $message = "Error updating system settings: " . $conn->error;
            $message_type = "error";
        }
        $stmt->close();
    }
}

// Get current user data
$user_result = $conn->query("SELECT name, email FROM users WHERE id = {$_SESSION['user_id']}");
$user_data = $user_result->fetch_assoc();

// Get system settings (for admin)
$system_settings = [
    'site_name' => 'KEFARM',
    'site_email' => 'info@kefarm.com',
    'items_per_page' => 10,
    'maintenance_mode' => false
];

if ($_SESSION["user_role"] === "Admin") {
    $settings_result = $conn->query("SELECT * FROM system_settings LIMIT 1");
    if ($settings_result->num_rows > 0) {
        $system_settings = $settings_result->fetch_assoc();
        $system_settings['maintenance_mode'] = (bool)$system_settings['maintenance_mode'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?= htmlspecialchars($system_settings['site_name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-color: #05480c;
            --secondary-color: #007bff;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #fd7e14;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 6px;
            --box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        .container {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        
        .message {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message .close-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: inherit;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border-left: 5px solid var(--success-color);
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 5px solid var(--danger-color);
        }
        
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 5px solid #17a2b8;
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
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 20px;
            transition: var(--transition);
        }
        
        .settings-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .settings-section h3 {
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            color: var(--primary-color);
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
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: var(--transition);
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: var(--secondary-color);
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
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        
        .strength-weak { color: var(--danger-color); }
        .strength-medium { color: var(--warning-color); }
        .strength-strong { color: var(--success-color); }
        
        .required:after {
            content: " *";
            color: var(--danger-color);
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
            background-color: var(--success-color);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        /* Recommended Content Section */
        .recommended-content {
            margin-top: 30px;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        
        .content-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 15px;
            transition: var(--transition);
        }
        
        .content-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .content-card h4 {
            margin-top: 0;
            color: var(--primary-color);
        }
        
        .content-card p {
            color: #666;
            font-size: 14px;
        }
        
        .content-card .btn {
            margin-top: 10px;
            padding: 8px 15px;
            font-size: 14px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .container {
                margin-left: 0;
            }
            
            .settings-section {
                min-width: 100%;
            }
            
            .content-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .form-actions {
                text-align: center;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2 style="color: var(--primary-color); margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-cog"></i> Account Settings
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
            <h3><i class="fas fa-user"></i> Profile Information</h3>
            <form method="POST" action="settings.php">
                <div class="form-group">
                    <label for="name" class="required">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email" class="required">Email Address</label>
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
            <h3><i class="fas fa-lock"></i> Password & Security</h3>
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
            <h3><i class="fas fa-server"></i> System Configuration</h3>
            <form method="POST" action="settings.php">
                <div class="form-group">
                    <label for="site_name" class="required">Application Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($system_settings['site_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="site_email" class="required">System Email</label>
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
                        <i class="fas fa-save"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Recommended Content for All Users -->
    <div class="recommended-content">
        <h3 style="color: var(--primary-color); display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-lightbulb"></i> Recommended Resources
        </h3>
        <div class="content-grid">
            <div class="content-card">
                <h4><i class="fas fa-book"></i> User Guide</h4>
                <p>Learn how to make the most of our platform with our comprehensive user guide.</p>
                <button class="btn btn-primary" onclick="window.open('user_guide.pdf', '_blank')">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
            
            <div class="content-card">
                <h4><i class="fas fa-video"></i> Tutorial Videos</h4>
                <p>Watch step-by-step tutorials to help you navigate the system effectively.</p>
                <button class="btn btn-primary" onclick="window.open('https://youtube.com/playlist?list=YOUR_PLAYLIST', '_blank')">
                    <i class="fas fa-play"></i> Watch Now
                </button>
            </div>
            
            <div class="content-card">
                <h4><i class="fas fa-question-circle"></i> FAQs</h4>
                <p>Find answers to common questions about using the platform.</p>
                <button class="btn btn-primary" onclick="window.location.href='faq.php'">
                    <i class="fas fa-search"></i> Browse FAQs
                </button>
            </div>
            
            <div class="content-card">
                <h4><i class="fas fa-headset"></i> Support</h4>
                <p>Need help? Contact our support team for assistance.</p>
                <button class="btn btn-primary" onclick="window.location.href='support.php'">
                    <i class="fas fa-envelope"></i> Contact Us
                </button>
            </div>
        </div>
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
        
        // Length check
        if (password.length >= 8) strength++;
        // Mixed case check
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        // Number check
        if (password.match(/[0-9]/)) strength++;
        // Special char check
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        if (password.length === 0) {
            strengthText.textContent = '-';
            strengthText.className = '';
        } else if (password.length < 8) {
            strengthText.textContent = 'Too short';
            strengthText.className = 'strength-weak';
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
            let valid = true;
            
            // Check all required fields
            this.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = 'var(--danger-color)';
                    valid = false;
                }
            });
            
            // Special password validation
            const newPass = this.querySelector('#new_password');
            if (newPass && newPass.value.length < 8) {
                newPass.style.borderColor = 'var(--danger-color)';
                alert('Password must be at least 8 characters long');
                valid = false;
            }
            
            // Confirm password match
            const confirmPass = this.querySelector('#confirm_password');
            if (newPass && confirmPass && newPass.value !== confirmPass.value) {
                confirmPass.style.borderColor = 'var(--danger-color)';
                alert('Passwords do not match');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = this.querySelector('[style*="border-color: var(--danger-color)"]');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
    
    // Reset field styles on focus
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('focus', function() {
            this.style.borderColor = '';
        });
    });
    
    // Auto-close messages after 5 seconds
    setTimeout(() => {
        const messages = document.querySelectorAll('.message');
        messages.forEach(msg => {
            msg.style.display = 'none';
        });
    }, 5000);
    
    // Add animation to cards on page load
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.settings-section, .content-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.1}s`;
        });
        
        // Add the animation to styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    });
</script>

</body>
</html>

<?php $conn->close(); ?>