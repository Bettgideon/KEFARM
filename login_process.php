<?php
session_start();
header("Content-Type: application/json");

// Database Connection
$host = "localhost";
$user = "root";
$password = "root";
$database = "kefarm";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed!"]);
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = $_POST["password"];

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $name, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["user_name"] = $name;
            $_SESSION["user_email"] = $email;

            echo json_encode(["status" => "success", "message" => "Login successful!", "redirect" => "Dashboard/index.php"]);
        } else {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid password!"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "User not found!"]);
    }

    $stmt->close();
}

$conn->close();
?>
