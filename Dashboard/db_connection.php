<?php
$servername = "localhost";
$username = "root"; // Change if using another user
$password = "root"; // Change if set
$dbname = "kefarm";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
