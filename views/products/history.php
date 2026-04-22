<?php
$activePage = 'history';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Riwayat Pembayaran | Renewal System</title>
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

        .badge-total {
            background: #eaf4ff;
            color: #4facfe;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .btn-primary {
            background: var(--primary-grad);
            border: none !important;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }

        .btn-danger-grad {
            background: linear-gradient(135deg, #ff7675, #d63031);
            border: none !important;
            color: white;
            box-shadow: 0 4px 15px rgba(214, 48, 49, 0.3);
        }

        .form-control {
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
                        <h2 class="fw-bold text-dark">Riwayat Pembayaran</h2>
                        <p class="text-muted">Pantau seluruh transaksi renewal produk Anda.</p>
                    </div>
                    <a href="/renewal-system/public/index.php?action=history-pdf" target="_blank" class="btn btn-danger-grad px-4 py-2 rounded-3">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Cetak Semua PDF
                    </a>
                </div>

                <div class="table-container">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchHistory" class="form-control border-start-0" placeholder="Cari product atau serial number...">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Product Name</th>
                                    <th>Serial Number</th>
                                    <th class="text-center">Total Transaksi</th>
                                    <th>Total Pembayaran</th>
                                    <th>Pembayaran Terakhir</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <?php if (!empty($histories)) : ?>
                                    <?php foreach ($histories as $index => $item) : ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></td>
                                            <td><code><?= htmlspecialchars($item['serial_number']) ?></code></td>
                                            <td class="text-center">
                                                <span class="badge-total"><?= $item['total_transaksi'] ?></span>
                                            </td>
                                            <td class="fw-bold text-primary">
                                                Rp <?= number_format($item['total_amount'], 0, ',', '.') ?>
                                            </td>
                                            <td>
                                                <span class="small text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?= !empty($item['last_payment_date']) ? date('d M Y', strtotime($item['last_payment_date'])) : '-' ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary detail-btn" 
                                                            data-product-id="<?= $item['product_id'] ?>" 
                                                            data-product-name="<?= htmlspecialchars($item['product_name']) ?>">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                    <a href="/renewal-system/public/index.php?action=history-pdf&product_id=<?= $item['product_id'] ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat pembayaran</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">
                        Detail Renewal: <span id="detailProductName" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody id="detailHistoryBody">
                            </tbody>
                        </table>
                    </div>
                </div>
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
                });
            }

            const searchInput = document.getElementById('searchHistory');
            const tableBody = document.getElementById('historyTableBody');

            searchInput.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });

            document.querySelectorAll('.detail-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;

                    document.getElementById('detailProductName').innerText = productName;
                    const tbody = document.getElementById('detailHistoryBody');
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

                    const modal = new bootstrap.Modal(document.getElementById('detailHistoryModal'));
                    modal.show();

                    fetch('/renewal-system/public/index.php?action=history-detail&product_id=' + productId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success' && data.data.length > 0) {
                                let html = '';
                                data.data.forEach((item, index) => {
                                    html += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${item.payment_date}</td>
                                            <td class="fw-bold">Rp ${Number(item.amount).toLocaleString('id-ID')}</td>
                                        </tr>
                                    `;
                                });
                                tbody.innerHTML = html;
                            } else {
                                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Tidak ada data.</td></tr>';
                            }
                        })
                        .catch(() => {
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data.</td></tr>';
                        });
                });
            });

            window.onpageshow = function(event) {
                if (event.persisted) location.reload();
            };
        });
    </script>
</body>

</html>