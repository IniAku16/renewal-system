<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("X-Frame-Options: DENY"); 
header("X-Content-Type-Options: nosniff");

require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../controllers/AdminController.php"; 

$userModel = new UserModel($koneksi);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
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
        header("Location: ../views/auth/login.php");
        exit();
    }
}

if (!isset($_SESSION['id_user'])) {
    include __DIR__ . "/../views/auth/login.php";
    exit();
}

$role = $_SESSION['role'];

$page = $_GET['page'] ?? ($role === 'admin' ? 'admin_dashboard' : 'user_dashboard');
$allowed_pages = ['admin_dashboard', 'user_dashboard'];

if (!in_array($page, $allowed_pages)) {
    $page = ($role === 'admin' ? 'admin_dashboard' : 'user_dashboard');
}

if ($page === 'admin_dashboard') {

    if ($role !== 'admin') { header("Location: index.php"); exit(); }

    $adminCtrl = new AdminController($koneksi);
    $action = $_GET['action'] ?? 'list';

    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    switch ($action) {
        case 'add_user':    $adminCtrl->create(); break;
        case 'edit_user':   $adminCtrl->update($id); break;
        case 'delete_user': $adminCtrl->delete($id); break;
        default:            $adminCtrl->index(); break;
    }

} else {
    
    require_once __DIR__ . "/../controllers/ProductController.php";
    $productController = new ProductController($koneksi);

    $action = $_GET['action'] ?? 'index';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null; 

    $allowed_actions = ['create', 'update', 'delete', 'exportExcel', 'importExcel', 'history', 'history-detail', 'history-pdf', 'index'];
    
    if (in_array($action, $allowed_actions)) {
        switch ($action) {
            case 'create':       $productController->create(); break;
            case 'update':       $productController->update($id); break;
            case 'delete':       $productController->delete($id); break;
            case 'exportExcel':  $productController->exportExcel(); break;
            case 'importExcel':  $productController->importExcel(); break;
            case 'history':      $productController->history(); break;
            case 'history-detail': $productController->historyDetail(); break;
            case 'history-pdf':  $productController->historyPdf(); break;
            default:             $productController->index(); break;
        }
    } else {
        $productController->index();
    }
}