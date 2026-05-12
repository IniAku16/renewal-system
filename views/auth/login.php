<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --pastel-bg: #eef6ff;
            --primary-pastel: #3ba4ff;
            --primary-dark: #1f7ae0;
            --dark-text: #1c2a3a;
            --muted-text: #7f9bb3;
            --soft-border: #cfe5ff;
        }

        body {
            background: linear-gradient(135deg, #a1caff, #6b9fff);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 50px 45px;
            width: 100%;
            max-width: 420px;

            border: 3px solid #000;
            box-shadow: 10px 10px 0px #000;

            animation: fadeInUp 0.6s ease-out;
        }

        h2 {
            font-weight: 700;
            color: var(--dark-text);
            letter-spacing: -1px;
            margin-bottom: 8px;
        }

        .subtitle {
            color: var(--muted-text);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 35px;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--dark-text);
            margin-left: 4px;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 16px;
            padding: 14px 20px;
            border: 2px solid #000;
            font-weight: 600;
            background-color: #f2f8ff;
            transition: all 0.2s ease;
            color: var(--dark-text);
        }

        .form-control:focus {
            border-color: #000;
            box-shadow: 4px 4px 0px #000;
            background-color: #ffffff;
            outline: none;
        }

        .password-wrapper {
            position: relative;
        }

        .form-control-password {
            padding-right: 55px !important;
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--muted-text);
            font-size: 1.25rem;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--primary-pastel);
        }

        input::-ms-reveal,
        input::-ms-clear,
        input::-webkit-password-reveal {
            display: none !important;
        }

        .btn-login {
            background: linear-gradient(135deg, #3ba4ff, #6ec1ff);
            border: 2px solid #000;
            border-radius: 16px;
            padding: 16px;
            color: white;
            font-weight: 700;
            width: 100%;
            margin-top: 15px;

            box-shadow: 6px 6px 0px #000;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 8px 8px 0px #000;
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 4px 4px 0px #000;
        }

        .alert-custom {
            background-color: #fff5f5;
            border: 2px solid #000;
            color: #c53030;
            font-weight: 600;
            border-radius: 14px;
            font-size: 0.85rem;
            padding: 12px;
            margin-bottom: 25px;
        }

        .footer-section {
            margin-top: 30px;
        }

        .footer-text {
            font-weight: 600;
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 8px;
        }

        .link-pastel {
            color: var(--primary-pastel);
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .link-pastel:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="login-card text-center">
        <h2>Welcome</h2>
        <p class="subtitle">Please enter your details to sign in</p>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-custom d-flex align-items-center justify-content-center">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['error_msg']) ?>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 14px; font-weight: 600; font-size: 0.85rem;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($_SESSION['success_msg']) ?>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <form action="../../public/index.php" method="POST" class="text-start">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="mb-3">
                <label class="form-label">Username / Email</label>
                <input type="text" name="login" class="form-control" placeholder="Enter username or email" required autocomplete="username">
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                    <i class="bi bi-eye-slash toggle-password" id="eyeIcon"></i>
                </div>
            </div>

            <button type="submit" name="login_btn" class="btn btn-login">
                Sign In <i class="bi bi-arrow-right-short ms-1"></i>
            </button>
        </form>

        <div class="footer-section">
            <p class="footer-text mt-2">
                <a href="forget_password.php" class="link-pastel">Forgot Password?</a>
            </p>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');

        eyeIcon.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>