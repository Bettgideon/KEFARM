<?php
// Start session and enable error reporting
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Database connection
require_once 'db_connection.php';

// Verify connection is working
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Include other files
include 'includes/sidebar.php';
include 'includes/header.php';
$isAdmin = ($_SESSION['user_role'] === 'Admin');

// Fetch user-specific dashboard data
$user_id = $_SESSION["user_id"];
$stats = [];
$activities = [];
$chartData = [];

// Function to safely execute queries
function executeQuery($conn, $sql) {
    $result = $conn->query($sql);
    if ($result === false) {
        error_log("Query failed: " . $conn->error . " | Query: " . $sql);
        return false;
    }
    return $result;
}

// Get user's orders count (last 30 days)
$result = executeQuery($conn, "SELECT COUNT(*) as total FROM orders 
                     WHERE user_id = $user_id 
                     AND order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stats['orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Get user's total spending (last 30 days)
$result = executeQuery($conn, "SELECT SUM(total_price) as total FROM orders 
                     WHERE user_id = $user_id 
                     AND order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stats['spending'] = $result ? $result->fetch_assoc()['total'] : 0;

// Get user's recent farm activities
$result = executeQuery($conn, "SELECT activity_type, description, activity_date 
                     FROM farm_activities 
                     WHERE user_id = $user_id 
                     ORDER BY activity_date DESC 
                     LIMIT 5");
$activities = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get user's order history for chart (last 6 months)
$result = executeQuery($conn, "
    SELECT 
        DATE_FORMAT(order_date, '%b') as month,
        COUNT(*) as orders,
        SUM(total_price) as spending
    FROM orders
    WHERE user_id = $user_id
    AND order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY order_date ASC
");
$chartData = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

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
    <title>My Dashboard - KEFARM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
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
        }
        
        .main-content {
            margin-left: 250px;
            padding: 25px;
            transition: margin-left 0.3s;
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
            transition: background-color 0.3s;
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
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
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>
                <p>Here's what's happening with your farm today</p>
            </div>
            <div class="quick-actions">
                <button class="quick-action-btn" onclick="window.location.href='farm_management.php'">
                    <i class="fas fa-tractor"></i> Farm Management
                </button>
                <button class="quick-action-btn" onclick="window.location.href='orders_sales.php'">
                    <i class="fas fa-shopping-cart"></i> Place Order
                </button>
                
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
                        <h3 class="card-title">Recent Orders</h3>
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
                        <div class="card-value">12</div>
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
                        <div class="card-value">3</div>
                    </div>
                </div>
                <p class="card-footer">For this week</p>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="chart-container">
            <h2 class="section-title"><i class="fas fa-chart-line"></i> My Activity</h2>
            <canvas id="activityChart" height="300"></canvas>
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
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript for Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Activity Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
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
                        }
                    }
                }
            }
        });

        // Animation for cards on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 150 * index);
            });
        });
    </script>
</body>
</html>