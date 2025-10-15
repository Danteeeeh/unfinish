<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SORSDatabase.php';
require_once __DIR__ . '/../models/OperatingRoom.php';

class RoomController extends BaseController {
    private $db;
    private $room;

    public function __construct() {
        parent::__construct();
        $this->db = SORSDatabase::getInstance();
        $this->room = new OperatingRoom();
    }

    public function list() {
        $this->requireAuth();

        try {
            $rooms = $this->getRooms();
            $data = [
                'title' => 'Operating Rooms',
                'rooms' => $rooms,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('rooms/list', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading rooms: ' . $e->getMessage());
            $this->redirect('index.php?route=dashboard');
        }
    }

    public function add() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAddRoom();
        }

        $data = [
            'title' => 'Add Operating Room',
            'roomTypes' => $this->getRoomTypes(),
            'currentUser' => $this->getCurrentUser()
        ];
        $this->view('rooms/add', $data);
    }

    public function edit() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Room ID required');
            $this->redirect('index.php?route=rooms/list');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditRoom($id);
        }

        try {
            $room = $this->getRoomById($id);
            if (!$room) {
                $this->setFlash('error', 'Room not found');
                $this->redirect('index.php?route=rooms/list');
                return;
            }

            $data = [
                'title' => 'Edit Operating Room',
                'room' => $room,
                'roomTypes' => $this->getRoomTypes(),
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('rooms/edit', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading room: ' . $e->getMessage());
            $this->redirect('index.php?route=rooms/list');
        }
    }

    public function delete() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Room ID required');
            $this->redirect('index.php?route=rooms/list');
            return;
        }

        try {
            $room = $this->getRoomById($id);
            if (!$room) {
                $this->setFlash('error', 'Room not found');
                $this->redirect('index.php?route=rooms/list');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleDeleteRoom($id);
            }

            $data = [
                'title' => 'Delete Operating Room',
                'room' => $room,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('rooms/delete', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error deleting room: ' . $e->getMessage());
            $this->redirect('index.php?route=rooms/list');
        }
    }

    public function view() {
        $this->requireAuth();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'Room ID required');
            $this->redirect('index.php?route=rooms/list');
            return;
        }

        try {
            $room = $this->getRoomById($id);
            if (!$room) {
                $this->setFlash('error', 'Room not found');
                $this->redirect('index.php?route=rooms/list');
                return;
            }

            $data = [
                'title' => 'View Operating Room',
                'room' => $room,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('rooms/view', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error viewing room: ' . $e->getMessage());
            $this->redirect('index.php?route=rooms/list');
        }
    }

    public function availability() {
        $this->requireAuth();

        try {
            $availability = $this->getRoomAvailability();
            $data = [
                'title' => 'Room Availability',
                'availability' => $availability,
                'currentUser' => $this->getCurrentUser()
            ];
            $this->view('rooms/availability', $data);
        } catch (Exception $e) {
            $this->setFlash('error', 'Error loading availability: ' . $e->getMessage());
            $this->redirect('index.php?route=rooms/list');
        }
    }

    private function getRooms() {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM sors_operating_rooms ORDER BY room_number";
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getRoomById($id) {
        try {
            $conn = $this->db->getConnection();
            $sql = "SELECT * FROM sors_operating_rooms WHERE id = ? LIMIT 1";
            return $this->db->fetchOne($sql, [$id]);
        } catch (Exception $e) {
            return null;
        }
    }

    private function getRoomTypes() {
        return [
            'major' => 'Major Operating Room',
            'minor' => 'Minor Operating Room',
            'emergency' => 'Emergency OR',
            'hybrid' => 'Hybrid OR'
        ];
    }

    private function handleAddRoom() {
        try {
            $data = [
                'room_number' => $_POST['room_number'] ?? '',
                'room_name' => $_POST['room_name'] ?? '',
                'room_type' => $_POST['room_type'] ?? '',
                'capacity' => $_POST['capacity'] ?? 1,
                'equipment' => $_POST['equipment'] ?? '',
                'status' => 'available',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('sors_operating_rooms', $data);
            $this->setFlash('success', 'Room added successfully');
            $this->redirect('index.php?route=rooms/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error adding room: ' . $e->getMessage());
        }
    }

    private function handleEditRoom($id) {
        try {
            $data = [
                'room_number' => $_POST['room_number'] ?? '',
                'room_name' => $_POST['room_name'] ?? '',
                'room_type' => $_POST['room_type'] ?? '',
                'capacity' => $_POST['capacity'] ?? 1,
                'equipment' => $_POST['equipment'] ?? '',
                'status' => $_POST['status'] ?? 'available'
            ];

            $this->db->update('sors_operating_rooms', $data, 'id = :id', [':id' => $id]);
            $this->setFlash('success', 'Room updated successfully');
            $this->redirect('index.php?route=rooms/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error updating room: ' . $e->getMessage());
        }
    }

    private function handleDeleteRoom($id) {
        try {
            $this->db->delete('sors_operating_rooms', 'id = :id', [':id' => $id]);
            $this->setFlash('success', 'Room deleted successfully');
            $this->redirect('index.php?route=rooms/list');
        } catch (Exception $e) {
            $this->setFlash('error', 'Error deleting room: ' . $e->getMessage());
        }
    }

    private function getRoomAvailability() {
        // Implementation for room availability
        return [
            'available' => 3,
            'occupied' => 2,
            'maintenance' => 1,
            'cleaning' => 0
        ];
    }
}
