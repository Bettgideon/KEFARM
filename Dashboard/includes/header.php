<?php
// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
        }

        /* Menu Toggle Button */
        .menu-toggle {
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            display: none;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header class="header">
    <button class="menu-toggle" id="menuToggle">☰</button>
    <h2>KEFARM</h2>
</header>

<!-- Include Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- JavaScript for Sidebar Toggle -->
<script>
   document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.getElementById("menuToggle");
    const sidebar = document.getElementById("sidebar");

    if (menuToggle && sidebar) { // Ensure elements exist
        menuToggle.addEventListener("click", function() {
            sidebar.classList.toggle("active");

            // Change button icon for open/close effect
            if (sidebar.classList.contains("active")) {
                menuToggle.innerHTML = "✖"; // Close icon
            } else {
                menuToggle.innerHTML = "☰"; // Menu icon
            }
        });
    }
});

</script>
