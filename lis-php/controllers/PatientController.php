<?php
/**
 * Patient Controller
 * Handles patient-related operations
 */

require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../includes/auth_functions.php';

class PatientController {
    private $model;

    public function __construct() {
        requireLogin();
        $this->model = new PatientModel();
    }

    /**
     * List all patients
     */
    public function list() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $patients = $this->model->getAll($page);
        $totalRecords = $this->model->getTotalCount();
        $totalPages = ceil($totalRecords / RECORDS_PER_PAGE);
        
        require_once __DIR__ . '/../views/patients/list_patients.php';
    }

    /**
     * Add new patient
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'patient_id' => $this->model->generatePatientId(),
                'first_name' => sanitizeInput($_POST['first_name']),
                'last_name' => sanitizeInput($_POST['last_name']),
                'date_of_birth' => sanitizeInput($_POST['date_of_birth']),
                'gender' => sanitizeInput($_POST['gender']),
                'email' => sanitizeInput($_POST['email']),
                'phone' => sanitizeInput($_POST['phone']),
                'address' => sanitizeInput($_POST['address']),
                'emergency_contact' => sanitizeInput($_POST['emergency_contact']),
                'emergency_phone' => sanitizeInput($_POST['emergency_phone'])
            ];
            
            if ($this->model->create($data)) {
                setFlashMessage('success', 'Patient added successfully!');
                redirect('index.php?route=patients/list');
            } else {
                setFlashMessage('error', 'Failed to add patient.');
            }
        }
        
        require_once __DIR__ . '/../views/patients/add_patient.php';
    }

    /**
     * Edit patient
     */
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('error', 'Invalid patient ID.');
            redirect('index.php?route=patients/list');
        }
        
        $patient = $this->model->getById($id);
        
        if (!$patient) {
            setFlashMessage('error', 'Patient not found.');
            redirect('index.php?route=patients/list');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => sanitizeInput($_POST['first_name']),
                'last_name' => sanitizeInput($_POST['last_name']),
                'date_of_birth' => sanitizeInput($_POST['date_of_birth']),
                'gender' => sanitizeInput($_POST['gender']),
                'email' => sanitizeInput($_POST['email']),
                'phone' => sanitizeInput($_POST['phone']),
                'address' => sanitizeInput($_POST['address']),
                'emergency_contact' => sanitizeInput($_POST['emergency_contact']),
                'emergency_phone' => sanitizeInput($_POST['emergency_phone'])
            ];
            
            if ($this->model->update($id, $data)) {
                setFlashMessage('success', 'Patient updated successfully!');
                redirect('index.php?route=patients/list');
            } else {
                setFlashMessage('error', 'Failed to update patient.');
            }
        }
        
        require_once __DIR__ . '/../views/patients/edit_patient.php';
    }

    /**
     * View patient details
     */
    public function view() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('error', 'Invalid patient ID.');
            redirect('index.php?route=patients/list');
        }
        
        $patient = $this->model->getById($id);
        
        if (!$patient) {
            setFlashMessage('error', 'Patient not found.');
            redirect('index.php?route=patients/list');
        }
        
        require_once __DIR__ . '/../views/patients/view_patient.php';
    }

    /**
     * Delete patient
     */
    public function delete() {
        requireRole([ROLE_ADMIN]);
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('error', 'Invalid patient ID.');
            redirect('index.php?route=patients/list');
        }
        
        if ($this->model->delete($id)) {
            setFlashMessage('success', 'Patient deleted successfully!');
        } else {
            setFlashMessage('error', 'Failed to delete patient.');
        }
        
        redirect('index.php?route=patients/list');
    }
}
