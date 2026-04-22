<?php
date_default_timezone_set("Asia/Jakarta");

$server   = "localhost";
$username = "root";
$password = "";
$database = "renewal_system";

$mysqli = new mysqli($server, $username, $password, $database);
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

$today = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$today->setTime(0, 0, 0);

$query = $mysqli->query("SELECT id, product_name, expired_date FROM products WHERE request_count = 0");

$rows = "";
$kirim_email = false;
$no = 1;

while ($data = $query->fetch_assoc()) {
    $nama_produk = htmlspecialchars($data['product_name']);
    $exp = new DateTime($data['expired_date'], new DateTimeZone('Asia/Jakarta'));

    $interval = $today->diff($exp);
    $selisih_hari = (int)$interval->format("%r%a");

    if ($selisih_hari > 60) {
        continue;
    }

    if ($selisih_hari < 0) {
        $status = "Expired";
        $badge = "#e74c3c";
        $text = "Expired " . abs($selisih_hari) . " hari lalu";
    } elseif ($selisih_hari == 0) {
        $status = "Hari Ini";
        $badge = "rgb(103, 255, 22)";
        $text = "Hari ini";
    } else {
        $status = "Expiring";
        $badge = "#f39c12";
        $text = $selisih_hari . " hari lagi";
    }

    $rows .= "
    <tr style='border-bottom: 1px solid #ededed;'>
        <td style='padding: 15px 10px; text-align: center; color: #7f8c8d; font-size: 14px;'>$no</td>
        <td style='padding: 15px 10px; font-weight: bold; color: #2c3e50; font-size: 15px;'>$nama_produk</td>
        <td style='padding: 15px 10px; text-align: center; color: #34495e; font-size: 14px;'>" . $exp->format("d M Y") . "</td>
        <td style='padding: 15px 10px; text-align: center; color: #34495e; font-size: 14px;'>$text</td>
        <td style='padding: 15px 10px; text-align: center;'>
            <span style='background:$badge; color:white; padding:4px 12px; border-radius:50px; font-size:11px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px;'>
                $status
            </span>
        </td>
        <td style='padding: 15px 10px; text-align: center;'>
            <a href='http://10.87.203.183/renewal-system/cron/request.php?id={$data['id']}'
               style='background:#27ae60; color:white; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:13px; font-weight:bold; display:inline-block; transition: all 0.3s;'>
               Request Quotation
            </a>
        </td>
    </tr>
    ";

    $kirim_email = true;
    $no++;
}

if ($kirim_email) {

    $isi_email = "
    <html>
    <body style='margin:0; padding:0; background-color:#f8f9fa; font-family: \"Segoe UI\", Helvetica, Arial, sans-serif;'>
        <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color:#f8f9fa; padding: 40px 10px;'>
            <tr>
                <td align='center'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0' style='max-width:850px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.05);'>

                        <!-- Body Content -->
                        <tr>
                            <td style='padding: 40px;'>
                                <h2 style='color:#2c3e50; margin:0 0 15px 0; font-size: 20px;'>Product Expiring Reminder</h2>
                                <p style='color:#5f676d; line-height: 1.6; font-size: 16px; margin-bottom: 30px;'>
                                    Sistem mendeteksi beberapa produk yang akan atau telah melewati masa berlaku dalam <strong>≤ 60 hari</strong>. Mohon segera ditindaklanjuti.
                                </p>

                                <!-- Table -->
                                <table width='100%' border='0' cellspacing='0' cellpadding='0' style='border-collapse: collapse; border: 1px solid #0a3d62;'>
                                    <thead>
                                        <tr style='background-color: #0a3d62; color: #ffffff;'>
                                            <th style='padding: 12px 10px; text-align: center; font-size: 13px; text-transform: uppercase; border: 1px solid #082d49;'>No</th>
                                            <th style='padding: 12px 10px; text-align: left; font-size: 13px; text-transform: uppercase; border: 1px solid #082d49;'>Product Name</th>
                                            <th style='padding: 12px 10px; text-align: center; font-size: 13px; text-transform: uppercase; border: 1px solid #082d49;'>Expired Date</th>
                                            <th style='padding: 12px 10px; text-align: center; font-size: 13px; text-transform: uppercase; border: 1px solid #082d49;'>Sisa Hari</th>
                                            <th style='padding: 12px 10px; text-align: center; font-size: 13px; text-transform: uppercase; border: 1px solid #082d49;'>Status</th>
                                            <th style='padding: 12px 10px; text-align: center; font-size: 13px; text-transform: uppercase; border: 1px solid #082d49;'>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        $rows
                                    </tbody>
                                </table>

                                <!-- Footer Note -->
                                <div style='margin-top: 40px; padding-top: 20px; border-top: 1px dashed #d1d8e0;'>
                                    <p style='margin:0; font-size:13px; color:#95a5a6; line-height: 1.5;'>
                                        * Pesan ini dibuat otomatis oleh <strong>Warranty Server System</strong>.<br>
                                        Harap tidak membalas email ini secara langsung.
                                    </p>
                                </div>
                            </td>
                        </tr>

                        <!-- Bottom Footer -->
                        <tr>
                            <td style='background:#f1f2f6; padding: 20px; text-align: center; color: #7f8c8d; font-size: 12px;'>
                                &copy; " . date('Y') . " PT Hexindo Adiperkasa Tbk. All Rights Reserved.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";

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

    $mail->CharSet = 'UTF-8';

    $mail->From = "HexindoWaranty@hexindo-tbk.co.id";
    $mail->FromName = "Warranty Server System";
    $mail->addAddress("andika@hexindo-tbk.co.id");
    $mail->addAddress("ara.rhzz16@gmail.com");


    $mail->isHTML(true);
    $mail->Subject = "Produk Mendekati / Melewati Expired (≤ 60 Hari)";
    $mail->Body    = $isi_email;
    $mail->AltBody = "Reminder produk akan expired";

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Email berhasil dikirim ke Pak Dika";
    }
} else {
    echo "Tidak ada produk expiring (≤ 60 hari)";
}
