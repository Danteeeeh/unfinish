<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Study.php';

class StudyController extends BaseController {
    private $db;
    private $study;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->study = new Study();
    }

    public function index() {
        $studies = $this->study->getAll();
        $data = [
            'studies' => $studies,
            'pageTitle' => 'Studies'
        ];
        $this->view('studies/index', $data);
    }

    public function list() {
        $studies = $this->study->getAll();
        $breadcrumbs = [
            ['title' => 'Studies', 'url' => 'index.php?route=studies/list']
        ];
        $data = [
            'studies' => $studies,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Studies List'
        ];
        $this->view('studies/list', $data);
    }

    public function add() {
        $breadcrumbs = [
            ['title' => 'Studies', 'url' => 'index.php?route=studies/list'],
            ['title' => 'Add Study']
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->study->create($data);
            $this->setFlash('success', 'Study added successfully');
            header('Location: index.php?route=studies/list');
            exit();
        }
        $data = [
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Add Study'
        ];
        $this->view('studies/add', $data);
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Study ID required');
            header('Location: index.php?route=studies/list');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->study->update($id, $data);
            $this->setFlash('success', 'Study updated successfully');
            header('Location: index.php?route=studies/list');
            exit();
        }
        $study = $this->study->getById($id);
        $breadcrumbs = [
            ['title' => 'Studies', 'url' => 'index.php?route=studies/list'],
            ['title' => 'Edit Study']
        ];
        $data = [
            'study' => $study,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Edit Study'
        ];
        $this->view('studies/edit', $data);
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Study ID required');
            header('Location: index.php?route=studies/list');
            exit();
        }
        $this->study->delete($id);
        $this->setFlash('success', 'Study deleted successfully');
        header('Location: index.php?route=studies/list');
        exit();
    }

    public function worklist() {
        $studies = $this->study->getWorklist();
        $breadcrumbs = [
            ['title' => 'Studies', 'url' => 'index.php?route=studies/list'],
            ['title' => 'Worklist']
        ];
        $data = [
            'studies' => $studies,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Study Worklist'
        ];
        $this->view('studies/worklist', $data);
    }
}
