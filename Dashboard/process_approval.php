<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

checkAdminAccess();

// Validate CSRF
if (CSRF_ENABLED && ($_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? ''))) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'CSRF token mismatch']));
}

// Validate input
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

if (!$id || !in_array($action, ['approve', 'reject'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid input']));
}

// Process approval
try {
    $conn = getDBConnection();
    $status = $action === 'approve' ? 'approved' : 'rejected';
    
    $stmt = $conn->prepare("UPDATE approvals 
                          SET status = ?, 
                              decision_date = NOW(), 
                              decided_by = ? 
                          WHERE approval_id = ?");
    $stmt->bind_param('sii', $status, $_SESSION['user_id'], $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No record updated']);
    }
} catch (Exception $e) {
    error_log("Approval Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}