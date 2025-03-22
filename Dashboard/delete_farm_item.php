<?php
session_start(); // Ensure the session is started

include 'db_connection.php'; // Ensure correct path
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $id = intval($data['id']); // Convert to integer for security

    $stmt = $conn->prepare("DELETE FROM farm_items WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Item deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete item."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
