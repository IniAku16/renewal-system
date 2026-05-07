<?php
$userModel = new UserModel($koneksi);
$users = $userModel->getAllUsers();
?>

<h1>Admin Panel - Monitoring User</h1>
<p>Selamat Datang, <b><?= $_SESSION['username'] ?></b></p>

<a href="index.php?action=add_user" style="display:inline-block; margin-bottom:10px; padding:5px 10px; background:blue; color:white; text-decoration:none; border-radius:3px;">+ Tambah User Baru</a>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align:left;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Username</th>
            <th>Email</th>
            <th>Departemen</th>
            <th>Role</th>
            <th>Status (Online/Offline)</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $users->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['departemen']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
                <?php 
                if ($row['last_activity']) {
                    $last_active = strtotime($row['last_activity']);
                    $five_minutes_ago = time() - (5 * 60);
                    
                    if ($last_active > $five_minutes_ago) {
                        echo "<span style='color:green; font-weight:bold;'>● Online</span>";
                    } else {
                        echo "<span style='color:red;'>○ Offline</span> <br><small>(Terakhir: ".date('H:i', $last_active).")</small>";
                    }
                } else {
                    echo "<span style='color:gray;'>Belum pernah login</span>";
                }
                ?>
            </td>
            <td>
                <a href="index.php?action=edit_user&id=<?= $row['id_user'] ?>">Edit</a> | 
                <a href="index.php?action=delete_user&id=<?= $row['id_user'] ?>" 
                   onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')" 
                   style="color:red;">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>