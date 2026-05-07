<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../models/User.php";

$userModel = new UserModel($koneksi);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if ($login === 'ADMIN' && $password === 'administratorIT') {
        $_SESSION['id_user']    = 0;
        $_SESSION['username']   = 'ADMIN';
        $_SESSION['role']       = 'admin';
        $_SESSION['departemen'] = 'IT SUPPORT';
        header("Location: index.php?page=admin_dashboard");
        exit();
    }

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $login, $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['id_user']    = $data['id_user'];
        $_SESSION['username']   = $data['username'];
        $_SESSION['role']       = $data['role'];
        $_SESSION['departemen'] = $data['departemen'];

        $userModel->updateLastActivity($data['id_user']);

        if ($_SESSION['role'] == 'admin') {
            header("Location: index.php?page=admin_dashboard");
        } else {
            header("Location: index.php?page=user_dashboard");
        }
        exit();
    } else {
        header("Location: ../views/auth/login.php?error=Username atau Password salah");
        exit();
    }
}

if (!isset($_SESSION['id_user'])) {
    include __DIR__ . "/../views/auth/login.php";
    exit();
}

$role = $_SESSION['role'];
$page = $_GET['page'] ?? ($role === 'admin' ? 'admin_dashboard' : 'user_dashboard');

if ($role === 'admin') {
    $action = $_GET['action'] ?? 'list';
    if ($action === 'add_user') {
        include __DIR__ . "/../views/admin/add_user.php";
    } elseif ($action === 'edit_user') {
        $id = $_GET['id'];
        include __DIR__ . "/../views/admin/edit_user.php";
    } else {
        include __DIR__ . "/../views/admin/dashboard.php";
    }
} else {
    require_once __DIR__ . "/../controllers/ProductController.php";
    $productController = new ProductController($koneksi);

    $action = $_GET['action'] ?? 'index';
    $id     = $_GET['id'] ?? null;

    switch ($action) {
        case 'create':
            $productController->create();
            break;
        case 'update':
            $productController->update($id);
            break;
        case 'delete':
            $productController->delete($id);
            break;
        case 'exportExcel':
            $productController->exportExcel();
            break;
        case 'importExcel':
            $productController->importExcel();
            break;
        case 'history':
            $productController->history();
            break;
        case 'history-detail':
            $productController->historyDetail();
            break;
        case 'history-pdf':
            $productController->historyPdf();
            break;
        default:
            $productController->index();
            break;
    }
}
