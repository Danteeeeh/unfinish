<?php
/**
 * Enhanced PMS Inventory Management with Dark Mode
 */

// Load main config for authentication
require_once __DIR__ . '/../../../config.php';
requireLogin();

// Connect to database
require_once __DIR__ . '/../../config/database.php';
$conn = getPMSDBConnection();

// Get inventory with additional stats
try {
    $inventory = $conn->query("SELECT * FROM pms_inventory ORDER BY expiry_date ASC")->fetchAll(PDO::FETCH_ASSOC);
    $totalItems = count($inventory);
    $lowStockItems = $conn->query("SELECT COUNT(*) as count FROM pms_inventory WHERE quantity <= 10")->fetch()['count'];
    $expiredItems = $conn->query("SELECT COUNT(*) as count FROM pms_inventory WHERE expiry_date < CURDATE()")->fetch()['count'];
    $totalValue = 0;
    foreach ($inventory as $item) {
        try {
            $priceQuery = $conn->prepare("SELECT price FROM pms_medicines WHERE id = ?");
            $priceQuery->execute([$item['medicine_id']]);
            $priceResult = $priceQuery->fetch(PDO::FETCH_ASSOC);
            $price = $priceResult ? $priceResult['price'] : 0;
            $totalValue += $price * $item['quantity'];
        } catch (Exception $e) {
            // If price query fails, use 0
            $price = 0;
        }
    }
} catch (Exception $e) {
    $inventory = [];
    $totalItems = 0;
    $lowStockItems = 0;
    $expiredItems = 0;
    $totalValue = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💊 Pharmacy Pro - Inventory Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
    <style>
        /* Enhanced PMS Inventory with Dark Mode */
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
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Sidebar Navigation - Full Screen */
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
            overflow-x: hidden;
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

        /* Main Content - Full Screen */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            height: 100vh;
            overflow: hidden;
            position: relative;
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

        /* Footer - Full Screen */
        .footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            color: var(--text-secondary);
            margin-top: auto;
            position: relative;
            z-index: 10;
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

        /* Navbar - Full Screen */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 999;
            height: 70px;
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

        /* Content - Full Screen Layout */
        .content {
            padding: 2rem;
            height: calc(100vh - 80px);
            overflow-y: auto;
            overflow-x: hidden;
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

        /* Enhanced Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--pharmacy-gradient);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(255, 107, 157, 0.3);
        }

        .stat-card.total {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05));
        }

        .stat-card.warning {
            background: linear-gradient(135deg, rgba(237, 137, 54, 0.1), rgba(221, 107, 32, 0.05));
        }

        .stat-card.danger {
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.1), rgba(229, 62, 62, 0.05));
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            background: var(--pharmacy-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
        }

        .stat-card h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        .stat-card p {
            font-size: 3rem;
            font-weight: 800;
            margin: 0;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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

        /* Mobile Card Layout */
        .inventory-grid {
            display: none;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .inventory-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .inventory-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .inventory-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .inventory-batch {
            background: var(--pharmacy-gradient);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .inventory-quantity {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .inventory-details {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: var(--pharmacy-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .fab:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(255, 107, 157, 0.6);
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #ff6b9d;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
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

            .dashboard-container.sidebar-open {
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

            .inventory-grid {
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

        .stat-card, .inventory-card {
            animation: fadeInUp 0.6s ease forwards;
        }

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

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: 5% auto;
            padding: 3rem;
            border-radius: 24px;
            width: 90%;
            max-width: 650px;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            border: 2px solid var(--border-color);
            animation: slideIn 0.4s ease;
            backdrop-filter: blur(10px);
        }

        .modal-content h2 {
            color: var(--text-primary);
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.8rem;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .modal-content h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--pharmacy-gradient);
            border-radius: 2px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #ff6b9d;
            box-shadow: 0 0 0 4px rgba(255, 107, 157, 0.15);
            transform: translateY(-2px);
        }

        .form-group input:hover, .form-group select:hover {
            border-color: rgba(255, 107, 157, 0.5);
        }

        .range-inputs {
            display: flex;
            gap: 0.5rem;
        }

        .range-inputs input {
            flex: 1;
        }

        /* Enhanced Card Styling */
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 2px solid var(--border-color);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 107, 157, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b9d, #c44569);
        }

        .card-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        .card-body {
            padding: 2.5rem;
        }

        /* Enhanced Buttons */
        .control-btn {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .control-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* Professional Animations */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .stat-card:hover .stat-icon {
            animation: pulse 1s infinite;
        }

        /* Enhanced Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status-available {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .status-reserved {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }

        .status-damaged {
            background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
            color: white;
        }
        .stock-indicators-section {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .stock-indicators-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .stock-indicator-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stock-indicator-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(255, 107, 157, 0.3);
        }

        .stock-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stock-medicine {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .stock-batch {
            font-size: 0.8rem;
            color: var(--text-secondary);
            background: var(--bg-tertiary);
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
        }

        .stock-level-container {
            margin-bottom: 1rem;
        }

        .stock-level-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .stock-progress-bar {
            width: 100%;
            height: 12px;
            background: var(--bg-tertiary);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .stock-progress-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 0.5s ease;
        }

        .level-high {
            background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
        }

        .level-medium {
            background: linear-gradient(90deg, #ed8936 0%, #dd6b20 100%);
        }

        .level-low {
            background: linear-gradient(90deg, #f56565 0%, #e53e3e 100%);
        }

        .stock-percentage {
            text-align: right;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stock-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stock-quantity {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        /* Future Features Section */
        .future-section {
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.1), rgba(196, 69, 105, 0.05));
            border: 2px solid rgba(255, 107, 157, 0.2);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .future-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 107, 157, 0.1) 0%, transparent 70%);
            animation: pulse 6s ease-in-out infinite;
        }

        .future-header {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 2rem;
        }

        .future-title {
            font-size: 2rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .future-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .future-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .future-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 107, 157, 0.2);
        }

        [data-theme="dark"] .future-item {
            background: rgba(45, 45, 45, 0.9);
            border: 1px solid rgba(255, 107, 157, 0.3);
        }

        .future-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.3);
            border-color: rgba(255, 107, 157, 0.5);
        }

        .future-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
        }

        .future-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .future-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .future-status {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-planned {
            background: rgba(245, 158, 11, 0.2);
            color: #d97706;
        }

        .status-testing {
            background: rgba(16, 185, 129, 0.2);
            color: #059669;
        }

        .status-development {
            background: rgba(59, 130, 246, 0.2);
            color: #2563eb;
        }

        .status-damaged {
            background: rgba(160, 174, 192, 0.2);
            color: #718096;
        }

        /* Enhanced Dashboard Container - Full Screen */
        .dashboard-container {
            width: 100vw;
            height: 100vh;
            margin: 0;
            padding: 0;
            background: var(--bg-primary);
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
            border: none;
            transition: margin-left 0.3s ease;
            position: relative;
        }

        .dashboard-container.sidebar-open {
            margin-left: 280px;
        }

        /* Enhanced Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--border-color);
        }

        .data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .data-table th {
            padding: 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .data-table td {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .data-table tbody tr {
            transition: all 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: rgba(255, 107, 157, 0.05);
            transform: translateX(5px);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .btn-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 140px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--pharmacy-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.5);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.5);
        }

        .close {
            color: var(--text-secondary);
            float: right;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            transition: all 0.3s ease;
        }

        .close:hover {
            color: var(--text-primary);
            transform: rotate(90deg);
        }

        @keyframes slideIn {
            from { transform: translateY(-100px) scale(0.9); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        /* Enhanced Form Validation */
        .form-group.error input, .form-group.error select {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-group.success input, .form-group.success select {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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

        /* Enhanced Quick Actions */
        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            padding: 1rem 1.5rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            min-width: 180px;
            justify-content: center;
        }

        .quick-action-btn:hover {
            background: var(--pharmacy-gradient);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.3);
        }

        /* Enhanced Progress Bars */
        .progress-container {
            margin-top: 1rem;
        }

        .progress-bar {
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: var(--pharmacy-gradient);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        /* Enhanced Loading States */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        /* Enhanced Animations and Micro-interactions */
        .fade-in {
            animation: fadeInUp 0.8s ease forwards;
        }

        .slide-in-left {
            animation: slideInLeft 0.6s ease forwards;
        }

        .bounce-in {
            animation: bounceIn 0.8s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Enhanced Cards */
        .stat-card, .inventory-card {
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card::before, .inventory-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.1) 0%, rgba(196, 69, 105, 0.05) 100%);
            opacity: 0;
            border-radius: inherit;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::before, .inventory-card:hover::before {
            opacity: 1;
        }

        /* Enhanced Progress Indicators */
        .progress-ring {
            position: relative;
            display: inline-block;
        }

        .progress-ring svg {
            transform: rotate(-90deg);
        }

        .progress-ring circle {
            transition: stroke-dasharray 0.5s ease;
        }

        /* Enhanced Loading States */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .loading-content {
            text-align: center;
            color: white;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #ff6b9d;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced Tooltips */
        .tooltip {
            position: relative;
            cursor: help;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--bg-tertiary);
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            z-index: 1000;
        }

        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }

        /* Enhanced Empty States */
        .empty-state {
            position: relative;
            overflow: hidden;
        }

        .empty-state::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 107, 157, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }

        /* Enhanced Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .pagination-btn.active {
            box-shadow: 0 0 20px rgba(255, 107, 157, 0.4);
        }

        /* Enhanced Search Bar */
        .search-container {
            position: relative;
        }

        .search-input {
            padding-left: 3rem;
            background: var(--bg-secondary);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #ff6b9d;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .search-input:focus + .search-icon {
            color: #ff6b9d;
        }

        /* Enhanced Modal Backdrop */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 999;
            animation: fadeIn 0.3s ease;
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        /* Enhanced Notification System */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--pharmacy-gradient);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            animation: slideInRight 0.3s ease;
            max-width: 350px;
        }

        .notification.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .notification.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .notification.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {

            /* Enhanced Loading States */
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                backdrop-filter: blur(5px);
            }

            .loading-content {
                text-align: center;
                color: white;
            }

            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top: 4px solid #ff6b9d;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 1rem;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Enhanced Tooltips */
            .tooltip {
                position: relative;
                cursor: help;
            }

            .tooltip::after {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 125%;
                left: 50%;
                transform: translateX(-50%);
                background: var(--bg-tertiary);
                color: var(--text-primary);
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 0.8rem;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                box-shadow: var(--shadow-md);
                z-index: 1000;
            }

            .tooltip:hover::after {
                opacity: 1;
                visibility: visible;
            }

            /* Enhanced Empty States */
            .empty-state {
                position: relative;
                overflow: hidden;
            }

            .empty-state::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 107, 157, 0.1) 0%, transparent 70%);
                animation: pulse 4s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 0.3; transform: scale(1); }
                50% { opacity: 0.6; transform: scale(1.1); }
            }

            /* Enhanced Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 2rem;
                margin-bottom: 3rem;
            }

            .pagination-btn.active {
                box-shadow: 0 0 20px rgba(255, 107, 157, 0.4);
            }

            /* Enhanced Search Bar */
            .search-container {
                position: relative;
            }

            .search-input {
                padding-left: 3rem;
                background: var(--bg-secondary);
                border: 2px solid transparent;
                transition: all 0.3s ease;
            }

            .search-input:focus {
                border-color: #ff6b9d;
                box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
            }

            .search-icon {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: var(--text-secondary);
                transition: color 0.3s ease;
            }

            .search-input:focus + .search-icon {
                color: #ff6b9d;
            }

            /* Enhanced Modal Backdrop */
            .modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(8px);
                z-index: 999;
                animation: fadeIn 0.3s ease;
            }

            @keyframes slideOut {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateY(-20px);
                }
            }

            /* Enhanced Notification System */
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--pharmacy-gradient);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                z-index: 10000;
                animation: slideInRight 0.3s ease;
                max-width: 350px;
            }

            .notification.success {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            }

            .notification.error {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            }

            .notification.warning {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            }

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

            /* Enhanced Mobile Responsiveness */
            @media (max-width: 480px) {
                .hero h1 {
                    font-size: 2rem;
                }

                .btn {
                    padding: 0.75rem 1.5rem;
                    font-size: 0.9rem;
                }

                .search-input {
                    padding-left: 2.5rem;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }
            }

            /* Responsive Design - Full Screen */
            @media (max-width: 768px) {
                .dashboard-container {
                    margin-left: 0 !important;
                }

                .main-content {
                    margin-left: 0 !important;
                }

                .sidebar {
                    transform: translateX(-100%);
                }

                .navbar {
                    padding: 1rem;
                    flex-direction: column;
                    gap: 1rem;
                    height: auto;
                }

                .nav-menu {
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .content {
                    padding: 1.5rem 1rem;
                    height: calc(100vh - 60px);
                }

                .controls-grid {
                    grid-template-columns: 1fr;
                }

                .stock-indicators-grid {
                    grid-template-columns: 1fr;
                }

                .future-grid {
                    grid-template-columns: 1fr;
                }

                .footer-links {
                    flex-direction: column;
                    gap: 1rem;
                }
            }
    </style>
</head>
<body>
    <div class="dashboard-container" id="dashboardContainer">
    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>📦 Inventory </h2>
            <p>Smart Inventory Management</p>
        </div>
        <div class="sidebar-nav">
            <a href="../dashboard/index.php">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="../medicines/list.php">
                <i class="fas fa-capsules"></i>
                Medicines
            </a>
            <a href="../inventory/" class="active">
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
                Inventory
            </div>
            </div>
        </nav>

        <div class="content">
            <div class="page-header">
                <h1>📦 Inventory Management</h1>
                <p>Real-time inventory tracking with expiry monitoring and stock level management</p>
            </div>

            <?php if (empty($inventory)): ?>
                <div class="no-data">
                    <h3>📦 No Inventory Items Found</h3>
                    <p>Your pharmacy inventory is currently empty.</p>
                    <p>Add medicines to your inventory to start tracking stock levels and expiry dates.</p>

                    <div class="no-data-actions">
                        <a href="../medicines/list.php" class="no-data-btn">
                            <i class="fas fa-capsules"></i>
                            Browse Medicines
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <button class="quick-action-btn" onclick="showQuickAddModal()">
                        <i class="fas fa-plus-circle"></i>
                        Add New Item
                    </button>
                    <button class="quick-action-btn" onclick="exportInventory()">
                        <i class="fas fa-file-export"></i>
                        Export Data
                    </button>
                    <button class="quick-action-btn" onclick="showExpiryReport()">
                        <i class="fas fa-calendar-times"></i>
                        Expiry Report
                    </button>
                    <button class="quick-action-btn" onclick="showLowStockReport()">
                        <i class="fas fa-exclamation-triangle"></i>
                        Low Stock Alert
                    </button>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card total">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3>Total Value</h3>
                        <p><?php echo '₱' . number_format($totalValue, 2); ?></p>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h3>Total Items</h3>
                        <p><?php echo number_format($totalItems); ?></p>
                    </div>

                    <?php if ($lowStockItems > 0): ?>
                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Low Stock Items</h3>
                        <p><?php echo number_format($lowStockItems); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($expiredItems > 0): ?>
                    <div class="stat-card danger">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3>Expired Items</h3>
                        <p><?php echo number_format($expiredItems); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Stock Level Indicators -->
                <div class="stock-indicators-section" id="stockIndicatorsSection" style="display: none;">
                    <h3 style="color: var(--text-primary); margin-bottom: 1.5rem; text-align: center;">
                        <i class="fas fa-chart-bar" style="color: var(--primary-gradient); margin-right: 0.5rem;"></i>
                        Stock Level Overview
                    </h3>
                    <div class="stock-indicators-grid">
                        <?php foreach ($inventory as $item): ?>
                        <?php
                        $medicineName = 'Unknown Medicine';
                        $price = 0;
                        try {
                            $medicineQuery = $conn->prepare("SELECT generic_name, price FROM pms_medicines WHERE id = ?");
                            $medicineQuery->execute([$item['medicine_id']]);
                            $medicine = $medicineQuery->fetch(PDO::FETCH_ASSOC);
                            $medicineName = $medicine ? htmlspecialchars($medicine['generic_name']) : 'Unknown Medicine';
                            $price = $medicine ? $medicine['price'] : 0;
                        } catch (Exception $e) {
                            // Keep default values
                        }

                        // Calculate stock level percentage (assuming max stock is 1000 for demo)
                        $maxStock = 1000;
                        $stockPercentage = min(($item['quantity'] / $maxStock) * 100, 100);
                        $stockLevel = $stockPercentage > 50 ? 'high' : ($stockPercentage > 20 ? 'medium' : 'low');
                        ?>
                        <div class="stock-indicator-card">
                            <div class="stock-header">
                                <span class="stock-medicine"><?php echo $medicineName; ?></span>
                                <span class="stock-batch"><?php echo htmlspecialchars($item['batch_number']); ?></span>
                            </div>
                            <div class="stock-level-container">
                                <div class="stock-level-label">Stock Level</div>
                                <div class="stock-progress-bar">
                                    <div class="stock-progress-fill level-<?php echo $stockLevel; ?>" style="width: <?php echo $stockPercentage; ?>%"></div>
                                </div>
                                <div class="stock-percentage"><?php echo number_format($stockPercentage, 1); ?>%</div>
                            </div>
                            <div class="stock-details">
                                <div class="stock-quantity">Quantity: <strong><?php echo number_format($item['quantity']); ?></strong></div>
                                <div class="stock-status">
                                    <span class="status-badge status-<?php echo strtolower($item['status']); ?>">
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button class="control-btn" onclick="hideStockIndicators()" style="background: #6c757d;">Hide Stock Levels</button>
                    </div>
                </div>

                <!-- Toggle Stock Indicators Button -->
                <div class="controls-section" style="text-align: center; margin-bottom: 2rem;">
                    <button class="control-btn" onclick="showStockIndicators()" data-action="toggle-stock" title="Show stock level indicators">
                        <i class="fas fa-chart-bar"></i>
                        Show Stock Levels
                    </button>
                </div>

                <!-- Future Features Section -->
                <div class="future-section">
                    <div class="future-header">
                        <h2 class="future-title">🚀 Future Inventory Features</h2>
                        <p class="future-subtitle">Advanced features coming to inventory management</p>
                    </div>

                    <div class="future-grid">
                        <div class="future-item">
                            <div class="future-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="future-name">AI Stock Prediction</div>
                            <div class="future-description">Machine learning algorithms to predict optimal stock levels and reorder points</div>
                            <span class="future-status status-development">In Development</span>
                        </div>

                        <div class="future-item">
                            <div class="future-icon">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <div class="future-name">Barcode Scanning</div>
                            <div class="future-description">Scan barcodes for instant inventory updates and stock tracking</div>
                            <span class="future-status status-planned">Planned</span>
                        </div>

                        <div class="future-item">
                            <div class="future-icon">
                                <i class="fas fa-truck-moving"></i>
                            </div>
                            <div class="future-name">Automated Reordering</div>
                            <div class="future-description">Smart reordering system that automatically places orders when stock runs low</div>
                            <span class="future-status status-planned">Planned</span>
                        </div>

                        <div class="future-item">
                            <div class="future-icon">
                                <i class="fas fa-chart-network"></i>
                            </div>
                            <div class="future-name">Supply Chain Analytics</div>
                            <div class="future-description">Track supplier performance and optimize supply chain efficiency</div>
                            <span class="future-status status-testing">In Testing</span>
                        </div>

                        <div class="future-item">
                            <div class="future-icon">
                                <i class="fas fa-temperature-half"></i>
                            </div>
                            <div class="future-name">Temperature Monitoring</div>
                            <div class="future-description">IoT sensors for real-time temperature and humidity monitoring</div>
                            <span class="future-status status-planned">Planned</span>
                        </div>

                        <div class="future-item">
                            <div class="future-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="future-name">Mobile Inventory App</div>
                            <div class="future-description">Native mobile app for on-the-go inventory management and scanning</div>
                            <span class="future-status status-development">In Development</span>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution Chart (Initially Hidden) -->
                <div class="chart-section" id="statusChartSection" style="display: none;">
                    <h3>Inventory Status Distribution</h3>
                    <canvas id="statusChart" width="400" height="200"></canvas>
                    <button class="control-btn" onclick="hideChart()" style="margin-top: 1rem; background: #6c757d;">Hide Chart</button>
                </div>

                <!-- Toggle Chart Button -->
                <div class="controls-section" style="text-align: center; margin-bottom: 2rem;">
                    <button class="control-btn" onclick="showChart()" data-action="toggle-chart" title="Show inventory status chart">
                        <i class="fas fa-chart-pie"></i>
                        Show Status Chart
                    </button>
                </div>

                <!-- Search and Filter Controls -->
                <div class="controls-section">
                    <div class="controls-grid">
                        <div class="control-group">
                            <label class="control-label">Search Inventory</label>
                            <input type="text" class="control-input" id="searchInput" placeholder="Search by batch number..." onkeyup="showSuggestions(this.value)">
                            <div id="suggestions" class="suggestions"></div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Status Filter</label>
                            <select class="control-input" id="statusFilter" onchange="filterInventory()">
                                <option value="">All Statuses</option>
                                <option value="available">Available</option>
                                <option value="reserved">Reserved</option>
                                <option value="expired">Expired</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                            <label class="control-label">Quantity Range</label>
                            <div class="range-inputs">
                                <input type="number" class="control-input" id="minQuantity" placeholder="Min" style="width: 48%;" onchange="filterInventory()">
                                <input type="number" class="control-input" id="maxQuantity" placeholder="Max" style="width: 48%;" onchange="filterInventory()">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Expiry Date Range</label>
                            <div class="range-inputs">
                                <input type="date" class="control-input" id="minExpiry" style="width: 48%;" onchange="filterInventory()">
                                <input type="date" class="control-input" id="maxExpiry" style="width: 48%;" onchange="filterInventory()">
                            </div>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="showQuickAddModal()" data-action="add-inventory" title="Add new inventory item">
                                <i class="fas fa-plus"></i>
                                Quick Add Item
                            </button>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="filterInventory()" data-action="filter-inventory" title="Apply current filters">
                                <i class="fas fa-search"></i>
                                Filter Results
                            </button>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Bulk Actions</label>
                            <select class="control-input" id="bulkAction">
                                <option value="">Select Action</option>
                                <option value="mark_available">Mark as Available</option>
                                <option value="mark_expired">Mark as Expired</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="applyBulkAction()" style="background: #dc3545;" data-action="bulk-action" title="Apply selected action to selected items">
                                <i class="fas fa-check-square"></i>
                                Apply Bulk Action
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="table-container">
                    <div class="card">
                        <div class="card-header">
                            <h3>📦 Inventory Items</h3>
                        </div>
                        <div class="card-body">
                            <table class="data-table" id="inventoryTable">
                                <thead>
                                    <tr>
                                        <th>Batch Number</th>
                                        <th>Medicine</th>
                                        <th>Quantity</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Supplier</th>
                                        <th>Price</th>
                                        <th>Total Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inventory as $item): ?>
                                    <?php
                                    // Get medicine name and price
                                    $medicineName = 'Unknown Medicine';
                                    $price = 0;
                                    try {
                                        $medicineQuery = $conn->prepare("SELECT generic_name, price FROM pms_medicines WHERE id = ?");
                                        $medicineQuery->execute([$item['medicine_id']]);
                                        $medicine = $medicineQuery->fetch(PDO::FETCH_ASSOC);
                                        $medicineName = $medicine ? htmlspecialchars($medicine['generic_name']) : 'Unknown Medicine';
                                        $price = $medicine ? $medicine['price'] : 0;
                                    } catch (Exception $e) {
                                        // Keep default values
                                    }
                                    ?>
                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox" value="<?php echo $item['id']; ?>"></td>
                                        <td><strong><?php echo htmlspecialchars($item['batch_number']); ?></strong></td>
                                        <td><?php echo $medicineName; ?></td>
                                        <td><strong><?php echo number_format($item['quantity']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($item['expiry_date']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($item['status']); ?>">
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['supplier'] ?? 'N/A'); ?></td>
                                        <td><strong><?php echo '₱' . number_format($price, 2); ?></strong></td>
                                        <td><strong><?php echo '₱' . number_format($price * $item['quantity'], 2); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="inventory-grid" id="inventoryGrid">
                    <?php foreach ($inventory as $index => $item): ?>
                    <?php
                    // Get medicine name and price for mobile view
                    $medicineName = 'Unknown Medicine';
                    $price = 0;
                    try {
                        $medicineQuery = $conn->prepare("SELECT generic_name, price FROM pms_medicines WHERE id = ?");
                        $medicineQuery->execute([$item['medicine_id']]);
                        $medicine = $medicineQuery->fetch(PDO::FETCH_ASSOC);
                        $medicineName = $medicine ? htmlspecialchars($medicine['generic_name']) : 'Unknown Medicine';
                        $price = $medicine ? $medicine['price'] : 0;
                    } catch (Exception $e) {
                        // Keep default values
                    }
                    ?>
                    <div class="inventory-card" data-status="<?php echo strtolower($item['status']); ?>">
                        <div class="inventory-header">
                            <span class="inventory-batch"><?php echo htmlspecialchars($item['batch_number']); ?></span>
                            <div class="inventory-quantity"><?php echo number_format($item['quantity']); ?></div>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <strong><?php echo $medicineName; ?></strong>
                        </div>
                        <div class="inventory-details">
                            <strong>Expires:</strong> <?php echo htmlspecialchars($item['expiry_date']); ?><br>
                            <strong>Supplier:</strong> <?php echo htmlspecialchars($item['supplier'] ?? 'N/A'); ?><br>
                            <strong>Price:</strong> ₱<?php echo number_format($price, 2); ?><br>
                            <strong>Total Value:</strong> ₱<?php echo number_format($price * $item['quantity'], 2); ?><br>
                            <strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($item['status']); ?>"><?php echo ucfirst($item['status']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Add Modal -->
    <div id="quickAddModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Quick Add Inventory Item</h2>
            <form id="quickAddForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="batchNumber">Batch Number:</label>
                        <input type="text" id="batchNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="medicineId">Medicine:</label>
                        <select id="medicineId" required>
                            <option value="">Select Medicine</option>
                            <?php
                            // Fetch medicines for dropdown
                            try {
                                $medicines = $conn->query("SELECT id, generic_name FROM pms_medicines ORDER BY generic_name")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($medicines as $med) {
                                    echo "<option value=\"{$med['id']}\">{$med['generic_name']}</option>";
                                }
                            } catch (Exception $e) {
                                echo "<option value=\"\">Error loading medicines</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="expiryDate">Expiry Date:</label>
                        <input type="date" id="expiryDate" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="supplier">Supplier:</label>
                        <input type="text" id="supplier">
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status">
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="expired">Expired</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Add Item</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
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
        function filterInventory() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const minQuantity = parseInt(document.getElementById('minQuantity').value) || 0;
            const maxQuantity = parseInt(document.getElementById('maxQuantity').value) || Infinity;
            const minExpiry = document.getElementById('minExpiry').value;
            const maxExpiry = document.getElementById('maxExpiry').value;

            // Filter table rows
            const tableRows = document.querySelectorAll('#inventoryTable tbody tr');
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const status = row.cells[4]?.textContent.toLowerCase() || '';
                const quantity = parseInt(row.cells[2]?.textContent.replace(/,/g, '')) || 0;
                const expiryDate = row.cells[3]?.textContent || '';

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status.includes(statusFilter);
                const matchesQuantity = quantity >= minQuantity && quantity <= maxQuantity;
                const matchesExpiry = (!minExpiry || expiryDate >= minExpiry) && (!maxExpiry || expiryDate <= maxExpiry);

                if (matchesSearch && matchesStatus && matchesQuantity && matchesExpiry) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter mobile cards
            const cards = document.querySelectorAll('.inventory-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const status = card.dataset.status?.toLowerCase() || '';
                const quantityText = card.querySelector('.inventory-quantity')?.textContent || '';
                const quantity = parseInt(quantityText.replace(/,/g, '')) || 0;
                const expiryText = card.textContent; // Simplified for demo

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status.includes(statusFilter);
                const matchesQuantity = quantity >= minQuantity && quantity <= maxQuantity;
                const matchesExpiry = (!minExpiry || expiryText.includes(minExpiry)) && (!maxExpiry || expiryText.includes(maxExpiry));

                if (matchesSearch && matchesStatus && matchesQuantity && matchesExpiry) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

            showNotification(`Filtered to ${document.querySelectorAll('#inventoryTable tbody tr[style*="display: none"]').length} hidden items`, 'info');
        }

        function applyBulkAction() {
            const action = document.getElementById('bulkAction').value;
            const selected = document.querySelectorAll('.row-checkbox:checked');
            const ids = Array.from(selected).map(cb => cb.value);

            if (ids.length === 0 || !action) {
                showNotification('Select items and action!', 'warning');
                return;
            }

            if (confirm(`Apply ${action} to ${ids.length} items?`)) {
                // Simulate bulk action
                selected.forEach(cb => {
                    const row = cb.closest('tr');
                    if (action === 'mark_available') {
                        row.cells[4].innerHTML = '<span class="status-badge status-available">Available</span>';
                    } else if (action === 'mark_expired') {
                        row.cells[4].innerHTML = '<span class="status-badge status-expired">Expired</span>';
                    } else if (action === 'delete') {
                        row.remove();
                    }
                });
                showNotification(`${action} applied to ${ids.length} items!`, 'success');
            }
        }

        // Enhanced button handling with tags
        function handleButtonClick(action) {
            switch (action) {
                case 'add-inventory':
                    showQuickAddModal();
                    break;
                case 'filter-inventory':
                    filterInventory();
                    break;
                case 'bulk-action':
                    applyBulkAction();
                    break;
                case 'toggle-stock':
                    showStockIndicators();
                    break;
                default:
                    showNotification('Unknown action!', 'error');
            }
        }

        // Ensure all functionalities work
        function initializeInventoryFunctions() {
            // Re-attach event listeners if needed
            document.querySelectorAll('button[data-action]').forEach(button => {
                button.addEventListener('click', function() {
                    handleButtonClick(this.getAttribute('data-action'));
                });
            });

            // Ensure filter function is available
            if (typeof filterInventory === 'undefined') {
                console.error('filterInventory function not defined');
            }
        }

        // Real-time filter updates
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('searchInput').addEventListener('input', filterInventory);
            document.getElementById('statusFilter').addEventListener('change', filterInventory);
            document.getElementById('minQuantity').addEventListener('input', filterInventory);
            document.getElementById('maxQuantity').addEventListener('input', filterInventory);
            document.getElementById('minExpiry').addEventListener('input', filterInventory);
            document.getElementById('maxExpiry').addEventListener('input', filterInventory);
        });

        function showQuickAddModal() {
            document.getElementById('quickAddModal').style.display = 'block';
            // Focus on first input
            setTimeout(() => document.getElementById('batchNumber').focus(), 100);
        }

        function closeModal() {
            document.getElementById('quickAddModal').style.display = 'none';
            // Reset form
            document.getElementById('quickAddForm').reset();
            // Remove validation classes
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error', 'success');
            });
        }

        // Form validation on input
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('quickAddForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // Basic validation
                    const requiredFields = ['batchNumber', 'medicineId', 'quantity', 'expiryDate'];
                    let isValid = true;

                    requiredFields.forEach(field => {
                        const input = document.getElementById(field);
                        const group = input.closest('.form-group');
                        if (input.value.trim() === '') {
                            group.classList.add('error');
                            isValid = false;
                        } else {
                            group.classList.remove('error').classList.add('success');
                        }
                    });

                    if (isValid) {
                        // Simulate add (in real app, send to server)
                        showNotification('Inventory item added successfully!', 'success');
                        closeModal();
                    } else {
                        showNotification('Please fill all required fields!', 'error');
                    }
                });

                // Real-time validation
                ['batchNumber', 'medicineId', 'quantity', 'expiryDate'].forEach(field => {
                    document.getElementById(field).addEventListener('blur', function() {
                        const group = this.closest('.form-group');
                        if (this.value.trim() === '') {
                            group.classList.add('error');
                        } else {
                            group.classList.remove('error').classList.add('success');
                        }
                    });
                });
            }
        });

        function showChart() {
            document.getElementById('statusChartSection').style.display = 'block';
            renderStatusChart();
            showNotification('Chart displayed!', 'info');
        }

        function showStockIndicators() {
            const section = document.getElementById('stockIndicatorsSection');
            section.style.display = 'block';
            showNotification('Stock level indicators displayed!', 'info');
        }

        function hideStockIndicators() {
            const section = document.getElementById('stockIndicatorsSection');
            section.style.display = 'none';
            showNotification('Stock level indicators hidden!', 'info');
        }

        // Enhanced search with suggestions
        function showSuggestions(query) {
            const suggestions = document.getElementById('suggestions');
            if (query.length < 2) {
                suggestions.style.display = 'none';
                return;
            }

            // Simulate suggestions (in real app, fetch from server)
            const mockSuggestions = ['Batch001', 'Batch002', 'ExpiredBatch', 'ReservedBatch'];
            const filtered = mockSuggestions.filter(item => item.toLowerCase().includes(query.toLowerCase()));

            suggestions.innerHTML = filtered.map(item => `<div onclick="selectSuggestion('${item}')">${item}</div>`).join('');
            suggestions.style.display = 'block';
        }

        function selectSuggestion(item) {
            document.getElementById('searchInput').value = item;
            document.getElementById('suggestions').style.display = 'none';
            filterInventory();
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            initializeInventoryFunctions();
            initializeEnhancedSearch();
            initializeAnimations();

            // Add staggered animations to cards
            const cards = document.querySelectorAll('.inventory-card, .stat-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Render chart
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

        // Chart rendering function
        function renderStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            
            // Count statuses for chart data
            const statusCounts = {
                available: 0,
                reserved: 0,
                expired: 0,
                damaged: 0
            };
            
            document.querySelectorAll('.inventory-card').forEach(card => {
                const status = card.dataset.status;
                if (statusCounts.hasOwnProperty(status)) {
                    statusCounts[status]++;
                }
            });
            
            // Create chart
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Available', 'Reserved', 'Expired', 'Damaged'],
                    datasets: [{
                        data: [
                            statusCounts.available,
                            statusCounts.reserved,
                            statusCounts.expired,
                            statusCounts.damaged
                        ],
                        backgroundColor: [
                            '#48bb78',
                            '#ed8936',
                            '#f56565',
                            '#a0aec0'
                        ],
                        borderWidth: 2,
                        borderColor: 'var(--card-bg)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'var(--text-primary)',
                                font: {
                                    family: 'Inter, sans-serif'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Inventory Status Distribution',
                            color: 'var(--text-primary)',
                            font: {
                                family: 'Inter, sans-serif',
                                size: 16,
                                weight: '600'
                            }
                        }
                    }
                }
            });
        }

        // Quick action functions
        function exportInventory() {
            showNotification('Exporting inventory data...', 'info');
            // In a real application, this would trigger a server-side export
        }

        function showExpiryReport() {
            showNotification('Generating expiry report...', 'info');
            // In a real application, this would show a modal with expiry report
            setTimeout(() => {
                showNotification('Expiry report generated!', 'success');
            }, 1500);
        }

        function showLowStockReport() {
            showNotification('Generating low stock report...', 'info');
            // In a real application, this would show a modal with low stock items
            setTimeout(() => {
                showNotification('Low stock report generated!', 'success');
            }, 1500);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('open');
            mainContent.classList.toggle('sidebar-open');
        }

        // Enhanced notification system
        function showNotification(message, type = 'info', duration = 4000) {
            const notification = document.createElement('div');
            const colors = {
                success: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                info: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)'
            };

            notification.className = `notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                z-index: 10000;
                animation: slideInRight 0.3s ease;
                max-width: 350px;
                font-family: 'Inter', sans-serif;
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
            }, duration);
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

        // Enhanced loading state
        function showLoading(message = 'Loading...') {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <p>${message}</p>
                </div>
            `;
            document.body.appendChild(overlay);
        }

        function hideLoading() {
            const overlay = document.querySelector('.loading-overlay');
            if (overlay) {
                overlay.remove();
            }
        }

        // Enhanced search functionality
        function initializeEnhancedSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.classList.add('search-input');
                searchInput.parentElement.classList.add('search-container');

                const searchIcon = document.createElement('i');
                searchIcon.className = 'fas fa-search search-icon';
                searchInput.parentElement.insertBefore(searchIcon, searchInput);
            }
        }

        // Enhanced animations on page load
        function initializeAnimations() {
            const cards = document.querySelectorAll('.stat-card, .inventory-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }

        // Enhanced modal system
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop';
                backdrop.onclick = () => hideModal(modalId);
                document.body.appendChild(backdrop);

                modal.style.display = 'block';
                modal.style.animation = 'slideIn 0.3s ease';
            }
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            const backdrop = document.querySelector('.modal-backdrop');
            if (modal) {
                modal.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
            if (backdrop) {
                backdrop.remove();
            }
        }
    </script>

    <!-- Floating Action Button -->
    <div class="fab" onclick="showQuickAddModal()" title="Quick Add Inventory Item">
        <i class="fas fa-plus"></i>
    </div>

</body>
</html>