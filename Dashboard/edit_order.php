<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
include '../db_connect.php';

// Check if order ID is provided
if (!isset($_GET['id'])) {
    die("Order ID not specified.");
}

$order_id = $_GET['id'];

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$stmt->close();
?>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #e8f5e9; /* Light Green */
        margin: 0;
        padding: 0;
    }

    .edit-order-container {
        background: #ffffff; /* White */
        border: 2px solid #2e7d32; /* Dark Green */
        padding: 20px;
        border-radius: 10px;
        max-width: 500px;
        margin: 50px auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    h2 {
        color: #1b5e20; /* Dark Green */
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
        color: #2e7d32;
    }

    input, select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #c8e6c9; /* Light Green Border */
        border-radius: 5px;
        box-sizing: border-box;
    }

    button {
        background: #1b5e20; /* Dark Green */
        color: white;
        border: none;
        padding: 10px 15px;
        margin-top: 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background: #388e3c; /* Slightly lighter green */
    }
</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - KEFARM</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="edit-order-container">
    <h2>Edit Order</h2>
    <form action="update_order.php" method="POST" class="edit-order-form">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

        <label for="customer_name">Customer Name</label>
        <input type="text" name="customer_name" value="<?= htmlspecialchars($order['customer_name']) ?>" required>

        <label for="product_name">Item</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($order['product_name']) ?>" required>

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" value="<?= $order['quantity'] ?>" required>

        <label for="total_price">Total Price</label>
        <input type="number" name="total_price" value="<?= $order['total_price'] ?>" required>

        <label for="status">Status</label>
        <select name="status">
            <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>

        <button type="submit">Update Order</button>
    </form>
</div>

</body>
</html>
