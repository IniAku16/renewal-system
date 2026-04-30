<?php
require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../models/Payment.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProductController
{
    private $model;
    private $paymentModel;

    public function __construct($koneksi)
    {
        $this->model = new ProductModel($koneksi);
        $this->paymentModel = new PaymentModel($koneksi);
    }

    public function index()
    {
        $products = $this->model->getAllProducts();
        $data = [];

        $activeCount = 0;
        $expiringCount = 0;
        $expiredCount = 0;
        $requestedCount = 0;
        $notRequestedCount = 0;
        $expiringProducts = [];

        date_default_timezone_set("Asia/Jakarta");
        $today = date("Y-m-d");

        while ($row = mysqli_fetch_assoc($products)) {
            $expired = $row['expired_date'];
            $request_count = $row['request_count'] ?? 0;

            if ($request_count > 0) {
                $requestedCount++;
            } else {
                $notRequestedCount++;
            }

            if (empty($expired)) {
                $status = "unknown";
                $color = "secondary";
                $sisa_hari = null;
            } else {
                $diff = floor((strtotime($expired) - strtotime($today)) / 86400);
                $sisa_hari = $diff;

                if ($diff < 0) {
                    $status = "expired";
                    $color = "danger";
                    $expiredCount++;
                } elseif ($diff <= 60 && $diff >= 0) {
                    $status = "expiring";
                    $color = "warning";
                    $expiringCount++;
                    if ($request_count == 0) {
                        $expiringProducts[] = $row;
                    }
                } else {
                    $status = "active";
                    $color = "success";
                    $activeCount++;
                }
            }

            $row['status'] = $status;
            $row['color'] = $color;
            $row['sisa_hari'] = $sisa_hari;
            $data[] = $row;
        }

        $products = $data;
        $totalProducts = count($products);

        if (!empty($expiringProducts) || $expiredCount > 0) {

            $tanggalHariIni = date("Y-m-d");
            $logFile = __DIR__ . '/../cron/last_email_sent.txt';

            $terakhirKirim = file_exists($logFile) ? trim(file_get_contents($logFile)) : '';

            if ($terakhirKirim !== $tanggalHariIni) {

                file_put_contents($logFile, $tanggalHariIni);

                ob_start();
                include __DIR__ . "/../cron/email_reminder.php";
                ob_end_clean();
                clearstatcache();
            }
        }

        include __DIR__ . "/../views/products/index.php";
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name    = trim($_POST['product_name'] ?? '');
            $serial  = trim($_POST['serial_number'] ?? '');
            $expired = $_POST['expired_date'] ?? '';
            $harga   = $_POST['harga_renewal'] ?? '';

            header('Content-Type: application/json');

            if ($this->model->isSerialNumberExists($serial)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Serial number sudah terdaftar, gunakan serial number lain"
                ]);
                exit;
            }

            $success = $this->model->create($name, $serial, $expired, $harga);

            if ($success) {
                $today = new DateTime();
                $expDate = new DateTime($expired);
                $diff = $today->diff($expDate)->format("%r%a");

                if ($diff <= 60) {
                    ob_start();
                    include __DIR__ . "/../cron/email_reminder.php";
                    ob_end_clean();
                }
            }

            echo json_encode([
                "status"  => $success ? "success" : "error",
                "message" => $success ? "Selamat data berhasil disimpan" : "Data gagal disimpan"
            ]);
            exit;
        }
    }

    public function update($id)
    {
        $product = $this->model->getById($id);

        if (!$product) {
            header('Content-Type: application/json');
            echo json_encode([
                "status"  => "error",
                "message" => "Product tidak ditemukan"
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_status = $_POST['payment_status'] ?? null;

            if ($payment_status === 'done') {
                $payment_date = $_POST['payment_date'] ?? null;

                if (empty($payment_date)) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        "status" => "error",
                        "message" => "Tanggal pembayaran wajib diisi"
                    ]);
                    exit;
                }

                if ($this->paymentModel->isPaymentExists($id, $payment_date)) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        "status" => "error",
                        "message" => "Pembayaran untuk produk ini pada tanggal tersebut sudah ada. Gunakan tanggal lain."
                    ]);
                    exit;
                }

                $success = $this->model->updatePayment($id, $payment_date);

                header('Content-Type: application/json');
                echo json_encode([
                    "status"  => $success ? "success" : "error",
                    "message" => $success ? "Payment berhasil disimpan" : "Gagal menyimpan payment"
                ]);
                exit;
            } else {
                $name    = trim($_POST['product_name'] ?? '');
                $serial  = trim($_POST['serial_number'] ?? '');
                $expired = $_POST['expired_date'] ?? '';
                $harga   = $_POST['harga_renewal'] ?? '';

                if ($this->model->isSerialNumberExistsForOther($serial, $id)) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        "status" => "error",
                        "message" => "Serial number sudah dipakai product lain"
                    ]);
                    exit;
                }

                $success = $this->model->update($id, $name, $serial, $expired, $harga);

                header('Content-Type: application/json');
                echo json_encode([
                    "status"  => $success ? "success" : "error",
                    "message" => $success ? "Data berhasil diupdate" : "Update gagal"
                ]);
                exit;
            }
        }
    }

    public function history()
    {
        $result = $this->paymentModel->getGroupedHistory();
        $histories = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $histories[] = $row;
        }

        include __DIR__ . "/../views/products/history.php";
    }

    public function historyDetail()
    {
        header('Content-Type: application/json');

        $product_id = $_GET['product_id'] ?? null;

        if (!$product_id) {
            echo json_encode([
                "status" => "error",
                "message" => "Product ID tidak ditemukan"
            ]);
            exit;
        }

        $result = $this->paymentModel->getPaymentDetailsByProduct($product_id);
        $details = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $details[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $details
        ]);
        exit;
    }

    public function historyPdf()
    {
        require_once __DIR__ . '/../vendor/autoload.php';

        $product_id = $_GET['product_id'] ?? null;

        $dompdf = new \Dompdf\Dompdf();

        if ($product_id) {
            $product = $this->model->getById($product_id);

            if (!$product) {
                die("Product tidak ditemukan");
            }

            $result = $this->paymentModel->getPaymentDetailsByProduct($product_id);
            $details = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $details[] = $row;
            }

            ob_start();
            include __DIR__ . '/../views/products/history_pdf_single.php';
            $html = ob_get_clean();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("riwayat-pembayaran-" . $product['product_name'] . ".pdf", ["Attachment" => false]);
            exit;
        } else {
            $histories = $this->paymentModel->getAllGroupedHistoryWithDetails();

            ob_start();
            include __DIR__ . '/../views/products/history_pdf_all.php';
            $html = ob_get_clean();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream("riwayat-pembayaran-semua.pdf", ["Attachment" => false]);
            exit;
        }
    }

    public function exportExcel()
    {
        if (ob_get_contents()) ob_end_clean();
        $startDate = $_GET['start_date'] ?? null;
        $endDate   = $_GET['end_date'] ?? null;
        $result = $this->model->getProductsByFilter($startDate, $endDate);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No', 'Product', 'Serial Number', 'Expired Date', 'Last Quotation'];
        $column = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($column . '1', $h);
            $column++;
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4AA3FF'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $rowNum = 2;
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $row['product_name']);
            $sheet->setCellValueExplicit('C' . $rowNum, $row['serial_number'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $rowNum, $row['expired_date']);
            $sheet->setCellValue('E' . $rowNum, $row['harga_renewal']);

            $sheet->getStyle('A' . $rowNum . ':E' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $rowNum++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Export_renewal-system_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function importExcel()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                if (count($sheetData) <= 1) {
                    echo json_encode(["status" => "error", "message" => "File kosong atau hanya berisi header"]);
                    exit;
                }

                $headerRow = $sheetData[1];
                $columnMapping = ['name' => null, 'serial' => null, 'expired' => null, 'harga' => null];

                foreach ($headerRow as $columnIndex => $headerValue) {
                    $headerValue = strtolower(trim($headerValue));
                    if (strpos($headerValue, 'product') !== false || strpos($headerValue, 'nama') !== false) $columnMapping['name'] = $columnIndex;
                    elseif (strpos($headerValue, 'serial') !== false || strpos($headerValue, 'sn') !== false) $columnMapping['serial'] = $columnIndex;
                    elseif (strpos($headerValue, 'expired') !== false || strpos($headerValue, 'date') !== false) $columnMapping['expired'] = $columnIndex;
                    elseif (strpos($headerValue, 'harga') !== false || strpos($headerValue, 'quotation') !== false) $columnMapping['harga'] = $columnIndex;
                }

                if (!$columnMapping['name'] || !$columnMapping['serial']) {
                    echo json_encode(["status" => "error", "message" => "Kolom 'Nama Produk' atau 'Serial Number' tidak ditemukan."]);
                    exit;
                }

                $successCount = 0;
                $skipCount = 0;
                $processedSerials = [];

                foreach ($sheetData as $index => $row) {
                    if ($index == 1) continue;

                    $name = trim($row[$columnMapping['name']] ?? '');
                    $serial = trim($row[$columnMapping['serial']] ?? '');

                    if (empty($name) || empty($serial)) {
                        $skipCount++;
                        continue;
                    }

                    if (in_array($serial, $processedSerials)) {
                        $skipCount++;
                        continue;
                    }

                    if ($this->model->isSerialNumberExists($serial)) {
                        $skipCount++;
                        continue;
                    }

                    $rawDate = $row[$columnMapping['expired']] ?? null;
                    if (is_numeric($rawDate)) {
                        $expired = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                    } else {
                        $expired = !empty($rawDate) ? date('Y-m-d', strtotime($rawDate)) : date('Y-m-d');
                    }

                    $rawHarga = $row[$columnMapping['harga']] ?? 0;
                    $harga = (int)preg_replace('/[^0-9]/', '', $rawHarga);

                    $res = $this->model->create($name, $serial, $expired, $harga);
                    if ($res) {
                        $successCount++;
                        $processedSerials[] = $serial;
                    }
                }

                echo json_encode([
                    "status" => "success",
                    "message" => "Import Selesai!\n- Berhasil: $successCount\n- Dilewati: $skipCount (Duplikat/Kosong)"
                ]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Terjadi kesalahan sistem: " . $e->getMessage()]);
            }
            exit;
        }
    }

    public function delete($id)
    {
        $this->model->delete($id);
        header("Location: index.php");
        exit;
    }
}
