<?php
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $stock_level_alert = $_POST['stock_level_alert'];
    $supplier = $_POST['supplier'];

    $stmt = $conn->prepare("UPDATE inventory SET product_name=?, category=?, quantity=?, unit_price=?, stock_level_alert=?, supplier=? WHERE id=?");
    $stmt->bind_param("ssiiisi", $product_name, $category, $quantity, $unit_price, $stock_level_alert, $supplier, $id);
    
    if ($stmt->execute()) {
        echo "Item updated successfully!";
    } else {
        echo "Error updating item.";
    }
    
    $stmt->close();
}
$conn->close();
?>
