<?php include 'includes/sidebar.php'; ?> <!-- Include Sidebar -->
<?php include 'includes/header.php'; ?> <!-- Include Header -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
?>
<style>
    
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
            color: #1a3e1f;
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
            background: #1a3e1f;
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
            background: #1a3e1f;
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
            color: #1a3e1f;
        }

        /* Mobile Sidebar */
        .mobile-menu {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            background: #1a3e1f;
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
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Management - KEFARM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
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
    <header>
        <h2><i class="fas fa-seedling"></i> Farm Management</h2>
        <button class="btn" onclick="openModal()">+ Add New</button>
        <button class="btn" onclick="window.location.href='view_farm_items.php'">View Farm Items</button>
    </header>

    <div class="cards">
        <div class="card">
            <h3>Total Crops</h3>
            <p>120</p>
        </div>
        <div class="card">
            <h3>Total Livestock</h3>
            <p>80</p>
        </div>
        <div class="card">
            <h3>Farm Resources</h3>
            <p>35</p>
        </div>
    </div>

    <div class="table-container">
        <h3>Farm Records</h3>
        <input type="text" id="search" placeholder="Search..." onkeyup="filterTable()">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Added On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="farmTable">
                <!-- Sample Data Rows -->
                <tr>
                    <td>1</td>
                    <td>Crop</td>
                    <td>Maize</td>
                    <td>500</td>
                    <td>2025-03-10</td>
                    <td>
                        <button class="edit-btn">Edit</button>
                        <button class="delete-btn">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Livestock</td>
                    <td>Cows</td>
                    <td>10</td>
                    <td>2025-03-11</td>
                    <td>
                        <button class="edit-btn">Edit</button>
                        <button class="delete-btn">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Farm Item Modal -->
<div class="modal" id="farmModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Add New Farm Item</h3>
        <form action="add_farm_item.php" method="POST">
            <label for="type">Type</label>
            <select name="type" id="type">
                <option value="Crop">Crop</option>
                <option value="Livestock">Livestock</option>
                <option value="Resource">Resource</option>
            </select>
            <label for="name">Name</label>
            <input type="text" name="name" required>
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" required>
            <button type="submit">Add Item</button>
        </form>
    </div>
</div>

<script>
// Modal Functions
function openModal() {
    document.getElementById("farmModal").style.display = "block";
}
function closeModal() {
    document.getElementById("farmModal").style.display = "none";
}

// Table Search
function filterTable() {
    let search = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("#farmTable tr");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(search) ? "" : "none";
    });
}

// Edit Button Functionality
const editButtons = document.querySelectorAll(".edit-btn");
editButtons.forEach(button => {
    button.addEventListener("click", function () {
        let row = this.closest("tr");
        let itemId = row.cells[0].innerText;
        let itemName = row.cells[2].innerText;
        let newName = prompt("Edit item name:", itemName);
        if (newName !== null) {
            row.cells[2].innerText = newName;
            alert("Item ID " + itemId + " updated successfully!");
        }
    });
});

// Delete Button Functionality
const deleteButtons = document.querySelectorAll(".delete-btn");
deleteButtons.forEach(button => {
    button.addEventListener("click", function () {
        let row = this.closest("tr");
        let itemId = row.cells[0].innerText;
        let confirmDelete = confirm("Are you sure you want to delete item ID " + itemId + "?");
        if (confirmDelete) {
            row.remove();
            alert("Item ID " + itemId + " deleted successfully!");
        }
    });
});
</script>

</body>
</html>
