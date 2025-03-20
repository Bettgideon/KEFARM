<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

?>

<?php include 'includes/sidebar.php'; ?> <!-- Include Sidebar -->
<?php include 'includes/header.php'; ?> <!-- Include Header -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - KEFARM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* General Styles */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
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

/* Header */
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

/* Buttons */
button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    opacity: 0.8;
}

button:active {
    transform: scale(0.95);
}

.edit-btn {
    background-color: #4CAF50; /* Green */
    color: white;
}

.delete-btn {
    background-color: #f44336; /* Red */
    color: white;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

th {
    background: #1e5631;
    color: white;
}

/* Action Buttons */
.button {
    background: #004d00;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s ease;
}

.button:hover {
    background: #003300;
}

/* Modal Overlay */


/* Modal Content */
.modal-content {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Ensure modal is centered on small screens */
@media (max-width: 768px) {
    .modal-content {
        width: 90%; /* Reduce width for smaller screens */
    }
}




.modal h3 {
    background: #1e5631;
    color: white;
    padding: 15px;
    border-radius: 10px 10px 0 0;
}

.modal input, .modal select {
    width: calc(100% - 20px);
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #1e5631;
    border-radius: 5px;
    background: #f0fff4;
}

.modal button {
    background: #1e5631;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.modal button:hover {
    background: #145a32;
}

/* Responsive Sidebar */
@media (min-width: 768px) {
    .sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        text-align: left;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar a i {
        width: 20px;
    }

    .main-content {
        margin-left: 260px;
        width: calc(100% - 260px);
    }
}
.menu-item.active {
    background-color: #28a745; /* Change to your desired color */
    color: white;
}

    </style>
    
</head>
<body>
<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
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
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>


<div class="main-content">
    <header>
        <h2>Inventory Management</h2>
    </header>
    
    <button style="background-color: #007bff; color: white; padding: 10px 15px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; transition: 0.3s;" 
    onmouseover="this.style.backgroundColor='#0056b3'" 
    onmouseout="this.style.backgroundColor='#007bff'"
    onclick="openAddModal()">
    + Add New Item
</button>

    
    <table>
        <thead>
            <tr>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="inventoryTable">
    <?php
    include '../db_connect.php';
    $stmt = $conn->prepare("SELECT * FROM inventory");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?> Kg</td>
            <td>Ksh <?php echo htmlspecialchars($row['unit_price']); ?></td>
            <td>
                <button style="background-color: #28a745; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;" 
                    onclick="openEditModal(<?php echo (int) $row['id']; ?>)">
                    Edit
                </button>
                
                <button style="background-color: #dc3545; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;" 
                    onclick="deleteItem(<?php echo (int) $row['id']; ?>)">
                    Delete
                </button>
            </td>
        </tr>
        <?php
    }
    $stmt->close();
    $conn->close();
    ?>
</tbody>


    <!-- Add Item Modal -->
    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <h3>Add New Inventory Item</h3>
            <form id="addItemForm">
                <input type="text" id="product_name" placeholder="Product Name" required>
                <input type="text" id="category" placeholder="Category" required>
                <input type="number" id="quantity" placeholder="Quantity" required>
                <input type="number" id="unit_price" placeholder="Unit Price" required>
                <button type="button" onclick="submitItem()">Add Item</button>
                <button type="button" onclick="closeAddModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Inventory Item</h3>
            <form id="editItemForm">
                <input type="hidden" id="edit_id">
                <input type="text" id="edit_product_name" placeholder="Product Name" required>
                <input type="text" id="edit_category" placeholder="Category" required>
                <input type="number" id="edit_quantity" placeholder="Quantity" required>
                <input type="number" id="edit_unit_price" placeholder="Unit Price" required>
                <button type="button" onclick="updateItem()">Update Item</button>
                <button type="button" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Open Add Item Modal
function openAddModal() {
    let modal = document.getElementById("addItemModal");
    modal.style.display = "flex"; // Ensures it uses flexbox for centering
}

// Close Add Item Modal
function closeAddModal() {
    document.getElementById("addItemModal").style.display = "none";
}

// Open Edit Item Modal
function openEditModal(id) {
    fetch(`get_inventory_item.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data) {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_product_name").value = data.product_name;
            document.getElementById("edit_category").value = data.category;
            document.getElementById("edit_quantity").value = data.quantity;
            document.getElementById("edit_unit_price").value = data.unit_price;
            
            let modal = document.getElementById("editModal");
            modal.style.display = "flex"; // Ensures modal appears at center
        } else {
            alert("Failed to fetch item data.");
        }
    })
    .catch(error => console.error("Error fetching item:", error));
}

// Close Edit Modal
function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

// Add New Inventory Item
function submitItem() {
    let formData = new FormData();
    formData.append("product_name", document.getElementById("product_name").value);
    formData.append("category", document.getElementById("category").value);
    formData.append("quantity", document.getElementById("quantity").value);
    formData.append("unit_price", document.getElementById("unit_price").value);

    fetch("add_inventory.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        closeAddModal();
        location.reload();
    })
    .catch(error => console.error("Error adding item:", error));
}

// Update Inventory Item
function updateItem() {
    let formData = new FormData();
    formData.append("id", document.getElementById("edit_id").value);
    formData.append("product_name", document.getElementById("edit_product_name").value);
    formData.append("category", document.getElementById("edit_category").value);
    formData.append("quantity", document.getElementById("edit_quantity").value);
    formData.append("unit_price", document.getElementById("edit_unit_price").value);

    fetch("update_inventory.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        closeEditModal();
        location.reload();
    })
    .catch(error => console.error("Error updating item:", error));
}

// Delete Inventory Item
function deleteItem(id) {
    if (confirm("Are you sure you want to delete this item?")) {
        let formData = new FormData();
        formData.append("id", id);

        fetch("delete_inventory.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        })
        .catch(error => console.error("Error deleting item:", error));
    }
}


    </script>
</body>
</html>
