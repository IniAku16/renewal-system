<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = (int) $_GET['id'];
$mysqli = new mysqli("localhost", "root", "", "renewal_system");

if (isset($_GET['status']) && $_GET['status'] === 'sent') {
?>
    <div style="text-align: center; margin-top: 100px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <div style="background: #f9f9f9; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: inline-block;">
            <h2 style="color: #27ae60; margin-bottom: 10px;">✔ Berhasil!</h2>
            <p style="font-size: 18px; color: #333; margin-bottom: 30px;">Request Quotation berhasil dikirim!</p>

            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="http://10.87.203.183/renewal-system/views/auth/login.php"
                    style="background:#27ae60; color:white; padding:12px 25px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block;">
                    Back To Login
                </a>
                <a href="javascript:void(0)" onclick="window.close();"
                    style="background:#e74c3c; color:white; padding:12px 25px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block;">
                    Tutup Jendela
                </a>
            </div>
        </div>
    </div>
<?php
    exit;
}

$stmt = $mysqli->prepare("SELECT product_name, serial_number, expired_date, request_count FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Produk tidak ditemukan");
}

if ($data['request_count'] > 0) {
?>
    <div style="text-align: center; margin-top: 100px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <div style="background: #fff4e5; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #ffcc80; display: inline-block;">
            <h2 style="color: #e67e22; margin-bottom: 10px;">⚠ Informasi</h2>
            <p style="font-size: 18px; color: #333; margin-bottom: 30px;">
                Produk <strong><?= htmlspecialchars($data['product_name']) ?></strong> sebelumnya sudah pernah dimintakan quotation.
            </p>

             <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="http://10.87.203.183/renewal-system/views/auth/login.php"
                    style="background:#27ae60; color:white; padding:12px 25px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block;">
                    Back To Login
                </a>
                <a href="javascript:void(0)" onclick="window.close();"
                    style="background:#e74c3c; color:white; padding:12px 25px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block;">
                    Tutup Jendela
                </a>
            </div>
        </div>
    </div>
<?php
    exit;
}

$exp = new DateTime($data['expired_date'], new DateTimeZone('Asia/Jakarta'));

require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host        = "10.87.200.12";
$mail->SMTPAuth    = false;
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure  = false;
$mail->Port        = 25;
$mail->CharSet     = 'UTF-8';

$mail->setFrom("itrenewalsystem@hexindo-tbk.co.id", "IT Renewal");
$mail->addAddress("andika@hexindo-tbk.co.id");
$mail->addAddress("ara.rhzz16@gmail.com");

$mail->isHTML(true);
$mail->Subject = "Permintaan Penawaran IT - " . $data['product_name'];

$mail->Body = "
<div style='font-family: Arial, sans-serif; background-color: #ffffff; padding: 20px; color: #333;'>
    <p style='font-size: 15px;'>Dear <b>Mas Fauzi / Mbak Nurhesty</b>,</p>
    <p style='font-size: 15px; line-height: 1.6;'>Mohon bantuannya untuk dimintakan penawaran atas barang sebagai berikut:</p>
   <table style='border-collapse: collapse; margin-top: 20px; min-width: 300px; border: 1px solid #e1e4e8;'>
        <thead>
            <tr style='background-color: #004a75; color: #ffffff;'>
                <th style='padding: 12px 15px; text-align: left; border: 1px solid #003d5b; text-transform: uppercase;'>Product Name</th>
                <th style='padding: 12px 15px; text-align: left; border: 1px solid #003d5b; text-transform: uppercase;'>Serial Number</th>
                <th style='padding: 12px 15px; text-align: left; border: 1px solid #003d5b; text-transform: uppercase;'>Expired Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style='padding: 12px 15px; border: 1px solid #dee2e6; font-weight: bold; color: #2c3e50;'>{$data['product_name']}</td>
                <td style='padding: 12px 15px; border: 1px solid #dee2e6; color: #555;'>{$data['serial_number']}</td>
                <td style='padding: 12px 15px; border: 1px solid #dee2e6; color: #e74c3c; font-weight: bold;'>{$exp->format('Y-m-d')}</td>
            </tr>
        </tbody>
    </table>
    <p style='margin-top: 25px; font-size: 15px;'>Atas perhatiannya, kami ucapkan terima kasih.</p>
    <div style='margin-top: 35px; padding-top: 15px; border-top: 1px solid #eee;'>
        <p style='margin: 0; font-size: 14px; color: #888;'>Best Regards,</p>
        <p style='margin: 5px 0 0 0; font-size: 16px; color: #004a75;'><b>Hexindo - IT System</b></p>
    </div>
</div>";

if (!$mail->send()) {
    echo "Error: " . $mail->ErrorInfo;
} else {
    $updateStmt = $mysqli->prepare("UPDATE products SET request_count = request_count + 1 WHERE id = ?");
    $updateStmt->bind_param("i", $id);
    $updateStmt->execute();

    header("Location: request.php?id=$id&status=sent");
    exit;
}
?>