<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Patient.php';

class PatientController extends BaseController {
    private $db;
    private $patient;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->patient = new Patient();
    }

    public function index() {
        $patients = $this->patient->getAll();
        $data = [
            'patients' => $patients,
            'pageTitle' => 'Patients'
        ];
        $this->view('patients/index', $data);
    }

    public function list() {
        $patients = $this->patient->getAll();
        $breadcrumbs = [
            ['title' => 'Patients', 'url' => 'index.php?route=patients/list']
        ];
        $data = [
            'patients' => $patients,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Patients List'
        ];
        $this->view('patients/list', $data);
    }

    public function add() {
        $breadcrumbs = [
            ['title' => 'Patients', 'url' => 'index.php?route=patients/list'],
            ['title' => 'Add Patient']
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->patient->create($data);
            $this->setFlash('success', 'Patient added successfully');
            header('Location: index.php?route=patients/list');
            exit();
        }
        $data = [
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Add Patient'
        ];
        $this->view('patients/add', $data);
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Patient ID required');
            header('Location: index.php?route=patients/list');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->patient->update($id, $data);
            $this->setFlash('success', 'Patient updated successfully');
            header('Location: index.php?route=patients/list');
            exit();
        }
        $patient = $this->patient->getById($id);
        $breadcrumbs = [
            ['title' => 'Patients', 'url' => 'index.php?route=patients/list'],
            ['title' => 'Edit Patient']
        ];
        $data = [
            'patient' => $patient,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Edit Patient'
        ];
        $this->view('patients/edit', $data);
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Patient ID required');
            header('Location: index.php?route=patients/list');
            exit();
        }
        $this->patient->delete($id);
        $this->setFlash('success', 'Patient deleted successfully');
        header('Location: index.php?route=patients/list');
        exit();
    }

    public function search() {
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            header('Location: index.php?route=patients/list');
            exit();
        }

        $patients = $this->patient->search($query);
        $breadcrumbs = [
            ['title' => 'Patients', 'url' => 'index.php?route=patients/list'],
            ['title' => 'Search Results']
        ];
        $data = [
            'patients' => $patients,
            'query' => $query,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Search Results'
        ];
        $this->view('patients/search', $data);
    }
}
