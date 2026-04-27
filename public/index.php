<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../controllers/ProductController.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../views/auth/login.php");
    exit();
}

$productController = new ProductController($koneksi);

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

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
?>