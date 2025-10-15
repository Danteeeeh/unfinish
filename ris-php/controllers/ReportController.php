<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Report.php';

class ReportController extends BaseController {
    private $db;
    private $report;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->report = new Report();
    }

    public function index() {
        $reports = $this->report->getAll();
        $data = [
            'reports' => $reports,
            'pageTitle' => 'Reports'
        ];
        $this->view('reports/index', $data);
    }

    public function list() {
        $reports = $this->report->getAll();
        $breadcrumbs = [
            ['title' => 'Reports', 'url' => 'index.php?route=reports/list']
        ];
        $data = [
            'reports' => $reports,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Reports List'
        ];
        $this->view('reports/list', $data);
    }

    public function create() {
        $breadcrumbs = [
            ['title' => 'Reports', 'url' => 'index.php?route=reports/list'],
            ['title' => 'Create Report']
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['created_by'] = $_SESSION['user_id'] ?? null;
            $this->report->create($data);
            $this->setFlash('success', 'Report created successfully');
            header('Location: index.php?route=reports/list');
            exit();
        }
        $data = [
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Create Report'
        ];
        $this->view('reports/create', $data);
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Report ID required');
            header('Location: index.php?route=reports/list');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->report->update($id, $data);
            $this->setFlash('success', 'Report updated successfully');
            header('Location: index.php?route=reports/list');
            exit();
        }
        $report = $this->report->getById($id);
        $breadcrumbs = [
            ['title' => 'Reports', 'url' => 'index.php?route=reports/list'],
            ['title' => 'Edit Report']
        ];
        $data = [
            'report' => $report,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Edit Report'
        ];
        $this->view('reports/edit', $data);
    }

    public function view($view, $data = []) {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Report ID required');
            header('Location: index.php?route=reports/list');
            exit();
        }
        $report = $this->report->getById($id);
        $breadcrumbs = [
            ['title' => 'Reports', 'url' => 'index.php?route=reports/list'],
            ['title' => 'Report Details']
        ];
        $data = [
            'report' => $report,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Report Details'
        ];
        parent::view($view, $data);
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Report ID required');
            header('Location: index.php?route=reports/list');
            exit();
        }
        $this->report->delete($id);
        $this->setFlash('success', 'Report deleted successfully');
        header('Location: index.php?route=reports/list');
        exit();
    }

    public function generatePDF() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Report ID required');
            header('Location: index.php?route=reports/list');
            exit();
        }
        // PDF generation logic here
        $this->setFlash('info', 'PDF generation feature coming soon');
        header('Location: index.php?route=reports/view&id=' . $id);
        exit();
    }

    public function finalize() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Report ID required');
            header('Location: index.php?route=reports/list');
            exit();
        }
        $this->report->finalize($id, $_SESSION['user_id'] ?? null);
        $this->setFlash('success', 'Report finalized successfully');
        header('Location: index.php?route=reports/view&id=' . $id);
        exit();
    }
}
