<?php
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']); // Ensuring it's an integer

    $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Item deleted successfully";
    } else {
        echo "Error deleting item";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>
