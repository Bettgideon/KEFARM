<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {
    die("Access denied.");
}

$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!in_array($action, ['approve', 'reject']) || !in_array($type, ['inventory', 'user_access', 'financial'])) {
    die("Invalid request.");
}

// Update status in the database
$table_map = [
    'inventory' => 'inventory_requests',
    'user_access' => 'user_requests',
    'financial' => 'payment_requests'
];

$table = $table_map[$type];
$new_status = $action === 'approve' ? 'approved' : 'rejected';
$query = "UPDATE $table SET status = '$new_status', reviewed_by = {$_SESSION['user_id']}, review_date = NOW() WHERE id = $id";

if (mysqli_query($conn, $query)) {
    // Log the action
    $log_query = "INSERT INTO audit_log (user_id, action, details) VALUES ({$_SESSION['user_id']}, '$action', '$type request ID $id')";
    mysqli_query($conn, $log_query);
    header("Location: approvals.php?success=action_completed");
} else {
    header("Location: approvals.php?error=update_failed");
}