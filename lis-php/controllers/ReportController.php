<?php
/**
 * Report Controller
 * Handles report generation and viewing
 */

require_once __DIR__ . '/../models/TestModel.php';
require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../includes/auth_functions.php';

class ReportController {
    private $testModel;
    private $patientModel;

    public function __construct() {
        requireLogin();
        $this->testModel = new TestModel();
        $this->patientModel = new PatientModel();
    }

    /**
     * Generate report
     */
    public function generate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $testId = (int)$_POST['test_id'];
            $test = $this->testModel->getById($testId);
            
            if (!$test) {
                setFlashMessage('error', 'Test not found.');
                redirect('index.php?route=reports/list');
            }
            
            if ($test['status'] !== TEST_STATUS_VERIFIED) {
                setFlashMessage('error', 'Only verified tests can be reported.');
                redirect('index.php?route=reports/list');
            }
            
            // Generate PDF report (placeholder - requires PDF library like TCPDF or FPDF)
            $reportPath = $this->generatePDFReport($test);
            
            if ($reportPath) {
                setFlashMessage('success', 'Report generated successfully!');
                redirect('index.php?route=reports/view&test_id=' . $testId);
            } else {
                setFlashMessage('error', 'Failed to generate report.');
            }
        }
        
        $verifiedTests = $this->testModel->getByStatus(TEST_STATUS_VERIFIED);
        require_once __DIR__ . '/../views/reports/generate_report.php';
    }

    /**
     * View report
     */
    public function view() {
        $testId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
        
        if (!$testId) {
            setFlashMessage('error', 'Invalid test ID.');
            redirect('index.php?route=reports/list');
        }
        
        $test = $this->testModel->getById($testId);
        
        if (!$test) {
            setFlashMessage('error', 'Test not found.');
            redirect('index.php?route=reports/list');
        }
        
        require_once __DIR__ . '/../views/reports/view_report.php';
    }

    /**
     * List all reports
     */
    public function list() {
        $completedTests = $this->testModel->getByStatus(TEST_STATUS_COMPLETED);
        $verifiedTests = $this->testModel->getByStatus(TEST_STATUS_VERIFIED);
        
        require_once __DIR__ . '/../views/reports/list_reports.php';
    }

    /**
     * Download report
     */
    public function download() {
        $testId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
        
        if (!$testId) {
            setFlashMessage('error', 'Invalid test ID.');
            redirect('index.php?route=reports/list');
        }
        
        $test = $this->testModel->getById($testId);
        
        if (!$test) {
            setFlashMessage('error', 'Test not found.');
            redirect('index.php?route=reports/list');
        }
        
        $reportPath = UPLOAD_PATH . 'reports/' . $test['test_id'] . '.pdf';
        
        if (file_exists($reportPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $test['test_id'] . '_report.pdf"');
            header('Content-Length: ' . filesize($reportPath));
            readfile($reportPath);
            exit;
        } else {
            setFlashMessage('error', 'Report file not found.');
            redirect('index.php?route=reports/list');
        }
    }

    /**
     * Generate PDF report (placeholder function)
     * In production, use TCPDF, FPDF, or similar library
     */
    private function generatePDFReport($test) {
        // Create reports directory if it doesn't exist
        $reportsDir = UPLOAD_PATH . 'reports/';
        if (!file_exists($reportsDir)) {
            mkdir($reportsDir, 0755, true);
        }
        
        $reportPath = $reportsDir . $test['test_id'] . '.pdf';
        
        // Placeholder: In production, implement actual PDF generation
        // Example with TCPDF or FPDF:
        /*
        require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
        
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        
        $html = $this->getReportHTML($test);
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $pdf->Output($reportPath, 'F');
        */
        
        // For now, create a simple text file as placeholder
        $content = "Laboratory Test Report\n\n";
        $content .= "Test ID: " . $test['test_id'] . "\n";
        $content .= "Patient: " . $test['first_name'] . " " . $test['last_name'] . "\n";
        $content .= "Test Name: " . $test['test_name'] . "\n";
        $content .= "Results: " . $test['test_results'] . "\n";
        $content .= "Verified By: " . $test['verified_by'] . "\n";
        $content .= "Date: " . date(DISPLAY_DATETIME_FORMAT) . "\n";
        
        file_put_contents($reportPath, $content);
        
        return $reportPath;
    }

    /**
     * Get HTML template for report
     */
    private function getReportHTML($test) {
        ob_start();
        include __DIR__ . '/../views/reports/report_template.php';
        return ob_get_clean();
    }
}
