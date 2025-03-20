<?php
include '../db_connect.php';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Order not found"]);
    }

    $stmt->close();
    $conn->close();
}
?>
