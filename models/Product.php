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

    public function getAllProducts()
    {
        $sql = "SELECT * FROM products ORDER BY expired_date ASC";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($name, $serial, $expired, $harga)
    {
        $sql = "INSERT INTO products (product_name, serial_number, expired_date, harga_renewal) VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssi", $name, $serial, $expired, $harga);
        return $stmt->execute();
    }

    public function update($id, $name, $serial, $expired, $harga)
    {
        $sql = "UPDATE products 
                SET product_name=?, serial_number=?, expired_date=?, harga_renewal=? 
                WHERE id=?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssii", $name, $serial, $expired, $harga, $id);

        return $stmt->execute();
    }

    public function updatePayment($id, $payment_date)
    {

        $product = $this->getById($id);

        if (!$product) {
            return false;
        }

        $expired_lama = $product['expired_date'];
        $amount = $product['harga_renewal'];

        $new_expired = date("Y-m-d", strtotime($expired_lama . " +1 year"));

        $paymentModel = new PaymentModel($this->db);

        $this->db->begin_transaction();

        try {
            $saveHistory = $paymentModel->create($id, $payment_date, $amount);

            if (!$saveHistory) {
                throw new Exception("Gagal simpan histori payment");
            }

            $sql = "UPDATE products 
                    SET payment_status='done', payment_date=?, expired_date=?, request_count=0 
                    WHERE id=?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $payment_date, $new_expired, $id);

            $updateProduct = $stmt->execute();

            if (!$updateProduct) {
                throw new Exception("Gagal update product");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM products WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function isSerialNumberExists($serial)
    {
        $sql = "SELECT id FROM products WHERE serial_number = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $serial);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function isSerialNumberExistsForOther($serial, $id)
    {
        $sql = "SELECT id FROM products WHERE serial_number = ? AND id != ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $serial, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public function getProductsByFilter($startDate = null, $endDate = null, $year = null)
    {
        $sql = "SELECT * FROM products WHERE 1=1";

        if (!empty($startDate) && !empty($endDate)) {
            $sql .= " AND expired_date BETWEEN '$startDate' AND '$endDate'";
        }

        if (!empty($year)) {
            $sql .= " AND YEAR(expired_date) = '$year'";
        }

        $sql .= " ORDER BY expired_date ASC";
        return mysqli_query($this->db, $sql);
    }

    public function incrementRequestCount($id) {
        $sql = "UPDATE products SET request_count = request_count + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
