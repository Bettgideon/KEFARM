<?php
require_once __DIR__ . '/../../config.php';

function authenticate() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "login.html");
        exit();
    }
}

function isAdmin() {
    return ($_SESSION['user_role'] ?? '') === 'Admin';
}

function checkAdminAccess() {
    authenticate();
    
    if (!isAdmin()) {
        header("HTTP/1.1 403 Forbidden");
        die("Access denied. Admin privileges required.");
    }
}