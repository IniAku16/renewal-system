<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Pembayaran Renewal</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            text-transform: uppercase;
            font-size: 18pt;
            color: #2c3e50;
            margin: 0;
        }

        .header p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 9pt;
        }

        .product-section {
            margin-bottom: 30px;
            page-break-inside: avoid; 
        }

        .product-info-table {
            width: 100%;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
        }

        .product-info-table td {
            padding: 8px 12px;
            border: none;
        }

        .label {
            font-weight: bold;
            color: #2c3e50;
            width: 150px;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .value {
            color: #333;
        }

        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.detail-table th {
            background-color: #2c3e50;
            color: #ffffff;
            text-align: left;
            padding: 10px;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.detail-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }

        table.detail-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .amount {
            font-family: 'Courier', monospace;
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            font-size: 11pt;
            color: #2c3e50;
            margin-bottom: 8px;
            border-left: 4px solid #3498db;
            padding-left: 10px;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #bdc3c7;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #95a5a6;
            border: 2px dashed #eee;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Riwayat Pembayaran Renewal</h1>
        <p>Laporan Keseluruhan Produk | Dicetak pada: <?= date('d/m/Y') ?></p>
    </div>

    <?php if (!empty($histories)) : ?>
        <?php foreach ($histories as $index => $item) : ?>
            <div class="product-section">
                <div class="section-title">Informasi Produk #<?= $index + 1 ?></div>
                
                <table class="product-info-table">
                    <tr>
                        <td class="label">Nama Produk</td>
                        <td class="value">: <strong><?= htmlspecialchars($item['product_name']) ?></strong></td>
                        <td class="label">Total Transaksi</td>
                        <td class="value">: <?= $item['total_transaksi'] ?> Kali</td>
                    </tr>
                    <tr>
                        <td class="label">Serial Number</td>
                        <td class="value">: <code><?= htmlspecialchars($item['serial_number']) ?></code></td>
                        <td class="label">Total Terbayar</td>
                        <td class="value">: <strong>Rp <?= number_format($item['total_amount'], 0, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <td class="label">Pembayaran Terakhir</td>
                        <td class="value">: <?= !empty($item['last_payment_date']) ? date('d M Y', strtotime($item['last_payment_date'])) : '-' ?></td>
                        <td colspan="2"></td>
                    </tr>
                </table>

                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">No</th>
                            <th>Tanggal Pembayaran</th>
                            <th class="text-right">Nominal Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($item['details'])) : ?>
                            <?php foreach ($item['details'] as $detailIndex => $detail) : ?>
                                <tr>
                                    <td class="text-center"><?= $detailIndex + 1 ?></td>
                                    <td><?= date('d F Y', strtotime($detail['payment_date'])) ?></td>
                                    <td class="text-right amount">Rp <?= number_format($detail['amount'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" class="text-center" style="color: #999 italic;">Belum ada detail riwayat pembayaran untuk produk ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="empty-state">
            <h3>Data Tidak Ditemukan</h3>
            <p>Belum ada riwayat pembayaran yang tercatat dalam sistem.</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        Renewal System
    </div>

</body>
</html>