<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
?>

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
<div class="sidebar">
    <h3 class="text-center">KEFARM</h3>
    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="farm_management.php" class="active"><i class="fas fa-seedling"></i> Farm Management</a>
    <a href="orders_sales.php"><i class="fas fa-shopping-cart"></i> Orders & Sales</a>
    <a href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <a href="users.php"><i class="fas fa-users"></i> User Management</a>
    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
                <tbody id="farmTable">
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
    <tr>
        <td>3</td>
        <td>Crop</td>
        <td>Wheat</td>
        <td>700</td>
        <td>2025-03-12</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    </tr>
    <tr>
        <td>4</td>
        <td>Livestock</td>
        <td>Goats</td>
        <td>25</td>
        <td>2025-03-13</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    </tr>
    <tr>
        <td>5</td>
        <td>Crop</td>
        <td>Tomatoes</td>
        <td>300</td>
        <td>2025-03-14</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    </tr>
    <tr>
        <td>6</td>
        <td>Resource</td>
        <td>Fertilizer</td>
        <td>50 Bags</td>
        <td>2025-03-15</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    </tr>
    <tr>
        <td>7</td>
        <td>Crop</td>
        <td>Bananas</td>
        <td>200</td>
        <td>2025-03-16</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    </tr>
    <tr>
        <td>8</td>
        <td>Livestock</td>
        <td>Sheep</td>
        <td>15</td>
        <td>2025-03-17</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    </tr>
</tbody>


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
// Function to handle Edit button click
document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", function () {
        let row = this.closest("tr"); // Get the row of the clicked button
        let itemId = row.cells[0].innerText; // Get the ID from the first cell
        let itemName = row.cells[2].innerText; // Get the item name

        let newName = prompt("Edit item name:", itemName);
        if (newName !== null) {
            row.cells[2].innerText = newName; // Update the name in the table
            alert("Item ID " + itemId + " updated successfully!");
        }
    });
});

// Function to handle Delete button click
document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", function () {
        let row = this.closest("tr"); // Get the row of the clicked button
        let itemId = row.cells[0].innerText; // Get the ID from the first cell

        let confirmDelete = confirm("Are you sure you want to delete item ID " + itemId + "?");
        if (confirmDelete) {
            row.remove(); // Remove the row from the table
            alert("Item ID " + itemId + " deleted successfully!");
        }
    });
});
</script>

</body>
</html>
