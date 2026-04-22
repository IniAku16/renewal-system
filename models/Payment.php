<?php

class PaymentModel
{
    private $db;

    public function __construct($koneksi)
    {
        $this->db = $koneksi;
    }

    public function create($product_id, $payment_date, $amount)
    {
        $sql = "INSERT INTO payments (product_id, payment_date, amount) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isi", $product_id, $payment_date, $amount);
        return $stmt->execute();
    }

    public function getByProduct($product_id)
    {
        $sql = "SELECT * FROM payments WHERE product_id=? ORDER BY payment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getGroupedHistory()
    {
        $sql = "SELECT 
                p.id AS product_id,
                p.product_name,
                p.serial_number,
                COUNT(py.id) AS total_transaksi,
                SUM(py.amount) AS total_amount,
                MAX(py.payment_date) AS last_payment_date
            FROM products p
            INNER JOIN payments py ON p.id = py.product_id
            GROUP BY p.id, p.product_name, p.serial_number
            ORDER BY last_payment_date DESC, p.product_name ASC";

        return mysqli_query($this->db, $sql);
    }

    public function getPaymentDetailsByProduct($product_id)
    {
        $sql = "SELECT id, payment_date, amount
            FROM payments
            WHERE product_id = ?
            ORDER BY payment_date DESC, id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAllGroupedHistoryWithDetails()
    {
        $sqlProducts = "SELECT 
                        p.id AS product_id,
                        p.product_name,
                        p.serial_number,
                        COUNT(py.id) AS total_transaksi,
                        SUM(py.amount) AS total_amount,
                        MAX(py.payment_date) AS last_payment_date
                    FROM products p
                    INNER JOIN payments py ON p.id = py.product_id
                    GROUP BY p.id, p.product_name, p.serial_number
                    ORDER BY p.product_name ASC";

        $resultProducts = mysqli_query($this->db, $sqlProducts);
        $data = [];

        while ($product = mysqli_fetch_assoc($resultProducts)) {
            $productId = $product['product_id'];

            $sqlDetails = "SELECT payment_date, amount
                       FROM payments
                       WHERE product_id = ?
                       ORDER BY payment_date ASC, id ASC";

            $stmt = $this->db->prepare($sqlDetails);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $resultDetails = $stmt->get_result();

            $details = [];
            while ($detail = mysqli_fetch_assoc($resultDetails)) {
                $details[] = $detail;
            }

            $product['details'] = $details;
            $data[] = $product;
        }

        return $data;
    }

    public function isPaymentExists($product_id, $payment_date)
    {
        $sql = "SELECT COUNT(*) as total FROM payments WHERE product_id = ? AND payment_date = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $product_id, $payment_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }
}
