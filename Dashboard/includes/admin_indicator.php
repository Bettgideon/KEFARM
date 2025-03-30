<?php if ($_SESSION['user_role'] === 'Admin'): ?>
<div class="admin-indicator">
    <i class="fas fa-shield-alt"></i>
    <span>Administrator Mode</span>
    <div class="admin-badge">ADMIN</div>
</div>
<?php endif; ?>
<style>
    /* Admin Indicators */
.admin-indicator {
    background: linear-gradient(135deg, #d32f2f, #b71c1c);
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.admin-badge {
    background: white;
    color: #d32f2f;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    margin-left: 5px;
}

.admin-only {
    position: relative;
    border-left: 3px solid #d32f2f;
}

.admin-only::after {
    content: "ADMIN";
    position: absolute;
    top: 10px;
    right: 10px;
    background: #d32f2f;
    color: white;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
}
</style>