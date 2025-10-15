<?php
/**
 * Sample Controller
 * Manages sample processing operations
 */

require_once __DIR__ . '/../models/SampleModel.php';
require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../includes/auth_functions.php';

class SampleController {
    private $model;
    private $patientModel;

    public function __construct() {
        requireLogin();
        $this->model = new SampleModel();
        $this->patientModel = new PatientModel();
    }

    /**
     * List all samples
     */
    public function list() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $samples = $this->model->getAll($page);
        
        require_once __DIR__ . '/../views/samples/list_samples.php';
    }

    /**
     * Add new sample
     */
    public function add() {
        $patients = $this->patientModel->getAll(1, 1000); // Get all patients for dropdown
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'sample_id' => $this->model->generateSampleId(),
                'patient_id' => (int)$_POST['patient_id'],
                'sample_type' => sanitizeInput($_POST['sample_type']),
                'collection_date' => sanitizeInput($_POST['collection_date']),
                'collection_time' => sanitizeInput($_POST['collection_time']),
                'collected_by' => sanitizeInput($_POST['collected_by']),
                'storage_location' => sanitizeInput($_POST['storage_location']),
                'status' => SAMPLE_STATUS_PENDING,
                'notes' => sanitizeInput($_POST['notes'])
            ];
            
            if ($this->model->create($data)) {
                setFlashMessage('success', 'Sample added successfully!');
                redirect('index.php?route=samples/list');
            } else {
                setFlashMessage('error', 'Failed to add sample.');
            }
        }
        
        require_once __DIR__ . '/../views/samples/add_sample.php';
    }

    /**
     * View sample status
     */
    public function status() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            // Show all samples grouped by status
            $pending = $this->model->getByStatus(SAMPLE_STATUS_PENDING);
            $processing = $this->model->getByStatus(SAMPLE_STATUS_PROCESSING);
            $completed = $this->model->getByStatus(SAMPLE_STATUS_COMPLETED);
            $rejected = $this->model->getByStatus(SAMPLE_STATUS_REJECTED);
            
            require_once __DIR__ . '/../views/samples/sample_status.php';
        } else {
            $sample = $this->model->getById($id);
            
            if (!$sample) {
                setFlashMessage('error', 'Sample not found.');
                redirect('index.php?route=samples/list');
            }
            
            require_once __DIR__ . '/../views/samples/view_sample.php';
        }
    }

    /**
     * Search samples
     */
    public function search() {
        $results = [];
        $keyword = '';
        
        if (isset($_GET['keyword'])) {
            $keyword = sanitizeInput($_GET['keyword']);
            $results = $this->model->search($keyword);
        }
        
        require_once __DIR__ . '/../views/samples/search_samples.php';
    }

    /**
     * Update sample status
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $status = sanitizeInput($_POST['status']);
            $notes = sanitizeInput($_POST['notes']);
            
            if ($this->model->updateStatus($id, $status, $notes)) {
                setFlashMessage('success', 'Sample status updated successfully!');
            } else {
                setFlashMessage('error', 'Failed to update sample status.');
            }
            
            redirect('index.php?route=samples/status&id=' . $id);
        }
    }

    /**
     * Delete sample
     */
    public function delete() {
        requireRole([ROLE_ADMIN]);
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('error', 'Invalid sample ID.');
            redirect('index.php?route=samples/list');
        }
        
        if ($this->model->delete($id)) {
            setFlashMessage('success', 'Sample deleted successfully!');
        } else {
            setFlashMessage('error', 'Failed to delete sample.');
        }
        
        redirect('index.php?route=samples/list');
    }
}
