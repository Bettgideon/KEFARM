
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
   /* Header Styling */
.header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60px; /* Fixed height */
    background-color: #1a3e1f;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    z-index: 3000; /* Ensure it's above sidebar */
}

/* Sidebar Styling */
.sidebar {
    position: fixed;
    left: -250px; /* Initially hidden on mobile */
    top: 60px; /* Adjusted to start below the header */
    width: 250px;
    height: calc(100vh - 60px); /* Prevent overlap with header */
    background-color: #1a3e1f;
    padding-top: 20px;
    transition: left 0.3s ease-in-out;
    z-index: 2000; /* Below header */
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
    background-color: #1a3e1f;
}

/* Sidebar Icons */
.sidebar a i {
    margin-right: 12px;
    font-size: 18px;
}

/* Menu Toggle Button */
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
    z-index: 3000;
}

/* Page Content Wrapper */
.content {
    margin-left: 0;
    transition: margin-left 0.3s ease-in-out;
    padding: 20px;
    margin-top: 60px; /* Prevent overlap with header */
}

/* Adjust when sidebar is active */
.sidebar.active + .content {
    margin-left: 250px;
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
        margin-left: 250px;
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
