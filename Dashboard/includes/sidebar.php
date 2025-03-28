<?php
session_start(); // Ensure session is started

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Get the base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/KEFARM";

// Define the correct logo path
$logo_url = $base_url . "/assets/images/Logo.png";
?>

<!-- Sidebar HTML -->
<div class="sidebar" id="sidebar">
    <!-- Logo Container -->
    <div class="sidebar-logo">
        <a href="index.php">
            <img src="<?php echo $logo_url; ?>" alt="KEFARM Logo" onerror="this.onerror=null; this.src='<?php echo $base_url; ?>/assets/images/fallback-logo.png';">
            <span>KEFARM</span>
        </a>
    </div>

    <!-- Navigation Menu -->
    <ul>
    <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
    <li><a href="farm_management.php" class="<?= ($current_page == 'farm_management.php') ? 'active' : '' ?>"><i class="fas fa-seedling"></i> Farm Management</a></li>
    <li><a href="orders_sales.php" class="<?= ($current_page == 'orders_sales.php') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Orders & Sales</a></li>
    <li><a href="inventory.php" class="<?= ($current_page == 'inventory.php') ? 'active' : '' ?>"><i class="fas fa-warehouse"></i> Inventory</a></li>
    <li><a href="reports.php" class="<?= ($current_page == 'reports.php') ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li><a href="users.php" class="<?= ($current_page == 'users.php') ? 'active' : '' ?>"><i class="fas fa-users"></i> User Management</a></li>
    <li><a href="settings.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"><i class="fas fa-cog"></i> Settings</a></li>
</ul>


    <!-- Logout Button -->
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
        width: 260px;
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
        background-color:rgb(2, 55, 2);
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
        display: block;
        width: 60px; /* Adjust width */
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        background-color: white; /* Debugging */
    }

    .sidebar-logo span {
        font-size: 1.3rem;
        font-weight: bold;
        margin-left: 10px;
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
        display: flex;
        align-items: center;
        transition: all 0.3s;
    }

    .sidebar ul li a i {
        width: 25px;
        text-align: center;
        margin-right: 12px;
        font-size: 18px;
    }

    .sidebar ul li a:hover {
        background-color: #2E8B57;
        padding-left: 10px;
    }

    .sidebar ul li a.active {
        background-color: #32CD32;
        border-radius: 5px;
    }

    /* Logout Button */
    .logout {
        padding: 15px;
        background-color: #006400;
        border-top: 1px solid rgba(255,255,255,0.1);
        text-align: center;
    }

    .logout-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 5px;
        cursor: pointer;
        width: 90%;
        transition: background-color 0.3s;
    }

    .logout-btn:hover {
        background-color: #b52b3a;
    }

    /* Mobile Menu Button */
    .mobile-menu {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        background-color: #228B22;
        color: #fff;
        border: none;
        padding: 12px 15px;
        border-radius: 5px;
        cursor: pointer;
        z-index: 1002;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        font-size: 18px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .sidebar {
            left: -260px;
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
</script>
