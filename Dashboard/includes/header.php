<?php
// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION["user_name"])) {
    $_SESSION["user_name"] = "Guest"; // Default for non-logged-in users
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEFARM</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="styles.css"> <!-- Ensure you have a styles.css file -->
    
    <style>
        /* Header Styles */
        .header {
            background-color: #1a3e1f; /* Dark Green */
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
            color: #28a745; /* Light Green */
            text-align: center;
            flex-grow: 1;
        }

        /* Welcome Message */
        .welcome-message {
            font-size: 16px;
            margin-right: 20px;
            color: #ffffff;
        }

        /* Responsive Toggle Menu */
        .menu-toggle {
            display: none;
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            margin-left: 15px;
        }

        /* Media Query for Smaller Screens */
        @media (max-width: 768px) {
            .header {
                justify-content: space-between;
                padding: 10px 15px;
            }

            .header h2 {
                font-size: 18px; /* Adjust for smaller screens */
                text-align: left;
            }

            .menu-toggle {
                display: block; /* Show toggle button on mobile */
            }

            .welcome-message {
                font-size: 14px; /* Smaller text for mobile */
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header class="header">
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i> <!-- Hamburger Icon -->
    </button>
    <h2>KEFARM</h2>
    <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</span>
</header>

<!-- Include Sidebar -->
<?php include 'sidebar.php'; ?>

<script>
    function toggleSidebar() {
        document.querySelector(".sidebar").classList.toggle("active");
    }
</script>

</body>
</html>
