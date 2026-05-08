<?php
session_start();

require_once "../config/koneksi.php"; 
require_once "../models/user.php";

if (isset($_SESSION['id_user'])) {
    $userModel = new UserModel($koneksi);
    
    $userModel->setOffline($_SESSION['id_user']); 
}

session_unset();
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Location: ../views/auth/login.php");
exit();