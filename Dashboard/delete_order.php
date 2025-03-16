<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access");
}

include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $orderId = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        echo "Order deleted successfully!";
    } else {
        echo "Error deleting order.";
    }

    $stmt->close();
    $conn->close();
}
?>
