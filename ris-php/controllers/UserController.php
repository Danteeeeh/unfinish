<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends BaseController {
    private $db;
    private $user;

    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->user = new User();
    }

    public function index() {
        $users = $this->user->getAll();
        $data = [
            'users' => $users,
            'pageTitle' => 'Users'
        ];
        $this->view('users/index', $data);
    }

    public function list() {
        $users = $this->user->getAll();
        $breadcrumbs = [
            ['title' => 'Users', 'url' => 'index.php?route=users/list']
        ];
        $data = [
            'users' => $users,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Users List'
        ];
        $this->view('users/list', $data);
    }

    public function add() {
        $breadcrumbs = [
            ['title' => 'Users', 'url' => 'index.php?route=users/list'],
            ['title' => 'Add User']
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->user->create($data);
            $this->setFlash('success', 'User added successfully');
            header('Location: index.php?route=users/list');
            exit();
        }
        $data = [
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Add User'
        ];
        $this->view('users/add', $data);
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'User ID required');
            header('Location: index.php?route=users/list');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->user->update($id, $data);
            $this->setFlash('success', 'User updated successfully');
            header('Location: index.php?route=users/list');
            exit();
        }
        $user = $this->user->getById($id);
        $breadcrumbs = [
            ['title' => 'Users', 'url' => 'index.php?route=users/list'],
            ['title' => 'Edit User']
        ];
        $data = [
            'user' => $user,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'Edit User'
        ];
        $this->view('users/edit', $data);
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->setFlash('error', 'User ID required');
            header('Location: index.php?route=users/list');
            exit();
        }
        $this->user->delete($id);
        $this->setFlash('success', 'User deleted successfully');
        header('Location: index.php?route=users/list');
        exit();
    }
