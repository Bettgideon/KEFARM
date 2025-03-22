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
        /* Ensure main content is pushed down to avoid overlapping the included header */
.main-content {
    margin-top: 80px;
    padding: 20px;
}

/* Table Styles */
.table-container {
    margin-top: 20px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

table th {
    background-color:rgb(6, 117, 21);
    font-weight: bold;
}

/* Modal Overlay */
.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 450px;
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    text-align: center;
}

/* Dark overlay background */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

/* Modal Title */
.modal h3 {
    color: #28a745;
    margin-bottom: 15px;
}

/* Modal Inputs */
.modal input, .modal textarea {
    width: 90%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #28a745;
    border-radius: 5px;
    outline: none;
}

/* Modal Buttons */
.modal button {
    width: 45%;
    padding: 10px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

/* Add & Update Buttons */
.modal button:first-child {
    background-color: #28a745;
    color: white;
}

.modal button:first-child:hover {
    background-color: #1e7e34;
}

/* Cancel Buttons */
.modal button:last-child {
    background-color: #dc3545;
    color: white;
}

.modal button:last-child:hover {
    background-color: #b52b3a;
}

/* Close Button */
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    color: red;
    cursor: pointer;
}

/* General Button Styles */
button {
    padding: 10px 15px;
    font-size: 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
}

/* Add New Button */
button.add-new {
    background-color: #28a745;
    color: white;
    font-weight: bold;
    border-radius: 5px;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}

button.add-new:hover {
    background-color: #1e7e34;
}

/* Edit Button */
button.edit-btn {
    background-color: #ffc107;
    color: #212529;
    font-weight: bold;
    margin-right: 5px;
}

button.edit-btn:hover {
    background-color: #e0a800;
}

/* Delete Button */
button.delete-btn {
    background-color: #dc3545;
    color: white;
    font-weight: bold;
    margin-left: 5px;
}

button.delete-btn:hover {
    background-color: #b52b3a;
}

/* Table Actions - Keep Buttons Inline */
td button {
    margin: 3px;
}

/* Responsive Styles */
@media screen and (max-width: 768px) {
    button {
        font-size: 12px;
        padding: 8px 12px;
    }

    .table-container {
        overflow-x: auto;
    }
}
        /* Ensure main content is pushed down to avoid overlapping the included header */
.main-content {
    margin-top: 80px; /* Adjust if needed to move content slightly below the header */
    padding: 20px;
}

/* Ensure the header inside includes/header.php remains fixed */
header {
    position: relative; /* Change from fixed to relative to avoid covering content */
    width: 100%;
    background-color: #fff; /* Maintain existing design */
    padding: 15px 20px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}
.mobile-menu {
    display: block;
    position: fixed;
    top: 70px; /* Move it slightly below the header */
    left: 15px;
    background: #28a745;
    color: white;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    z-index: 1001;
}
/* Mobile View Adjustments */
@media screen and (max-width: 768px) {
    .main-content {
        margin-top: 90px; /* Increase margin slightly for smaller screens */
        padding: 15px;
    }
}
/* Modal Overlay */
.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 450px;
    background: #ffffff; /* White background */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    text-align: center;
}

/* Dark overlay background */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

/* Modal Title */
.modal h3 {
    color: #28a745; /* KEFARM Green */
    margin-bottom: 15px;
}

/* Modal Inputs */
.modal input {
    width: 90%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #28a745;
    border-radius: 5px;
    outline: none;
}

/* Modal Buttons */
.modal button {
    width: 45%;
    padding: 10px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

/* Add & Update Buttons */
.modal button:first-child {
    background-color: #28a745; /* KEFARM Green */
    color: white;
}

.modal button:first-child:hover {
    background-color: #1e7e34; /* Dark Green */
}

/* Cancel Buttons */
.modal button:last-child {
    background-color: #dc3545; /* Red for cancel */
    color: white;
}

.modal button:last-child:hover {
    background-color: #b52b3a;
}

/* Close Button */
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    color: red;
    cursor: pointer;
}

/* Responsive */
@media screen and (max-width: 480px) {
    .modal {
        width: 95%;
    }
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
