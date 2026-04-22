<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Pembayaran Produk</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            text-transform: uppercase;
            font-size: 16pt;
            margin: 0;
            color: #2c3e50;
        }

        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 9pt;
        }

        .product-card {
            background-color: #fcfcfc;
            border: 1px solid #e1e8ed;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .product-card-title {
            font-size: 11pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 1px solid #e1e8ed;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .info-grid {
            width: 100%;
        }

        .info-grid td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            width: 160px;
            color: #7f8c8d;
            font-weight: bold;
            font-size: 9pt;
        }

        .value {
            color: #2c3e50;
            font-weight: bold;
        }

        .table-title {
            font-weight: bold;
            font-size: 11pt;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-left: 8px;
            border-left: 4px solid #3498db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #2c3e50;
            color: #ffffff;
            text-align: left;
            padding: 12px 10px;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .currency {
            font-family: 'Courier', monospace;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #bdc3c7;
            padding: 10px 0;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Riwayat Pembayaran Produk</h1>
        <p>Dicetak otomatis oleh Renewal System | Tanggal: <?= date('d F Y ') ?></p>
    </div>

    <div class="product-card">
        <div class="product-card-title">Informasi Produk</div>
        <table class="info-grid">
            <tr>
                <td class="label">Nama Produk</td>
                <td class="value">: <?= htmlspecialchars($product['product_name']) ?></td>
            </tr>
            <tr>
                <td class="label">Serial Number</td>
                <td class="value">: <code><?= htmlspecialchars($product['serial_number']) ?></code></td>
            </tr>
            <tr>
                <td class="label">Expired Date Saat Ini</td>
                <td class="value">: <?= date('d M Y', strtotime($product['expired_date'])) ?></td>
            </tr>
        </table>
    </div>

    <div class="table-title">Detail Riwayat Transaksi</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No</th>
                <th>Tanggal Pembayaran</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($details)) : ?>
                <?php foreach ($details as $i => $item) : ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= date('d F Y', strtotime($item['payment_date'])) ?></td>
                        <td class="text-right currency">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3" class="text-center" style="color: #95a5a6; padding: 20px;">
                        Belum ada riwayat transaksi yang tercatat untuk produk ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan oleh Renewal System.
    </div>

</body>
</html>