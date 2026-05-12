<?php
$activePage = 'admin';
if (isset($_SESSION['id_user']) && isset($_GET['update_activity'])) {
    $userModel->updateLastActivity($_SESSION['id_user']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Renewal System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        :root {
            --primary-grad: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        body {
            background: #f8f9fa;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar-custom {
            background: white;
            box-shadow: var(--card-shadow);
            padding: 15px 0;
            margin-bottom: 30px;
        }

        .card-custom {
            background: white;
            border: none;
            border-radius: 24px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
            background: white;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            position: relative;
        }

        .stat-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: #4facfe;
        }

        .table-container {
            background: white;
            border-radius: 24px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .badge-role {
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 0.75rem;
        }

        .btn-primary {
            background: var(--primary-grad);
            border: none;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#"><i class="bi bi-shield-lock-fill me-2"></i> RENEWAL <span class="text-dark">SYSTEM</span></a>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 d-none d-md-block text-muted small">Halo, <b><?= $_SESSION['username'] ?></b></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">User Management</h2>
                <p class="text-muted">Kelola akses pengguna sistem di sini.</p>
            </div>
            <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus me-2"></i> Add New User
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary"><i class="bi bi-people fs-3"></i></div>
                        <div>
                            <p class="text-muted mb-0">Total Users</p>
                            <h3 class="fw-bold mb-0" id="total-users-count"><?= count($users) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Departemen</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['departemen']) ?></td>
                                <td><span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-info text-dark' ?> badge-role"><?= strtoupper($user['role']) ?></span></td>
                                <td>
                                    <?php
                                    $isOnline = ($user['last_activity'] && strtotime($user['last_activity']) > time() - 60);
                                    if ($isOnline): ?>
                                        <span class="text-success small fw-bold"><i class="bi bi-circle-fill me-1"></i> Online</span>
                                    <?php else: ?>
                                        <span class="text-muted small">Offline</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-warning edit-user-btn" data-id="<?= $user['id_user'] ?>" data-username="<?= $user['username'] ?>" data-email="<?= $user['email'] ?>" data-dept="<?= $user['departemen'] ?>" data-role="<?= $user['role'] ?>"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user['id_user'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <form id="addUserForm">
                    <div class="modal-header border-0">
                        <h5 class="fw-bold">Tambah User Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Departemen</label><input type="text" name="departemen" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="-">-</option>
                                <option value="user">User / Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <form id="editUserForm">
                    <input type="hidden" name="id_user" id="edit_id_user">
                    <div class="modal-header border-0">
                        <h5 class="fw-bold">Update User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" id="edit_username" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Password (Kosongkan jika tidak ganti)</label><input type="password" name="password" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Departemen</label><input type="text" name="departemen" id="edit_dept" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="user">User / Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary w-100">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function initEditButtons() {
            document.querySelectorAll('.edit-user-btn').forEach(btn => {
                btn.onclick = function() {
                    document.getElementById('edit_id_user').value = this.dataset.id;
                    document.getElementById('edit_username').value = this.dataset.username;
                    document.getElementById('edit_email').value = this.dataset.email;
                    document.getElementById('edit_dept').value = this.dataset.dept;
                    document.getElementById('edit_role').value = this.dataset.role;
                    new bootstrap.Modal(document.getElementById('editUserModal')).show();
                };
            });
        }

        function refreshData() {
            const currentUrl = window.location.href;
            const updateUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'update_activity=1';

            fetch(updateUrl);

            fetch(currentUrl)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    document.getElementById('user-table-body').innerHTML = doc.getElementById('user-table-body').innerHTML;
                    document.getElementById('total-users-count').innerText = doc.getElementById('total-users-count').innerText;

                    initEditButtons();
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initEditButtons();

            setInterval(refreshData, 5000);

            document.getElementById('addUserForm').onsubmit = function(e) {
                e.preventDefault();
                fetch('index.php?page=admin_dashboard&action=add_user', {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(res => res.json()).then(data => {
                        alert(data.message);
                        if (data.status === 'success') location.reload();
                    });
            };

            document.getElementById('editUserForm').onsubmit = function(e) {
                e.preventDefault();
                const id = document.getElementById('edit_id_user').value;
                fetch('index.php?page=admin_dashboard&action=edit_user&id=' + id, {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(res => res.json()).then(data => {
                        alert(data.message);
                        if (data.status === 'success') location.reload();
                    });
            };
        });

        function deleteUser(id) {
            if (confirm('Hapus user ini?')) {
                fetch('index.php?page=admin_dashboard&action=delete_user&id=' + id, {
                    method: 'POST'
                }).then(() => location.reload());
            }
        }
    </script>
</body>

</html>