<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // MAMP default
define('DB_PASS', 'root'); // MAMP default
define('DB_NAME', 'kefarm');

// Application Paths
define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/KEFARM/');
define('ADMIN_URL', BASE_URL . 'Dashboard/');

// Security
define('CSRF_ENABLED', true);
define('DEBUG_MODE', true);

// Database Connection Function
function getDBConnection() {
    static $conn;
    
    if (!$conn) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("DB Connection failed: " . $conn->connect_error);
            die(DEBUG_MODE ? "DB Error: " . $conn->connect_error : "System error. Please try later.");
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}