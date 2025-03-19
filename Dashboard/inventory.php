



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - KEFARM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f0fff4;
        }
        .sidebar {
            width: 100%;
            background: #1b5e20;
            padding: 10px;
            text-align: center;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #145a32;
        }
        .main-content {
            width: 90%;
            max-width: 1200px;
            padding: 20px;
            text-align: center;
        }
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
        .modal {
            display: none;
            position: fixed;
            top: 50;
            left: 50;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
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
    gap: 10px; /* Adds space between icon and text */
}

.sidebar a i {
    width: 20px; /* Ensures icons have space */
}

            .main-content {
                margin-left: 260px;
                width: calc(100% - 260px);
            }
        }
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
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.delete-btn {
    background-color: #f44336; /* Red */
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}



    </style>
</head>
<body>
<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <h3>KEFARM</h3>
    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
<a href="farm_management.php"><i class="fas fa-tractor"></i> Farm Management</a>
<a href="orders_sales.php"><i class="fas fa-shopping-cart"></i> Orders & Sales</a>
<a href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
<a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
<a href="users.php"><i class="fas fa-users"></i> User Management</a>
<a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
<a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

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
        function openAddModal() {
            document.getElementById("addItemModal").style.display = "flex";
        }
        function closeAddModal() {
            document.getElementById("addItemModal").style.display = "none";
        }
        function openEditModal(id) {
            fetch(`get_inventory_item.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById("edit_id").value = data.id;
                document.getElementById("edit_product_name").value = data.product_name;
                document.getElementById("edit_category").value = data.category;
                document.getElementById("edit_quantity").value = data.quantity;
                document.getElementById("edit_unit_price").value = data.unit_price;
                document.getElementById("editModal").style.display = "flex";
            });
        }
        function closeEditModal() {
            document.getElementById("editModal").style.display = "none";
        }
        function submitItem() {
            let formData = new FormData();
            formData.append("product_name", document.getElementById("product_name").value);
            formData.append("category", document.getElementById("category").value);
            formData.append("quantity", document.getElementById("quantity").value);
            formData.append("unit_price", document.getElementById("unit_price").value);
            fetch("add_inventory.php", { method: "POST", body: formData })
            .then(response => response.text())
            .then(data => { alert(data); closeAddModal(); location.reload(); });
        }
        function updateItem() {
            let formData = new FormData();
            formData.append("id", document.getElementById("edit_id").value);
            formData.append("product_name", document.getElementById("edit_product_name").value);
            formData.append("category", document.getElementById("edit_category").value);
            formData.append("quantity", document.getElementById("edit_quantity").value);
            formData.append("unit_price", document.getElementById("edit_unit_price").value);
            fetch("update_inventory.php", { method: "POST", body: formData })
            .then(response => response.text())
            .then(data => { alert(data); closeEditModal(); location.reload(); });
        }
        function deleteItem(id) {
    if (confirm("Are you sure you want to delete this item?")) {
        let formData = new FormData();
        formData.append("id", id);

        fetch("delete_inventory.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => { alert(data); location.reload(); })
        .catch(error => console.error("Error:", error));
    }
}

    </script>
</body>
</html>
