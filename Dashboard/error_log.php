<?php
// error_log.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Custom error logging function
function logError($errorMessage) {
    $logFile = __DIR__ . '/../logs/error_log.txt'; // Make sure this folder exists and is writable
    $logEntry = "[" . date("Y-m-d H:i:s") . "] " . $errorMessage . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Get error message from session
$errorMsg = "No error to display.";
if (isset($_SESSION['error_message'])) {
    $errorMsg = $_SESSION['error_message'];
    logError($errorMsg);
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error Log - KEFARM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .error-box h2 {
            color: #dc3545;
        }
        .error-icon {
            font-size: 50px;
        }
    </style>
</head>
<body>

<div class="error-box">
    <div class="error-icon mb-3">⚠️</div>
    <h2 class="mb-3">An Error Occurred</h2>
    <p class="text-muted"><?= htmlspecialchars($errorMsg) ?></p>
    <a href="admin_panel.php" class="btn btn-danger mt-4">← Back to Admin Panel</a>
</div>

</body>
</html>
