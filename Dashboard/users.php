<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "Admin") {
    header("Location: ../login.html");
    exit();
}

include '../db_connect.php';
$current_page = 'users.php';
include 'includes/sidebar.php';
include 'includes/header.php';

// Initialize message variables
$message = '';
$message_type = '';

// Handle Add New User
if (isset($_POST["add_user"])) {
    $name = $conn->real_escape_string(trim($_POST["name"]));
    $email = $conn->real_escape_string(trim($_POST["email"]));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($_POST["role"]);
    $status = $conn->real_escape_string($_POST["status"]);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($_POST["password"])) {
        $message = "All fields are required";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $message_type = "error";
    } elseif (strlen($_POST["password"]) < 8) {
        $message = "Password must be at least 8 characters";
        $message_type = "error";
    } else {
        // Check if email exists
        $check = $conn->query("SELECT id FROM users WHERE email='$email'");
        if ($check->num_rows > 0) {
            $message = "Email already exists";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $password, $role, $status);
            if ($stmt->execute()) {
                $message = "User added successfully";
                $message_type = "success";
                // Clear form fields
                $_POST["name"] = $_POST["email"] = $_POST["password"] = '';
            } else {
                $message = "Error adding user: " . $conn->error;
                $message_type = "error";
            }
            $stmt->close();
        }
    }
}

// Handle Role Update
if (isset($_POST["update_role"])) {
    $user_id = (int)$_POST["user_id"];
    $role = $conn->real_escape_string($_POST["role"]);
    
    // Prevent changing your own role
    if ($user_id == $_SESSION["user_id"]) {
        $message = "You cannot change your own role";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $user_id);
        if ($stmt->execute()) {
            $message = "Role updated successfully";
            $message_type = "success";
        } else {
            $message = "Error updating role: " . $conn->error;
            $message_type = "error";
        }
        $stmt->close();
    }
}

// Handle Status Update
if (isset($_POST["update_status"])) {
    $user_id = (int)$_POST["user_id"];
    $status = $conn->real_escape_string($_POST["status"]);
    
    // Prevent deactivating yourself
    if ($user_id == $_SESSION["user_id"] && $status == 'Inactive') {
        $message = "You cannot deactivate your own account";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $user_id);
        if ($stmt->execute()) {
            $message = "Status updated successfully";
            $message_type = "success";
        } else {
            $message = "Error updating status: " . $conn->error;
            $message_type = "error";
        }
        $stmt->close();
    }
}

// Handle User Deletion
if (isset($_POST["delete_user"])) {
    $user_id = (int)$_POST["user_id"];
    
    // Prevent deleting yourself
    if ($user_id == $_SESSION["user_id"]) {
        $message = "You cannot delete your own account";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "User deleted successfully";
            $message_type = "success";
        } else {
            $message = "Error deleting user: " . $conn->error;
            $message_type = "error";
        }
        $stmt->close();
    }
}

// Handle Bulk Actions
if (isset($_POST["bulk_action"])) {
    if (!empty($_POST["selected_users"])) {
        $user_ids = implode(",", array_map('intval', $_POST["selected_users"]));
        
        switch ($_POST["bulk_action"]) {
            case 'activate':
                $conn->query("UPDATE users SET status='Active' WHERE id IN ($user_ids)");
                $message = "Selected users activated";
                $message_type = "success";
                break;
            case 'deactivate':
                // Exclude current user from deactivation
                $conn->query("UPDATE users SET status='Inactive' WHERE id IN ($user_ids) AND id != {$_SESSION['user_id']}");
                $message = "Selected users deactivated";
                $message_type = "success";
                break;
            case 'delete':
                // Exclude current user from deletion
                $conn->query("DELETE FROM users WHERE id IN ($user_ids) AND id != {$_SESSION['user_id']}");
                $message = "Selected users deleted";
                $message_type = "success";
                break;
        }
    }
}

// Fetch all users with pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = $search ? "WHERE name LIKE '%$search%' OR email LIKE '%$search%'" : '';

// Get total users for pagination
$total_result = $conn->query("SELECT COUNT(*) as total FROM users $search_condition");
$total_users = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $per_page);

// Get users for current page
$result = $conn->query("SELECT id, name, email, role, status, created_at FROM users $search_condition ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - KEFARM</title>
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
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .user-management-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
        }
        
        .search-box input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .search-box button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color:rgb(5, 72, 12);
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        select, button {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .add-user-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .bulk-actions {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .pagination a:hover {
            background-color: #f1f1f1;
        }
        
        .pagination .active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-actions {
            margin-top: 20px;
            text-align: right;
        }
        
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            margin-right: 10px;
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .strength-weak { color: red; }
        .strength-medium { color: orange; }
        .strength-strong { color: green; }

        /* Required field indicator */
        .required:after {
            content: " *";
            color: red;
        }
        
        @media (max-width: 768px) {
            .container {
                margin-left: 0;
            }
            
            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="user-management-header">
        <h2>User Management</h2>
        <div class="search-box">
            <form method="GET" action="users.php">
                <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
                <?php if ($search): ?>
                    <a href="users.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <?php if ($message): ?>
        <div class="message <?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>
    
    
    <form method="POST" action="users.php">
        <div class="bulk-actions">
            <select name="bulk_action">
                <option value="">Bulk Actions</option>
                <option value="activate">Activate</option>
                <option value="deactivate">Deactivate</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="btn btn-primary">Apply</button>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_users[]" value="<?= $row['id'] ?>"></td>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <form method="POST" action="users.php">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="Admin" <?= ($row['role'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                                    <option value="Farmer" <?= ($row['role'] === 'Farmer') ? 'selected' : '' ?>>Farmer</option>
                                    <option value="Staff" <?= ($row['role'] === 'Staff') ? 'selected' : '' ?>>Staff</option>
                                </select>
                                <input type="hidden" name="update_role" value="1">
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="users.php">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="Active" <?= ($row['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= ($row['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <form method="POST" action="users.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete_user" class="delete-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1&search=<?= urlencode($search) ?>">First</a>
                    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Prev</a>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" <?= ($i == $page) ? 'class="active"' : '' ?>>
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a>
                    <a href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>">Last</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <h3>Add New User</h3>
        <form method="POST" action="users.php" id="addUserForm">
            <div class="form-group">
                <label for="name" class="required">Full Name</label>
                <input type="text" id="name" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="password" class="required">Password</label>
                <input type="password" id="password" name="password" required minlength="8">
                <div class="password-strength" id="passwordStrength"></div>
            </div>
            <div class="form-group">
                <label for="role" class="required">Role</label>
                <select id="role" name="role" required>
                    <option value="Admin" <?= (isset($_POST['role']) && $_POST['role'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="Farmer" <?= (isset($_POST['role']) && $_POST['role'] === 'Farmer') ? 'selected' : '' ?>>Farmer</option>
                    <option value="Staff" <?= (!isset($_POST['role']) || (isset($_POST['role']) && $_POST['role'] === 'Staff')) ? 'selected' : '' ?>>Staff</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status" class="required">Status</label>
                <select id="status" name="status" required>
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal functions
    function openAddUserModal() {
        document.getElementById('addUserModal').style.display = 'block';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }
    
    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthText = document.getElementById('passwordStrength');
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        if (password.length === 0) {
            strengthText.textContent = '';
            strengthText.className = 'password-strength';
        } else if (strength <= 2) {
            strengthText.textContent = 'Weak password';
            strengthText.className = 'password-strength strength-weak';
        } else if (strength === 3) {
            strengthText.textContent = 'Medium strength password';
            strengthText.className = 'password-strength strength-medium';
        } else {
            strengthText.textContent = 'Strong password';
            strengthText.className = 'password-strength strength-strong';
        }
    });
    
    // Form validation
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long');
        }
    });
</script>

</body>
</html>

<?php $conn->close(); ?>