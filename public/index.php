<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("X-Frame-Options: DENY"); 
header("X-Content-Type-Options: nosniff");

require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../models/User.php";

$userModel = new UserModel($koneksi);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $login = trim($_POST['login']);
        $password = $_POST['password'];
        
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE BINARY username = ? OR BINARY email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $login, $login);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        if ($data && password_verify($password, $data['password'])) {
            session_regenerate_id(true);
            $_SESSION['id_user']    = $data['id_user'];
            $_SESSION['username']   = $data['username'];
            $_SESSION['role']       = $data['role'];
            $_SESSION['departemen'] = $data['departemen'];

            $userModel->updateLastActivity($data['id_user']);

            $redirect = ($data['role'] === 'admin') ? 'admin_dashboard' : 'user_dashboard';
            header("Location: index.php?page=" . $redirect);
            exit();
        } else {
            $_SESSION['error_msg'] = "Login Gagal! Periksa Kembali Data Anda!";
            header("Location: index.php");
            exit();
        }
    }

    if (isset($_POST['identifier'])) {
        $identifier = trim($_POST['identifier']);
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            header("Location: index.php?action=forgot_password&error=" . urlencode("Password confirmation does not match!"));
            exit();
        }

        if ($userModel->updatePassword($identifier, $newPassword)) {
            $_SESSION['success_msg'] = "Password updated successfully! Please login.";
            header("Location: index.php");
            exit();
        }

        header("Location: index.php?action=forgot_password&error=" . urlencode("Username or Email not found!"));
        exit();
    }
}

if (!isset($_SESSION['id_user'])) {
    $action = $_GET['action'] ?? '';
    if ($action === 'forgot_password') {
        include __DIR__ . "/../views/auth/forget_password.php";
        exit();
    }

    include __DIR__ . "/../views/auth/login.php";
    exit();
}

$role = $_SESSION['role'];
$page = $_GET['page'] ?? ($role === 'admin' ? 'admin_dashboard' : 'user_dashboard');

$access_map = [
    'admin_dashboard' => ['admin'],
    'user_dashboard'  => ['user'],
];

if (!isset($access_map[$page]) || !in_array($role, $access_map[$page])) {
    $redirect = ($role === 'admin') ? 'admin_dashboard' : 'user_dashboard';
    header("Location: index.php?page=" . $redirect);
    exit();
}

$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($action === 'logout') {
    if (isset($_SESSION['id_user'])) {
        $userModel->setOffline($_SESSION['id_user']);
    }
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if ($page === 'admin_dashboard') {
    
    require_once __DIR__ . "/../controllers/AdminController.php";
    $adminCtrl = new AdminController($koneksi);

    switch ($action) {
        case 'add_user':    $adminCtrl->create(); break;
        case 'edit_user':   $adminCtrl->update($id); break;
        case 'delete_user': $adminCtrl->delete($id); break;
        default:            $adminCtrl->index(); break;
    }

} elseif ($page === 'user_dashboard') {

    require_once __DIR__ . "/../controllers/ProductController.php";
    $productController = new ProductController($koneksi);

    $allowed_actions = ['create', 'update', 'delete', 'exportExcel', 'importExcel', 'history', 'history-detail', 'history-pdf', 'index'];
    
    if (in_array($action, $allowed_actions)) {
        switch ($action) {
            case 'create':         $productController->create(); break;
            case 'update':         $productController->update($id); break;
            case 'delete':         $productController->delete($id); break;
            case 'exportExcel':    $productController->exportExcel(); break;
            case 'importExcel':    $productController->importExcel(); break;
            case 'history':        $productController->history(); break;
            case 'history-detail': $productController->historyDetail(); break;
            case 'history-pdf':    $productController->historyPdf(); break;
            default:               $productController->index(); break;
        }
    } else {
        $productController->index();
    }
}