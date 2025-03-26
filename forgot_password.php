<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "kefarm"; // Make sure this matches your actual database

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!empty($email)) {
        // Check if email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>A password reset link has been sent to your email.</p>";
        } else {
            echo "<p style='color: red;'>Email not found.</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>Please enter an email address.</p>";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<style>
    /* Reset default margin & padding */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Centering the form */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Form container */
.form-container {
    background: white;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    text-align: center;
    width: 100%;
    max-width: 400px;
}

/* Heading */
h2 {
    color: #333;
    margin-bottom: 15px;
}

/* Input field */
input[type="email"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

/* Submit Button */
button {
    width: 100%;
    padding: 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background-color: #218838;
}

/* Back to Login */
.back-link {
    display: block;
    margin-top: 15px;
    text-decoration: none;
    color: #007bff;
    font-size: 14px;
}

.back-link:hover {
    text-decoration: underline;
}

</style>
<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <?php if (!empty($msg)) { echo "<p class='message'>$msg</p>"; } ?>
        <form method="POST" action="">
            <label>Email:</label>
            <input type="email" name="email" required placeholder="Enter your email">
            <button type="submit">Submit</button>
        </form>
        <p><a href="login.html" class="back-link">Back to Login</a></p>
    </div>
</body>
</html>
