<?php
/**
 * Enhanced PMS Medicines List with Dark Mode
 */

// Load PMS config first
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

// Load main config for authentication functions only
require_once __DIR__ . '/../../../config.php';
requireLogin();

// Connect to database and get medicines with pagination
try {
    $conn = getPMSDBConnection();
    $limit = 10; // Items per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Get total count
    $totalMedicines = $conn->query("SELECT COUNT(*) as count FROM pms_medicines")->fetch()['count'];
    $totalPages = ceil($totalMedicines / $limit);

    // Get medicines for current page
    $medicines = $conn->query("SELECT * FROM pms_medicines ORDER BY generic_name ASC LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
    $conn = null; // Close connection
} catch (Exception $e) {
    $medicines = [];
    $totalPages = 0;
    $page = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💊 Pharmacy Pro - Medicines Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
    <style>
        /* Enhanced PMS Medicines List with Dark Mode */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --pharmacy-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);

            /* Light Theme Variables */
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #868e96;
            --border-color: #dee2e6;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --card-bg: #ffffff;
            --navbar-bg: rgba(255, 255, 255, 0.95);
        }

        /* Dark Theme Variables */
        [data-theme="dark"] {
            --primary-gradient: linear-gradient(135deg, #9f7aea 0%, #667eea 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            --danger-gradient: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            --pharmacy-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);

            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --bg-tertiary: #404040;
            --text-primary: #ffffff;
            --text-secondary: #b3b3b3;
            --text-muted: #888888;
            --border-color: #4a4a4a;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.3);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.5);
            --card-bg: #2d2d2d;
            --navbar-bg: rgba(26, 26, 26, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar Navigation */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: var(--card-bg);
            border-right: 2px solid var(--border-color);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .sidebar-header h2 {
            color: var(--text-primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .sidebar-nav {
            padding: 1rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: var(--pharmacy-gradient);
            color: white;
        }

        .sidebar-nav a i {
            font-size: 1.2rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-open {
            margin-left: 280px;
        }

        /* Hamburger Menu */
        .menu-toggle {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: var(--bg-secondary);
        }

        /* Footer */
        .footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            margin-top: 3rem;
        }

        .footer p {
            margin: 0;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--text-primary);
        }

        /* Modern Navbar */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--pharmacy-gradient);
        }

        .nav-brand {
            font-size: 2rem;
            font-weight: 800;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-brand i {
            font-size: 2.2rem;
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-menu a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu a:hover {
            color: var(--text-primary);
            background: var(--bg-secondary);
            transform: translateY(-2px);
        }

        .nav-menu a i {
            font-size: 1.1rem;
        }

        /* Enhanced Content */
        .content {
            padding: 3rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .page-header p {
            font-size: 1.3rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Enhanced Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .data-table thead {
            background: var(--pharmacy-gradient);
            color: white;
        }

        .data-table th {
            padding: 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: none;
        }

        .data-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .data-table tbody tr {
            transition: all 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: var(--bg-secondary);
            transform: translateX(4px);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-active {
            background: rgba(72, 187, 120, 0.2);
            color: #10b981;
        }

        .status-discontinued {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .status-recall {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        /* Enhanced No Data Message */
        .no-data {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .no-data h3 {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .no-data p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .no-data-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .no-data-btn {
            padding: 1rem 2rem;
            background: var(--pharmacy-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .no-data-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.4);
        }

        /* Search and Filter Section */
        .controls-section {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .controls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            position: relative;
        }

        .control-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .control-input {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .control-input:focus {
            outline: none;
            border-color: #ff6b9d;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        .control-btn {
            padding: 0.75rem 2rem;
            background: var(--pharmacy-gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        /* Medicine Cards for Mobile */
        .medicines-grid {
            display: none;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .medicine-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .medicine-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .medicine-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .medicine-code {
            background: var(--pharmacy-gradient);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .medicine-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .medicine-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .medicine-details {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* Theme Toggle */
        .theme-toggle {
            background: var(--border-color);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .theme-toggle:hover {
            background: var(--bg-secondary);
            border-color: #ff6b9d;
            transform: scale(1.05);
        }

        .theme-toggle .toggle-ball {
            width: 18px;
            height: 18px;
            background: var(--pharmacy-gradient);
            border-radius: 50%;
            transition: transform 0.3s ease;
            transform: translateX(0);
        }

        [data-theme="dark"] .theme-toggle .toggle-ball {
            transform: translateX(20px);
        }

        .theme-toggle i {
            font-size: 0.75rem;
            color: var(--text-primary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content.sidebar-open {
                margin-left: 0;
            }

            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .content {
                padding: 2rem 1rem;
            }

            .page-header h1 {
                font-size: 2.5rem;
            }

            .page-header p {
                font-size: 1.1rem;
            }

            .controls-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .data-table {
                display: none;
            }

            .medicines-grid {
                display: grid;
            }

            .no-data {
                padding: 3rem 1rem;
            }

            .no-data-actions {
                flex-direction: column;
                align-items: center;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .medicine-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            background: var(--card-bg);
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        /* Chart Section */
        .chart-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .chart-section h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        /* Small Button */
        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            background: var(--pharmacy-gradient);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: 10% auto;
            padding: 2rem;
            border-radius: 16px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        .close {
            color: var(--text-secondary);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: var(--text-primary);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .suggestions {
            position: absolute;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            width: 100%;
            display: none;
        }

        .suggestions div {
            padding: 0.5rem;
            cursor: pointer;
            color: var(--text-primary);
        }

        .suggestions div:hover {
            background: var(--bg-secondary);
        }

        /* New Enhanced Elements */
        
        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            background: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-btn:hover {
            background: var(--bg-secondary);
            transform: translateY(-2px);
        }

        .action-btn.primary {
            background: var(--pharmacy-gradient);
            color: white;
            border: none;
        }

        .action-btn.primary:hover {
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        /* Enhanced Table Actions */
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-icon:hover {
            transform: scale(1.1);
        }

        .action-edit {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .action-view {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .action-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Enhanced Pagination */
        .pagination a.active {
            background: var(--pharmacy-gradient);
            color: white;
            border-color: transparent;
        }

        .pagination a:hover:not(.active) {
            background: var(--bg-secondary);
        }

        /* Search Highlight */
        .highlight {
            background-color: rgba(255, 107, 157, 0.2);
            padding: 0.1rem 0.2rem;
            border-radius: 4px;
        }

        /* Quick Actions Bar */
        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            padding: 0.75rem 1.5rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quick-action-btn:hover {
            background: var(--bg-secondary);
            transform: translateY(-2px);
        }

        /* Enhanced Form Elements */
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b9d;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        /* Enhanced Select */
        .select-wrapper {
            position: relative;
        }

        .select-wrapper::after {
            content: '▼';
            font-size: 0.75rem;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--text-secondary);
        }

        .select-wrapper select {
            appearance: none;
            padding-right: 2.5rem;
        }

        /* Enhanced Checkbox */
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .checkbox-container input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #ff6b9d;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>🏥 Pharmacy </h2>
            <p>Professional Management</p>
        </div>
        <div class="sidebar-nav">
            <a href="../dashboard/index.php">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="../medicines/list.php" class="active">
                <i class="fas fa-capsules"></i>
                Medicines
            </a>
            <a href="../inventory/">
                <i class="fas fa-boxes"></i>
                Inventory
            </a>
            <a href="../reports/">
                <i class="fas fa-chart-line"></i>
                Reports
            </a>
            <a href="../prescriptions/">
                <i class="fas fa-prescription"></i>
                Prescriptions
            </a>
            <a href="../../settings.php">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <nav class="navbar">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-brand">
                <i class="fas fa-capsules"></i>
                Pharmacy
            </div>
            </div>
        </nav>

        <div class="content">
            <div class="page-header">
                <h1>💊 Medicines Management</h1>
                <p>Comprehensive database of medications with detailed information and pricing</p>
            </div>

            <!-- Dashboard Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-capsules"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalMedicines; ?></div>
                    <div class="stat-label">Total Medicines</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="active-medicines">0</div>
                    <div class="stat-label">Active Medicines</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-value" id="low-stock">0</div>
                    <div class="stat-label">Low Stock</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-value" id="avg-price">₱0.00</div>
                    <div class="stat-label">Average Price</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="quick-action-btn" onclick="showQuickAddModal()">
                    <i class="fas fa-plus"></i>
                    Add Medicine
                </button>
                <button class="quick-action-btn" onclick="exportToCSV()">
                    <i class="fas fa-file-export"></i>
                    Export CSV
                </button>
                <button class="quick-action-btn" onclick="printMedicines()">
                    <i class="fas fa-print"></i>
                    Print List
                </button>
                <button class="quick-action-btn" onclick="bulkDelete()">
                    <i class="fas fa-trash"></i>
                    Bulk Delete
                </button>
            </div>

            <?php if (empty($medicines)): ?>
                <div class="no-data">
                    <h3>🏥 No Medicines Found</h3>
                    <p>Your pharmacy database doesn't have any medicines yet.</p>
                    <p>Set up your database to start managing medications.</p>

                    <div class="no-data-actions">
                        <a href="../../../database_setup.php" class="no-data-btn">
                            <i class="fas fa-database"></i>
                            Setup Database
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Search and Filter Controls -->
                <div class="controls-section">
                    <div class="controls-grid">
                        <div class="control-group">
                            <label class="control-label">Search Medicines</label>
                            <input type="text" class="control-input" id="searchInput" placeholder="Search by name or code..." onkeyup="showSuggestions(this.value)">
                            <div id="suggestions" class="suggestions"></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Min Price (₱)</label>
                            <input type="number" step="0.01" class="control-input" id="minPrice" placeholder="0.00">
                        </div>
                        <div class="control-group">
                            <label class="control-label">Max Price (₱)</label>
                            <input type="number" step="0.01" class="control-input" id="maxPrice" placeholder="1000.00">
                        </div>
                        <div class="control-group">
                            <label class="control-label">Sort By</label>
                            <div class="select-wrapper">
                                <select class="control-input" id="sortBy">
                                    <option value="generic_name">Name</option>
                                    <option value="price">Price</option>
                                    <option value="medicine_code">Code</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="applyFilters()">
                                <i class="fas fa-filter"></i>
                                Apply Filters
                            </button>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="saveCurrentFilter()" style="background: #10b981;">
                                <i class="fas fa-save"></i>
                                Save Filter
                            </button>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="loadSavedFilters()" style="background: #3b82f6;">
                                <i class="fas fa-folder-open"></i>
                                Load Filters
                            </button>
                        </div>
                        <div class="control-group">
                            <div class="checkbox-container">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                <label for="selectAll">Select All</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="chart-section">
                    <h3>Status Distribution</h3>
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>

                <div class="table-container">
                    <table class="data-table" id="medicinesTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllRows" onchange="toggleSelectAll()"></th>
                                <th onclick="sortTable('medicine_code')" style="cursor: pointer;">Medicine Code <span id="sort-code">↕</span></th>
                                <th onclick="sortTable('generic_name')" style="cursor: pointer;">Generic Name <span id="sort-name">↕</span></th>
                                <th onclick="sortTable('brand_name')" style="cursor: pointer;">Brand Name <span id="sort-brand">↕</span></th>
                                <th onclick="sortTable('form')" style="cursor: pointer;">Form <span id="sort-form">↕</span></th>
                                <th onclick="sortTable('price')" style="cursor: pointer;">Price <span id="sort-price">↕</span></th>
                                <th onclick="sortTable('status')" style="cursor: pointer;">Status <span id="sort-status">↕</span></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicines as $medicine): ?>
                            <tr>
                                <td><input type="checkbox" class="row-checkbox" value="<?php echo $medicine['id']; ?>"></td>
                                <td><strong><?php echo htmlspecialchars($medicine['medicine_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($medicine['generic_name']); ?></td>
                                <td><?php echo htmlspecialchars($medicine['brand_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span style="background: rgba(102, 126, 234, 0.1); color: #667eea; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem;">
                                        <?php echo htmlspecialchars($medicine['form']); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo '₱' . number_format($medicine['price'], 2); ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($medicine['status']); ?>">
                                        <?php echo ucfirst($medicine['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <div class="action-icon action-edit" onclick="editPrice(<?php echo $medicine['id']; ?>, <?php echo $medicine['price']; ?>)" title="Edit Price">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="action-icon action-view" onclick="viewDetails(<?php echo $medicine['id']; ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <div class="action-icon action-delete" onclick="deleteMedicine(<?php echo $medicine['id']; ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="medicines-grid" id="medicinesGrid">
                    <?php foreach ($medicines as $index => $medicine): ?>
                    <div class="medicine-card" data-category="<?php echo htmlspecialchars($medicine['category'] ?? ''); ?>">
                        <div class="medicine-header">
                            <span class="medicine-code"><?php echo htmlspecialchars($medicine['medicine_code']); ?></span>
                            <div class="medicine-price">₱<?php echo number_format($medicine['price'], 2); ?></div>
                        </div>
                        <div class="medicine-name"><?php echo htmlspecialchars($medicine['generic_name']); ?></div>
                        <div class="medicine-details">
                            <strong>Brand:</strong> <?php echo htmlspecialchars($medicine['brand_name'] ?? 'N/A'); ?><br>
                            <strong>Form:</strong> <?php echo htmlspecialchars($medicine['form']); ?><br>
                            <strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($medicine['status']); ?>"><?php echo ucfirst($medicine['status']); ?></span>
                        </div>
                        <div class="table-actions" style="margin-top: 1rem;">
                            <div class="action-icon action-edit" onclick="editPrice(<?php echo $medicine['id']; ?>, <?php echo $medicine['price']; ?>)" title="Edit Price">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="action-icon action-view" onclick="viewDetails(<?php echo $medicine['id']; ?>)" title="View Details">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="action-icon action-delete" onclick="deleteMedicine(<?php echo $medicine['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pharmacy Pro. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Support</a>
            </div>
        </footer>
    </div>

    <!-- Quick Add Modal -->
    <div id="quickAddModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Quick Add Medicine</h2>
            <form id="quickAddForm">
                <div class="form-group">
                    <label for="medicineCode">Medicine Code:</label>
                    <input type="text" id="medicineCode" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="genericName">Generic Name:</label>
                    <input type="text" id="genericName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="brandName">Brand Name:</label>
                    <input type="text" id="brandName" class="form-control">
                </div>
                <div class="form-group">
                    <label for="form">Form:</label>
                    <div class="select-wrapper">
                        <select id="form" class="form-control">
                            <option value="Tablet">Tablet</option>
                            <option value="Capsule">Capsule</option>
                            <option value="Syrup">Syrup</option>
                            <option value="Injection">Injection</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="price">Price (₱):</label>
                    <input type="number" step="0.01" id="price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <div class="select-wrapper">
                        <select id="status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="Discontinued">Discontinued</option>
                            <option value="Recall">Recall</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="control-btn">Add Medicine</button>
            </form>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <script>
        // Theme Management
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Update toggle button
            const toggleBall = document.querySelector('.toggle-ball');
            if (newTheme === 'dark') {
                toggleBall.style.transform = 'translateX(20px)';
            } else {
                toggleBall.style.transform = 'translateX(0)';
            }

            showNotification(`Switched to ${newTheme} mode!`, 'success');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('open');
            mainContent.classList.toggle('sidebar-open');
        }

        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Update toggle button on load
            const toggleBall = document.querySelector('.toggle-ball');
            if (savedTheme === 'dark') {
                toggleBall.style.transform = 'translateX(20px)';
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                success: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                info: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)'
            };

            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                z-index: 10001;
                max-width: 350px;
                font-family: 'Inter', sans-serif;
                animation: slideInRight 0.3s ease;
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-${getIcon(type)}" style="font-size: 18px;"></i>
                    <p style="margin: 0; font-size: 14px; line-height: 1.4;">${message}</p>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideInRight 0.3s ease reverse';
                    setTimeout(() => notification.parentNode.removeChild(notification), 300);
                }
            }, 4000);
        }

        function getIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        // Search and Filter Functionality
        function filterMedicines() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;

            // Filter table rows
            const tableRows = document.querySelectorAll('#medicinesTable tbody tr');
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const price = parseFloat(row.cells[5].textContent.replace('₱', '').replace(',', ''));

                const matchesSearch = text.includes(searchTerm);
                const matchesPrice = price >= minPrice && price <= maxPrice;

                if (matchesSearch && matchesPrice) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter mobile cards
            const cards = document.querySelectorAll('.medicine-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const priceText = card.querySelector('.medicine-price')?.textContent || '';
                const price = parseFloat(priceText.replace('₱', ''));

                const matchesSearch = text.includes(searchTerm);
                const matchesPrice = price >= minPrice && price <= maxPrice;

                if (matchesSearch && matchesPrice) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

            showNotification(`Filtered to ${document.querySelectorAll('#medicinesTable tbody tr[style*="display: none"]').length} hidden items`, 'info');
        }

        function exportToCSV() {
            const table = document.getElementById('medicinesTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];

            rows.forEach(row => {
                const cols = row.querySelectorAll('td, th');
                const rowData = Array.from(cols).map(col => col.textContent.replace(/,/g, ''));
                csv.push(rowData.join(','));
            });

            const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'medicines.csv';
            a.click();
            window.URL.revokeObjectURL(url);

            showNotification('Medicines exported to CSV!', 'success');
        }

        // Multi-column sorting
        let sortColumn = 'generic_name';
        let sortDirection = 'asc';

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            const table = document.getElementById('medicinesTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                let aVal, bVal;
                switch (column) {
                    case 'price':
                        aVal = parseFloat(a.cells[5].textContent.replace('₱', '').replace(',', ''));
                        bVal = parseFloat(b.cells[5].textContent.replace('₱', '').replace(',', ''));
                        break;
                    case 'medicine_code':
                        aVal = a.cells[1].textContent.toLowerCase();
                        bVal = b.cells[1].textContent.toLowerCase();
                        break;
                    case 'generic_name':
                        aVal = a.cells[2].textContent.toLowerCase();
                        bVal = b.cells[2].textContent.toLowerCase();
                        break;
                    case 'brand_name':
                        aVal = a.cells[3].textContent.toLowerCase();
                        bVal = b.cells[3].textContent.toLowerCase();
                        break;
                    case 'form':
                        aVal = a.cells[4].textContent.toLowerCase();
                        bVal = b.cells[4].textContent.toLowerCase();
                        break;
                    case 'status':
                        aVal = a.cells[6].textContent.toLowerCase();
                        bVal = b.cells[6].textContent.toLowerCase();
                        break;
                    default:
                        return 0;
                }
                return sortDirection === 'asc' ? (aVal > bVal ? 1 : -1) : (aVal < bVal ? 1 : -1);
            });

            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));

            // Update sort indicators
            document.querySelectorAll('[id^="sort-"]').forEach(span => span.textContent = '↕');
            document.getElementById(`sort-${column}`).textContent = sortDirection === 'asc' ? '↑' : '↓';
        }

        function saveCurrentFilter() {
            const filterData = {
                search: document.getElementById('searchInput').value,
                minPrice: document.getElementById('minPrice').value,
                maxPrice: document.getElementById('maxPrice').value,
                sortBy: document.getElementById('sortBy').value
            };
            const savedFilters = JSON.parse(localStorage.getItem('medicinesFilters') || '[]');
            savedFilters.push(filterData);
            localStorage.setItem('medicinesFilters', JSON.stringify(savedFilters));
            showNotification('Filter saved!', 'success');
        }

        function loadSavedFilters() {
            const savedFilters = JSON.parse(localStorage.getItem('medicinesFilters') || '[]');
            if (savedFilters.length === 0) {
                showNotification('No saved filters found!', 'warning');
                return;
            }
            // Load the last saved filter
            const lastFilter = savedFilters[savedFilters.length - 1];
            document.getElementById('searchInput').value = lastFilter.search;
            document.getElementById('minPrice').value = lastFilter.minPrice;
            document.getElementById('maxPrice').value = lastFilter.maxPrice;
            document.getElementById('sortBy').value = lastFilter.sortBy;
            applyFilters();
            showNotification('Last saved filter loaded!', 'info');
        }

        function viewDetails(id) {
            // Simulate view details (in real app, open modal or navigate)
            showNotification(`Viewing details for medicine ID: ${id}`, 'info');
        }

        function deleteMedicine(id) {
            if (confirm(`Are you sure you want to delete medicine ID: ${id}?`)) {
                // Simulate delete (in real app, send to server)
                const row = document.querySelector(`.row-checkbox[value="${id}"]`).closest('tr');
                row.remove();
                showNotification(`Medicine ID: ${id} deleted!`, 'success');
            }
        }

        function bulkDelete() {
            const selected = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(selected).map(cb => cb.value);

            if (ids.length === 0) {
                showNotification('No items selected!', 'warning');
                return;
            }

            if (confirm(`Delete ${ids.length} selected medicines?`)) {
                // Simulate delete (in real app, send to server)
                ids.forEach(id => {
                    const row = document.querySelector(`.row-checkbox[value="${id}"]`).closest('tr');
                    if (row) row.remove();
                });
                showNotification(`${ids.length} medicines deleted!`, 'success');
            }
        }

        function editPrice(id, currentPrice) {
            const newPrice = prompt('Enter new price for medicine ID ' + id + ':', currentPrice);
            if (newPrice !== null && !isNaN(newPrice)) {
                // Simulate update (in real app, send to server)
                const rows = document.querySelectorAll('#medicinesTable tbody tr');
                rows.forEach(row => {
                    const checkbox = row.querySelector('.row-checkbox');
                    if (checkbox && checkbox.value == id) {
                        row.cells[5].innerHTML = '<strong>₱' + parseFloat(newPrice).toFixed(2) + '</strong>';
                    }
                });
                showNotification('Price updated!', 'success');
            }
        }

        function showQuickAddModal() {
            document.getElementById('quickAddModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('quickAddModal').style.display = 'none';
        }

        function renderStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            const statuses = <?php echo json_encode(array_column($medicines, 'status')); ?>;
            
            const statusCounts = {};
            statuses.forEach(status => {
                statusCounts[status] = (statusCounts[status] || 0) + 1;
            });

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(statusCounts),
                    datasets: [{
                        data: Object.values(statusCounts),
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 2
                    }]
                }
            });
        }

        // Enhanced search with suggestions
        function showSuggestions(query) {
            const suggestions = document.getElementById('suggestions');
            if (query.length < 2) {
                suggestions.style.display = 'none';
                return;
            }

            // Simulate suggestions (in real app, fetch from server)
            const mockSuggestions = ['Paracetamol', 'Ibuprofen', 'Aspirin', 'Amoxicillin'];
            const filtered = mockSuggestions.filter(item => item.toLowerCase().includes(query.toLowerCase()));

            suggestions.innerHTML = filtered.map(item => `<div onclick="selectSuggestion('${item}')">${item}</div>`).join('');
            suggestions.style.display = 'block';
        }

        function selectSuggestion(item) {
            document.getElementById('searchInput').value = item;
            document.getElementById('suggestions').style.display = 'none';
            filterMedicines();
        }

        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const selectAllRows = document.getElementById('selectAllRows');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            
            const isChecked = selectAll.checked || selectAllRows.checked;
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        }

        // Print medicines list
        function printMedicines() {
            window.print();
        }

        // Calculate dashboard statistics
        function calculateStats() {
            const medicines = <?php echo json_encode($medicines); ?>;
            
            // Active medicines count
            const activeMedicines = medicines.filter(med => med.status === 'Active').length;
            document.getElementById('active-medicines').textContent = activeMedicines;
            
            // Average price
            const totalPrice = medicines.reduce((sum, med) => sum + parseFloat(med.price), 0);
            const avgPrice = totalPrice / medicines.length;
            document.getElementById('avg-price').textContent = '₱' + avgPrice.toFixed(2);
            
            // Low stock (simulated)
            const lowStock = Math.floor(Math.random() * 10);
            document.getElementById('low-stock').textContent = lowStock;
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            calculateStats();

            // Add staggered animations to cards
            const cards = document.querySelectorAll('.medicine-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Add event listener for select all
            document.getElementById('selectAllRows').addEventListener('change', toggleSelectAll);

            // Render charts
            renderStatusChart();
        });

        // Add CSS animations for notifications
        const notificationStyles = document.createElement('style');
        notificationStyles.textContent = `
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        `;
        document.head.appendChild(notificationStyles);
    </script>
</body>
</html>