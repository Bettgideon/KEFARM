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
    <style>
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
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3 class="text-center">KEFARM</h3>
    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="farm_management.php"><i class="fas fa-seedling"></i> Farm Management</a>
    <a href="orders_sales.php" class="active"><i class="fas fa-shopping-cart"></i> Orders & Sales</a>
    <a href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a>
    <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
    <a href="users.php"><i class="fas fa-users"></i> User Management</a>
    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
                                <button class='edit-btn' onclick='editOrder({$row['id']})'>Edit</button>
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

</body>
</html>
