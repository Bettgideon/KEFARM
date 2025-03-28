<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
require_once '../db_connect.php'; // Include database connection
?>
<?php include 'includes/sidebar.php'; ?> <!-- Include Sidebar -->
<?php include 'includes/header.php'; ?> <!-- Include Header -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - KEFARM</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.container {
    width: 90%;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h2 {
    text-align: center;
    color: #333;
}

.filters {
    text-align: center;
    margin-bottom: 20px;
}

.filters label {
    font-weight: bold;
}

.filters input, .filters button {
    padding: 8px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.filters button {
    background: #28a745;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}

.filters button:hover {
    background: #218838;
}

.tabs {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.tab-link {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 15px;
    margin: 5px;
    cursor: pointer;
    transition: 0.3s;
    border-radius: 5px;
}

.tab-link:hover, .tab-link.active {
    background: #218838;
}

.tab-content {
    display: none;
    text-align: center;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.tab-content.active {
    display: block;
}

canvas {
    max-width: 100%;
    height: 400px;
}

</style>
<body>
    <div class="container">
        <h2>Reports Dashboard</h2>
        <div class="filters">
            <label for="date_from">From:</label>
            <input type="date" id="date_from">
            <label for="date_to">To:</label>
            <input type="date" id="date_to">
            <button onclick="applyFilters()">Apply Filters</button>
            <button onclick="exportCSV()">Export CSV</button>
            <button onclick="exportPDF()">Export PDF</button>
        </div>
        <div class="tabs">
            <button class="tab-link active" onclick="openReport(event, 'inventory')">Inventory Reports</button>
            <button class="tab-link" onclick="openReport(event, 'sales')">Sales Reports</button>
            <button class="tab-link" onclick="openReport(event, 'orders')">Orders Reports</button>
            <button class="tab-link" onclick="openReport(event, 'user_activity')">User Activity Reports</button>
        </div>

        <?php
        // Fetch Inventory Data
        $inventoryData = [];
        $inventoryLabels = [];
        $result = $conn->query("SELECT item_name, quantity FROM inventory");
        while ($row = $result->fetch_assoc()) {
            $inventoryLabels[] = $row['item_name'];
            $inventoryData[] = $row['quantity'];
        }
        
        // Fetch Sales Data
        $salesData = [];
        $salesLabels = [];
        $result = $conn->query("SELECT product_name, SUM(total_amount) as total_sales FROM orders GROUP BY product_name");
        while ($row = $result->fetch_assoc()) {
            $salesLabels[] = $row['product_name'];
            $salesData[] = $row['total_sales'];
        }
        
        // Fetch Orders Data
        $ordersData = [];
        $ordersLabels = [];
        $result = $conn->query("SELECT order_id, total_amount FROM orders");
        while ($row = $result->fetch_assoc()) {
            $ordersLabels[] = 'Order ' . $row['order_id'];
            $ordersData[] = $row['total_amount'];
        }
        
        // Fetch User Activity Data
        $userActivityData = [];
        $userActivityLabels = [];
        $result = $conn->query("SELECT username, last_login FROM users");
        while ($row = $result->fetch_assoc()) {
            $userActivityLabels[] = $row['username'];
            $userActivityData[] = strtotime($row['last_login']);
        }
        
        $conn->close();
        ?>

        <div id="inventory" class="tab-content active">
            <h3>Inventory Reports</h3>
            <canvas id="inventoryChart"></canvas>
        </div>

        <div id="sales" class="tab-content">
            <h3>Sales Reports</h3>
            <canvas id="salesChart"></canvas>
        </div>

        <div id="orders" class="tab-content">
            <h3>Orders Reports</h3>
            <canvas id="ordersChart"></canvas>
        </div>

        <div id="user_activity" class="tab-content">
            <h3>User Activity Reports</h3>
            <canvas id="userActivityChart"></canvas>
        </div>
    </div>

    <script>
        function openReport(evt, reportName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(reportName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        function generateChart(canvasId, labels, data) {
            var ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Data',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        window.onload = function() {
            generateChart('inventoryChart', <?php echo json_encode($inventoryLabels); ?>, <?php echo json_encode($inventoryData); ?>);
            generateChart('salesChart', <?php echo json_encode($salesLabels); ?>, <?php echo json_encode($salesData); ?>);
            generateChart('ordersChart', <?php echo json_encode($ordersLabels); ?>, <?php echo json_encode($ordersData); ?>);
            generateChart('userActivityChart', <?php echo json_encode($userActivityLabels); ?>, <?php echo json_encode($userActivityData); ?>);
        };
    </script>
</body>
</html>
