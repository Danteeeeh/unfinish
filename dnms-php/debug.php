<?php
/**
 * DNMS System Diagnostic Tool
 * Comprehensive testing and debugging interface for DNMS
 */

// Prevent direct access
if (!defined('ALLOW_DEBUG')) {
    define('ALLOW_DEBUG', true);
}

// Start output buffering for clean HTML
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏥 DNMS System Diagnostic Tool</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            --warning-gradient: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            --error-gradient: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            --info-gradient: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --border-color: #dee2e6;
            --border-radius: 12px;
            --box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            box-shadow: var(--box-shadow);
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-success {
            background: var(--success-gradient);
            color: white;
        }

        .status-warning {
            background: var(--warning-gradient);
            color: white;
        }

        .status-error {
            background: var(--error-gradient);
            color: white;
        }

        .status-info {
            background: var(--info-gradient);
            color: white;
        }

        .test-section {
            background: var(--bg-secondary);
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
            border-left: 5px solid var(--info-gradient);
        }

        .test-section.success {
            border-left-color: #2ecc71;
        }

        .test-section.warning {
            border-left-color: #f39c12;
        }

        .test-section.error {
            border-left-color: #e74c3c;
        }

        .test-title {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .test-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .test-item:last-child {
            border-bottom: none;
        }

        .test-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8em;
            flex-shrink: 0;
        }

        .test-icon.success {
            background: var(--success-gradient);
            color: white;
        }

        .test-icon.warning {
            background: var(--warning-gradient);
            color: white;
        }

        .test-icon.error {
            background: var(--error-gradient);
            color: white;
        }

        .test-icon.info {
            background: var(--info-gradient);
            color: white;
        }

        .test-text {
            flex: 1;
            font-weight: 500;
        }

        .test-details {
            font-size: 0.9em;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            overflow: hidden;
            margin-top: 20px;
        }

        .progress-fill {
            height: 100%;
            background: var(--success-gradient);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .recommendations {
            background: linear-gradient(135deg, #fff7e6 0%, #ffeaa7 100%);
            border: 1px solid #ffd700;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-top: 30px;
        }

        .recommendations h3 {
            color: #d68910;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .recommendations ul {
            list-style: none;
            padding: 0;
        }

        .recommendations li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }

        .recommendations li::before {
            content: '🔧';
            position: absolute;
            left: 0;
        }

        .footer {
            text-align: center;
            color: var(--text-secondary);
            margin-top: 40px;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }

        .timestamp {
            font-size: 0.9em;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 2em;
            }

            .test-section {
                padding: 20px;
            }

            .test-title {
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-stethoscope"></i> DNMS System Diagnostic Tool</h1>
            <p>Comprehensive system health check for Diet & Nutrition Management System</p>
            <div class="status-badge status-info">Running Diagnostics...</div>
        </div>

        <?php
        // Initialize counters
        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;
        $warningTests = 0;

        function testResult($condition, $message, $details = '', $type = 'info') {
            global $totalTests, $passedTests, $failedTests, $warningTests;

            $totalTests++;
            $iconClass = 'info';

            if ($type === 'success' && $condition) {
                $passedTests++;
                $iconClass = 'success';
            } elseif ($type === 'error' || !$condition) {
                if ($condition) {
                    $passedTests++;
                    $iconClass = 'success';
                } else {
                    $failedTests++;
                    $iconClass = 'error';
                }
            } elseif ($type === 'warning') {
                $warningTests++;
                $iconClass = 'warning';
            }

            echo "<div class='test-item'>";
            echo "<div class='test-icon $iconClass'>";
            echo $iconClass === 'success' ? '<i class="fas fa-check"></i>' :
                 ($iconClass === 'error' ? '<i class="fas fa-times"></i>' :
                 ($iconClass === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' : '<i class="fas fa-info-circle"></i>'));
            echo "</div>";
            echo "<div class='test-text'>";
            echo htmlspecialchars($message);
            if ($details) {
                echo "<div class='test-details'>" . htmlspecialchars($details) . "</div>";
            }
            echo "</div>";
            echo "</div>";
        }

        function getSectionClass() {
            global $passedTests, $failedTests, $warningTests, $totalTests;
            if ($failedTests > 0) return 'error';
            if ($warningTests > 0) return 'warning';
            return 'success';
        }
        ?>

        <!-- File System Tests -->
        <div class="test-section <?php echo getSectionClass(); ?>">
            <div class="test-title">
                <i class="fas fa-folder-open"></i>
                File System Check
            </div>

            <?php
            $files_to_check = [
                'config/database.php' => 'Database configuration',
                'config/constants.php' => 'System constants',
                'config/environment.php' => 'Environment settings',
                'classes/User.php' => 'User class',
                'classes/Food.php' => 'Food class',
                'classes/Meal.php' => 'Meal class',
                'controllers/user/dashboard.php' => 'Dashboard controller',
                'views/user/dashboard_view.php' => 'Dashboard view',
                'includes/navigation.php' => 'Navigation component'
            ];

            foreach ($files_to_check as $file => $description) {
                $path = __DIR__ . '/' . $file;
                $exists = file_exists($path);
                $readable = $exists ? is_readable($path) : false;

                if ($exists && $readable) {
                    testResult(true, "$description found", "Path: $file", 'success');
                } elseif ($exists) {
                    testResult(false, "$description exists but not readable", "Path: $file", 'warning');
                } else {
                    testResult(false, "$description missing", "Path: $file", 'error');
                }
            }
            ?>
        </div>

        <!-- Database Connection Tests -->
        <div class="test-section <?php echo getSectionClass(); ?>">
            <div class="test-title">
                <i class="fas fa-database"></i>
                Database Connection Test
            </div>

            <?php
            try {
                // Load DNMS config
                require_once __DIR__ . '/config/database.php';

                // Try to connect
                $db = getDNMSDBConnection();
                testResult(true, "Database connection successful", "Connected to healthcare_unified", 'success');

                // Check if required tables exist
                $tables = ['users', 'dnms_foods', 'dnms_meals'];
                foreach ($tables as $table) {
                    try {
                        $result = $db->query("SHOW TABLES LIKE '$table'");
                        if ($result->rowCount() > 0) {
                            testResult(true, "Table '$table' exists", "Required for DNMS functionality", 'success');
                        } else {
                            testResult(false, "Table '$table' missing", "Run database_setup.php to create tables", 'error');
                        }
                    } catch (Exception $e) {
                        testResult(false, "Error checking table '$table'", $e->getMessage(), 'error');
                    }
                }

                // Test sample data
                try {
                    $result = $db->query("SELECT COUNT(*) as count FROM users");
                    $userCount = $result->fetch()['count'];
                    testResult($userCount > 0, "Users table has data", "$userCount users found", $userCount > 0 ? 'success' : 'warning');
                } catch (Exception $e) {
                    testResult(false, "Cannot query users table", $e->getMessage(), 'error');
                }

                $db = null;
            } catch (Exception $e) {
                testResult(false, "Database connection failed", $e->getMessage(), 'error');
            }
            ?>
        </div>

        <!-- Session Tests -->
        <div class="test-section <?php echo getSectionClass(); ?>">
            <div class="test-title">
                <i class="fas fa-user-circle"></i>
                Session & Authentication Test
            </div>

            <?php
            // Test session status
            $sessionStatus = session_status();
            $sessionActive = $sessionStatus === PHP_SESSION_ACTIVE;
            testResult($sessionActive, "Session is active", "Session ID: " . session_id(), $sessionActive ? 'success' : 'error');

            // Test session variables
            if (isset($_SESSION['user_id'])) {
                testResult(true, "User is logged in", "User ID: " . $_SESSION['user_id'], 'success');
            } else {
                testResult(false, "No active user session", "User needs to login first", 'warning');
            }

            // Test main config availability
            if (function_exists('isLoggedIn')) {
                testResult(function_exists('isLoggedIn'), "Main authentication functions available", "isLoggedIn() function found", 'success');
            } else {
                testResult(false, "Main authentication functions missing", "config.php may not be loaded", 'warning');
            }
            ?>
        </div>

        <!-- System Information -->
        <div class="test-section">
            <div class="test-title">
                <i class="fas fa-info-circle"></i>
                System Information
            </div>

            <?php
            testResult(true, "PHP Version", PHP_VERSION, 'info');
            testResult(true, "Server Software", $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown', 'info');
            testResult(true, "Document Root", $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown', 'info');
            testResult(true, "Current Script", $_SERVER['PHP_SELF'] ?? 'Unknown', 'info');
            testResult(true, "Operating System", PHP_OS, 'info');
            ?>
        </div>

        <!-- Progress Summary -->
        <?php
        $progressPercentage = $totalTests > 0 ? round(($passedTests / $totalTests) * 100) : 0;
        ?>

        <div class="test-section success" style="text-align: center;">
            <h3 style="margin-bottom: 20px; color: #27ae60;">
                <i class="fas fa-chart-line"></i> Diagnostic Summary
            </h3>
            <div style="font-size: 3em; font-weight: bold; margin-bottom: 10px; color: #2ecc71;">
                <?php echo $progressPercentage; ?>%
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progressPercentage; ?>%"></div>
            </div>
            <p style="margin-top: 15px; color: #7f8c8d;">
                <?php echo $passedTests; ?> passed • <?php echo $failedTests; ?> failed • <?php echo $warningTests; ?> warnings
            </p>
        </div>

        <!-- Recommendations -->
        <?php if ($failedTests > 0 || $warningTests > 0): ?>
        <div class="recommendations">
            <h3><i class="fas fa-lightbulb"></i> Recommendations</h3>
            <ul>
                <?php if ($failedTests > 0): ?>
                    <li>Run the database setup script: <strong>database_setup.php</strong></li>
                    <li>Check file permissions for missing files</li>
                    <li>Verify database server is running</li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li>Login to the system to test full functionality</li>
                <?php endif; ?>
                <li>Check PHP error logs for detailed error information</li>
                <li>Ensure all required PHP extensions are enabled</li>
            </ul>
        </div>
        <?php endif; ?>

        <div class="footer">
            <div class="timestamp">
                <i class="fas fa-clock"></i>
                Report generated on: <?php echo date('Y-m-d H:i:s'); ?>
            </div>
            <p>DNMS System Diagnostic Tool v1.0 | © <?php echo date('Y'); ?> Healthcare System</p>
        </div>
    </div>

    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress bar
            const progressFill = document.querySelector('.progress-fill');
            if (progressFill) {
                progressFill.style.width = '0%';
                setTimeout(() => {
                    progressFill.style.width = '<?php echo $progressPercentage; ?>%';
                }, 500);
            }

            // Add hover effects to test items
            document.querySelectorAll('.test-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</body>
</html>

<?php
// Flush output buffer
ob_end_flush();
?>
