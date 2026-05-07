<?php

require_once __DIR__ . "/../config/koneksi.php";

class UserModel
{

    private $db;
    private $table = "users";

    public $id_user;
    public $username;
    public $email;
    public $password;
    public $role;
    public $departemen;

    public function __construct($koneksi)   
    {
        $this->db = $koneksi;
    }

    public function login (){
        $query= " SELECT id_user, username, email, role, departemen password FROM " . $this->table . "WHERE username=? OR email=?"
                . $this->table . " WHERE username=? OR email=?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $this->username, $this->username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            if(password_verify($this->password, $row['password'])){
                $this->id_user = $row['id_user'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                $this->departemen = $row['departemen'];
                return true;
            }
        }
        return false;
    }

     public function updateLastActivity($id_user) {
        $query = "UPDATE users SET last_activity = NOW() WHERE id_user = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
    }

    public function getAllUsers() {
        $query = "SELECT id_user, username, email, role, departemen, last_activity FROM " . $this->table . " ORDER BY last_activity DESC";
        return $this->db->query($query);
    }

    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id_user = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createUser($username, $email, $password, $dept, $role) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, departemen, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssss", $username, $email, $hashed, $dept, $role);
        return $stmt->execute();
    }
    
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id_user = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function updatePassword($identifier, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $query = "UPDATE " . $this->table . " SET password = ? WHERE username = ? OR email = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sss", $hashedPassword, $identifier, $identifier);

        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        }
        return false;
    }
}

?>