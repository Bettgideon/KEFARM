
<?php include 'includes/sidebar.php'; ?> <!-- Include Sidebar -->
<?php include 'includes/header.php'; ?> <!-- Include Header -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders & Sales - KEFARM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <styl>
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

        header {
            background: #1e5631;
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
            color: #28a745;
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
    

        /* KEFARM Color Theme */
        .modal-content {
            background: #e6f4ea; /* Light green background */
            border-radius: 10px;
            padding: 20px;
            width: 50%;
            max-width: 500px;
            margin: auto;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal h3 {
            background: #1e5631; /* Dark green header */
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .modal input, .modal select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #1e5631;
            border-radius: 5px;
            outline: none;
            background: #f0fff4; /* Very light green */
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
        .menu-item.active {
    background-color: #28a745; /* Change to your desired color */
    color: white;
}

    </style>
</head>
<body>

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

function editOrder(orderId) {
    window.location.href = 'edit_order.php?id=' + orderId;
}

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
        </form>
    </div>
</div>
<script>
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
</script>

</body>
</html>
