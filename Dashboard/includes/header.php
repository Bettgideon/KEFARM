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

// Get the base URL dynamically
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$project_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', realpath(__DIR__)));
$full_url = $base_url . $project_path;
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

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .header h2 {
            flex-grow: 1;
            text-align: center;
            font-size: 22px;
            color: #ffffff;
            margin: 0;
        }

        .welcome-message {
            font-size: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-right: 20px;
            flex-shrink: 0;
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

            .logo {
                width: 36px;
                height: 36px;
            }

            .header h2 {
                font-size: 18px;
            }

            .welcome-message {
                font-size: 14px;
                max-width: 120px;
            }
            
            main {
                padding-top: 60px;
            }
        }

        @media (max-width: 480px) {
            .logo {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header class="header">
    <div class="logo-container">
        <img src="<?php echo $full_url; ?>/assets/images/Logo.png" alt="KEFARM Logo" class="logo">
    </div>
    <h2>KEFARM</h2>
    <span class="welcome-message"><?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</span>
</header>

<main>