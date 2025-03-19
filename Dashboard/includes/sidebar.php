
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="farm_management.php"><i class="fas fa-tractor"></i> Farm Management</a></li>
        <li><a href="orders_sales.php"><i class="fas fa-shopping-cart"></i> Orders & Sales</a></li>
        <li><a href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a></li>

        <li><a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Page Content Wrapper -->
<div class="content" id="content">
    <!-- Your page content goes here -->
</div>

<style>
    /* Sidebar Styling */
    .sidebar {
        position: fixed;
        left: -250px; /* Initially hidden on mobile */
        top: 0;
        width: 250px;
        height: 100vh;
        background-color: #1a3e1f; /* Dark Green */
        padding-top: 60px; /* Push items down to prevent overlap */
        transition: left 0.3s ease-in-out;
        z-index: 2000;
    }

    .sidebar.active {
        left: 0; /* Slide in when active */
    }

    /* Sidebar Links */
    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin-bottom: 10px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        padding: 12px;
        color: white;
        text-decoration: none;
        font-size: 16px;
        border-radius: 5px;
        transition: background 0.3s;
    }

    .sidebar a:hover {
        background-color: #28a745; /* Light Green */
    }

    /* Sidebar Icons */
    .sidebar a i {
        margin-right: 12px;
        font-size: 18px;
    }

    /* Menu Toggle Button (Fixed at Top-Left) */
    .menu-toggle {
        display: block;
        position: fixed;
        top: 15px;
        left: 15px;
        background-color: #1a3e1f;
        padding: 8px 12px;
        border-radius: 5px;
        color: white;
        border: none;
        font-size: 24px;
        cursor: pointer;
        z-index: 3000; /* Higher than sidebar */
    }

    /* Page Content Wrapper */
    .content {
        margin-left: 0; /* Default (Mobile) */
        transition: margin-left 0.3s ease-in-out;
        padding: 20px;
    }

    /* Adjust when sidebar is active */
    .sidebar.active + .content {
        margin-left: 250px; /* Push content right */
    }

    /* Desktop View */
    @media (min-width: 769px) {
        .menu-toggle {
            display: none;
        }

        .sidebar {
            left: 0;
        }

        .content {
            margin-left: 250px; /* Always visible on desktop */
        }
    }
</style>

<!-- JavaScript for Sidebar Toggle -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuToggle = document.getElementById("menuToggle");
        const sidebar = document.getElementById("sidebar");

        if (menuToggle && sidebar) {
            menuToggle.addEventListener("click", function() {
                sidebar.classList.toggle("active");
            });
        }
    });
</script>
