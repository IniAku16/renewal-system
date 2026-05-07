<?php

class PaymentModel
{
    private $db;

    public function __construct($koneksi)
    {
        $this->db = $koneksi;
    }

    public function create($product_id, $payment_date, $amount, $user_id)
    {
        $sql = "INSERT INTO payments (product_id, payment_date, amount, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isii", $product_id, $payment_date, $amount, $user_id);
        return $stmt->execute();
    }

    public function getGroupedHistory($user_id)
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
            WHERE p.user_id = ?
            GROUP BY p.id, p.product_name, p.serial_number
            ORDER BY last_payment_date DESC, p.product_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getPaymentDetailsByProduct($product_id, $user_id)
    {
        $sql = "SELECT py.id, py.payment_date, py.amount
            FROM payments py
            JOIN products p ON py.product_id = p.id
            WHERE py.product_id = ? AND p.user_id = ?
            ORDER BY py.payment_date DESC, py.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $product_id, $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAllGroupedHistoryWithDetails($user_id)
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
                    WHERE p.user_id = ?
                    GROUP BY p.id, p.product_name, p.serial_number
                    ORDER BY p.product_name ASC";

        $stmt = $this->db->prepare($sqlProducts);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $resultProducts = $stmt->get_result();

        $data = [];
        while ($product = $resultProducts->fetch_assoc()) {
            $productId = $product['product_id'];

            $sqlDetails = "SELECT payment_date, amount
                       FROM payments
                       WHERE product_id = ?
                       ORDER BY payment_date ASC, id ASC";

            $stmtDet = $this->db->prepare($sqlDetails);
            $stmtDet->bind_param("i", $productId);
            $stmtDet->execute();
            $resultDetails = $stmtDet->get_result();

            $details = [];
            while ($detail = $resultDetails->fetch_assoc()) {
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
