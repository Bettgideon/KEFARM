<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $category = $_POST['category']; // ✅ Changed from 'type' to 'category'
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];

    // Use category instead of type in the SQL statement
    $sql = "UPDATE farm_items SET category='$category', name='$name', quantity=$quantity WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Item updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating item: " . $conn->error]);
    }

    $conn->close();
}
?>
