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

        .requirement-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #666;
            padding: 5px 0;
        }

        .requirement-item i {
            width: 20px;
            margin-right: 8px;
            font-size: 14px;
            text-align: center;
        }

        .requirement-item.met {
            color: #10b981;
        }

        .requirement-item.met i {
            color: #10b981;
        }

        .requirement-item.unmet {
            color: #ef4444;
        }

        .requirement-item.unmet i {
            color: #ef4444;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#"><i class="bi bi-shield-lock-fill me-2"></i> RENEWAL <span class="text-dark">SYSTEM</span></a>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 d-none d-md-block text-muted small">Halo, <b><?= $_SESSION['username'] ?></b></span>
                <a href="/renewal-system/public/index.php?action=logout" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
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
                        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" id="add_username" class="form-control" required oninput="validateAddUsername()">
                            <div id="add_username_feedback" class="mt-2" style="font-size: 0.85rem; display: none;"></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="text" class="form-control" value="Akan dibuat otomatis" disabled>
                            <div class="form-text">Password default akan dibuat secara acak dan user diminta untuk mengubahnya saat login pertama.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" name="departemen" id="add_dept" list="departemen_list_add" class="form-control" placeholder="Pilih departemen atau ketik untuk mencari..." required autocomplete="off">
                            <datalist id="departemen_list_add">
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= htmlspecialchars($branch) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
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

    <div class="modal fade" id="passwordGeneratedModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <div class="modal-header border-0">
                    <h5 class="fw-bold">Detail Akun Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Detail akun berikut siap dikirimkan kepada pengguna:</p>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Username</label>
                        <input type="text" id="generated_username_value" class="form-control bg-light border-0" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Email</label>
                        <input type="text" id="generated_email_value" class="form-control bg-light border-0" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted mb-1">Password Default</label>
                        <input type="text" id="generated_password_value" class="form-control bg-light border-0" readonly style="font-family: monospace;">
                    </div>

                    <button class="btn btn-primary w-100 py-2 mb-2 shadow-sm" id="copyAllDetailsBtn">
                        <i class="bi bi-clipboard-check me-2"></i> Salin Semua Detail Akun
                    </button>

                    <div id="copy_feedback" class="form-text text-success text-center fw-bold" style="display:none;">
                        <i class="bi bi-check2-circle"></i> Berhasil disalin ke clipboard!
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
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
                        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" id="edit_username" class="form-control" required oninput="validateEditUsername()">
                            <div id="edit_username_feedback" class="mt-2" style="font-size: 0.85rem; display: none;"></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Password (Kosongkan jika tidak ganti)</label>
                            <input type="password" name="password" id="edit_password" class="form-control" oninput="validateEditPassword()">
                            <div id="edit_password_requirements" class="mt-3" style="font-size: 0.85rem; display: none;">
                                <div class="requirement-item" id="edit_req_minlength">
                                    <i class="bi bi-circle"></i>
                                    <span>Minimal 8 karakter</span>
                                </div>
                                <div class="requirement-item" id="edit_req_uppercase">
                                    <i class="bi bi-circle"></i>
                                    <span>Huruf besar (A-Z)</span>
                                </div>
                                <div class="requirement-item" id="edit_req_lowercase">
                                    <i class="bi bi-circle"></i>
                                    <span>Huruf kecil (a-z)</span>
                                </div>
                                <div class="requirement-item" id="edit_req_symbol">
                                    <i class="bi bi-circle"></i>
                                    <span>Simbol (!@#$%^&* dll)</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" name="departemen" id="edit_dept" list="departemen_list_edit" class="form-control" placeholder="Pilih departemen atau ketik untuk mencari..." required autocomplete="off">
                            <datalist id="departemen_list_edit">
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= htmlspecialchars($branch) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
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
                    const modal = document.getElementById('editUserModal');
                    document.getElementById('edit_id_user').value = this.dataset.id;
                    document.getElementById('edit_username').value = this.dataset.username;
                    document.getElementById('edit_email').value = this.dataset.email;
                    document.getElementById('edit_dept').value = this.dataset.dept;
                    document.getElementById('edit_role').value = this.dataset.role;
                    new bootstrap.Modal(modal).show();
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
                    const newTableBody = doc.getElementById('user-table-body').innerHTML;
                    const newTotalCount = doc.getElementById('total-users-count').innerText;

                    document.getElementById('user-table-body').innerHTML = newTableBody;
                    document.getElementById('total-users-count').innerText = newTotalCount;

                    initEditButtons();
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initEditButtons();
            setInterval(refreshData, 10000); 

            const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
            const passwordGeneratedModal = new bootstrap.Modal(document.getElementById('passwordGeneratedModal'));

            document.getElementById('addUserForm').onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('index.php?page=admin_dashboard&action=add_user', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.reset();
                            addUserModal.hide();

                            document.getElementById('generated_username_value').value = data.username;
                            document.getElementById('generated_email_value').value = data.email;
                            document.getElementById('generated_password_value').value = data.generatedPassword;

                            document.getElementById('copy_feedback').style.display = 'none';
                            passwordGeneratedModal.show();
                        } else {
                            alert(data.message + (data.errors ? ':\n' + data.errors.join('\n') : ''));
                        }
                    })
                    .catch(err => alert('Terjadi kesalahan pada server.'));
            };

            document.getElementById('copyAllDetailsBtn').addEventListener('click', function() {
                const username = document.getElementById('generated_username_value').value;
                const email = document.getElementById('generated_email_value').value;
                const password = document.getElementById('generated_password_value').value;

                const textToCopy = `Detail Akun Sistem Renewal:\n` +
                    `---------------------------\n` +
                    `Username : ${username}\n` +
                    `Email    : ${email}\n` +
                    `Password : ${password}\n` +
                    `---------------------------\n` +
                    `Silakan login dan segera ubah password Anda.`;

                navigator.clipboard.writeText(textToCopy).then(() => {
                    const feedback = document.getElementById('copy_feedback');
                    feedback.style.display = 'block';

                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check2"></i> Tersalin!';
                    this.classList.replace('btn-primary', 'btn-success');

                    setTimeout(() => {
                        feedback.style.display = 'none';
                        this.innerHTML = originalContent;
                        this.classList.replace('btn-success', 'btn-primary');
                    }, 2500);
                }).catch(err => {
                    alert('Gagal menyalin, silakan salin manual.');
                });
            });

            document.getElementById('editUserForm').onsubmit = function(e) {
                e.preventDefault();
                const id = document.getElementById('edit_id_user').value;
                fetch('index.php?page=admin_dashboard&action=edit_user&id=' + id, {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') location.reload();
                    });
            };

            document.getElementById('passwordGeneratedModal').addEventListener('hidden.bs.modal', () => location.reload());

            document.getElementById('addUserModal').addEventListener('show.bs.modal', () => {
                document.getElementById('add_dept').value = '';
            });
        });

        function deleteUser(id) {
            if (confirm('Hapus user ini selamanya?')) {
                fetch('index.php?page=admin_dashboard&action=delete_user&id=' + id, {
                        method: 'POST'
                    })
                    .then(() => location.reload());
            }
        }

        function validateAddUsername() {
            validateUsername('add', document.getElementById('add_username').value);
        }

        function validateEditUsername() {
            validateUsername('edit', document.getElementById('edit_username').value);
        }

        function validateUsername(prefix, username) {
            const feedback = document.getElementById(`${prefix}_username_feedback`);
            const errors = [];
            if (!/^[a-zA-Z0-9._-]*$/.test(username)) {
                errors.push('❌ Simbol tidak diizinkan (kecuali . _ -)');
            }
            if (username.length > 0 && username.length < 3) errors.push('❌ Minimal 3 karakter');
            if (username.length > 50) errors.push('❌ Maksimal 50 karakter');

            if (errors.length > 0) {
                feedback.style.display = 'block';
                feedback.innerHTML = errors.join('<br>');
            } else {
                feedback.style.display = (username.length > 0) ? 'block' : 'none';
                feedback.innerHTML = '✓ Format username valid';
            }
        }

        function validateEditPassword() {
            const password = document.getElementById('edit_password').value;
            const reqBox = document.getElementById('edit_password_requirements');
            if (password.length > 0) {
                reqBox.style.display = 'block';
                updatePasswordRequirements('edit', password);
            } else {
                reqBox.style.display = 'none';
            }
        }

        function updatePasswordRequirements(prefix, password) {
            const reqs = {
                minlength: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                symbol: /[!@#$%^&*()_+\-=\[\]{};:'",.< >?\/ ]/.test(password)
            };

            for (const [key, met] of Object.entries(reqs)) {
                const el = document.getElementById(`${prefix}_req_${key}`);
                if (el) {
                    el.className = `requirement-item ${met ? 'met' : 'unmet'}`;
                    el.querySelector('i').className = `bi ${met ? 'bi-check-circle' : 'bi-x-circle'}`;
                }
            }
        }
    </script>

</body>

</html>