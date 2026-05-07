<?php
$activePage = 'products';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Produk | Renewal System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        :root {
            --bg-color: #f0f4f8;
            --primary-grad: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        body {
            background: #f8f9fa;
            background-image: radial-gradient(#d1d9e6 1px, transparent 1px);
            background-size: 20px 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #main-content {
            padding: 30px;
            width: calc(100% - 260px);
            margin-left: 260px;
            transition: 0.4s ease;
        }

        #main-content.expanded {
            width: calc(100% - 85px);
            margin-left: 85px;
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
            padding: 20px;
            transition: transform 0.3s ease;
            background: white;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
        }

        .stat-card.total::after {
            background: #4facfe;
        }

        .stat-card.active::after {
            background: #00b894;
        }

        .stat-card.expiring::after {
            background: #fdcb6e;
        }

        .stat-card.expired::after {
            background: #ff7675;
        }

        .table-container {
            background: white;
            border-radius: 24px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #edf2f7;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 15px;
        }

        .badge-status {
            border-radius: 8px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-primary {
            background: var(--primary-grad);
            border: none !important;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #00b894, #55efc4);
            border: none !important;
            box-shadow: 0 4px 15px rgba(0, 184, 148, 0.3);
        }

        .form-control,
        .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.1);
        }

        h2 {
            font-weight: 800;
            color: #1e293b;
        }

        .modal-content {
            border: none;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <div id="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></h2>
                        <p class="text-muted">Kelola renewal produk Anda di sini.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary px-4 py-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#createProductModal">
                            <i class="bi bi-plus-lg me-2"></i> Add New Product
                        </button>

                        <button class="btn btn-success px-4 py-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                            <i class="bi bi-file-earmark-excel me-2"></i> Import from Excel
                        </button>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-white shadow-sm p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
                                    <i class="bi bi-box-seam fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Total Products</p>
                                    <h3 class="fw-bold mb-0"><?= $totalProducts ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-white shadow-sm p-3 border-start border-4 border-success">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3 text-success">
                                    <i class="bi bi-check-circle fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Active</p>
                                    <h3 class="fw-bold mb-0 text-success"><?= $activeCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-white shadow-sm p-3 border-start border-4 border-warning">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3 text-warning">
                                    <i class="bi bi-exclamation-triangle fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Expiring Soon</p>
                                    <h3 class="fw-bold mb-0 text-warning"><?= $expiringCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-white shadow-sm p-3 border-start border-4 border-danger">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3 text-danger">
                                    <i class="bi bi-x-circle fs-3"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Expired</p>
                                    <h3 class="fw-bold mb-0 text-danger"><?= $expiredCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-custom mb-4">
                    <form action="/renewal-system/public/index.php" method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="action" value="exportExcel">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100 fw-bold">
                                <i class="bi bi-file-earmark-excel me-2"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
                <div class="table-container">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchProduct" class="form-control border-start-0" placeholder="Search product name or serial...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="filterExpired" class="form-select">
                                <option value="">All Status</option>
                                <option value="week">Expiring this week</option>
                                <option value="month">Expiring this month</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <span class="me-2 small">Show:</span>
                                <select id="rowsPerPage" class="form-select w-auto">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="productTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product Name</th>
                                    <th>Serial Number</th>
                                    <th>Last Quotation</th>
                                    <th>Expired Date</th>
                                    <th>Sisa Hari</th>
                                    <th>Status</th>
                                    <th>Request Quotation</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)) : $no = 1;
                                    foreach ($products as $product) :
                                        $statusClass = ($product['status'] == 'expired') ? 'danger' : (($product['status'] == 'expiring') ? 'warning text-dark' : 'success');
                                ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($product['product_name']) ?></td>
                                            <td><code><?= htmlspecialchars($product['serial_number']) ?></code></td>
                                            <td>Rp <?= number_format($product['harga_renewal'], 0, ',', '.') ?></td>
                                            <td><?= date('d M Y', strtotime($product['expired_date'])) ?></td>
                                            <td>
                                                <?php if ($product['sisa_hari'] < 0): ?>
                                                    <span class="text-danger small fw-bold">Expired <?= abs($product['sisa_hari']) ?> days ago</span>
                                                <?php else: ?>
                                                    <span class="text-primary small fw-bold"><?= $product['sisa_hari'] ?> days left</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge bg-<?= $statusClass ?> badge-status"><?= ucfirst($product['status']) ?></span></td>
                                            <td class="text-center">
                                                <?php if ($product['request_count'] > 0): ?>
                                                    <span class="badge bg-info text-dark">
                                                        <i class="bi bi-send-check me-1"></i><?= $product['request_count'] ?> Requested
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border">
                                                        <i class="bi bi-clock-history me-1"></i> Not Yet
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-warning btn-update"
                                                        data-id="<?= $product['id'] ?>"
                                                        data-name="<?= htmlspecialchars($product['product_name']) ?>"
                                                        data-serial="<?= htmlspecialchars($product['serial_number']) ?>"
                                                        data-harga="<?= $product['harga_renewal'] ?>"
                                                        data-expired="<?= $product['expired_date'] ?>"
                                                        data-request="<?= $product['request_count'] ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success done-btn" data-id="<?= $product['id'] ?>">
                                                        <i class="bi bi-check2-circle"></i> Renewal
                                                    </button>
                                                    <a href="?action=delete&id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No data found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div id="pagination" class="mt-3 d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <form id="productForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProductModalLabel">Add New Product</h5> <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"> <label for="product_name" class="form-label">Product Name</label> <input type="text" class="form-control" id="product_name" name="product_name" required /> </div>
                        <div class="mb-3"> <label for="serial_number" class="form-label">Serial Number</label> <input type="text" class="form-control" id="serial_number" name="serial_number" required /> </div>
                        <div class="mb-3"> <label for="harga_renewal" class="form-label">Last Quotation</label> <input type="number" class="form-control" id="harga_renewal" name="harga_renewal" required /> </div>
                        <div class="mb-3"> <label for="expired_date" class="form-label">Expired Date</label> <input type="date" class="form-control" id="expired_date" name="expired_date" required /> </div>
                    </div>
                    <div class="modal-footer"> <button type="submit" class="btn btn-primary">Save</button> </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <form id="editProductForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Update Product</h5> <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body"> <input type="hidden" id="edit_product_id" name="id" />
                        <div class="mb-3"> <label for="edit_product_name" class="form-label">Product Name</label> <input type="text" class="form-control" id="edit_product_name" name="product_name" required /> </div>
                        <div class="mb-3"> <label for="edit_serial_number" class="form-label">Serial Number</label> <input type="text" class="form-control" id="edit_serial_number" name="serial_number" required /> </div>
                        <div class="mb-3"> <label for="edit_harga_renewal" class="form-label">Last Quotation</label> <input type="number" class="form-control" id="edit_harga_renewal" name="harga_renewal" required /> </div>
                        <div class="mb-3"> <label for="edit_expired_date" class="form-label">Expired Date</label> <input type="date" class="form-control" id="edit_expired_date" name="expired_date" required /> </div>
                    </div>
                    <div class="modal-footer"> <button type="submit" class="btn btn-primary">Update</button> </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <form id="paymentForm">
                    <div class="modal-header">
                        <h5 class="modal-title">PO Rilis</h5> <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="payment_product_id" />
                        <label for="payment_date" class="form-label"></label>
                        <input type="date" class="form-control" id="payment_date" required />
                    </div>
                    <div class="modal-footer"> <button type="submit" class="btn btn-success">Save</button> </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom">
                <form id="importExcelForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <small>Format Excel harus memiliki header: <b>Product Name, Serial Number, Expired Date (YYYY-MM-DD), Last Quotation</b></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih File Excel (.xlsx / .xls)</label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx, .xls" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');

                    console.log("Sidebar status: ", sidebar.classList.contains('collapsed') ? 'Tertutup' : 'Terbuka');
                });
            }

            let currentPage = 1;

            const searchInput = document.getElementById('searchProduct');
            const filterSelect = document.getElementById('filterExpired');
            const rowsPerPageSelect = document.getElementById('rowsPerPage');
            const tableBody = document.querySelector("#productTable tbody");
            const paginationContainer = document.getElementById("pagination");

            let allRows = Array.from(tableBody.querySelectorAll("tr"));

            function updateTable() {
                const searchText = searchInput.value.toLowerCase();
                const filterValue = filterSelect.value;
                const rowsPerPage = parseInt(rowsPerPageSelect.value);
                const today = new Date();

                let filteredRows = [];

                allRows.forEach(row => {
                    if (row.cells.length < 5) return;

                    const name = row.cells[1].innerText.toLowerCase();
                    const serial = row.cells[2].innerText.toLowerCase();
                    const expiredDate = new Date(row.cells[4].innerText);

                    let matchesSearch = name.includes(searchText) || serial.includes(searchText);
                    let matchesFilter = true;

                    const diff = (expiredDate - today) / (1000 * 60 * 60 * 24);

                    if (filterValue === 'week') {
                        matchesFilter = diff >= 0 && diff <= 7;
                    } else if (filterValue === 'month') {
                        matchesFilter = diff >= 0 && diff <= 30;
                    }

                    if (matchesSearch && matchesFilter) {
                        filteredRows.push(row);
                    }

                    row.style.display = "none";
                });

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                filteredRows.slice(start, end).forEach(row => {
                    row.style.display = "";
                });

                renderPagination(filteredRows.length, rowsPerPage);
            }

            function renderPagination(totalRows, rowsPerPage) {
                const totalPages = Math.ceil(totalRows / rowsPerPage);
                paginationContainer.innerHTML = "";

                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement("button");
                    btn.innerText = i;

                    btn.className = "btn btn-sm mx-1 " +
                        (i === currentPage ? "btn-primary" : "btn-outline-primary");

                    btn.addEventListener("click", () => {
                        currentPage = i;
                        updateTable();
                    });

                    paginationContainer.appendChild(btn);
                }
            }

            function resetPage() {
                currentPage = 1;
            }

            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    resetPage();
                    updateTable();
                });
            }

            if (filterSelect) {
                filterSelect.addEventListener('change', () => {
                    resetPage();
                    updateTable();
                });
            }

            if (rowsPerPageSelect) {
                rowsPerPageSelect.addEventListener('change', () => {
                    resetPage();
                    updateTable();
                });
            }

            updateTable();

            const productForm = document.getElementById('productForm');

            if (productForm) {
                productForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    fetch('/renewal-system/public/index.php?action=create', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message);
                            if (data.status === 'success') {
                                this.reset();
                                bootstrap.Modal.getInstance(
                                    document.getElementById('createProductModal')
                                ).hide();
                                location.reload();
                            }
                        })
                        .catch(() => alert('Terjadi kesalahan!'));
                });
            }

            document.querySelectorAll('.btn-update').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('edit_product_id').value = this.dataset.id;
                    document.getElementById('edit_product_name').value = this.dataset.name;
                    document.getElementById('edit_serial_number').value = this.dataset.serial;
                    document.getElementById('edit_harga_renewal').value = this.dataset.harga;
                    document.getElementById('edit_expired_date').value = this.dataset.expired;

                    new bootstrap.Modal(
                        document.getElementById('editProductModal')
                    ).show();
                });
            });

            const editProductForm = document.getElementById('editProductForm');

            if (editProductForm) {
                editProductForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const id = document.getElementById('edit_product_id').value;
                    const formData = new FormData(this);

                    fetch('/renewal-system/public/index.php?action=update&id=' + id, {
                            method: 'POST',
                            body: formData,
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message);
                            if (data.status === 'success') {
                                bootstrap.Modal.getInstance(
                                    document.getElementById('editProductModal')
                                ).hide();
                                location.reload();
                            }
                        })
                        .catch(() => alert('Update gagal!'));
                });
            }

            document.querySelectorAll('.done-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('payment_product_id').value = this.dataset.id;

                    new bootstrap.Modal(
                        document.getElementById('paymentModal')
                    ).show();
                });
            });

            const paymentForm = document.getElementById('paymentForm');

            if (paymentForm) {
                paymentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const id = document.getElementById('payment_product_id').value;
                    const date = document.getElementById('payment_date').value;

                    const formData = new FormData();
                    formData.append('payment_status', 'done');
                    formData.append('payment_date', date);

                    fetch('/renewal-system/public/index.php?action=update&id=' + id, {
                            method: 'POST',
                            body: formData,
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.status === 'success' ? "Payment Success!" : data.message);
                            if (data.status === 'success') location.reload();
                        })
                        .catch(() => alert('Error sistem!'));
                });
            }

            const importExcelForm = document.getElementById('importExcelForm');
            if (importExcelForm) {
                importExcelForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('/renewal-system/public/index.php?action=importExcel', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message);
                            if (data.status === 'success') {
                                location.reload();
                            }
                        })
                        .catch(() => alert('Terjadi kesalahan saat import!'));
                });
            }

            window.onpageshow = function(event) {
                if (event.persisted) location.reload();
            };

        });
    </script>
</body>

</html>