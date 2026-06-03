<?php
$conn1 = new mysqli("localhost", "root", "", "renewal_system"); 

if ($conn1->connect_error) { die("Koneksi gagal ke renewal_system"); }

$query = "SELECT DISTINCT user_id FROM products 
          WHERE request_count = 0 
          AND DATEDIFF(expired_date, CURDATE()) IN (60, 30, 3, 2, 1, 0, -1)";

$users = $conn1->query($query);

if ($users->num_rows > 0) {
    while ($user = $users->fetch_assoc()) {
        $user_id_reminder = $user['user_id']; 
        $koneksi = $conn1; 
        include __DIR__ . "/cron/email_reminder.php";
    }
}

$conn1->close(); 
?>