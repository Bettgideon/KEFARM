<?php
$servername = "localhost"; // Change if using a remote server
$username = "root"; // Change if using another MySQL user
$password = "root"; // Change if your MySQL user has a password
$database = "kefarm"; // Ensure this matches your actual database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding to avoid charset issues
$conn->set_charset("utf8");
?>
