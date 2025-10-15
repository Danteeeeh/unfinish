<?php
/**
 * Dashboard Controller
 * Handles dashboard display and statistics
 */

require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/SampleModel.php';
require_once __DIR__ . '/../models/TestModel.php';
require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../config/database.php';

class DashboardController {
    private $patientModel;
    private $sampleModel;
    private $testModel;
    private $db;

    public function __construct() {
        requireLogin();
        $this->patientModel = new PatientModel();
        $this->sampleModel = new SampleModel();
        $this->testModel = new TestModel();

        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Display dashboard
     */
    public function index() {
        $stats = $this->getStatistics();
        $recentActivity = $this->getRecentActivity();

        require_once __DIR__ . '/../views/dashboard/index.php';
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics() {
        // Get total patients
        $totalPatients = $this->patientModel->getTotalCount();

        // Get samples today
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM lis_results WHERE DATE(collection_date) = ?");
        $stmt->execute([$today]);
        $samplesToday = $stmt->fetch()['count'];

        // Get pending tests
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM lis_tests WHERE status = 'pending'");
        $stmt->execute();
        $pendingTests = $stmt->fetch()['count'];

        // Get completed tests today
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM lis_tests WHERE status = 'completed' AND DATE(updated_at) = ?");
        $stmt->execute([$today]);
        $completedToday = $stmt->fetch()['count'];

        return [
            'total_patients' => $totalPatients,
            'samples_today' => $samplesToday,
            'pending_tests' => $pendingTests,
            'completed_today' => $completedToday
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity() {
        $activities = [];

        // Get recent samples
        $stmt = $this->db->prepare("
            SELECT lr.id, lr.order_number, lr.collection_date, lr.status,
                   p.full_name, p.patient_id
            FROM lis_results lr
            JOIN patients p ON lr.patient_id = p.id
            ORDER BY lr.created_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $samples = $stmt->fetchAll();

        foreach ($samples as $row) {
            $statusText = ucfirst($row['status']);
            $activities[] = [
                'type' => 'sample',
                'icon' => 'fa-file-medical',
                'title' => "Order {$row['order_number']}",
                'description' => "{$statusText} for patient ID: {$row['patient_id']}",
                'time' => $row['collection_date'],
                'status' => $row['status']
            ];
        }

        // Get recent tests
        $stmt = $this->db->prepare("
            SELECT lt.test_name, lt.status, lt.created_at,
                   lr.order_number, p.patient_id
            FROM lis_tests lt
            JOIN lis_results lr ON lt.id = lr.test_id
            JOIN patients p ON lr.patient_id = p.id
            ORDER BY lt.created_at DESC
            LIMIT 5
        ");
        $stmt->execute();
        $tests = $stmt->fetchAll();

        foreach ($tests as $row) {
            $activities[] = [
                'type' => 'test',
                'icon' => 'fa-flask',
                'title' => $row['test_name'] . ' completed',
                'description' => "For patient ID: {$row['patient_id']}",
                'time' => $row['created_at'],
                'status' => $row['status']
            ];
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }
}
