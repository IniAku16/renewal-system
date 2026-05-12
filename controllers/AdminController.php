<?php
require_once __DIR__ . "/../models/User.php";

class AdminController
{
    private $userModel;

    public function __construct($koneksi)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php");
            exit();
        }

        $this->userModel = new UserModel($koneksi);
    }

    public function index()
    {
        $usersResult = $this->userModel->getAllUsers();
        $users = [];
        while ($row = $usersResult->fetch_assoc()) {
            $users[] = $row;
        }

        $totalUsers = count($users);
        $adminCount = count(array_filter($users, fn($u) => $u['role'] == 'admin'));

        include __DIR__ . "/../views/admin/dashboard.php";
    }

    public function create()
    {
        header('Content-Type: application/json');

        $username = trim(htmlspecialchars($_POST['username']));
        $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $dept     = $_POST['departemen'];
        $role     = $_POST['role'];

        if (empty($username) || empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak boleh kosong']);
            return;
        }

        $res = $this->userModel->createUser($username, $email, $password, $dept, $role);
        echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'User berhasil ditambah' : 'Gagal tambah user']);
    }

    public function update($id)
    {
        header('Content-Type: application/json');

        $username = trim(htmlspecialchars($_POST['username']));
        $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $dept     = htmlspecialchars($_POST['departemen']);
        $role     = $_POST['role'];
        $password = !empty($_POST['password']) ? $_POST['password'] : null;

        $res = $this->userModel->updateUser($id, $username, $email, $dept, $role, $password);
        echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'User berhasil diupdate' : 'Gagal update user']);
    }

    public function delete($id)
    {
        $res = $this->userModel->deleteUser($id);
        header("Location: index.php?page=admin_dashboard&status=" . ($res ? 'deleted' : 'error'));
    }
}
