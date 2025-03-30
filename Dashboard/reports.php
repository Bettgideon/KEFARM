
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

$current_page = 'reports.php'; // Define the current page
include 'includes/sidebar.php'; // Include the sidebar
include 'includes/header.php'; // Include Header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEFARM Reports Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
            --primary-color:rgb(10, 65, 25);
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --card-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            padding-top: calc(var(--header-height) + 20px);
            background-color: var(--light-bg);
            min-height: 100vh;
        }
        
        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Report Cards */
        .report-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: transform 0.3s;
        }
        .report-card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        .card-icon {
            font-size: 20px;
            color: var(--primary-color);
        }
        
        /* Charts */
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
            margin-bottom: 15px;
        }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .data-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 10px;
            text-align: left;
        }
        .data-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .data-table tr:hover {
            background-color: #f1f1f1;
        }
        
        /* Status Badges */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Filters */
        .filter-bar {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-group label {
            font-weight: 500;
            color: #555;
        }
        select, input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .btn-success {
            background-color: var(--success-color);
        }
        .btn-success:hover {
            background-color: #219653;
        }
        
        
        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
                padding-top: calc(var(--header-height) + 15px);
            }
            .filter-bar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        @media (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: fixed;
        left: 0;
        top: var(--header-height);
        height: calc(100vh - var(--header-height));
        background: white;
        box-shadow: var(--card-shadow);
        z-index: 1000;
    }
}

        
    </style>
</head>
<body>
    <div class="main-content">
        <div class="filter-bar">
            <div class="filter-group">
                <label for="time_range"><i class="fas fa-calendar-alt"></i> Time Range:</label>
                <select id="time_range">
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div class="filter-group" id="custom_dates" style="display:none;">
                <label for="start_date">From:</label>
                <input type="date" id="start_date">
                <label for="end_date">To:</label>
                <input type="date" id="end_date">
            </div>
            <button onclick="applyFilters()"><i class="fas fa-filter"></i> Apply</button>
            <button class="btn-success" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        </div>

        <?php
        // Fetch data
        $inventory = $conn->query("SELECT id, product_name, category, quantity, unit_price FROM inventory ORDER BY quantity ASC");
        $low_stock = $conn->query("SELECT * FROM inventory WHERE quantity < 10");
        $orders = $conn->query("SELECT id, customer_name, product_name, quantity, total_price, order_date, status FROM orders ORDER BY order_date DESC LIMIT 5");
        $sales_data = $conn->query("
            SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(total_price) as total_sales 
            FROM orders 
            GROUP BY DATE_FORMAT(order_date, '%Y-%m') 
            ORDER BY month DESC LIMIT 6
        ");
        $top_products = $conn->query("
            SELECT product_name, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue 
            FROM orders 
            GROUP BY product_name 
            ORDER BY total_revenue DESC 
            LIMIT 5
        ");
        $users = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
        ?>

        <div class="dashboard-grid">
            <!-- Inventory Summary -->
            <div class="report-card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Summary</h3>
                    <i class="fas fa-boxes card-icon"></i>
                </div>
                <div class="chart-container">
                    <canvas id="inventoryChart"></canvas>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Items</th>
                            <th>Low Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $categories = [];
                        $inventory->data_seek(0);
                        while($item = $inventory->fetch_assoc()) {
                            $categories[$item['category']] = ($categories[$item['category']] ?? 0) + 1;
                        }
                        $low_stock->data_seek(0);
                        $low_stock_counts = [];
                        while($item = $low_stock->fetch_assoc()) {
                            $low_stock_counts[$item['category']] = ($low_stock_counts[$item['category']] ?? 0) + 1;
                        }
                        foreach($categories as $cat => $count): 
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($cat) ?></td>
                            <td><?= $count ?></td>
                            <td><?= $low_stock_counts[$cat] ?? 0 ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Sales Performance -->
            <div class="report-card">
                <div class="card-header">
                    <h3 class="card-title">Sales Performance</h3>
                    <i class="fas fa-chart-line card-icon"></i>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Sales</th>
                            <th>Growth</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sales = $sales_data->fetch_all(MYSQLI_ASSOC);
                        $prev_month = null;
                        foreach(array_reverse($sales) as $sale): 
                            $growth = $prev_month ? 
                                ($sale['total_sales'] - $prev_month) / $prev_month * 100 : 0;
                            $prev_month = $sale['total_sales'];
                        ?>
                        <tr>
                            <td><?= date('M Y', strtotime($sale['month'].'-01')) ?></td>
                            <td><?= number_format($sale['total_sales'], 2) ?></td>
                            <td style="color: <?= $growth >= 0 ? 'green' : 'red' ?>;">
                                <?= $growth >= 0 ? '+' : '' ?><?= number_format($growth, 1) ?>%
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top Products -->
            <div class="report-card">
                <div class="card-header">
                    <h3 class="card-title">Top Products</h3>
                    <i class="fas fa-star card-icon"></i>
                </div>
                <div class="chart-container">
                    <canvas id="productsChart"></canvas>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = $top_products->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td><?= $product['total_quantity'] ?></td>
                            <td><?= number_format($product['total_revenue'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Orders -->
            <div class="report-card">
                <div class="card-header">
                    <h3 class="card-title">Recent Orders</h3>
                    <i class="fas fa-shopping-cart card-icon"></i>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars(substr($order['customer_name'], 0, 15)) ?></td>
                            <td><?= number_format($order['total_price'], 2) ?></td>
                            <td>
                                <span class="badge 
                                    <?= $order['status'] == 'completed' ? 'badge-success' : 
                                       ($order['status'] == 'pending' ? 'badge-warning' : 'badge-danger') ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- User Activity -->
            <div class="report-card">
                <div class="card-header">
                    <h3 class="card-title">Recent Users</h3>
                    <i class="fas fa-users card-icon"></i>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User #</th>
                            <th>Name</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Low Stock Alert -->
            <div class="report-card" style="border-left: 4px solid var(--danger-color);">
                <div class="card-header">
                    <h3 class="card-title">Low Stock Alert</h3>
                    <i class="fas fa-exclamation-triangle card-icon" style="color: var(--danger-color);"></i>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Qty Left</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $low_stock->data_seek(0);
                        while($item = $low_stock->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td style="color: var(--danger-color); font-weight: bold;">
                                <?= $item['quantity'] ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($low_stock->num_rows == 0): ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No low stock items</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            // Initialize Charts
            document.addEventListener('DOMContentLoaded', function() {
                // Inventory Chart (Doughnut)
                new Chart(document.getElementById('inventoryChart'), {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode(array_keys($categories)) ?>,
                        datasets: [{
                            data: <?= json_encode(array_values($categories)) ?>,
                            backgroundColor: [
                                '#3498db', '#2ecc71', '#f1c40f', '#e74c3c', '#9b59b6'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Sales Chart (Line)
                const salesData = <?= json_encode(array_reverse($sales_data->fetch_all(MYSQLI_ASSOC))) ?>;
                new Chart(document.getElementById('salesChart'), {
                    type: 'line',
                    data: {
                        labels: salesData.map(item => new Date(item.month + '-01').toLocaleDateString('en-US', {month: 'short', year: 'numeric'})),
                        datasets: [{
                            label: 'Monthly Sales',
                            data: salesData.map(item => item.total_sales),
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Products Chart (Bar)
                const productsData = <?= json_encode($top_products->fetch_all(MYSQLI_ASSOC)) ?>;
                new Chart(document.getElementById('productsChart'), {
                    type: 'bar',
                    data: {
                        labels: productsData.map(item => item.product_name),
                        datasets: [{
                            label: 'Revenue',
                            data: productsData.map(item => item.total_revenue),
                            backgroundColor: '#27ae60'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Show/hide custom date range
                document.getElementById('time_range').addEventListener('change', function() {
                    document.getElementById('custom_dates').style.display = 
                        this.value === 'custom' ? 'flex' : 'none';
                });
            });

            function applyFilters() {
                const range = document.getElementById('time_range').value;
                let fromDate, toDate = new Date().toISOString().split('T')[0];
                
                if (range === 'custom') {
                    fromDate = document.getElementById('start_date').value;
                    toDate = document.getElementById('end_date').value;
                    if (!fromDate || !toDate) {
                        alert('Please select both dates for custom range');
                        return;
                    }
                } else {
                    const date = new Date();
                    date.setDate(date.getDate() - parseInt(range));
                    fromDate = date.toISOString().split('T')[0];
                }
                
                alert(`Filter applied from ${fromDate} to ${toDate}`);
                // In real implementation, reload data via AJAX
            }
        </script>
    </div>
</body>
</html>