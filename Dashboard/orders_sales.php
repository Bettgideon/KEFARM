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
    <title>Orders & Sales - KEFARM</title>
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
            background-color: rgb(6, 117, 21);
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

        .edit_button {
            background: red;
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

<!-- Main Content -->
<div class="main-content">
    <header>
        <h2><i class="fas fa-shopping-cart"></i> Orders & Sales</h2>
        <button class="btn" onclick="openModal()">+ Add New Order</button>
    </header>

    <div class="table-container">
        <h3>Order Records</h3>
        <input type="text" id="search" placeholder="Search..." onkeyup="filterTable()">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="orderTable">
                <?php
                $result = $conn->query("SELECT * FROM orders");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='{$row['id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['product_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>Ksh {$row['total_price']}</td>
                            <td><span class='status {$row['status']}'>{$row['status']}</span></td>
                            <td>
                                <button class='edit-btn' onclick='openEditModal({$row['id']})'>Edit</button>
                                <button class='delete-btn' onclick='deleteOrder({$row['id']})'>Delete</button>
                            </td>
                        </tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Order Modal -->
<div class="modal" id="orderModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Add New Order</h3>
        <form action="add_order.php" method="POST">
            <label for="customer_name">Customer Name</label>
            <input type="text" name="customer_name" required>

            <label for="product_name">Item</label>
            <input type="text" name="product_name" required>

            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" required>

            <label for="total_price">Total Price</label>
            <input type="number" name="total_price" required>

            <label for="status">Status</label>
            <select name="status">
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
            </select>

            <button type="submit">Add Order</button>
            <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="modal" id="editOrderModal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Order</h3>
        <form id="editOrderForm">
            <input type="hidden" name="id" id="edit_order_id">

            <label for="edit_customer_name">Customer Name</label>
            <input type="text" name="customer_name" id="edit_customer_name" required>

            <label for="edit_product_name">Item</label>
            <input type="text" name="product_name" id="edit_product_name" required>

            <label for="edit_quantity">Quantity</label>
            <input type="number" name="quantity" id="edit_quantity" required>

            <label for="edit_total_price">Total Price</label>
            <input type="number" name="total_price" id="edit_total_price" required>

            <label for="edit_status">Status</label>
            <select name="status" id="edit_status">
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
            </select>

            <button type="submit">Update Order</button>
            <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById("orderModal").style.display = "block";
}

function closeModal() {
    document.getElementById("orderModal").style.display = "none";
}

function filterTable() {
    let search = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("#orderTable tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(search) ? "" : "none";
    });
}

function openEditModal(orderId) {
    // Open the modal
    document.getElementById("editOrderModal").style.display = "block";

    // Fetch order details using AJAX
    fetch("get_order.php?id=" + orderId)
        .then(response => response.json())
        .then(data => {
            // Populate the form with order data
            document.getElementById("edit_order_id").value = data.id;
            document.getElementById("edit_customer_name").value = data.customer_name;
            document.getElementById("edit_product_name").value = data.product_name;
            document.getElementById("edit_quantity").value = data.quantity;
            document.getElementById("edit_total_price").value = data.total_price;
            document.getElementById("edit_status").value = data.status;
        })
        .catch(error => console.error("Error fetching order:", error));
}

function closeEditModal() {
    document.getElementById("editOrderModal").style.display = "none";
}

// Handle form submission via AJAX
document.getElementById("editOrderForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent normal form submission

    let formData = new FormData(this);

    fetch("update_order.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload(); // Refresh page to reflect changes
    })
    .catch(error => console.error("Error updating order:", error));
});

function deleteOrder(orderId) {
    if (confirm("Are you sure you want to delete this order?")) {
        fetch('delete_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + orderId
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>

</body>
</html>