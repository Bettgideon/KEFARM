<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

// Include Sidebar
include 'includes/sidebar.php';

// Include Header
include 'includes/header.php'; // This already contains the greeting message
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Make sure styles are correctly linked -->
</head>
<style>
    /* Cards Container */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

/* Individual Card Styling */
.card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Hover Effect */
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Icons */
.card i {
    font-size: 40px;
    color: #2e7d32; /* Dark Green */
    margin-bottom: 10px;
}

/* Numbers */
.card h3 {
    font-size: 24px;
    font-weight: bold;
    margin: 10px 0;
    color: #333;
}

/* Descriptions */
.card p {
    font-size: 16px;
    color: #666;
}

</style>
<body>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="cards">
        <div class="card"><i class="fas fa-seedling"></i><h3>150+</h3><p>Farms Registered</p></div>
        <div class="card"><i class="fas fa-shopping-cart"></i><h3>250+</h3><p>Orders Processed</p></div>
        <div class="card"><i class="fas fa-users"></i><h3>500+</h3><p>Active Users</p></div>
        <div class="card"><i class="fas fa-chart-line"></i><h3>Ksh 50K+</h3><p>Revenue</p></div>
    </div>
</div>

</body>
</html>
