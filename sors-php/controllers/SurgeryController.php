<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SORSDatabase.php';
require_once __DIR__ . '/../models/Surgery.php';
require_once __DIR__ . '/../models/OperatingRoom.php';
require_once __DIR__ . '/../models/Staff.php';

class SurgeryController extends BaseController {
    private $db;
    private $surgery;
    private $room;
    private $staff;

    public function __construct() {
        parent::__construct();
        $this->db = SORSDatabase::getInstance();
        $this->surgery = new Surgery();
        $this->room = new OperatingRoom();
        $this->staff = new Staff();
    }

    public function list() {
        $this->requireAuth();

        try {
            $surgeries = $this->getSurgeries();
            $data = [
                'title' => 'Surgeries',
                'surgeries' => $surgeries,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('surgeries/list', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading surgeries: ' . $e->getMessage());
            $this->redirect('index.php?route=dashboard');
        }
    }

    public function add() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAddSurgery();
        }

        $data = [
            'title' => 'Schedule Surgery',
            'surgeryTypes' => $this->getSurgeryTypes(),
            'priorities' => $this->getPriorities(),
            'rooms' => $this->getAvailableRooms(),
            'surgeons' => $this->getAvailableSurgeons(),
            'currentUser' => $this->getCurrentUser()
        ];
        $this->view('surgeries/add', $data);
    }

    public function edit() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Surgery ID required');
            $this->redirect('index.php?route=surgeries/list');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditSurgery($id);
        }

        try {
            $surgery = $this->getSurgeryById($id);
            if (!$surgery) {
                $this->setFlash('error', 'Surgery not found');
                $this->redirect('index.php?route=surgeries/list');
                return;
            }

            $data = [
                'title' => 'Edit Surgery',
                'surgery' => $surgery,
                'surgeryTypes' => $this->getSurgeryTypes(),
                'priorities' => $this->getPriorities(),
                'statuses' => $this->getStatuses(),
                'rooms' => $this->getAvailableRooms(),
                'surgeons' => $this->getAvailableSurgeons(),
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('surgeries/edit', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading surgery: ' . $e->getMessage());
            $this->redirect('index.php?route=surgeries/list');
        }
    }

    public function delete() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Surgery ID required');
            $this->redirect('index.php?route=surgeries/list');
            return;
        }

        try {
            $surgery = $this->getSurgeryById($id);
            if (!$surgery) {
                $this->setFlash('error', 'Surgery not found');
                $this->redirect('index.php?route=surgeries/list');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleDeleteSurgery($id);
            }

            $data = [
                'title' => 'Cancel Surgery',
                'surgery' => $surgery,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('surgeries/delete', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error deleting surgery: ' . $e->getMessage());
            $this->redirect('index.php?route=surgeries/list');
        }
    }

    public function view() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Surgery ID required');
            $this->redirect('index.php?route=surgeries/list');
            return;
        }

        try {
            $surgery = $this->getSurgeryById($id);
            if (!$surgery) {
                $this->setFlash('error', 'Surgery not found');
                $this->redirect('index.php?route=surgeries/list');
                return;
            }

            $data = [
                'title' => 'Surgery Details',
                'surgery' => $surgery,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('surgeries/view', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error viewing surgery: ' . $e->getMessage());
            $this->redirect('index.php?route=surgeries/list');
        }
    }

    public function schedule() {
        $this->requireAuth();

        try {
            $schedule = $this->getSurgerySchedule();
            $data = [
                'title' => 'Surgery Schedule',
                'schedule' => $schedule,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('surgeries/schedule', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading schedule: ' . $e->getMessage());
            $this->redirect('index.php?route=surgeries/list');
        }
    }

    public function calendar() {
        $this->requireAuth();

        try {
            $calendar = $this->getSurgeryCalendar();
            $data = [
                'title' => 'Surgery Calendar',
                'calendar' => $calendar,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('surgeries/calendar', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading calendar: ' . $e->getMessage());
            $this->redirect('index.php?route=surgeries/list');
        }
    }

    private function getSurgeries() {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT s.*, r.room_number, st.full_name as surgeon_name FROM sors_surgeries s
                    LEFT JOIN sors_operating_rooms r ON s.room_id = r.id
                    LEFT JOIN staff st ON s.surgeon_id = st.id
                    ORDER BY s.scheduled_date, s.scheduled_time";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getSurgeryById($id) {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT s.*, r.room_number, st.full_name as surgeon_name FROM sors_surgeries s
                    LEFT JOIN sors_operating_rooms r ON s.room_id = r.id
                    LEFT JOIN staff st ON s.surgeon_id = st.id
                    WHERE s.id = ? LIMIT 1";
            return $this->db->fetchOne($sql, [$id]);
        } catch (Exception $e) {
            return null;
        }
    }

    private function getSurgeryTypes() {
        return [
            'general' => 'General Surgery',
            'orthopedic' => 'Orthopedic Surgery',
            'cardiovascular' => 'Cardiovascular Surgery',
            'neurosurgery' => 'Neurosurgery',
            'plastic' => 'Plastic Surgery',
            'urology' => 'Urology',
            'gynecology' => 'Gynecology',
            'ophthalmology' => 'Ophthalmology',
            'ent' => 'ENT Surgery',
            'pediatric' => 'Pediatric Surgery'
        ];
    }

    private function getPriorities() {
        return [
            'elective' => 'Elective',
            'urgent' => 'Urgent',
            'emergency' => 'Emergency'
        ];
    }

    private function getStatuses() {
        return [
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed'
        ];
    }

    private function getAvailableRooms() {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM sors_operating_rooms WHERE status = 'available' ORDER BY room_number";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getAvailableSurgeons() {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM staff WHERE role = 'surgeon' AND status = 'active' ORDER BY full_name";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }

    private function handleAddSurgery() {
        try {
            $data = [
                'patient_id' => $_POST['patient_id'] ?? '',
                'patient_name' => $_POST['patient_name'] ?? '',
                'surgery_type' => $_POST['surgery_type'] ?? '',
                'priority' => $_POST['priority'] ?? 'elective',
                'scheduled_date' => $_POST['scheduled_date'] ?? '',
                'scheduled_time' => $_POST['scheduled_time'] ?? '',
                'estimated_duration' => $_POST['estimated_duration'] ?? 60,
                'room_id' => $_POST['room_id'] ?? null,
                'surgeon_id' => $_POST['surgeon_id'] ?? null,
                'anesthesiologist_id' => $_POST['anesthesiologist_id'] ?? null,
                'notes' => $_POST['notes'] ?? '',
                'status' => 'scheduled',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('sors_surgeries', $data);
            $this->setFlash('success', 'Surgery scheduled successfully');
            $this->redirect('index.php?route=surgeries/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error scheduling surgery: ' . $e->getMessage());
        }
    }

    private function handleEditSurgery($id) {
        try {
            $data = [
                'patient_id' => $_POST['patient_id'] ?? '',
                'patient_name' => $_POST['patient_name'] ?? '',
                'surgery_type' => $_POST['surgery_type'] ?? '',
                'priority' => $_POST['priority'] ?? 'elective',
                'scheduled_date' => $_POST['scheduled_date'] ?? '',
                'scheduled_time' => $_POST['scheduled_time'] ?? '',
                'estimated_duration' => $_POST['estimated_duration'] ?? 60,
                'room_id' => $_POST['room_id'] ?? null,
                'surgeon_id' => $_POST['surgeon_id'] ?? null,
                'anesthesiologist_id' => $_POST['anesthesiologist_id'] ?? null,
                'notes' => $_POST['notes'] ?? '',
                'status' => $_POST['status'] ?? 'scheduled'
            ];

            $this->db->update('sors_surgeries', $data, 'id = :id', [':id' => $id]);
            $this->setFlash('success', 'Surgery updated successfully');
            $this->redirect('index.php?route=surgeries/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error updating surgery: ' . $e->getMessage());
        }
    }

    private function handleDeleteSurgery($id) {
        try {
            $this->db->delete('sors_surgeries', 'id = :id', [':id' => $id]);
            $this->setFlash('success', 'Surgery cancelled successfully');
            $this->redirect('index.php?route=surgeries/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error cancelling surgery: ' . $e->getMessage());
        }
    }

    private function getSurgerySchedule() {
        // Implementation for surgery schedule
        return [
            'today' => 5,
            'tomorrow' => 8,
            'week' => 35
        ];
    }

    public function calendar() {
        $this->requireAuth();

        try {
            $calendar = $this->getSurgeryCalendar();
            $data = [
                'title' => 'Surgery Calendar',
                'calendar' => $calendar,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('surgeries/calendar', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading calendar: ' . $e->getMessage());
            $this->redirect('index.php?route=surgeries/list');
        }
    }
