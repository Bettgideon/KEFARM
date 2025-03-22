<?php
// sidebar.php

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}
?>

<!-- Sidebar HTML -->
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
        <button class="logout-btn" onclick="window.location.href='../logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
</div>

<!-- Mobile Menu Button (Only Include This Once) -->
<button class="mobile-menu" onclick="toggleSidebar()">☰</button>

<!-- Sidebar Styles -->
<style>
    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0; /* Always visible on desktop */
        width: 250px;
        height: 100%;
        background-color: #228B22; /* Dark green */
        color: #fff;
        transition: left 0.3s;
        z-index: 1001;
    }

    /* Hide sidebar by default on mobile */
    @media (max-width: 768px) {
        .sidebar {
            left: -250px; /* Hidden by default on mobile */
        }
        .sidebar.active {
            left: 0; /* Visible when toggled */
        }
    }

    .sidebar h2 {
        text-align: center;
        padding: 20px;
        margin: 0;
        background-color: #006400; /* Darker green */
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar ul li {
        padding: 15px 20px;
        border-bottom: 1px solid #006400; /* Darker green border */
    }

    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        display: block;
    }

    .sidebar ul li a:hover {
        background-color: #32CD32; /* Light green hover */
    }

    /* Logout Button */
    .logout {
        position: absolute;
        bottom: 0;
        width: 100%;
        text-align: center;
        padding: 15px 0;
        background-color: #006400; /* Darker green */
    }

    .logout-btn {
        background-color: #dc3545; /* Red color */
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
        width: 80%; /* Adjust width as needed */
    }

    .logout-btn:hover {
        background-color: #b52b3a; /* Darker red on hover */
    }

    .logout-btn i {
        margin-right: 8px; /* Add space between icon and text */
    }

    /* Mobile Menu Button */
    .mobile-menu {
        display: none; /* Hidden by default */
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #228B22; /* Dark green */
        color: #fff;
        border: none;
        padding: 10px;
        cursor: pointer;
        z-index: 1002;
    }

    /* Show mobile menu button only on mobile */
    @media (max-width: 768px) {
        .mobile-menu {
            display: block; /* Visible on mobile */
        }
    }
</style>

<!-- Sidebar Script -->
<script>
    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");
    }
</script>