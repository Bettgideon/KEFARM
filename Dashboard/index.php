<?php
// Start session and enable error reporting
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
require_once 'db_connection.php';

// Verify connection is working
if (!isset($conn) || $conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("System maintenance in progress. Please try again later.");
}

// Include other files
include 'includes/sidebar.php';
include 'includes/header.php';
$isAdmin = ($_SESSION['user_role'] === 'Admin');

// Dashboard Data Class
class DashboardData {
    private $conn;
    private $userId;
    
    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
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
    
    public function getUserStats() {
        $stats = [];
        
        // Get orders count
        $stmt = $this->executeSafeQuery(
            "SELECT COUNT(*) as total FROM orders 
             WHERE user_id = ? AND order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [$this->userId], 'i'
        );
        $stats['orders'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        // Get total spending
        $stmt = $this->executeSafeQuery(
            "SELECT SUM(total_price) as total FROM orders 
             WHERE user_id = ? AND order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [$this->userId], 'i'
        );
        $stats['spending'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        // Get active crops
        $stmt = $this->executeSafeQuery(
            "SELECT COUNT(*) as total FROM crops 
             WHERE user_id = ? AND status = 'active'",
            [$this->userId], 'i'
        );
        $stats['active_crops'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        // Get upcoming tasks
        $stmt = $this->executeSafeQuery(
            "SELECT COUNT(*) as total FROM tasks 
             WHERE user_id = ? AND due_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)",
            [$this->userId], 'i'
        );
        $stats['upcoming_tasks'] = $stmt ? $stmt->get_result()->fetch_assoc()['total'] : 0;
        if ($stmt) $stmt->close();
        
        return $stats;
    }
    
    public function getRecentActivities($limit = 5) {
        $stmt = $this->executeSafeQuery(
            "SELECT activity_type, description, activity_date 
             FROM farm_activities 
             WHERE user_id = ? 
             ORDER BY activity_date DESC 
             LIMIT ?",
            [$this->userId, $limit], 'ii'
        );
        
        $activities = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];
        if ($stmt) $stmt->close();
        return $activities;
    }
    
    public function getChartData($months = 6) {
        $stmt = $this->executeSafeQuery(
            "SELECT 
                DATE_FORMAT(order_date, '%b') as month,
                COUNT(*) as orders,
                SUM(total_price) as spending
             FROM orders
             WHERE user_id = ?
             AND order_date >= DATE_SUB(NOW(), INTERVAL ? MONTH)
             GROUP BY DATE_FORMAT(order_date, '%Y-%m')
             ORDER BY order_date ASC",
            [$this->userId, $months], 'ii'
        );
        
        $chartData = $stmt ? $stmt->get_result()->fetch_all(MYSQLI_ASSOC) : [];
        if ($stmt) $stmt->close();
        return $chartData;
    }
}

// Initialize dashboard data
$dashboard = new DashboardData($conn, $_SESSION["user_id"]);
$stats = $dashboard->getUserStats();
$activities = $dashboard->getRecentActivities();
$chartData = $dashboard->getChartData();

// Prepare chart data
$chartLabels = $chartData ? array_column($chartData, 'month') : [];
$ordersData = $chartData ? array_column($chartData, 'orders') : [];
$spendingData = $chartData ? array_column($chartData, 'spending') : [];

// Format currency values
$stats['spending'] = isset($stats['spending']) && is_numeric($stats['spending']) ? 
    number_format($stats['spending'], 2) : '0.00';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KEFARM Dashboard - Manage your farming activities">
    <title>My Dashboard - KEFARM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #1565c0;
            --accent-color: #6a1b9a;
            --warning-color: #c62828;
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
        
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-color), #1b5e20);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--card-shadow);
            transition: all var(--transition-speed);
        }
        
        .welcome-text h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .welcome-text p {
            margin: 5px 0 0;
            opacity: 0.9;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
        }
        
        .quick-action-btn {
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color var(--transition-speed);
        }
        
        .quick-action-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
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
            border-left: 5px solid var(--primary-color);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
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
            background-color: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
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
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            position: relative;
        }
        
        .chart-actions {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        
        .chart-action-btn {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 14px;
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
        
        .recent-activities {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f5f5f5;
            transition: background-color 0.2s;
        }
        
        .activity-item:hover {
            background-color: #f9f9f9;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            background-color: rgba(21, 101, 192, 0.1);
            color: var(--secondary-color);
            flex-shrink: 0;
        }
        
        .activity-details {
            flex-grow: 1;
        }
        
        .activity-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .activity-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .activity-meta {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #888;
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
        
        /* Tooltips */
        .info-tooltip {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: help;
            margin-left: 5px;
        }
        
        [data-tooltip] {
            position: relative;
        }
        
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            white-space: nowrap;
            z-index: 100;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .welcome-banner {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .quick-actions {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            .cards {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                flex-wrap: wrap;
            }
            
            .chart-actions {
                position: static;
                justify-content: flex-end;
                margin-bottom: 15px;
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
            
            .welcome-text h1 {
                font-size: 20px;
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
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>
                <p>Here's what's happening with your farm today</p>
            </div>
            <div class="quick-actions">
                <button class="quick-action-btn" onclick="navigateTo('farm_management.php')">
                    <i class="fas fa-tractor"></i> Farm Management
                </button>
                <button class="quick-action-btn" onclick="navigateTo('orders_sales.php')">
                    <i class="fas fa-shopping-cart"></i> Place Order
                </button>
                <?php if ($isAdmin): ?>
                <button class="quick-action-btn" onclick="navigateTo('admin_panel.php')">
                    <i class="fas fa-cog"></i> Admin Panel
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="cards">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Recent Orders 
                            <button class="info-tooltip" data-tooltip="Number of orders placed in last 30 days">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </h3>
                        <div class="card-value"><?php echo $stats['orders']; ?></div>
                    </div>
                </div>
                <p class="card-footer">Last 30 days</p>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Total Spending</h3>
                        <div class="card-value">Ksh <?php echo $stats['spending']; ?></div>
                    </div>
                </div>
                <p class="card-footer">Last 30 days</p>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Active Crops</h3>
                        <div class="card-value"><?php echo $stats['active_crops']; ?></div>
                    </div>
                </div>
                <p class="card-footer">Currently growing</p>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Upcoming Tasks</h3>
                        <div class="card-value"><?php echo $stats['upcoming_tasks']; ?></div>
                    </div>
                </div>
                <p class="card-footer">For this week</p>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="chart-container">
            <h2 class="section-title"><i class="fas fa-chart-line"></i> My Activity</h2>
            <div class="chart-actions">
                <button class="chart-action-btn" onclick="downloadChart()">
                    <i class="fas fa-download"></i> Export
                </button>
                <button class="chart-action-btn" onclick="refreshChart()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            
            <?php if (empty($chartData)): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-pie"></i>
                    <h3>No chart data available</h3>
                    <p>Your activity data will appear here once available</p>
                </div>
            <?php else: ?>
                <canvas id="activityChart" height="300"></canvas>
            <?php endif; ?>
        </div>

        <!-- Recent Activities -->
        <div class="recent-activities">
            <h2 class="section-title"><i class="fas fa-history"></i> Recent Activities</h2>
            <?php if (empty($activities)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No recent activities found</h3>
                    <p>Your recent farm activities will appear here</p>
                </div>
            <?php else: ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-<?php 
                                switch($activity['activity_type']) {
                                    case 'planting': echo 'seedling'; break;
                                    case 'harvest': echo 'leaf'; break;
                                    case 'purchase': echo 'shopping-cart'; break;
                                    default: echo 'calendar-check';
                                }
                            ?>"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">
                                <?php echo ucfirst(htmlspecialchars($activity['activity_type'])); ?>
                            </div>
                            <div class="activity-description">
                                <?php echo htmlspecialchars($activity['description']); ?>
                            </div>
                            <div class="activity-meta">
                                <span><?php echo date('M j, Y', strtotime($activity['activity_date'])); ?></span>
                                <span><?php echo date('h:i A', strtotime($activity['activity_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="text-align: center; margin-top: 15px;">
                    <button class="quick-action-btn" onclick="navigateTo('activities.php')" 
                            style="background-color: var(--primary-color); color: white;">
                        View All Activities
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
    <script>
        // Global variables
        let activityChart;
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
            const background = type === 'error' ? '#c62828' : '#2e7d32';
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
        
        // Download chart as image
        function downloadChart() {
            if (!activityChart) {
                showToast('No chart available to download', 'error');
                return;
            }
            
            const link = document.createElement('a');
            link.download = 'kefarm-activity-chart.png';
            link.href = activityChart.toBase64Image();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Refresh chart data
        function refreshChart() {
            showLoading();
            
            fetch('api/get_chart_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({ months: 6 })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateChart(data.chartData);
                    showToast('Chart data refreshed successfully');
                } else {
                    throw new Error(data.message || 'Failed to refresh data');
                }
            })
            .catch(error => {
                console.error('Error refreshing chart:', error);
                showToast(error.message, 'error');
            })
            .finally(() => {
                hideLoading();
            });
        }
        
        // Update chart with new data
        function updateChart(data) {
            if (!activityChart) return;
            
            activityChart.data.labels = data.labels;
            activityChart.data.datasets[0].data = data.orders;
            activityChart.data.datasets[1].data = data.spending;
            activityChart.update();
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
            
            // Initialize chart if data exists
            <?php if (!empty($chartData)): ?>
                const activityCtx = document.getElementById('activityChart').getContext('2d');
                activityChart = new Chart(activityCtx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($chartLabels); ?>,
                        datasets: [{
                            label: 'Orders',
                            data: <?php echo json_encode($ordersData); ?>,
                            backgroundColor: 'rgba(46, 125, 50, 0.7)',
                            borderColor: 'rgba(46, 125, 50, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Spending (Ksh)',
                            data: <?php echo json_encode($spendingData); ?>,
                            backgroundColor: 'rgba(21, 101, 192, 0.7)',
                            borderColor: 'rgba(21, 101, 192, 1)',
                            borderWidth: 1,
                            type: 'line',
                            tension: 0.4,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 1) {
                                            label += 'Ksh ' + context.parsed.y.toLocaleString();
                                        } else {
                                            label += context.parsed.y;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Orders'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Total Spending (Ksh)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Ksh ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
            
            // Set up periodic refresh (every 5 minutes)
            setInterval(() => {
                refreshChart();
            }, 300000);
        });
        
        // Handle page unloading
        window.addEventListener('beforeunload', function() {
            showLoading();
        });
    </script>
</body>
</html>