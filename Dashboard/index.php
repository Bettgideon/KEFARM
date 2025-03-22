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
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #2E8B57; /* Dark green */
            padding: 15px 20px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff; /* White text */
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        /* Main Content */
        .main-content {
            margin-top: 80px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        /* Push content to the right on desktop */
        @media (min-width: 769px) {
            .main-content {
                margin-left: 250px; /* Adjust for sidebar width */
            }
        }

        /* Push content to the right when sidebar is active on mobile */
        @media (max-width: 768px) {
            .main-content.active {
                margin-left: 250px;
            }
        }

        /* Cards */
        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            flex: 1 1 calc(25% - 20px);
            text-align: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card i {
            font-size: 40px;
            color: #228B22; /* Dark green */
            margin-bottom: 10px;
        }

        .card h3 {
            margin: 10px 0;
            font-size: 24px;
            color: #333;
        }

        .card p {
            margin: 0;
            color: #777;
        }

        @media (max-width: 768px) {
            .card {
                flex: 1 1 calc(50% - 20px); /* 2 columns on tablets */
            }
        }

        @media (max-width: 480px) {
            .card {
                flex: 1 1 100%; /* 1 column on mobile */
            }
        }
    </style>
</head>
<body>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <!-- Header is already included in includes/header.php -->
    
    <div class="cards">
        <div class="card"><i class="fas fa-seedling"></i><h3>150+</h3><p>Farms Registered</p></div>
        <div class="card"><i class="fas fa-shopping-cart"></i><h3>250+</h3><p>Orders Processed</p></div>
        <div class="card"><i class="fas fa-users"></i><h3>500+</h3><p>Active Users</p></div>
        <div class="card"><i class="fas fa-chart-line"></i><h3>Ksh 50K+</h3><p>Revenue</p></div>
    </div>
</div>

</body>
</html>