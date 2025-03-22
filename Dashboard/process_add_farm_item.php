<?php
session_start();
header("Content-Type: application/json"); // Ensure JSON response

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Database Connection
$host = "localhost";
$user = "root";
$password = "root";
$database = "kefarm";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = htmlspecialchars(trim($_POST["item_name"]));
    $category = htmlspecialchars(trim($_POST["category"]));
    $quantity = filter_var($_POST["quantity"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $price = filter_var($_POST["price"], FILTER_VALIDATE_FLOAT, ["options" => ["min_range" => 0]]);
    $description = !empty(trim($_POST["description"])) ? htmlspecialchars(trim($_POST["description"])) : NULL;

    if (!$quantity || !$price) {
        echo json_encode(["success" => false, "message" => "Invalid quantity or price"]);
        exit();
    }

    if (empty($item_name) || empty($category)) {
        echo json_encode(["success" => false, "message" => "Item name and category are required"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO farm_items (name, category, quantity, price, description) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssids", $item_name, $category, $quantity, $price, $description);
    
    if ($stmt->execute()) {
        $inserted_id = $stmt->insert_id;
        echo json_encode([
            "success" => true,
            "id" => $inserted_id,
            "item_name" => $item_name,
            "category" => $category,
            "quantity" => $quantity,
            "price" => $price,
            "description" => $description,
            "date_added" => date("Y-m-d") // Use current date
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding item: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
