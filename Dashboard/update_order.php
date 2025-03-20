<?php
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['id'];
    $customer_name = $_POST['customer_name'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];

    $query = "UPDATE orders SET customer_name=?, product_name=?, quantity=?, total_price=?, status=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssidsi", $customer_name, $product_name, $quantity, $total_price, $status, $order_id);
    
    if ($stmt->execute()) {
        echo "Order updated successfully!";
    } else {
        echo "Error updating order: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
