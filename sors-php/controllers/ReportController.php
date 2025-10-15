<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SORSDatabase.php';
require_once __DIR__ . '/../models/Surgery.php';

class ReportController extends BaseController {
    private $db;
    private $surgery;

    public function __construct() {
        parent::__construct();
        $this->db = SORSDatabase::getInstance();
        $this->surgery = new Surgery();
    }

    public function list() {
        $this->requireAuth();

        try {
            $reports = $this->getReports();
            $data = [
                'title' => 'Reports',
                'reports' => $reports,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('reports/list', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading reports: ' . $e->getMessage());
            $this->redirect('index.php?route=dashboard');
        }
    }

    public function generate() {
        $this->requireAuth();

        try {
            $type = $_POST['type'] ?? 'summary';
            $data = $_POST['data'] ?? [];

            $reportData = $this->generateReportData($type, $data);
            $data = [
                'title' => 'Generate Report',
                'reportData' => $reportData,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('reports/generate', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error generating report: ' . $e->getMessage());
            $this->redirect('index.php?route=reports/list');
        }
    }

    public function view() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Report ID required');
            $this->redirect('index.php?route=reports/list');
            return;
        }

        try {
            $report = $this->getReportById($id);
            if (!$report) {
                $this->setFlash('error', 'Report not found');
                $this->redirect('index.php?route=reports/list');
                return;
            }

            $data = [
                'title' => 'View Report',
                'report' => $report,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('reports/view', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error viewing report: ' . $e->getMessage());
            $this->redirect('index.php?route=reports/list');
        }
    }

    public function download() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Report ID required');
            $this->redirect('index.php?route=reports/list');
            return;
        }

        try {
            $report = $this->getReportById($id);
            if (!$report) {
                $this->setFlash('error', 'Report not found');
                $this->redirect('index.php?route=reports/list');
                return;
            }

            // Generate PDF or export functionality here
            $this->setFlash('info', 'Download functionality coming soon');
            $this->redirect('index.php?route=reports/view&id=' . $id);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error downloading report: ' . $e->getMessage());
            $this->redirect('index.php?route=reports/list');
        }
    }

    private function getReports() {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM sors_reports ORDER BY created_at DESC";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getReportById($id) {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM sors_reports WHERE id = ? LIMIT 1";
            return $this->db->fetchOne($sql, [$id]);
        } catch (Exception $e) {
            return null;
        }
    }

    private function generateReportData($type, $data) {
        switch ($type) {
            case 'surgery_summary':
                return $this->generateSurgerySummary($data);
            case 'room_utilization':
                return $this->generateRoomUtilization($data);
            case 'staff_performance':
                return $this->generateStaffPerformance($data);
            default:
                return ['error' => 'Invalid report type'];
        }
    }

    private function generateSurgerySummary($data) {
        // Implementation for surgery summary report
        return [
            'title' => 'Surgery Summary Report',
            'data' => 'Surgery summary data would be generated here'
        ];
    }

    private function generateRoomUtilization($data) {
        // Implementation for room utilization report
        return [
            'title' => 'Room Utilization Report',
            'data' => 'Room utilization data would be generated here'
        ];
    }

    private function generateStaffPerformance($data) {
        // Implementation for staff performance report
        return [
            'title' => 'Staff Performance Report',
            'data' => 'Staff performance data would be generated here'
        ];
    }
}
