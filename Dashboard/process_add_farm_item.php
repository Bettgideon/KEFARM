<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Database Connection
$host = "localhost";
$user = "root";
$password = "root";
$database = "kefarm";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = htmlspecialchars(trim($_POST["item_name"]));
    $category = htmlspecialchars(trim($_POST["category"]));
    $quantity = (int) $_POST["quantity"];
    $price = (float) $_POST["price"];
    $description = htmlspecialchars(trim($_POST["description"]));

    if (empty($item_name) || empty($category) || empty($quantity) || empty($price)) {
        $_SESSION["error"] = "All fields are required!";
        header("Location: add_farm_item.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO farm_items (name, category, quantity, price, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $item_name, $category, $quantity, $price, $description);

    if ($stmt->execute()) {
        $_SESSION["success"] = "Farm item added successfully!";
    } else {
        $_SESSION["error"] = "Error adding item: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: add_farm_item.php");
    exit();
}
?>
