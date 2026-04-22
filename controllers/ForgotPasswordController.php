<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../models/User.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier']; 
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        header("Location: ../views/auth/forget_password.php?error=Password confirmation does not match!");
        exit();
    }

    $userModel = new UserModel($koneksi);

    if ($userModel->updatePassword($identifier, $newPassword)) {
        header("Location: ../views/auth/login.php?success=Password updated successfully! Please login.");
    } else {
        header("Location: ../views/auth/forget_password.php?error=Username or Email not found!");
    }
    exit();
}