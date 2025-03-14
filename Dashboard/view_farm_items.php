<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Database Connection
$host = "localhost";
$user = "root";
$password = "root";
$database = "kefarm";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch farm items from the database
$sql = "SELECT * FROM farm_items ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Items - KEFARM</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center">KEFARM</h3>
    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="farm_management.php" class="active"><i class="fas fa-seedling"></i> Farm Management</a>
    <a href="orders_sales.php"><i class="fas fa-shopping-cart"></i> Orders & Sales</a>
    <a href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <a href="users.php"><i class="fas fa-users"></i> User Management</a>
    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h2>Farm Items</h2>
        </header>

        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Price (Ksh)</th>
                        <th>Description</th>
                        <th>Added On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['category']}</td>
                                <td>{$row['quantity']}</td>
                                <td>Ksh {$row['price']}</td>
                                <td>{$row['description']}</td>
                                <td>{$row['created_at']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center;'>No farm items found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>
