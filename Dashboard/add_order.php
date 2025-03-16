<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, product_name, quantity, total_price, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $customer_name, $product_name, $quantity, $total_price, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Order added successfully!'); window.location.href='orders_sales.php';</script>";
    } else {
        echo "<script>alert('Failed to add order!');</script>";
    }
    $stmt->close();
    $conn->close();
}
?>
