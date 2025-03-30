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

// Fetch dashboard statistics from database
$stats = [];
$activities = [];
$chartData = [];
$orderData = [];

// Function to safely execute queries
function executeQuery($conn, $sql) {
    $result = $conn->query($sql);
    if ($result === false) {
        error_log("Query failed: " . $conn->error . " | Query: " . $sql);
        return false;
    }
    return $result;
}

// Total Users
$result = executeQuery($conn, "SELECT COUNT(*) as total FROM users");
$stats['users'] = $result ? $result->fetch_assoc()['total'] : 'N/A';

// Total Orders (last 30 days)
$result = executeQuery($conn, "SELECT COUNT(*) as total, SUM(total_price) as revenue 
                     FROM orders 
                     WHERE order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
if ($result) {
    $orderStats = $result->fetch_assoc();
    $stats['orders'] = $orderStats['total'] ?? 0;
    $stats['revenue'] = $orderStats['revenue'] ?? 0;
} else {
    $stats['orders'] = 'N/A';
    $stats['revenue'] = 'N/A';
}

// Total Inventory Items
$result = executeQuery($conn, "SELECT COUNT(*) as total FROM inventory");
$stats['inventory'] = $result ? $result->fetch_assoc()['total'] : 'N/A';

// Total Farm Items
$result = executeQuery($conn, "SELECT COUNT(*) as total FROM farm_items");
$stats['farm_items'] = $result ? $result->fetch_assoc()['total'] : 'N/A';

// Recent Orders (last 5)
$result = executeQuery($conn, "
    SELECT o.id, o.customer_name, o.product_name, o.quantity, o.total_price, o.order_date, o.status
    FROM orders o
    ORDER BY o.order_date DESC
    LIMIT 5
");
$activities = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Chart Data - Monthly revenue for last 6 months
$result = executeQuery($conn, "
    SELECT 
        DATE_FORMAT(order_date, '%b') as month,
        SUM(total_price) as revenue
    FROM orders
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY order_date ASC
");
$chartData = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Chart Data - Monthly orders for last 6 months
$result = executeQuery($conn, "
    SELECT 
        DATE_FORMAT(order_date, '%b') as month,
        COUNT(*) as orders
    FROM orders
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY order_date ASC
");
$orderData = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Prepare chart labels and data
$chartLabels = $chartData ? array_column($chartData, 'month') : [];
$revenueData = $chartData ? array_column($chartData, 'revenue') : [];
$ordersData = $orderData ? array_column($orderData, 'orders') : [];

// Format revenue with thousands separator
$stats['revenue'] = isset($stats['revenue']) && is_numeric($stats['revenue']) ? number_format($stats['revenue']) : '0';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEFARM Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .card i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .card h3 {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            color: #333;
        }
        .card p {
            font-size: 16px;
            color: #666;
        }
        .card:nth-child(1) { border-top: 4px solid #2e7d32; }
        .card:nth-child(1) i { color: #2e7d32; }
        .card:nth-child(2) { border-top: 4px solid #1565c0; }
        .card:nth-child(2) i { color: #1565c0; }
        .card:nth-child(3) { border-top: 4px solid #6a1b9a; }
        .card:nth-child(3) i { color: #6a1b9a; }
        .card:nth-child(4) { border-top: 4px solid #c62828; }
        .card:nth-child(4) i { color: #c62828; }
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .recent-orders {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .recent-orders h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            background-color: #1565c0;
        }
        .order-icon i {
            color: white;
        }
        .order-details {
            flex-grow: 1;
        }
        .order-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .order-meta {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
        }
        .order-status {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Summary Cards -->
        <div class="cards">
            <div class="card">
                <i class="fas fa-users"></i>
                <h3><?php echo $stats['users']; ?></h3>
                <p>Registered Users</p>
            </div>
            <div class="card">
                <i class="fas fa-shopping-cart"></i>
                <h3><?php echo $stats['orders']; ?></h3>
                <p>Recent Orders</p>
            </div>
            <div class="card">
                <i class="fas fa-warehouse"></i>
                <h3><?php echo $stats['inventory']; ?></h3>
                <p>Inventory Items</p>
            </div>
            <div class="card">
                <i class="fas fa-seedling"></i>
                <h3>Ksh <?php echo $stats['revenue']; ?></h3>
                <p>30-Day Revenue</p>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="chart-container">
            <h2>Monthly Performance</h2>
            <canvas id="salesChart" height="300"></canvas>
        </div>

        <!-- Recent Orders -->
        <div class="recent-orders">
            <h2>Recent Orders</h2>
            <?php if (empty($activities)): ?>
                <p>No recent orders found.</p>
            <?php else: ?>
                <?php foreach ($activities as $order): ?>
                    <div class="order-item">
                        <div class="order-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="order-details">
                            <div class="order-title">
                                <?php echo htmlspecialchars($order['product_name']); ?> 
                                (<?php echo htmlspecialchars($order['quantity']); ?> units)
                            </div>
                            <div class="order-meta">
                                <span>Customer: <?php echo htmlspecialchars($order['customer_name']); ?></span>
                                <span>Ksh <?php echo number_format($order['total_price'], 2); ?></span>
                                <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
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
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Revenue (Ksh)',
                    data: <?php echo json_encode($revenueData); ?>,
                    backgroundColor: 'rgba(46, 125, 50, 0.2)',
                    borderColor: 'rgba(46, 125, 50, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Orders',
                    data: <?php echo json_encode($ordersData); ?>,
                    backgroundColor: 'rgba(21, 101, 192, 0.2)',
                    borderColor: 'rgba(21, 101, 192, 1)',
                    borderWidth: 2,
                    tension: 0.4
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
                        beginAtZero: true
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
                }, 100 * index);
            });
        });
    </script>
</body>
</html>