<?php
session_start();

require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../models/User.php";

$user = new UserModel($koneksi);

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];

    $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if($user->register()){
        header("Location: ../views/auth/login.php?register=success");
        exit();
    } else {
        echo "Registrasi Anda Gagal!";
    }
}
?>