<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Inventory Item</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Add New Inventory Item</h2>
    <form action="add_inventory.php" method="POST">
        <label>Product Name:</label>
        <input type="text" name="product_name" required>

        <label>Category:</label>
        <input type="text" name="category">

        <label>Quantity:</label>
        <input type="number" name="quantity" required>

        <label>Unit Price:</label>
        <input type="text" name="unit_price" required>

        <label>Stock Level Alert:</label>
        <input type="number" name="stock_level_alert" value="10">

        <label>Supplier:</label>
        <input type="text" name="supplier">

        <button type="submit">Add Item</button>
        <button type="button" onclick="window.close();">Cancel</button>
    </form>
</body>
</html>
