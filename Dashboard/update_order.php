<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access");
}
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $customer_name = $_POST['customer_name'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET customer_name=?, product_name=?, quantity=?, total_price=?, status=? WHERE id=?");
    $stmt->bind_param("ssidsi", $customer_name, $product_name, $quantity, $total_price, $status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order updated successfully!'); window.location.href='orders_sales.php';</script>";
    } else {
        echo "<script>alert('Error updating order.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
