<?php
require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/Payment.php";

class ProductModel
{
    private $db;

    public function __construct($koneksi)
    {
        $this->db = $koneksi;
    }

    public function getAllProducts($user_id)
    {
        $sql = "SELECT * FROM products WHERE user_id = ? ORDER BY expired_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getById($id, $user_id)
    {
        $sql = "SELECT * FROM products WHERE id = ? AND user_id= ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($name, $serial, $expired, $harga, $user_id)
    {
        $sql = "INSERT INTO products (product_name, serial_number, expired_date, harga_renewal, user_id) VALUES (?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssii", $name, $serial, $expired, $harga, $user_id);
        return $stmt->execute();
    }

    public function update($id, $name, $serial, $expired, $harga, $user_id)
    {
        $sql = "UPDATE products 
                SET product_name=?, serial_number=?, expired_date=?, harga_renewal=? 
                WHERE id=? AND user_id=?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssiii", $name, $serial, $expired, $harga, $id, $user_id);
        return $stmt->execute();
    }

    public function updatePayment($id, $payment_date, $user_id, $amount)
    {
        $product = $this->getById($id, $user_id);

        if (!$product) return false;

        $current_expired = $product['expired_date'];
        $paymentModel = new PaymentModel($this->db);

        if ($paymentModel->isPaymentExists($id, $payment_date)) {
            return "duplicate_date";
        }

        $this->db->begin_transaction();

        try {
            $paymentSaved = $paymentModel->create($id, $payment_date, $amount, $user_id);

            if (!$paymentSaved) {
                throw new Exception("Gagal menyimpan data pembayaran");
            }

            if (!empty($current_expired)) {
                $new_expired = date('Y-m-d', strtotime('+1 year', strtotime($current_expired)));
            } else {
                $new_expired = date('Y-m-d', strtotime('+1 year', strtotime($payment_date)));
            }

            $sqlProduct = "UPDATE products SET 
                        expired_date = ?, 
                        harga_renewal = ?,
                        request_count = 0 
                      WHERE id = ? AND user_id = ?";
            $stmtProd = $this->db->prepare($sqlProduct);
            $stmtProd->bind_param("siii", $new_expired, $amount, $id, $user_id);
            $productUpdated = $stmtProd->execute();

            if (!$productUpdated) {
                throw new Exception("Gagal mengupdate masa aktif produk");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Payment Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id, $user_id)
    {
        $sql = "DELETE FROM products WHERE id=? AND user_id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }

    public function isSerialNumberExists($serial, $user_id)
    {
        $sql = "SELECT id FROM products WHERE serial_number = ? AND user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $serial, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function isSerialNumberExistsForOther($serial, $id, $user_id)
    {
        $sql = "SELECT id FROM products WHERE serial_number = ? AND id != ? AND user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $serial, $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function getProductsByFilter($user_id, $startDate = null, $endDate = null, $year = null)
    {
        $sql = "SELECT * FROM products WHERE user_id = '$user_id'";

        if (!empty($startDate) && !empty($endDate)) {
            $sql .= " AND expired_date BETWEEN '$startDate' AND '$endDate'";
        }

        if (!empty($year)) {
            $sql .= " AND YEAR(expired_date) = '$year'";
        }

        $sql .= " ORDER BY expired_date ASC";
        return mysqli_query($this->db, $sql);
    }

    public function incrementRequestCount($id)
    {
        $sql = "UPDATE products SET request_count = request_count + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
