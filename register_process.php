<?php
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
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Input Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All fields are required!"]);
        exit();
    }

    // Email Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid email format!"]);
        exit();
    }

    // Password Validation (Minimum 8 chars, 1 special character)
    if (strlen($password) < 8 || !preg_match('/[\W_]/', $password)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long and include a special character!"]);
        exit();
    }

    // Confirm Password Check
    if ($password !== $confirm_password) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Passwords do not match!"]);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["status" => "error", "message" => "Email already registered!"]);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Insert User Data into Database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "Registration successful!", "redirect" => "login.html"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
