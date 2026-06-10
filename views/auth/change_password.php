<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../../config/koneksi.php";
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../helpers/PasswordValidator.php";

$userModel = new UserModel($koneksi);
$user = $userModel->getUserById($_SESSION['id_user']);

if ($user && $user['password_change_required'] == 0) {
    header("Location: /renewal-system/public/index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($newPassword)) {
        $error = 'Password baru tidak boleh kosong!';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Password baru dan konfirmasi tidak cocok!';
    } else {
        $validation = PasswordValidator::validate($newPassword);
        if (!$validation['isValid']) {
            $error = implode('<br>', $validation['errors']);
        } else {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $koneksi->prepare("UPDATE users SET password = ?, password_change_required = 0 WHERE id_user = ?");
            $stmt->bind_param("si", $hashed, $_SESSION['id_user']);

            if ($stmt->execute()) {
                $success = 'Password berhasil diubah! Mengarahkan ke dashboard...';
                header("Location: /renewal-system/public/index.php?page=" . ($user['role'] === 'admin' ? 'admin_dashboard' : 'user_dashboard'));
                exit;
            } else {
                $error = 'Gagal mengubah password!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #3ba4ff;
            --primary-dark: #1f7ae0;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
        }

        body {
            background: linear-gradient(135deg, #a1caff, #6b9fff);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .change-password-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .card-header h2 {
            color: #1c2a3a;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .card-header p {
            color: #7f9bb3;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #1c2a3a;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #cfe5ff;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 164, 255, 0.1);
            background-color: #f8fbff;
        }

        .password-input-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #7f9bb3;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
            padding: 0;
            line-height: 1;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .requirements {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            font-size: 0.9rem;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #7f9bb3;
        }

        .requirement-item:last-child {
            margin-bottom: 0;
        }

        .requirement-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .requirement-item.met {
            color: var(--success-color);
        }

        .requirement-item.met i {
            color: var(--success-color);
        }

        .requirement-item.unmet i {
            color: var(--danger-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-primary:disabled {
            background-color: #d1d5db;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
        }

        @media (max-width: 576px) {
            .change-password-card {
                padding: 25px;
            }

            .card-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="change-password-card">
        <div class="card-header">
            <h2><i class="bi bi-lock"></i> Ubah Password</h2>
            <p>Silakan buat password yang aman untuk akun Anda</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="changePasswordForm">
            <div class="form-group">
                <label for="new_password" class="form-label">Password Baru</label>
                <div class="password-input-wrapper">
                    <input type="password" class="form-control" id="new_password" name="new_password" required
                        oninput="validatePasswordRequirements()">
                    <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="requirements">
                <div class="requirement-item" id="req-minlength">
                    <i class="bi bi-circle"></i>
                    <span>Minimal 8 karakter</span>
                </div>
                <div class="requirement-item" id="req-uppercase">
                    <i class="bi bi-circle"></i>
                    <span>Memiliki huruf besar (A-Z)</span>
                </div>
                <div class="requirement-item" id="req-lowercase">
                    <i class="bi bi-circle"></i>
                    <span>Memiliki huruf kecil (a-z)</span>
                </div>
                <div class="requirement-item" id="req-symbol">
                    <i class="bi bi-circle"></i>
                    <span>Memiliki simbol (!@#$%^&* dll)</span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                <div class="password-input-wrapper">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                        oninput="validatePasswordRequirements()">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="requirements match-requirement">
                <div class="requirement-item" id="req-match">
                    <i class="bi bi-circle"></i>
                    <span>Password cocok</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                <i class="bi bi-check-lg"></i> Ubah Password
            </button>
        </form>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = event.target.closest('button');
            const icon = button.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        function validatePasswordRequirements() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            const requirements = {
                minlength: newPassword.length >= 8,
                uppercase: /[A-Z]/.test(newPassword),
                lowercase: /[a-z]/.test(newPassword),
                symbol: /[!@#$%^&*()_+\-=[\]{};:'",.< >?/ ]/.test(newPassword)
            };

            for (const [key, met] of Object.entries(requirements)) {
                const element = document.getElementById(`req-${key}`);
                if (element) {
                    element.classList.remove('met', 'unmet');
                    element.classList.add(met ? 'met' : 'unmet');

                    const icon = element.querySelector('i');
                    icon.classList.remove('bi-circle', 'bi-check-circle', 'bi-x-circle');
                    icon.classList.add(met ? 'bi-check-circle' : 'bi-x-circle');
                }
            }

            const matchRequirement = newPassword === confirmPassword && confirmPassword.length > 0;
            const matchElement = document.getElementById('req-match');
            if (matchElement) {
                matchElement.classList.remove('met', 'unmet');
                if (confirmPassword.length === 0) {
                    matchElement.classList.add('unmet');
                    const icon = matchElement.querySelector('i');
                    icon.classList.remove('bi-circle', 'bi-check-circle', 'bi-x-circle');
                    icon.classList.add('bi-circle');
                } else {
                    matchElement.classList.add(matchRequirement ? 'met' : 'unmet');
                    const icon = matchElement.querySelector('i');
                    icon.classList.remove('bi-circle', 'bi-check-circle', 'bi-x-circle');
                    icon.classList.add(matchRequirement ? 'bi-check-circle' : 'bi-x-circle');
                }
            }

            const allMet = Object.values(requirements).every(req => req) && matchRequirement;
            document.getElementById('submitBtn').disabled = !allMet;
        }

        document.addEventListener('DOMContentLoaded', function() {
            validatePasswordRequirements();
        });
    </script>
</body>

</html>
