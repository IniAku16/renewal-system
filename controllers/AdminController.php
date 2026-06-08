<?php
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../helpers/PasswordValidator.php";
require_once __DIR__ . "/../helpers/UsernameValidator.php";

class AdminController
{
    private $userModel;
    private $db;

    public function __construct($koneksi)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php");
            exit();
        }

        $this->db = $koneksi;
        $this->userModel = new UserModel($koneksi);
    }

    public function index()
    {
        $usersResult = $this->userModel->getAllUsers();
        $users = [];
        while ($row = $usersResult->fetch_assoc()) {
            $users[] = $row;
        }

        $branches = [];
        $branchResult = $this->db->query("SELECT nama_branch FROM tb_branch ORDER BY nama_branch");
        if ($branchResult) {
            while ($row = $branchResult->fetch_assoc()) {
                $branches[] = $row['nama_branch'];
            }
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
        $dept     = $_POST['departemen'];
        $role     = $_POST['role'];

        if (empty($username) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak boleh kosong']);
            return;
        }

        $password = $this->generateRandomPassword();

        $usernameValidation = UsernameValidator::validate($username);
        if (!$usernameValidation['isValid']) {
            echo json_encode(['status' => 'error', 'message' => 'Username tidak valid', 'errors' => $usernameValidation['errors']]);
            return;
        }

        $res = $this->userModel->createUser($username, $email, $password, $dept, $role, 1);

        echo json_encode([
            'status' => $res ? 'success' : 'error',
            'message' => $res ? 'User berhasil ditambah' : 'Gagal tambah user ke database',
            'generatedPassword' => $res ? $password : null,
            'username' => $res ? $username : null,
            'email' => $res ? $email : null
        ]);
    }

    private function generateRandomPassword($length = 12)
    {
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $symbols = '!@#$%^&*()-_=+[]{}<>?';
        $all = $upper . $lower . $digits . $symbols;

        do {
            $password = '';
            $password .= $upper[random_int(0, strlen($upper) - 1)];
            $password .= $lower[random_int(0, strlen($lower) - 1)];
            $password .= $digits[random_int(0, strlen($digits) - 1)];
            $password .= $symbols[random_int(0, strlen($symbols) - 1)];

            for ($i = 4; $i < $length; $i++) {
                $password .= $all[random_int(0, strlen($all) - 1)];
            }

            $password = str_shuffle($password);
            $validation = PasswordValidator::validate($password);
        } while (!$validation['isValid']);

        return $password;
    }

    public function update($id)
    {
        header('Content-Type: application/json');

        $username = trim(htmlspecialchars($_POST['username']));
        $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $dept     = htmlspecialchars($_POST['departemen']);
        $role     = $_POST['role'];
        $password = !empty($_POST['password']) ? $_POST['password'] : null;

        $usernameValidation = UsernameValidator::validate($username);
        if (!$usernameValidation['isValid']) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Username tidak valid',
                'errors' => $usernameValidation['errors']
            ]);
            return;
        }

        if ($password) {
            $validation = PasswordValidator::validate($password);
            if (!$validation['isValid']) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Password tidak memenuhi syarat keamanan',
                    'errors' => $validation['errors']
                ]);
                return;
            }
        }

        $res = $this->userModel->updateUser($id, $username, $email, $dept, $role, $password, $password ? 1 : null);
        echo json_encode(['status' => $res ? 'success' : 'error', 'message' => $res ? 'User berhasil diupdate' : 'Gagal update user']);
    }

    public function delete($id)
    {
        $res = $this->userModel->deleteUser($id);
        header("Location: index.php?page=admin_dashboard&status=" . ($res ? 'deleted' : 'error'));
    }
}
