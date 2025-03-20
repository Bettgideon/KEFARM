<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2>KEFARM</h2>
    <ul>
        <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
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

<!-- Sidebar Styles -->
<style>
/* Sidebar */
.sidebar {
    position: fixed;
    left: -260px; /* Initially hidden */
    top: 0;
    width: 250px;
    height: 100vh;
    background-color: #1a3e1f;
    padding-top: 20px;
    transition: transform 0.3s ease-in-out;
    z-index: 9999; /* Keep above other elements */
    overflow-y: auto;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
}

.sidebar.active {
    left: 0; /* Show sidebar */
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
    z-index: 10000; /* Ensure above everything */
}

/* Fix Button Overlap */
.sidebar.active + .content {
    pointer-events: auto; /* Allow button clicks */
}

/* Desktop View */
@media (min-width: 769px) {
    .menu-toggle {
        display: none;
    }
    .sidebar {
        left: 0; /* Sidebar always visible on desktop */
    }
}
</style>

<!-- Sidebar Toggle Script -->
<script>
    function toggleSidebar() {
        let sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");
    }

    // Prevent sidebar from blocking buttons
    document.addEventListener("click", function (event) {
        let sidebar = document.getElementById("sidebar");
        let menuButton = document.querySelector(".menu-toggle");

        if (!sidebar.contains(event.target) && !menuButton.contains(event.target) && window.innerWidth <= 768) {
            sidebar.classList.remove("active");
        }
    });
</script>
