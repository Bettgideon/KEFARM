<?php include 'includes/header.php'; ?> <!-- Include Header -->

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

    <!-- Fix FontAwesome CORS Issue -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            background-color: #f4f4f4;
        }

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

        header {
            background: #28a745;
            padding: 15px;
            color: white;
            text-align: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            height: 60px;
            line-height: 30px;
            font-size: 22px;
            font-weight: bold;
        }

        /* Cards Layout */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .card i {
            font-size: 30px;
            color: #28a745;
        }

        /* Mobile Sidebar */
        .mobile-menu {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1001;
        }

        @media (min-width: 769px) {
            .sidebar {
                left: 0;
            }
            .mobile-menu {
                display: none;
            }
            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
            }
            header {
                width: calc(100% - 250px);
                left: 250px;
            }
        }
        .menu-item.active {
    background-color: #28a745; /* Change to your desired color */
    color: white;
}

    </style>
</head>
<body>

<!-- Mobile Menu Button -->
<div class="mobile-menu" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2>KEFARM</h2>
    <ul>
        <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="farm_management.php"><i class="fas fa-seedling"></i> Farm Management</a></li>
        <li><a href="orders_sales.php"><i class="fas fa-shopping-cart"></i> Orders & Sales</a></li>
        <li><a href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a></li>
        <li><a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
    <div class="logout">
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <header>Dashboard</header>
    <div class="cards">
        <div class="card"><i class="fas fa-seedling"></i><h3>150+</h3><p>Farms Registered</p></div>
        <div class="card"><i class="fas fa-shopping-cart"></i><h3>250+</h3><p>Orders Processed</p></div>
        <div class="card"><i class="fas fa-users"></i><h3>500+</h3><p>Active Users</p></div>
        <div class="card"><i class="fas fa-chart-line"></i><h3>Ksh 50K+</h3><p>Revenue</p></div>
    </div>
</div>

<script>
    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");

        if (sidebar.classList.contains("active")) {
            sidebar.style.left = "0";
        } else {
            sidebar.style.left = "-250px";
        }
    }

    // Close sidebar when clicking outside (only for mobile)
    document.addEventListener("click", function (event) {
        let sidebar = document.getElementById("sidebar");
        let menuButton = document.querySelector(".mobile-menu");

        if (!sidebar.contains(event.target) && !menuButton.contains(event.target) && window.innerWidth <= 768) {
            sidebar.classList.remove("active");
            sidebar.style.left = "-250px";
        }
    });
</script>

</body>
</html>
