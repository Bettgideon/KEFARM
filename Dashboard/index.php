<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEFARM Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>KEFARM</h2>
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-seedling"></i> Farm Management</a></li>
            <li><a href="#"><i class="fas fa-shopping-cart"></i> Orders & Sales</a></li>
            <li><a href="#"><i class="fas fa-warehouse"></i> Inventory</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-users"></i> User Management</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <div class="toggle-menu" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </div>
            <h1>Dashboard</h1>
        </header>

        <div class="cards">
            <div class="card">
                <i class="fas fa-seedling"></i>
                <div>
                    <h3>150+</h3>
                    <p>Farms Registered</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-shopping-cart"></i>
                <div>
                    <h3>250+</h3>
                    <p>Orders Processed</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-users"></i>
                <div>
                    <h3>500+</h3>
                    <p>Active Users</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-chart-line"></i>
                <div>
                    <h3>$50K+</h3>
                    <p>Revenue</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.querySelector(".sidebar").classList.toggle("active");
    }
</script>

</body>
</html>
