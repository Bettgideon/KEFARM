<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Farm Item - KEFARM</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <h2>Add Farm Item</h2>
        </header>

        <div class="container">
            <form action="process_add_farm_item.php" method="POST">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" required placeholder="Enter farm item name">

                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">-- Select Category --</option>
                    <option value="Crops">Crops</option>
                    <option value="Livestock">Livestock</option>
                    <option value="Fertilizers">Fertilizers</option>
                    <option value="Equipment">Equipment</option>
                </select>

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required placeholder="Enter quantity">

                <label for="price">Price (Ksh)</label>
                <input type="number" id="price" name="price" required placeholder="Enter price per unit">

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Enter item description"></textarea>

                <button type="submit" class="btn">Add Item</button>
            </form>
        </div>
    </div>

</body>
</html>
