<?php
require_once __DIR__ . "/../models/User.php";

class AdminController {
    private $userModel;
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
        $this->userModel = new UserModel($koneksi);
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        include __DIR__ . "/../views/admin/dashboard.php";
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username   = $_POST['username'];
            $email      = $_POST['email'];
            $password   = $_POST['password']; 
            $departemen = $_POST['departemen'];
            $role       = $_POST['role'];

            if ($this->userModel->createUser($username, $email, $password, $departemen, $role)) {
                header("Location: index.php?page=admin_dashboard&status=success_add");
                exit();
            } else {
                echo "Gagal menambah user.";
            }
        } else {
            include __DIR__ . "/../views/admin/add_user.php";
        }
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username   = $_POST['username'];
            $email      = $_POST['email'];
            $departemen = $_POST['departemen'];
            $role       = $_POST['role'];
    
            $password = !empty($_POST['password']) ? $_POST['password'] : null;

            $query = "UPDATE users SET username=?, email=?, departemen=?, role=? " . ($password ? ", password=?" : "") . " WHERE id_user=?";
            $stmt = $this->db->prepare($query);
            
            if ($password) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bind_param("sssssi", $username, $email, $departemen, $role, $hashed, $id);
            } else {
                $stmt->bind_param("ssssi", $username, $email, $departemen, $role, $id);
            }

            if ($stmt->execute()) {
                header("Location: index.php?page=admin_dashboard&status=success_update");
                exit();
            }
        } else {
            $user = $this->userModel->getUserById($id);
            include __DIR__ . "/../views/admin/edit_user.php";
        }
    }

    public function delete($id) {
        if ($this->userModel->deleteUser($id)) {
            header("Location: index.php?page=admin_dashboard&status=success_delete");
            exit();
        } else {
            echo "Gagal menghapus user.";
        }
    }
}