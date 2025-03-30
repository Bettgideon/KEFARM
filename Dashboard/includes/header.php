<?php
// header.php

// Prevent caching to ensure the greeting updates dynamically
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set the correct timezone
date_default_timezone_set("Africa/Nairobi");

// Set default user name if not logged in
if (!isset($_SESSION["user_name"])) {
    $_SESSION["user_name"] = "Guest";
    $_SESSION["user_role"] = "Guest"; // Add default role
}

// Dynamic greeting based on time
$hour = date("H");
if ($hour < 12) {
    $greeting = "Good morning";
} elseif ($hour < 16) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

// Check if user is admin
$isAdmin = isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEFARM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #1a3e1f;
            color: white;
            padding: 10px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            gap: 15px;
        }

        .header h2 {
            flex-grow: 1;
            text-align: center;
            font-size: 22px;
            color: #ffffff;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .welcome-message {
            font-size: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-shrink: 0;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .badge-admin {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid #ffcc00;
            color: #ffcc00;
        }

        .badge-user {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #ffffff;
        }

        .badge-guest {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid #aaaaaa;
            color: #aaaaaa;
        }

        /* Ensure content isn't hidden behind fixed header */
        main {
            padding-top: 70px;
        }

        @media (max-width: 768px) {
            .header {
                padding: 8px 12px;
                gap: 10px;
            }

            .header h2 {
                font-size: 18px;
            }

            .user-info {
                gap: 8px;
            }

            .welcome-message {
                font-size: 14px;
                max-width: 120px;
            }

            .user-badge {
                padding: 2px 6px;
                font-size: 12px;
            }
            
            main {
                padding-top: 60px;
            }
        }

        @media (max-width: 480px) {
            .welcome-message {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header class="header">
    <h2>KEFARM</h2>
    <div class="user-info">
        <span class="welcome-message"><?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></span>
        <span class="user-badge <?php echo 'badge-' . strtolower($_SESSION["user_role"] ?? 'guest'); ?>">
            <?php if ($isAdmin): ?>
                <i class="fas fa-shield-alt"></i>
            <?php elseif ($_SESSION["user_role"] === "User"): ?>
                <i class="fas fa-user"></i>
            <?php else: ?>
                <i class="fas fa-user-clock"></i>
            <?php endif; ?>
            <?php echo htmlspecialchars($_SESSION["user_role"] ?? 'Guest'); ?>
        </span>
    </div>
</header>

<main>