<?php
// Start session and enable error reporting
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Database connection
require_once 'db_connection.php';

// Verify connection is working
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Include other files
include 'includes/sidebar.php';
include 'includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

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
</style>

<div class="main-content">
    <div class="table-container">
        <h3>Farm Records</h3>
        <button onclick="openAddModal()" class="add-new">+ Add New</button>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="farmTable">
                <?php
                $sql = "SELECT * FROM farm_items";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr id='row{$row['id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['category']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['price']}</td>
                            <td>{$row['description']}</td>
                            <td>
                                <button class='edit-btn' onclick=\"openEditModal({$row['id']}, '{$row['category']}', '{$row['name']}', {$row['quantity']}, {$row['price']}, '{$row['description']}')\">Edit</button>
                                <button class='delete-btn' onclick=\"deleteItem({$row['id']})\">Delete</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3>Add New Farm Item</h3>
        <form id="addForm">
            <label>Category:</label>
            <input type="text" id="add_category" required>
            <label>Name:</label>
            <input type="text" id="add_name" required>
            <label>Quantity:</label>
            <input type="number" id="add_quantity" required>
            <label>Price:</label>
            <input type="number" id="add_price" step="0.01" required>
            <label>Description:</label>
            <textarea id="add_description" required></textarea>
            <button type="submit">Add</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit Farm Item</h3>
        <form id="editForm">
            <input type="hidden" id="edit_id">
            
            <label>Category:</label>
            <input type="text" id="edit_category" required>

            <label>Name:</label>
            <input type="text" id="edit_name" required>

            <label>Quantity:</label>
            <input type="number" id="edit_quantity" required>

            <label>Price:</label>
            <input type="number" id="edit_price" step="0.01" required>

            <label>Description:</label>
            <textarea id="edit_description" required></textarea>

            <button type="submit">Update</button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this item?</p>
        <input type="hidden" id="delete_id">
        <button onclick="confirmDelete()">Yes, Delete</button>
        <button onclick="closeModal('deleteModal')">Cancel</button>
    </div>
</div>

<script>
console.log("JavaScript Loaded");

// Open Add Modal
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}

// Open Edit Modal
function openEditModal(id, category, name, quantity, price, description) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_quantity').value = quantity;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_description').value = description;
    document.getElementById('editModal').style.display = 'block';
}

// Close Modal
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Add Item
document.getElementById('addForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData();
    formData.append('category', document.getElementById('add_category').value);
    formData.append('name', document.getElementById('add_name').value);
    formData.append('quantity', document.getElementById('add_quantity').value);
    formData.append('price', document.getElementById('add_price').value || 0.00);
    formData.append('description', document.getElementById('add_description').value);

    fetch('add_farm_item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    })
    .catch(error => console.error('Error:', error));
});

// Handle Edit Form Submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let id = document.getElementById('edit_id').value;
    let category = document.getElementById('edit_category').value;
    let name = document.getElementById('edit_name').value;
    let quantity = document.getElementById('edit_quantity').value;
    let price = document.getElementById('edit_price').value;
    let description = document.getElementById('edit_description').value;

    let formData = new FormData();
    formData.append('id', id);
    formData.append('category', category);
    formData.append('name', name);
    formData.append('quantity', quantity);
    formData.append('price', price);
    formData.append('description', description);

    fetch('edit_farm_item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            // Update table row dynamically
            let row = document.getElementById('row' + id);
            row.cells[1].innerText = category;
            row.cells[2].innerText = name;
            row.cells[3].innerText = quantity;
            row.cells[4].innerText = price;
            row.cells[5].innerText = description;

            closeModal('editModal');
        }
    })
    .catch(error => console.error('Error:', error));
});

// Handle Delete Function
function deleteItem(id) {
    if (!confirm("Are you sure you want to delete this item?")) return;

    fetch('delete_farm_item.php', {
        method: 'POST',
        body: JSON.stringify({ id: id }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('row' + id).remove();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>