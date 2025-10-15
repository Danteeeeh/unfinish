<?php
/**
 * Test Controller
 * Manages test workflows and operations
 */

require_once __DIR__ . '/../models/TestModel.php';
require_once __DIR__ . '/../models/SampleModel.php';
require_once __DIR__ . '/../models/LisTestModel.php'; // New model for test definitions
require_once __DIR__ . '/../includes/auth_functions.php';

class TestController {
    private $model;
    private $sampleModel;
    private $testDefinitionModel;

    public function __construct() {
        requireLogin();
        $this->model = new TestModel();
        $this->sampleModel = new SampleModel();
        $this->testDefinitionModel = new LisTestModel(); // For accessing test definitions
    }

    /**
     * Order new test
     */
    public function order() {
        $samples = $this->sampleModel->getAll(1, 1000);
        $availableTests = $this->testDefinitionModel->getActiveTests(); // Get available test definitions

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedTestId = (int)$_POST['test_id'];

            // Verify the selected test exists
            $testDefinition = $this->testDefinitionModel->getById($selectedTestId);
            if (!$testDefinition) {
                setFlashMessage('error', 'Invalid test selected.');
                redirect('index.php?route=tests/order');
                return;
            }

            $data = [
                'test_id' => $selectedTestId, // Use the actual test ID from lis_tests table
                'sample_id' => (int)$_POST['sample_id'],
                'test_name' => $testDefinition['test_name'], // Get name from test definition
                'test_type' => $testDefinition['category'], // Get category from test definition
                'ordered_by' => getCurrentUser()['full_name'],
                'order_date' => date(DATE_FORMAT),
                'priority' => sanitizeInput($_POST['priority']),
                'status' => TEST_STATUS_ORDERED,
                'notes' => sanitizeInput($_POST['notes'])
            ];

            if ($this->model->create($data)) {
                // Update sample status to processing
                $this->sampleModel->updateStatus($data['sample_id'], SAMPLE_STATUS_PROCESSING);

                setFlashMessage('success', 'Test ordered successfully!');
                redirect('index.php?route=tests/pending');
            } else {
                setFlashMessage('error', 'Failed to order test.');
            }
        }

        require_once __DIR__ . '/../views/tests/order_test.php';
    }

    /**
     * View test results
     */
    public function results() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('error', 'Invalid test ID.');
            redirect('index.php?route=tests/pending');
        }
        
        $test = $this->model->getById($id);
        
        if (!$test) {
            setFlashMessage('error', 'Test not found.');
            redirect('index.php?route=tests/pending');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $results = sanitizeInput($_POST['results']);
            $performedBy = getCurrentUser()['full_name'];
            
            if ($this->model->updateResults($id, $results, $performedBy)) {
                setFlashMessage('success', 'Test results saved successfully!');
                redirect('index.php?route=tests/results&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to save test results.');
            }
        }
        
        require_once __DIR__ . '/../views/tests/test_results.php';
    }

    /**
     * View pending tests
     */
    public function pending() {
        $pendingTests = $this->model->getByStatus(TEST_STATUS_ORDERED);
        $inProgressTests = $this->model->getByStatus(TEST_STATUS_IN_PROGRESS);
        
        require_once __DIR__ . '/../views/tests/pending_tests.php';
    }

    /**
     * Update test status
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $status = sanitizeInput($_POST['status']);
            
            if ($this->model->updateStatus($id, $status)) {
                setFlashMessage('success', 'Test status updated successfully!');
            } else {
                setFlashMessage('error', 'Failed to update test status.');
            }
            
            redirect('index.php?route=tests/results&id=' . $id);
        }
    }

    /**
     * Verify test results
     */
    public function verify() {
        requireRole([ROLE_ADMIN, ROLE_DOCTOR]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $verificationNotes = sanitizeInput($_POST['verification_notes']);
            $verifiedBy = getCurrentUser()['full_name'];

            if ($this->model->verify($id, $verifiedBy, $verificationNotes)) {
                setFlashMessage('success', 'Test results verified successfully!');
            } else {
                setFlashMessage('error', 'Failed to verify test results.');
            }

            redirect('index.php?route=tests/results&id=' . $id);
        }
    }

    /**
     * Delete test
     */
    public function delete() {
        requireRole([ROLE_ADMIN]);
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            setFlashMessage('error', 'Invalid test ID.');
            redirect('index.php?route=tests/pending');
        }
        
        if ($this->model->delete($id)) {
            setFlashMessage('success', 'Test deleted successfully!');
        } else {
            setFlashMessage('error', 'Failed to delete test.');
        }
        
        redirect('index.php?route=tests/pending');
    }
}
