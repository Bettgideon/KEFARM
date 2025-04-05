<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"]) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: " . ROOT_PATH . "/login.html");
    exit();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection - using absolute path
require_once 'db_connection.php';

// Verify connection is working
if (!isset($conn) || $conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("System maintenance in progress. Please try again later.");
}

// Include other files
include 'includes/sidebar.php';
include 'includes/header.php';


// Admin Data Class
class AdminData {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    private function executeSafeQuery($sql, $params = [], $types = '') {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
        
        return $stmt;
    }
    
    public function getSystemStats() {
        $stats = [];
        
        // Total users
        $stmt = $this->executeSafeQuery("SELECT COUNT(*) as total FROM users");
        $stats['total_users'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        // Active users (logged in last 30 days)
        $stmt = $this->executeSafeQuery(
            "SELECT COUNT(*) as total FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        $stats['active_users'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        // Total orders
        $stmt = $this->executeSafeQuery("SELECT COUNT(*) as total FROM orders");
        $stats['total_orders'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        // Revenue (last 30 days)
        $stmt = $this->executeSafeQuery(
            "SELECT SUM(total_price) as total FROM orders WHERE order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        $stats['recent_revenue'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        // Pending approvals
        $stmt = $this->executeSafeQuery(
            "SELECT COUNT(*) as total FROM approvals WHERE status = 'pending'"
        );
        $stats['pending_approvals'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        return $stats;
    }
    
    public function getRecentUsers($limit = 5) {
        $stmt = $this->executeSafeQuery(
            "SELECT user_id, username, email, registration_date, last_login 
             FROM users 
             ORDER BY registration_date DESC 
             LIMIT ?",
            [$limit], 'i'
        );
        
        $users = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];
        if ($stmt) $stmt->close();
        return $users;
    }
    
    public function getRecentOrders($limit = 5) {
        $stmt = $this->executeSafeQuery(
            "SELECT o.order_id, u.username, o.total_price, o.order_date, o.status 
             FROM orders o
             JOIN users u ON o.user_id = u.user_id
             ORDER BY o.order_date DESC 
             LIMIT ?",
            [$limit], 'i'
        );
        
        $orders = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];
        if ($stmt) $stmt->close();
        return $orders;
    }
    
    public function getSystemHealth() {
        $health = [];
        
        // Database status
        $health['database'] = $this->conn->ping() ? 'healthy' : 'unhealthy';
        
        // Storage space (simulated)
        $health['storage'] = [
            'used' => '75%',
            'status' => 'warning'
        ];
        
        // Recent errors
        $stmt = $this->executeSafeQuery(
            "SELECT COUNT(*) as total FROM error_log 
             WHERE error_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
        $error_count = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        $health['errors'] = [
            'count' => $error_count,
            'status' => $error_count > 10 ? 'critical' : ($error_count > 0 ? 'warning' : 'healthy')
        ];
        
        if ($stmt) $stmt->close();
        return $health;
    }
}

// Initialize admin data
$admin = new AdminData($conn);
$stats = $admin->getSystemStats();
$recentUsers = $admin->getRecentUsers();
$recentOrders = $admin->getRecentOrders();
$systemHealth = $admin->getSystemHealth();

// Format currency values
$stats['recent_revenue'] = isset($stats['recent_revenue']) && is_numeric($stats['recent_revenue']) ? 
    number_format($stats['recent_revenue'], 2) : '0.00';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KEFARM Admin Panel - System Administration">
    <title>Admin Panel - KEFARM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #1565c0;
            --accent-color: #6a1b9a;
            --warning-color: #ff9800;
            --danger-color: #c62828;
            --success-color: #2e7d32;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --card-hover-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            --transition-speed: 0.3s;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 25px;
            transition: margin-left var(--transition-speed);
            background-color: var(--light-bg);
            min-height: 100vh;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .admin-title {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .admin-actions {
            display: flex;
            gap: 15px;
        }
        
        .admin-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color var(--transition-speed);
        }
        
        .admin-btn:hover {
            background-color: #1b5e20;
        }
        
        .admin-btn.secondary {
            background-color: var(--secondary-color);
        }
        
        .admin-btn.secondary:hover {
            background-color: #0d47a1;
        }
        
        .admin-btn.warning {
            background-color: var(--warning-color);
        }
        
        .admin-btn.warning:hover {
            background-color: #e65100;
        }
        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-speed) ease, 
                        box-shadow var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }
        
        .card.primary {
            border-left: 5px solid var(--primary-color);
        }
        
        .card.secondary {
            border-left: 5px solid var(--secondary-color);
        }
        
        .card.warning {
            border-left: 5px solid var(--warning-color);
        }
        
        .card.danger {
            border-left: 5px solid var(--danger-color);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .card.primary .card-icon {
            background-color: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
        }
        
        .card.secondary .card-icon {
            background-color: rgba(21, 101, 192, 0.1);
            color: var(--secondary-color);
        }
        
        .card.warning .card-icon {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }
        
        .card.danger .card-icon {
            background-color: rgba(198, 40, 40, 0.1);
            color: var(--danger-color);
        }
        
        .card-title {
            font-size: 16px;
            color: #666;
            margin: 0;
        }
        
        .card-value {
            font-size: 28px;
            font-weight: bold;
            margin: 5px 0;
            color: #333;
        }
        
        .card-footer {
            font-size: 14px;
            color: #888;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-healthy {
            background-color: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
        }
        
        .status-warning {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }
        
        .status-critical {
            background-color: rgba(198, 40, 40, 0.1);
            color: var(--danger-color);
        }
        
        .data-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1200px) {
            .data-section {
                grid-template-columns: 1fr;
            }
        }
        
        .data-table {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }
        
        .section-title {
            margin-top: 0;
            color: #333;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            
        }
        
        th {
            font-weight: 600;
            color: white;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .table-actions {
            display: flex;
            gap: 8px;
        }
        
        .table-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--secondary-color);
            font-size: 14px;
        }
        
        .table-btn.warning {
            color: var(--warning-color);
        }
        
        .table-btn.danger {
            color: var(--danger-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        /* Loading overlay */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* System health */
        .health-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .health-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        
        .dot-healthy {
            background-color: var(--primary-color);
        }
        
        .dot-warning {
            background-color: var(--warning-color);
        }
        
        .dot-critical {
            background-color: var(--danger-color);
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .admin-actions {
                width: 100%;
                flex-wrap: wrap;
            }
        }
        
        @media (max-width: 768px) {
            .cards {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 576px) {
            .card {
                padding: 15px;
            }
            
            .card-icon {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
            
            .card-value {
                font-size: 24px;
            }
            
            .admin-title {
                font-size: 20px;
            }
            
            .data-section {
                gap: 15px;
            }
            
            .data-table {
                padding: 15px;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1 class="admin-title">Admin Dashboard</h1>
            <div class="admin-actions">
                <button class="admin-btn" onclick="navigateTo('users.php')">
                    <i class="fas fa-users-cog"></i> Manage Users
                </button>
                <button class="admin-btn secondary" onclick="navigateTo('system_settings.php')">
                    <i class="fas fa-cogs"></i> System Settings
                </button>
                <button class="admin-btn warning" onclick="showBackupModal()">
                    <i class="fas fa-database"></i> Backup System
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="cards">
            <div class="card primary">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Total Users</h3>
                        <div class="card-value"><?php echo $stats['total_users']; ?></div>
                    </div>
                </div>
                <div class="card-footer">
                    <span><?php echo $stats['active_users']; ?> active</span>
                    <span class="health-indicator">
                        <span class="health-dot dot-healthy"></span>
                        <span>Healthy</span>
                    </span>
                </div>
            </div>
            
            <div class="card secondary">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Total Orders</h3>
                        <div class="card-value"><?php echo $stats['total_orders']; ?></div>
                    </div>
                </div>
                <div class="card-footer">
                    <span>Ksh <?php echo $stats['recent_revenue']; ?> revenue</span>
                    <span class="health-indicator">
                        <span class="health-dot dot-healthy"></span>
                        <span>Healthy</span>
                    </span>
                </div>
            </div>
            
            <div class="card warning">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Pending Approvals</h3>
                        <div class="card-value"><?php echo $stats['pending_approvals']; ?></div>
                    </div>
                </div>
                <div class="card-footer">
                    <span>Requires attention</span>
                    <button class="table-btn" onclick="navigateTo('approvals.php')">
                        <i class="fas fa-eye"></i> Review
                    </button>
                </div>
            </div>
            
            <div class="card danger">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <h3 class="card-title">System Health</h3>
                        <div class="card-value">
                            <?php echo ucfirst($systemHealth['errors']['status']); ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span><?php echo $systemHealth['errors']['count']; ?> errors today</span>
                    <span class="status-badge status-<?php echo $systemHealth['errors']['status']; ?>">
                        <?php echo $systemHealth['errors']['status']; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Data Tables Section -->
        <div class="data-section">
            <div class="data-table">
                <h2 class="section-title"><i class="fas fa-users"></i> Recent Users</h2>
                <?php if (empty($recentUsers)): ?>
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <h3>No users found</h3>
                        <p>New user registrations will appear here</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($user['registration_date'])); ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="table-btn" 
                                                    onclick="viewUser(<?php echo $user['user_id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button class="table-btn warning" 
                                                    onclick="editUser(<?php echo $user['user_id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <button class="admin-btn secondary" onclick="navigateTo('user_management.php')">
                            <i class="fas fa-users"></i> View All Users
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="data-table">
                <h2 class="section-title"><i class="fas fa-shopping-cart"></i> Recent Orders</h2>
                <?php if (empty($recentOrders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-cart-arrow-down"></i>
                        <h3>No orders found</h3>
                        <p>New orders will appear here</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td>Ksh <?php echo number_format($order['total_price'], 2); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <span class="status-badge 
                                            <?php echo $order['status'] === 'completed' ? 'status-healthy' : 
                                                  ($order['status'] === 'processing' ? 'status-warning' : 'status-critical'); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="table-btn" 
                                                    onclick="viewOrder(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <?php if ($order['status'] !== 'completed'): ?>
                                            <button class="table-btn warning" 
                                                    onclick="updateOrderStatus(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <button class="admin-btn secondary" onclick="navigateTo('order_management.php')">
                            <i class="fas fa-list-alt"></i> View All Orders
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- System Health Section -->
        <div class="data-table">
            <h2 class="section-title"><i class="fas fa-heartbeat"></i> System Health</h2>
            <table>
                <thead>
                    <tr>
                        <th>Component</th>
                        <th>Status</th>
                        <th>Details</th>
                        <th>Last Checked</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Database</td>
                        <td>
                            <span class="health-indicator">
                                <span class="health-dot dot-healthy"></span>
                                <span>Healthy</span>
                            </span>
                        </td>
                        <td>Connection stable</td>
                        <td><?php echo date('M j, Y H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td>Storage</td>
                        <td>
                            <span class="health-indicator">
                                <span class="health-dot dot-warning"></span>
                                <span>Warning</span>
                            </span>
                        </td>
                        <td><?php echo $systemHealth['storage']['used']; ?> used</td>
                        <td><?php echo date('M j, Y H:i:s'); ?></td>
                    </tr>
                    <tr>
                        <td>Error Log</td>
                        <td>
                            <span class="health-indicator">
                                <span class="health-dot <?php 
                                    echo $systemHealth['errors']['status'] === 'healthy' ? 'dot-healthy' : 
                                         ($systemHealth['errors']['status'] === 'warning' ? 'dot-warning' : 'dot-critical'); 
                                ?>"></span>
                                <span><?php echo ucfirst($systemHealth['errors']['status']); ?></span>
                            </span>
                        </td>
                        <td><?php echo $systemHealth['errors']['count']; ?> errors in last 24 hours</td>
                        <td><?php echo date('M j, Y H:i:s'); ?></td>
                    </tr>
                </tbody>
            </table>
            <div style="text-align: center; margin-top: 15px;">
                <button class="admin-btn" onclick="runSystemDiagnostics()">
                    <i class="fas fa-search"></i> Run Diagnostics
                </button>
                <button class="admin-btn warning" onclick="viewErrorLog()">
                    <i class="fas fa-bug"></i> View Error Log
                </button>
            </div>
        </div>
    </div>

    <!-- Backup Modal (hidden by default) -->
    <div id="backup-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 25px; border-radius: 10px; width: 500px; max-width: 90%;">
            <h2 style="margin-top: 0;"><i class="fas fa-database"></i> System Backup</h2>
            <p>Select backup options:</p>
            
            <div style="margin: 20px 0;">
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" id="backup-db" checked> Database
                </label>
                <label style="display: block; margin-bottom: 10px;">
                    <input type="checkbox" id="backup-files"> File System
                </label>
                <label style="display: block; margin-bottom: 15px;">
                    <input type="checkbox" id="backup-encrypt"> Encrypt Backup
                </label>
                
                <div style="margin-top: 15px;">
                    <label>Backup Name:</label>
                    <input type="text" id="backup-name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" 
                           value="kefarm-backup-<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button onclick="document.getElementById('backup-modal').style.display = 'none'" 
                        style="padding: 8px 15px; background: #ddd; border: none; border-radius: 4px; cursor: pointer;">
                    Cancel
                </button>
                <button onclick="startBackup()" 
                        style="padding: 8px 15px; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-play"></i> Start Backup
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
    <script>
        // Global variables
        const csrfToken = "<?php echo $_SESSION['csrf_token']; ?>";
        
        // Navigation function with loading indicator
        function navigateTo(url) {
            showLoading();
            window.location.href = url;
        }
        
        // Show loading overlay
        function showLoading() {
            document.getElementById('loading-overlay').style.display = 'flex';
        }
        
        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loading-overlay').style.display = 'none';
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            const background = type === 'error' ? '#c62828' : 
                             type === 'warning' ? '#ff9800' : '#2e7d32';
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: background,
                stopOnFocus: true,
            }).showToast();
        }
        
        // Show backup modal
        function showBackupModal() {
            document.getElementById('backup-modal').style.display = 'flex';
        }
        
        // Start backup process
        function startBackup() {
            const backupName = document.getElementById('backup-name').value;
            const backupDB = document.getElementById('backup-db').checked;
            const backupFiles = document.getElementById('backup-files').checked;
            const backupEncrypt = document.getElementById('backup-encrypt').checked;
            
            if (!backupDB && !backupFiles) {
                showToast('Please select at least one backup option', 'warning');
                return;
            }
            
            showLoading();
            document.getElementById('backup-modal').style.display = 'none';
            
            // Simulate backup process (in real implementation, this would be an AJAX call)
            setTimeout(() => {
                hideLoading();
                showToast('Backup completed successfully and saved as ' + backupName);
            }, 3000);
        }
        
        // View user details
        function viewUser(userId) {
            showLoading();
            window.location.href = `user_details.php?id=${userId}`;
        }
        
        // Edit user
        function editUser(userId) {
            showLoading();
            window.location.href = `edit_user.php?id=${userId}`;
        }
        
        // View order details
        function viewOrder(orderId) {
            showLoading();
            window.location.href = `order_details.php?id=${orderId}`;
        }
        
        // Update order status
        function updateOrderStatus(orderId) {
            showLoading();
            window.location.href = `update_order.php?id=${orderId}`;
        }
        
        // Run system diagnostics
        function runSystemDiagnostics() {
            showLoading();
            
            fetch('api/run_diagnostics.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Diagnostics completed. ' + data.message);
                } else {
                    showToast(data.message || 'Diagnostics failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to run diagnostics', 'error');
            })
            .finally(() => {
                hideLoading();
            });
        }
        
        // View error log
        function viewErrorLog() {
            showLoading();
            window.location.href = 'error_log.php';
        }
        
        // Initialize the dashboard when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cards animation
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            });
            
            // Set up periodic refresh (every 5 minutes)
            setInterval(() => {
                // In a real implementation, this would refresh the data
                console.log('Auto-refreshing admin data...');
            }, 300000);
        });
        
        // Handle page unloading
        window.addEventListener('beforeunload', function() {
            showLoading();
        });
    </script>
</body>
</html>