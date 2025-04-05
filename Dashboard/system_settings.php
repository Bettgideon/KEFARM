<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// Restrict access to Admin users only
if ($_SESSION["user_role"] !== "Admin") {
    header("HTTP/1.1 403 Forbidden");
    die("<div class='alert alert-danger'>Access Denied: Administrator privileges required</div>");
}

// Include database connection (MySQLi)
require_once '../db_connect.php';

// Set current page for sidebar highlighting
$current_page = 'system_settings.php';

// Initialize settings array with default values
$settings = [
    'system_name' => 'KeFarm Management System',
    'system_email' => 'admin@kefarm.com',
    'timezone' => 'Africa/Nairobi',
    'date_format' => 'Y-m-d',
    'time_format' => 'H:i',
    'theme_style' => 'light',
    'records_per_page' => '25',
    // Add more default values as needed
];

// Try to load settings from database
try {
    // First check if table exists
    $result = $conn->query("SELECT 1 FROM system_settings LIMIT 1");
    
    if ($result === false) {
        // Table doesn't exist, create it
        $conn->query("CREATE TABLE IF NOT EXISTS system_settings (
            setting_key VARCHAR(255) PRIMARY KEY,
            setting_value TEXT
        )");
        
        // Insert default settings
        $stmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($settings as $key => $value) {
            $stmt->bind_param("ss", $key, $value);
            $stmt->execute();
        }
        $stmt->close();
    } else {
        // Table exists, load settings
        $result = $conn->query("SELECT setting_key, setting_value FROM system_settings");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            $result->free();
        }
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    // Continue with default values
}

// Include header and sidebar
try {
    include 'includes/header.php';
    include 'includes/sidebar.php';
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error loading components: " . $e->getMessage() . "</div>");
}
?>

<!-- Rest of your HTML remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings | KeFarm Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../assets/img/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Inline CSS -->
    <style>
        :root {
            --kefarm-primary: #2e7d32;
            --kefarm-primary-light: #4caf50;
            --kefarm-primary-dark: #1b5e20;
            --kefarm-secondary: #f8f9fa;
            --kefarm-text: #212529;
            --kefarm-text-light: #6c757d;
            --kefarm-border: #dee2e6;
            --kefarm-success: #388e3c;
            --kefarm-warning: #ffa000;
            --kefarm-danger: #d32f2f;
        }
        
        .settings-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .settings-header {
            background-color: var(--kefarm-primary);
            color: white;
            padding: 1.5rem;
            border-bottom: 4px solid var(--kefarm-primary-dark);
        }
        
        .settings-tabs {
            background-color: var(--kefarm-secondary);
            border-bottom: 1px solid var(--kefarm-border);
        }
        
        .settings-tabs .nav-link {
            color: var(--kefarm-text-light);
            font-weight: 500;
            border: none;
            padding: 1rem 1.75rem;
            transition: all 0.2s;
        }
        
        .settings-tabs .nav-link.active {
            color: var(--kefarm-primary);
            background-color: transparent;
            border-bottom: 3px solid var(--kefarm-primary);
        }
        
        .settings-tabs .nav-link:hover:not(.active) {
            color: var(--kefarm-primary);
            background-color: rgba(46, 125, 50, 0.05);
        }
        
        .settings-content {
            padding: 2rem;
        }
        
        .settings-section {
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--kefarm-border);
        }
        
        .settings-section:last-child {
            border-bottom: none;
            margin-bottom: 1rem;
        }
        
        .section-title {
            color: var(--kefarm-primary);
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        
        .setting-card {
            border: 1px solid var(--kefarm-border);
            border-radius: 0.375rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            background-color: white;
        }
        
        .btn-save {
            background-color: var(--kefarm-primary);
            color: white;
            padding: 0.75rem 1.75rem;
            font-weight: 500;
            border: none;
            transition: background-color 0.2s;
        }
        
        .btn-save:hover {
            background-color: var(--kefarm-primary-dark);
            color: white;
        }
        
        .form-switch .form-check-input:checked {
            background-color: var(--kefarm-primary);
            border-color: var(--kefarm-primary);
        }
        
        .advanced-settings {
            background-color: var(--kefarm-secondary);
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-top: 2rem;
            border-left: 4px solid var(--kefarm-primary);
        }
        
        .setting-description {
            font-size: 0.875rem;
            color: var(--kefarm-text-light);
            margin-top: 0.25rem;
        }
        
        @media (max-width: 992px) {
            .settings-tabs .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .settings-content {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .settings-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .settings-tabs .nav-link {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <div class="main-content">
        <div class="container-fluid px-4">
            <div class="settings-container">
                <!-- Page Header -->
                <div class="settings-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="fas fa-cog me-2"></i>System Configuration</h2>
                            <p class="mb-0">Manage all system settings and configurations</p>
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-outline-light me-2" id="resetBtn">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button class="btn btn-light" id="helpBtn">
                                <i class="fas fa-question-circle me-1"></i> Help
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Settings Tabs -->
                <ul class="nav settings-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="fas fa-globe me-1"></i> General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="farm-tab" data-bs-toggle="tab" data-bs-target="#farm" type="button" role="tab">
                            <i class="fas fa-tractor me-1"></i> Farm
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
                            <i class="fas fa-money-bill-wave me-1"></i> Financial
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                            <i class="fas fa-bell me-1"></i> Notifications
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                            <i class="fas fa-shield-alt me-1"></i> Security
                        </button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content settings-content" id="settingsTabsContent">
                    
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <form id="generalSettingsForm" action="process_settings.php" method="POST">
                            <input type="hidden" name="form_type" value="general_settings">
                            
                            <!-- System Information -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-info-circle"></i> System Information
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="system_name" class="form-label">System Name</label>
                                            <input type="text" class="form-control" id="system_name" name="system_name" 
                                                   value="<?= htmlspecialchars($settings['system_name'] ?? 'KeFarm Management System') ?>" required>
                                            <div class="setting-description">The name displayed throughout the system</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="system_email" class="form-label">System Email</label>
                                            <input type="email" class="form-control" id="system_email" name="system_email" 
                                                   value="<?= htmlspecialchars($settings['system_email'] ?? 'admin@kefarm.com') ?>" required>
                                            <div class="setting-description">Used for system notifications and communications</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_phone" class="form-label">Contact Phone</label>
                                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                                   value="<?= htmlspecialchars($settings['contact_phone'] ?? '+254700000000') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_address" class="form-label">Physical Address</label>
                                            <input type="text" class="form-control" id="contact_address" name="contact_address" 
                                                   value="<?= htmlspecialchars($settings['contact_address'] ?? 'Nairobi, Kenya') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Regional Settings -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-globe-africa"></i> Regional Settings
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <select class="form-select" id="timezone" name="timezone" required>
                                                <option value="Africa/Nairobi" selected>Africa/Nairobi (EAT)</option>
                                                <option value="UTC">UTC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="date_format" class="form-label">Date Format</label>
                                            <select class="form-select" id="date_format" name="date_format" required>
                                                <option value="d/m/Y">DD/MM/YYYY</option>
                                                <option value="m/d/Y">MM/DD/YYYY</option>
                                                <option value="Y-m-d" selected>YYYY-MM-DD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="time_format" class="form-label">Time Format</label>
                                            <select class="form-select" id="time_format" name="time_format" required>
                                                <option value="H:i" selected>24-hour (14:30)</option>
                                                <option value="h:i A">12-hour (2:30 PM)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- UI Settings -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-desktop"></i> Interface Settings
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="theme_style" class="form-label">Theme Style</label>
                                            <select class="form-select" id="theme_style" name="theme_style" required>
                                                <option value="light" selected>Light</option>
                                                <option value="dark">Dark</option>
                                                <option value="auto">Auto (System Preference)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="records_per_page" class="form-label">Records Per Page</label>
                                            <select class="form-select" id="records_per_page" name="records_per_page" required>
                                                <option value="10">10</option>
                                                <option value="25" selected>25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_animations" name="enable_animations" checked>
                                            <label class="form-check-label" for="enable_animations">Enable UI Animations</label>
                                            <div class="setting-description">Toggle smooth transitions and animations</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="compact_mode" name="compact_mode">
                                            <label class="form-check-label" for="compact_mode">Compact Mode</label>
                                            <div class="setting-description">Reduce padding for more compact displays</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-save" name="save_general_settings">
                                    <i class="fas fa-save me-1"></i> Save General Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Farm Settings -->
                    <div class="tab-pane fade" id="farm" role="tabpanel">
                        <form id="farmSettingsForm" action="process_settings.php" method="POST">
                            <input type="hidden" name="form_type" value="farm_settings">
                            
                            <!-- Units and Measurements -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-ruler-combined"></i> Units & Measurements
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="land_unit" class="form-label">Land Unit</label>
                                            <select class="form-select" id="land_unit" name="land_unit" required>
                                                <option value="hectares" selected>Hectares</option>
                                                <option value="acres">Acres</option>
                                                <option value="square_meters">Square Meters</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="harvest_unit" class="form-label">Harvest Unit</label>
                                            <select class="form-select" id="harvest_unit" name="harvest_unit" required>
                                                <option value="kg" selected>Kilograms (kg)</option>
                                                <option value="tons">Metric Tons</option>
                                                <option value="bags">Bags (90kg)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="liquid_unit" class="form-label">Liquid Unit</label>
                                            <select class="form-select" id="liquid_unit" name="liquid_unit" required>
                                                <option value="liters" selected>Liters</option>
                                                <option value="gallons">Gallons</option>
                                                <option value="milliliters">Milliliters</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inventory Management -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-boxes"></i> Inventory Management
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                            <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" 
                                                   value="<?= htmlspecialchars($settings['low_stock_threshold'] ?? '10') ?>" min="1" required>
                                            <div class="setting-description">Trigger warnings when inventory reaches this level</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expiry_alert_days" class="form-label">Expiry Alert Days</label>
                                            <input type="number" class="form-control" id="expiry_alert_days" name="expiry_alert_days" 
                                                   value="<?= htmlspecialchars($settings['expiry_alert_days'] ?? '30') ?>" min="1" required>
                                            <div class="setting-description">Days before expiry to send alerts</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_barcode" name="enable_barcode" checked>
                                            <label class="form-check-label" for="enable_barcode">Enable Barcode Scanning</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_auto_reorder" name="enable_auto_reorder">
                                            <label class="form-check-label" for="enable_auto_reorder">Enable Auto Reorder</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Crop Management -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-seedling"></i> Crop Management
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_crop_rotation" name="enable_crop_rotation" checked>
                                            <label class="form-check-label" for="enable_crop_rotation">Enable Crop Rotation Planner</label>
                                            <div class="setting-description">Suggest crop rotation based on previous plantings</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_pest_alerts" name="enable_pest_alerts" checked>
                                            <label class="form-check-label" for="enable_pest_alerts">Enable Pest/Disease Alerts</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="harvest_forecast_days" class="form-label">Harvest Forecast Period</label>
                                            <select class="form-select" id="harvest_forecast_days" name="harvest_forecast_days">
                                                <option value="7">1 Week</option>
                                                <option value="14" selected>2 Weeks</option>
                                                <option value="30">1 Month</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-save" name="save_farm_settings">
                                    <i class="fas fa-save me-1"></i> Save Farm Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Financial Settings -->
                    <div class="tab-pane fade" id="financial" role="tabpanel">
                        <form id="financialSettingsForm" action="process_settings.php" method="POST">
                            <input type="hidden" name="form_type" value="financial_settings">
                            
                            <!-- Currency Settings -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-money-bill-wave"></i> Currency & Pricing
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">System Currency</label>
                                            <select class="form-select" id="currency" name="currency" required>
                                                <option value="KES" selected>Kenyan Shilling (KES)</option>
                                                <option value="USD">US Dollar (USD)</option>
                                                <option value="EUR">Euro (EUR)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="currency_position" class="form-label">Currency Position</label>
                                            <select class="form-select" id="currency_position" name="currency_position" required>
                                                <option value="before" selected>Before amount (KES 100)</option>
                                                <option value="after">After amount (100 KES)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="decimal_places" class="form-label">Decimal Places</label>
                                            <select class="form-select" id="decimal_places" name="decimal_places" required>
                                                <option value="0" selected>0 (KES 100)</option>
                                                <option value="2">2 (KES 100.00)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Settings -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-credit-card"></i> Payment Settings
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_mpesa" name="enable_mpesa" checked>
                                            <label class="form-check-label" for="enable_mpesa">Enable M-Pesa Payments</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_card" name="enable_card">
                                            <label class="form-check-label" for="enable_card">Enable Card Payments</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_terms" class="form-label">Default Payment Terms</label>
                                            <select class="form-select" id="payment_terms" name="payment_terms">
                                                <option value="net_15">Net 15 Days</option>
                                                <option value="net_30" selected>Net 30 Days</option>
                                                <option value="due_on_receipt">Due on Receipt</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-save" name="save_financial_settings">
                                    <i class="fas fa-save me-1"></i> Save Financial Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Notification Settings -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel">
                        <form id="notificationSettingsForm" action="process_settings.php" method="POST">
                            <input type="hidden" name="form_type" value="notification_settings">
                            
                            <!-- Notification Methods -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-bell"></i> Notification Methods
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_email_notifications" name="enable_email_notifications" checked>
                                            <label class="form-check-label" for="enable_email_notifications">Enable Email Notifications</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_sms_notifications" name="enable_sms_notifications" checked>
                                            <label class="form-check-label" for="enable_sms_notifications">Enable SMS Notifications</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_push_notifications" name="enable_push_notifications">
                                            <label class="form-check-label" for="enable_push_notifications">Enable Push Notifications</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Email Configuration -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-envelope"></i> Email Configuration
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_host" class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="smtp.gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_port" class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="587">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_username" class="form-label">SMTP Username</label>
                                            <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="noreply@kefarm.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_password" class="form-label">SMTP Password</label>
                                            <input type="password" class="form-control" id="smtp_password" name="smtp_password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-save" name="save_notification_settings">
                                    <i class="fas fa-save me-1"></i> Save Notification Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <form id="securitySettingsForm" action="process_settings.php" method="POST">
                            <input type="hidden" name="form_type" value="security_settings">
                            
                            <!-- Authentication -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user-shield"></i> Authentication
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa">
                                            <label class="form-check-label" for="enable_2fa">Enable Two-Factor Authentication (2FA)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="password_expiry" name="password_expiry" checked>
                                            <label class="form-check-label" for="password_expiry">Enable Password Expiry (90 days)</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="login_attempts" name="login_attempts" checked>
                                            <label class="form-check-label" for="login_attempts">Enable Login Attempts Limit (5 attempts)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Password Policy -->
                            <div class="settings-section">
                                <h4 class="section-title">
                                    <i class="fas fa-lock"></i> Password Policy
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="min_password_length" class="form-label">Minimum Password Length</label>
                                            <input type="number" class="form-control" id="min_password_length" name="min_password_length" value="8" min="6" max="32">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="require_mixed_case" name="require_mixed_case" checked>
                                            <label class="form-check-label" for="require_mixed_case">Require Mixed Case (A-Z, a-z)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="require_numbers" name="require_numbers" checked>
                                            <label class="form-check-label" for="require_numbers">Require Numbers (0-9)</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="require_special_chars" name="require_special_chars">
                                            <label class="form-check-label" for="require_special_chars">Require Special Characters (!@#$%^&*)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-save" name="save_security_settings">
                                    <i class="fas fa-save me-1"></i> Save Security Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.form-select').select2({
                minimumResultsForSearch: 10,
                width: '100%'
            });
            
            // Tab functionality
            $('#settingsTabs button').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
            
            // Reset form button
            $('#resetBtn').click(function() {
                if(confirm('Are you sure you want to reset all settings to default values?')) {
                    window.location.reload();
                }
            });
            
            // Help button
            $('#helpBtn').click(function() {
                alert('Help documentation will open in a new window.');
                // window.open('help/system_settings.html', '_blank');
            });
            
            // Form submission handling
            $('form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                
                $.ajax({
                    type: "POST",
                    url: form.attr('action'),
                    data: formData,
                    success: function(response) {
                        alert('Settings saved successfully!');
                    },
                    error: function(xhr, status, error) {
                        alert('Error saving settings: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>