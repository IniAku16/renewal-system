    <?php
    session_start();
    require_once __DIR__ . "/../config/koneksi.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $login = trim($_POST['login']);
        $password = $_POST['password'];

        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE BINARY username = ? OR BINARY email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $login, $login);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        if ($data) {

            if ($data ['username'] !== $login && $data['email'] !==$login){
                header("Location: ../views/auth/login.php?error=Username atau Email tidak sesuai (Perhatikan Kembali Besar Kecil Huruf!)");
                exit;
            }
            if (password_verify($password, $data['password'])) {
                $_SESSION['id_user'] = $data['id_user'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['role'] = $data['role']; 
                $_SESSION['departemen'] = $data['departemen'];

                if ($_SESSION['role'] == 'admin') {
                    header("Location: /renewal-system/public/index.php?page=admin_dashboard");
                } else {
                    header("Location: /renewal-system/public/index.php");
                }
                exit();
            } else {
                header("Location: ../views/auth/login.php?error=Password salah");
                exit();
            }
        } else {
            header("Location: ../views/auth/login.php?error=User tidak ditemukan");
            exit();
        }
    }
