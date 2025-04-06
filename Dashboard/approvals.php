<?php
require_once __DIR__ . '/includes/auth.php';
checkAdminAccess();

$conn = getDBConnection();

// Pagination configuration
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Search and filter functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Get approvals count
$count_query = "SELECT COUNT(*) FROM approvals WHERE status = 'pending'";
if ($search) {
    $count_query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}
if ($filter !== 'all') {
    $count_query .= " AND approval_type = '$filter'";
}
$total = $conn->query($count_query)->fetch_row()[0];

// Get approvals data
$query = "SELECT a.*, u.name as requester_name 
          FROM approvals a
          JOIN users u ON a.user_id = u.id
          WHERE a.status = 'pending'";

if ($search) {
    $query .= " AND (a.title LIKE '%$search%' OR a.description LIKE '%$search%')";
}
if ($filter !== 'all') {
    $query .= " AND a.approval_type = '$filter'";
}

$query .= " ORDER BY a.request_date DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$approvals = $result->fetch_all(MYSQLI_ASSOC);

$page_title = "Pending Approvals";
require_once __DIR__ . '/includes/header.php';
// Include other files


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approvals</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

</body>
<style>
/* Main Container */
.approvals-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Styles */
.approvals-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    gap: 20px;
}

.approvals-header h1 {
    color: #2c3e50;
    font-size: 28px;
    margin: 0;
}

.approvals-header h1 i {
    margin-right: 10px;
    color: #3498db;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

.pending-count {
    background-color: #e74c3c;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 600;
}

/* Search and Filter Controls */
.approvals-controls {
    display: flex;
    gap: 15px;
}

.search-form .input-group {
    width: 250px;
}

.btn-search {
    background-color: #3498db;
    color: white;
}

.filter-select {
    width: 180px;
}

/* Approval Cards Grid */
.approvals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.approval-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 4px solid;
}

.approval-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Card Type Colors */
.approval-card.inventory {
    border-left-color: #f39c12;
}

.approval-card.user_access {
    border-left-color: #3498db;
}

.approval-card.financial {
    border-left-color: #2ecc71;
}

/* Card Header */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.approval-meta {
    display: flex;
    align-items: center;
    gap: 10px;
}

.approval-id {
    color: #7f8c8d;
    font-size: 13px;
    font-weight: 500;
}

.approval-type {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.approval-type.inventory {
    background-color: rgba(243, 156, 18, 0.1);
    color: #f39c12;
}

.approval-type.user_access {
    background-color: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.approval-type.financial {
    background-color: rgba(46, 204, 113, 0.1);
    color: #2ecc71;
}

.approval-date {
    font-size: 12px;
    color: #7f8c8d;
}

/* Card Body */
.card-body {
    padding: 20px;
}

.approval-title {
    font-size: 18px;
    margin: 0 0 10px 0;
    color: #2c3e50;
}

.approval-requester {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 15px;
}

.approval-requester i {
    margin-right: 5px;
    color: #3498db;
}

.approval-details {
    margin-top: 15px;
    font-size: 14px;
    color: #34495e;
    line-height: 1.6;
}

.detail-row {
    display: flex;
    margin-bottom: 8px;
}

.detail-label {
    font-weight: 600;
    color: #2c3e50;
    min-width: 120px;
}

.detail-value {
    color: #7f8c8d;
}

/* Card Footer */
.card-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 15px;
    background-color: #f8f9fa;
    border-top: 1px solid #eee;
}

.btn-approve, .btn-reject, .btn-details {
    padding: 8px 15px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-approve {
    background-color: #2ecc71;
    color: white;
}

.btn-approve:hover {
    background-color: #27ae60;
}

.btn-reject {
    background-color: #e74c3c;
    color: white;
}

.btn-reject:hover {
    background-color: #c0392b;
}

.btn-details {
    background-color: #3498db;
    color: white;
}

.btn-details:hover {
    background-color: #2980b9;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 50px 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.empty-state i {
    font-size: 50px;
    color: #2ecc71;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    color: #7f8c8d;
    font-size: 15px;
}

/* Pagination */
.approvals-pagination {
    margin-top: 30px;
}

.pagination {
    justify-content: center;
}

.page-item.active .page-link {
    background-color: #3498db;
    border-color: #3498db;
}

.page-link {
    color: #3498db;
    border: 1px solid #dee2e6;
    padding: 8px 15px;
}

.page-link:hover {
    color: #2980b9;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-overlay .spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .approvals-grid {
        grid-template-columns: 1fr;
    }
    
    .approvals-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .approvals-controls {
        width: 100%;
        flex-direction: column;
    }
    
    .search-form .input-group {
        width: 100%;
    }
    
    .filter-select {
        width: 100%;
    }
}
</style>

<div class="approvals-container">
    <div class="approvals-header">
        <h1><i class="fas fa-clipboard-check"></i> Pending Approvals</h1>
        <div class="header-actions">
            <span class="badge pending-count"><?= $total ?> pending</span>
            
            <!-- Search and Filter -->
            <div class="approvals-controls">
                <form method="GET" class="search-form">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search approvals..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <select class="form-select filter-select" onchange="this.form.submit()" name="filter">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Types</option>
                    <option value="inventory" <?= $filter === 'inventory' ? 'selected' : '' ?>>Inventory</option>
                    <option value="user_access" <?= $filter === 'user_access' ? 'selected' : '' ?>>User Access</option>
                    <option value="financial" <?= $filter === 'financial' ? 'selected' : '' ?>>Financial</option>
                </select>
            </div>
        </div>
    </div>

    <?php if (empty($approvals)): ?>
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3>No pending approvals found</h3>
            <p>All approval requests have been processed</p>
        </div>
    <?php else: ?>
        <div class="approvals-grid">
            <?php foreach ($approvals as $approval): ?>
                <div class="approval-card <?= $approval['approval_type'] ?>">
                    <div class="card-header">
                        <div class="approval-meta">
                            <span class="approval-id">#<?= $approval['approval_id'] ?></span>
                            <span class="approval-type <?= $approval['approval_type'] ?>">
                                <?= ucfirst($approval['approval_type']) ?>
                            </span>
                        </div>
                        <div class="approval-date">
                            <?= date('M j, Y g:i A', strtotime($approval['request_date'])) ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="approval-title"><?= htmlspecialchars($approval['title']) ?></h3>
                        <p class="approval-requester">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($approval['requester_name']) ?>
                        </p>
                        
                        <div class="approval-details">
                            <p><?= htmlspecialchars($approval['description']) ?></p>
                            
                            <?php if ($approval['approval_type'] === 'inventory'): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Item:</span>
                                    <span class="detail-value"><?= htmlspecialchars($approval['item_name']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Quantity:</span>
                                    <span class="detail-value"><?= (int)$approval['quantity'] ?></span>
                                </div>
                            <?php elseif ($approval['approval_type'] === 'user_access'): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Requested Role:</span>
                                    <span class="detail-value"><?= htmlspecialchars($approval['new_role']) ?></span>
                                </div>
                            <?php elseif ($approval['approval_type'] === 'financial'): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Amount:</span>
                                    <span class="detail-value">Ksh <?= number_format($approval['amount'], 2) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button onclick="processApproval(<?= $approval['approval_id'] ?>, 'approve')" 
                                class="btn btn-approve">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button onclick="processApproval(<?= $approval['approval_id'] ?>, 'reject')" 
                                class="btn btn-reject">
                            <i class="fas fa-times"></i> Reject
                        </button>
                        <button onclick="viewApprovalDetails(<?= $approval['approval_id'] ?>)" 
                                class="btn btn-details">
                            <i class="fas fa-eye"></i> Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total > $per_page): ?>
            <nav class="approvals-pagination">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php 
                    $total_pages = ceil($total / $per_page);
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1&search='.urlencode($search).'&filter='.$filter.'">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++): 
                    ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; 
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page='.$total_pages.'&search='.urlencode($search).'&filter='.$filter.'">'.$total_pages.'</a></li>';
                    }
                    ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// Enhanced approval processing with confirmation
function processApproval(id, action) {
    if (!confirm(`Are you sure you want to ${action} this request?`)) {
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: '<?= ADMIN_URL ?>api/process_approval.php',
        method: 'POST',
        data: {
            id: id,
            action: action,
            csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
        },
        success: function(response) {
            if (response.success) {
                Toastify({
                    text: `Request ${action}d successfully`,
                    duration: 3000,
                    close: true,
                    backgroundColor: action === 'approve' ? '#28a745' : '#dc3545'
                }).showToast();
                
                // Reload after 1 second
                setTimeout(() => location.reload(), 1000);
            } else {
                hideLoading();
                Toastify({
                    text: response.message || `Failed to ${action} request`,
                    duration: 3000,
                    close: true,
                    backgroundColor: '#dc3545'
                }).showToast();
            }
        },
        error: function() {
            hideLoading();
            Toastify({
                text: "Network error occurred",
                duration: 3000,
                close: true,
                backgroundColor: '#dc3545'
            }).showToast();
        }
    });
}

function viewApprovalDetails(id) {
    window.location.href = `approval_details.php?id=${id}`;
}

function showLoading() {
    $('.approvals-container').append('<div class="loading-overlay"><div class="spinner"></div></div>');
}

function hideLoading() {
    $('.loading-overlay').remove();
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';