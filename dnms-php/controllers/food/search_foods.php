<?php
/**
 * Food Search Controller
 * Handles both web interface and API requests for food search
 */

// Prevent direct access issues
if (!defined('DNMS_ENTRY_POINT')) {
    define('DNMS_ENTRY_POINT', true);
}

// Load main config for authentication functions only (don't restart session)
try {
    require_once __DIR__ . '/../../../config.php';
} catch (Exception $e) {
    // If main config fails, redirect to setup
    header('Location: ../../../../database_setup.php');
    exit();
}

// Load DNMS config
try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../config/constants.php';
} catch (Exception $e) {
    die("DNMS Configuration Error: " . $e->getMessage());
}

// Load includes with error handling
try {
    require_once __DIR__ . '/../../includes/auth_functions.php';
} catch (Exception $e) {
    error_log("DNMS Auth Functions Error: " . $e->getMessage());
}

// Check if user is logged in using main system's session
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

// Initialize Food class
require_once __DIR__ . '/../../classes/Food.php';

try {
    $food = new Food();

    // Handle different request types
    if (isset($_GET['api']) && $_GET['api'] === '1') {
        // API request - return JSON
        handleAPIRequest($food);
    } else {
        // Web request - use layout system
        handleWebRequest($food);
    }
} catch (Exception $e) {
    error_log("DNMS Food Search Error: " . $e->getMessage());

    if (isset($_GET['api']) && $_GET['api'] === '1') {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error loading food search']);
    } else {
        showError("Failed to load food search: " . $e->getMessage());
    }
}

/**
 * Handle API requests (return JSON)
 */
function handleAPIRequest($food) {
    header('Content-Type: application/json');

    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $limit = 50; // Default limit

    try {
        if (!empty($searchTerm)) {
            $foods = $food->searchFoods($searchTerm, $limit);
        } elseif (!empty($category)) {
            $foods = $food->getFoodsByCategory($category, $limit);
        } else {
            $foods = $food->getAll();
        }

        echo json_encode($foods);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error searching foods']);
    }
}

/**
 * Handle web requests (use layout system)
 */
function handleWebRequest($food) {
    // Get search parameters
    $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';

    // Get foods data
    $foods = [];
    try {
        if (!empty($searchTerm)) {
            $foods = $food->searchFoods($searchTerm, 50);
        } elseif (!empty($category)) {
            $foods = $food->getFoodsByCategory($category, 50);
        } else {
            $foods = $food->getAll();
        }
    } catch (Exception $e) {
        $foods = [];
    }

    // Load layout and inject food search content
    ob_start();
    include __DIR__ . '/../../views/food/search.php';
    $searchContent = ob_get_clean();

    // Use the same layout structure as dashboard
    $layoutFile = __DIR__ . '/../../views/layouts/header.php';
    if (file_exists($layoutFile)) {
        $layoutContent = file_get_contents($layoutFile);
        // Replace the placeholder with food search content
        $layoutContent = str_replace('<?php if (function_exists(\'displayFlashMessage\')) displayFlashMessage(); ?>', $searchContent, $layoutContent);
        echo $layoutContent;
    } else {
        // Fallback to simple layout if header.php doesn't exist
        echo $searchContent;
    }
}

/**
 * Show error page
 */
function showError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="en" data-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Food Search Error - NutriTrack Pro</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                /* Unified DNMS Color System - Single Primary Color */
                --dnms-primary: #2563eb;          /* Professional blue */
                --dnms-primary-dark: #1d4ed8;     /* Darker blue for hover */
                --dnms-primary-light: #3b82f6;    /* Lighter blue for accents */
                --dnms-secondary: #10b981;        /* Success green */
                --dnms-warning: #f59e0b;          /* Warning orange */
                --dnms-danger: #ef4444;           /* Error red */
                --dnms-info: #06b6d4;             /* Info cyan */

                /* Background Colors */
                --bg-primary: #ffffff;
                --bg-secondary: #f8fafc;
                --bg-tertiary: #f1f5f9;
                --bg-card: #ffffff;

                /* Text Colors */
                --text-primary: #1e293b;
                --text-secondary: #64748b;
                --text-muted: #94a3b8;

                /* Border and Shadow */
                --border-color: #e2e8f0;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            /* Dark Theme - Consistent with light theme colors */
            [data-theme="dark"] {
                --bg-primary: #0f172a;
                --bg-secondary: #1e293b;
                --bg-tertiary: #334155;
                --bg-card: #1e293b;
                --text-primary: #f1f5f9;
                --text-secondary: #cbd5e1;
                --border-color: #334155;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
                --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg-secondary);
                color: var(--text-primary);
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .error-container {
                background: var(--bg-primary);
                padding: 3rem;
                border-radius: 20px;
                box-shadow: var(--shadow-md);
                text-align: center;
                max-width: 600px;
                border: 1px solid var(--border-color);
                position: relative;
                overflow: hidden;
            }

            .error-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: var(--dnms-danger);
            }

            .error-icon {
                width: 80px;
                height: 80px;
                background: var(--dnms-danger);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 2.5rem;
                margin: 0 auto 2rem;
                animation: pulse 2s infinite;
            }

            .error-title {
                font-size: 2rem;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 1rem;
            }

            .error-message {
                color: var(--text-secondary);
                font-size: 1.125rem;
                margin-bottom: 2rem;
                line-height: 1.6;
            }

            .error-actions {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn {
                padding: 1rem 2rem;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .btn-primary {
                background: var(--dnms-primary);
                color: white;
                box-shadow: var(--shadow-md);
            }

            .btn-primary:hover {
                background: var(--dnms-primary-dark);
                transform: translateY(-2px);
                box-shadow: var(--shadow-lg);
            }

            .btn-secondary {
                background: var(--bg-secondary);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
            }

            .btn-secondary:hover {
                background: var(--border-color);
                transform: translateY(-1px);
            }

            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
            }

            @media (max-width: 768px) {
                .error-container {
                    padding: 2rem 1.5rem;
                    margin: 1rem;
                }

                .error-actions {
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .btn {
                    justify-content: center;
                }
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>🍽️ Food Search Error</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <div class="error-actions">
                <a href="../food/search_foods.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Food Search
                </a>
                <a href="../../../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i>
                    Main Dashboard
                </a>
            </div>
        </div>

        <script>
            // Initialize theme
            document.addEventListener('DOMContentLoaded', function() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>
