<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 0;
    $price = isset($_POST['price']) ? $_POST['price'] : 0.00;
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $created_at = date("Y-m-d H:i:s"); // Get current timestamp

    // Validate required fields
    if (empty($name) || empty($category) || $quantity == 0) {
        echo json_encode(["success" => false, "message" => "Please fill in all required fields."]);
        exit();
    }

    // Insert query
    $sql = "INSERT INTO farm_items (category, name, quantity, price, description, created_at) 
            VALUES ('$category', '$name', $quantity, $price, '$description', '$created_at')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Item added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding item: " . $conn->error]);
    }

    $conn->close();
}
?>
