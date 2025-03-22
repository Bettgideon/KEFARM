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
<style>
    /* Sidebar Styles */
    .sidebar {
            width: 250px;
            background: #1a3e1f;
            color: white;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100vh;
            overflow-y: auto;
            padding-top: 20px;
            transition: left 0.3s ease-in-out;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            color: #28a745;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            padding: 12px;
        }

        .sidebar ul li a:hover, 
        .sidebar ul li a.active {
            background: #28a745;
            border-radius: 5px;
        }

        .logout {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
        }

        .logout a {
            background: red;
            color: white;
            display: block;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .logout a:hover {
            background: darkred;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 80px 20px 20px;
            width: 100%;
            transition: margin-left 0.3s ease-in-out;
        }
</style>
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
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar" id="sidebar">
    <h2>KEFARM</h2>
    <ul>
        <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="farm_management.php" class="<?= ($current_page == 'farm_management.php') ? 'active' : '' ?>"><i class="fas fa-seedling"></i> Farm Management</a></li>
        <li><a href="orders_sales.php" class="<?= ($current_page == 'orders_sales.php') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Orders & Sales</a></li>
        <li><a href="inventory.php" class="<?= ($current_page == 'inventory.php') ? 'active' : '' ?>"><i class="fas fa-warehouse"></i> Inventory</a></li>
        <li><a href="reports.php" class="<?= ($current_page == 'reports.php') ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Reports</a></li>
        <li><a href="users.php" class="<?= ($current_page == 'users.php') ? 'active' : '' ?>"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="settings.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
    <div class="logout">
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
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
