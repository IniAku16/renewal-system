<?php
require_once __DIR__ . "/../models/User.php";

class AdminController {
    private $userModel;

    public function __construct($koneksi) {
        $this->userModel = new UserModel($koneksi);
    }

    public function index() {
        $usersResult = $this->userModel->getAllUsers();
        $users = [];
        while($row = $usersResult->fetch_assoc()) {
            $users[] = $row;
        }
        
        $totalUsers = count($users);
        $adminCount = count(array_filter($users, fn($u) => $u['role'] == 'admin'));
        
        include __DIR__ . "/../views/admin/dashboard.php";
    }

    public function create() {
        header('Content-Type: application/json');
        $res = $this->userModel->createUser($_POST['username'], $_POST['email'], $_POST['password'], $_POST['departemen'], $_POST['role']);
        echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'User berhasil ditambah' : 'Gagal tambah user']);
    }

    public function update($id) {
        header('Content-Type: application/json');
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
        $res = $this->userModel->updateUser($id, $_POST['username'], $_POST['email'], $_POST['departemen'], $_POST['role'], $password);
        echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'User berhasil diupdate' : 'Gagal update user']);
    }

    public function delete($id) {
        $res = $this->userModel->deleteUser($id);
        header("Location: index.php?page=admin_dashboard&status=" . ($res ? 'deleted' : 'error'));
    }
}