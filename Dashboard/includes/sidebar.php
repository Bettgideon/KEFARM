<?php
// sidebar.php

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Get correct base path
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$project_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', realpath(__DIR__)));
$full_url = $base_url . $project_path;
?>

<!-- Sidebar HTML -->
<div class="sidebar" id="sidebar">
    <!-- Logo Container -->
    <div class="sidebar-logo">
        <a href="index.php">
            <img src="<?php echo $full_url; ?>/assets/images/Logo.png" alt="KEFARM Logo" onerror="this.src='<?php echo $full_url; ?>/assets/images/fallback-logo.png';this.onerror=null;">
            <span>KEFARM</span>
        </a>
    </div>
    
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

<!-- Mobile Menu Button -->
<button class="mobile-menu" onclick="toggleSidebar()">☰</button>

<!-- Sidebar Styles -->
<style>
    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100%;
        background-color: #228B22;
        color: #fff;
        transition: left 0.3s;
        z-index: 1001;
        display: flex;
        flex-direction: column;
    }

    /* Logo Container */
    .sidebar-logo {
        padding: 20px;
        text-align: center;
        background-color: #006400;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-logo a {
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
    }

    .sidebar-logo img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        margin-right: 10px;
        background-color: #f0f0f0; /* Fallback color */
    }

    .sidebar-logo span {
        font-size: 1.2rem;
        font-weight: bold;
    }

    /* Navigation Items */
    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;
        overflow-y: auto;
    }

    .sidebar ul li {
        padding: 12px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }

    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        display: block;
        transition: all 0.3s;
    }

    .sidebar ul li a:hover {
        background-color: transparent;
        padding-left: 5px;
    }

    .sidebar ul li a i {
        width: 25px;
        text-align: center;
        margin-right: 10px;
    }

    .sidebar ul li a.active {
        background-color: #32CD32;
        border-radius: 4px;
    }

    /* Logout Button */
    .logout {
        padding: 15px;
        background-color: #006400;
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    .logout-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s;
    }

    .logout-btn:hover {
        background-color: #b52b3a;
    }

    /* Mobile Menu Button */
    .mobile-menu {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #228B22;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        z-index: 1002;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .sidebar {
            left: -250px;
        }
        .sidebar.active {
            left: 0;
        }
        .mobile-menu {
            display: block;
        }
    }
</style>

<script>
    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");
    }
    
    // Verify logo loaded
    document.addEventListener('DOMContentLoaded', function() {
        const logo = document.querySelector('.sidebar-logo img');
        logo.onerror = function() {
            this.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23228B22"/><text x="50" y="50" font-size="10" text-anchor="middle" fill="white">KEFARM</text></svg>';
        };
    });
</script>