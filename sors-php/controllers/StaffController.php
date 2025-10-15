<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SORSDatabase.php';
require_once __DIR__ . '/../models/Staff.php';

class StaffController extends BaseController {
    private $db;
    private $staff;

    public function __construct() {
        parent::__construct();
        $this->db = SORSDatabase::getInstance();
        $this->staff = new Staff();
    }

    public function list() {
        $this->requireAuth();

        try {
            $staff = $this->getStaff();
            $data = [
                'title' => 'Staff Management',
                'staff' => $staff,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('staff/list', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading staff: ' . $e->getMessage());
            $this->redirect('index.php?route=dashboard');
        }
    }

    public function add() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAddStaff();
        }

        $data = [
            'title' => 'Add Staff Member',
            'staffRoles' => $this->getStaffRoles(),
            'currentUser' => $this->getCurrentUser()
        ];
        $this->view('staff/add', $data);
    }

    public function edit() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Staff ID required');
            $this->redirect('index.php?route=staff/list');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditStaff($id);
        }

        try {
            $staffMember = $this->getStaffById($id);
            if (!$staffMember) {
                $this->setFlash('error', 'Staff member not found');
                $this->redirect('index.php?route=staff/list');
                return;
            }

            $data = [
                'title' => 'Edit Staff Member',
                'staffMember' => $staffMember,
                'staffRoles' => $this->getStaffRoles(),
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('staff/edit', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading staff member: ' . $e->getMessage());
            $this->redirect('index.php?route=staff/list');
        }
    }

    public function delete() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Staff ID required');
            $this->redirect('index.php?route=staff/list');
            return;
        }

        try {
            $staffMember = $this->getStaffById($id);
            if (!$staffMember) {
                $this->setFlash('error', 'Staff member not found');
                $this->redirect('index.php?route=staff/list');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleDeleteStaff($id);
            }

            $data = [
                'title' => 'Delete Staff Member',
                'staffMember' => $staffMember,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('staff/delete', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error deleting staff member: ' . $e->getMessage());
            $this->redirect('index.php?route=staff/list');
        }
    }

    public function view() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Staff ID required');
            $this->redirect('index.php?route=staff/list');
            return;
        }

        try {
            $staffMember = $this->getStaffById($id);
            if (!$staffMember) {
                $this->setFlash('error', 'Staff member not found');
                $this->redirect('index.php?route=staff/list');
                return;
            }

            $data = [
                'title' => 'View Staff Member',
                'staffMember' => $staffMember,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('staff/view', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error viewing staff member: ' . $e->getMessage());
            $this->redirect('index.php?route=staff/list');
        }
    }

    public function schedule() {
        $this->requireAuth();

        try {
            $schedule = $this->getStaffSchedule();
            $data = [
                'title' => 'Staff Schedule',
                'schedule' => $schedule,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('staff/schedule', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading schedule: ' . $e->getMessage());
            $this->redirect('index.php?route=staff/list');
        }
    }

    private function getStaff() {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM staff ORDER BY full_name";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getStaffById($id) {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM staff WHERE id = ? LIMIT 1";
            return $this->db->fetchOne($sql, [$id]);
        } catch (Exception $e) {
            return null;
        }
    }

    private function getStaffRoles() {
        return [
            'surgeon' => 'Surgeon',
            'anesthesiologist' => 'Anesthesiologist',
            'nurse' => 'Surgical Nurse',
            'technician' => 'Surgical Technician',
            'assistant' => 'Surgical Assistant'
        ];
    }

    private function handleAddStaff() {
        try {
            $data = [
                'employee_id' => $_POST['employee_id'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'department' => $_POST['department'] ?? '',
                'role' => $_POST['role'] ?? '',
                'specialization' => $_POST['specialization'] ?? '',
                'license_number' => $_POST['license_number'] ?? '',
                'experience_years' => $_POST['experience_years'] ?? 0,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('staff', $data);
            $this->setFlash('success', 'Staff member added successfully');
            $this->redirect('index.php?route=staff/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error adding staff member: ' . $e->getMessage());
        }
    }

    private function handleEditStaff($id) {
        try {
            $data = [
                'employee_id' => $_POST['employee_id'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'department' => $_POST['department'] ?? '',
                'role' => $_POST['role'] ?? '',
                'specialization' => $_POST['specialization'] ?? '',
                'license_number' => $_POST['license_number'] ?? '',
                'experience_years' => $_POST['experience_years'] ?? 0,
                'status' => $_POST['status'] ?? 'active'
            ];

            $this->db->update('staff', $data, 'id = :id', [':id' => $id]);
            $this->setFlash('success', 'Staff member updated successfully');
            $this->redirect('index.php?route=staff/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error updating staff member: ' . $e->getMessage());
        }
    }

    private function handleDeleteStaff($id) {
        try {
            $this->db->delete('staff', 'id = :id', [':id' => $id]);
            $this->setFlash('success', 'Staff member deleted successfully');
            $this->redirect('index.php?route=staff/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error deleting staff member: ' . $e->getMessage());
        }
    }

    public function schedule() {
        $this->requireAuth();

        try {
            $schedule = $this->getStaffSchedule();
            $data = [
                'title' => 'Staff Schedule',
                'schedule' => $schedule,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('staff/schedule', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading schedule: ' . $e->getMessage());
            $this->redirect('index.php?route=staff/list');
        }
    }
