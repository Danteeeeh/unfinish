<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SORSDatabase.php';
require_once __DIR__ . '/../models/Surgery.php';
require_once __DIR__ . '/../models/OperatingRoom.php';
require_once __DIR__ . '/../models/Staff.php';

class DashboardController extends BaseController {
    private $db;
    private $surgery;
    private $room;
    private $staff;

    public function __construct() {
        parent::__construct();
        $this->db = SORSDatabase::getInstance();
        $this->surgery = new Surgery();
        $this->room = new OperatingRoom();
        $this->staff = new Staff();
    }

    public function index() {
        try {
            // Test database connection first
            $this->testDatabaseConnection();

            $stats = $this->getStats();
            $recentSurgeries = $this->surgery->getRecent();
            $roomStatus = $this->room->getStatus();

            $data = [
                'title' => 'Dashboard',
                'stats' => $stats,
                'recentSurgeries' => $recentSurgeries,
                'roomStatus' => $roomStatus,
                'currentUser' => $this->getCurrentUser()
            ];

            $this->view('dashboard/index', $data);
        } catch (Exception $e) {
            $this->renderErrorPage($e->getMessage());
        }
    }

    private function testDatabaseConnection() {
        try {
            $conn = $this->db->getConnection();

            // Check if required tables exist
            $requiredTables = ['sors_surgeries', 'sors_operating_rooms', 'staff'];
            foreach ($requiredTables as $table) {
                $stmt = $conn->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() == 0) {
                    throw new Exception("Required table '$table' not found. Please import the database schema.");
                }
            }

        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage() . ". Please ensure the 'healthcare_unified' database exists and contains the required tables.");
        }
    }

    private function renderErrorPage($error) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>SORS - Connection Error</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                /* Enhanced SORS Error Styling with LIS Design Pattern */
                :root {
                    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                    --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
                    --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
                    --sors-gradient: linear-gradient(135deg, #059669 0%, #047857 100%);

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
                    --sidebar-bg: #f8f9fa;
                }

                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: var(--primary-gradient);
                    color: var(--text-primary);
                    line-height: 1.6;
                    transition: background-color 0.3s ease, color 0.3s ease;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .error-container {
                    max-width: 800px;
                    margin: 2rem;
                    background: var(--card-bg);
                    padding: 3rem;
                    border-radius: 20px;
                    box-shadow: var(--shadow-lg);
                    text-align: center;
                }

                .error-title {
                    color: #ef4444;
                    font-size: 2.5em;
                    margin-bottom: 1.5rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5rem;
                }

                .error-message {
                    background: #fef2f2;
                    padding: 1.5rem;
                    border-radius: 12px;
                    border-left: 5px solid #ef4444;
                    margin-bottom: 2rem;
                    line-height: 1.6;
                    color: #7f1d1d;
                }

                .solution-steps {
                    background: #f0f9ff;
                    padding: 1.5rem;
                    border-radius: 12px;
                    margin-bottom: 2rem;
                    text-align: left;
                }

                .solution-steps h3 {
                    color: #1e40af;
                    margin-bottom: 1rem;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .solution-steps ol {
                    margin: 0;
                    padding-left: 1.5rem;
                }

                .solution-steps li {
                    margin-bottom: 0.75rem;
                    line-height: 1.5;
                }

                .action-buttons {
                    display: flex;
                    gap: 1rem;
                    justify-content: center;
                    flex-wrap: wrap;
                }

                .btn {
                    padding: 0.75rem 1.5rem;
                    border: none;
                    border-radius: 8px;
                    font-weight: 600;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-size: 0.875rem;
                    transition: all 0.3s ease;
                    cursor: pointer;
                }

                .btn-primary {
                    background: var(--sors-gradient);
                    color: white;
                }

                .btn-primary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
                }

                .btn-success {
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: white;
                }

                .btn-success:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
                }

                code {
                    background: #f1f5f9;
                    padding: 0.25rem 0.5rem;
                    border-radius: 4px;
                    font-family: 'Courier New', monospace;
                    color: #e11d48;
                }

                @media (max-width: 768px) {
                    .error-container {
                        margin: 1rem;
                        padding: 2rem;
                    }

                    .error-title {
                        font-size: 2em;
                    }

                    .action-buttons {
                        flex-direction: column;
                    }
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1 class="error-title"><i class="fas fa-exclamation-triangle"></i> SORS System Error</h1>

                <div class="error-message">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>

                <div class="solution-steps">
                    <h3><i class="fas fa-wrench"></i> How to Fix This:</h3>
                    <ol>
                        <li><strong>Ensure MySQL is running</strong> - Make sure XAMPP/WAMP is started</li>
                        <li><strong>Create the database</strong> - Run: <code>CREATE DATABASE healthcare_unified;</code></li>
                        <li><strong>Import the schema</strong> - Import the file: <code>healthcare_unified.sql</code></li>
                        <li><strong>Test the connection</strong> - Visit: <a href="../ris_test.php" target="_blank">Database Test</a></li>
                    </ol>
                </div>

                <div class="action-buttons">
                    <a href="../../../dashboard.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Main Dashboard
                    </a>
                    <a href="../ris_test.php" class="btn btn-success" target="_blank">
                        <i class="fas fa-database"></i> Test Database Connection
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    private function getStats() {
        try {
            $conn = $this->db->getConnection();
            $today = date('Y-m-d');

            // Total surgeries
            $stmt = $conn->query("SELECT COUNT(*) as count FROM sors_surgeries");
            $totalSurgeries = $stmt ? $stmt->fetch()['count'] : 0;

            // Surgeries today
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM sors_surgeries WHERE DATE(scheduled_date) = ?");
            $stmt->execute([$today]);
            $surgeriesToday = $stmt ? $stmt->fetch()['count'] : 0;

            // Available rooms
            $stmt = $conn->query("SELECT COUNT(*) as count FROM sors_operating_rooms WHERE status = 'available'");
            $availableRooms = $stmt ? $stmt->fetch()['count'] : 0;

            return [
                'total_surgeries' => $totalSurgeries,
                'surgeries_today' => $surgeriesToday,
                'available_rooms' => $availableRooms
            ];
        } catch (Exception $e) {
            return [
                'total_surgeries' => 0,
                'surgeries_today' => 0,
                'available_rooms' => 0
            ];
        }
    }
}
?>
