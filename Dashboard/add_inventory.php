<?php
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $stock_level_alert = $_POST['stock_level_alert'];
    $supplier = $_POST['supplier'];
    $added_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");

    // Prepare and execute SQL query
    $stmt = $conn->prepare("INSERT INTO inventory (product_name, category, quantity, unit_price, stock_level_alert, supplier, added_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiissss", $product_name, $category, $quantity, $unit_price, $stock_level_alert, $supplier, $added_at, $updated_at);

    if ($stmt->execute()) {
        echo "Item added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
