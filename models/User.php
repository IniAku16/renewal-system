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

    public function __construct($koneksi)   
    {
        $this->db = $koneksi;
    }

    public function login (){
        $query= " SELECT id_user, username, email, password FROM "
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

                return true;
            }
        }
        return false;
    }

    public function register (){
        $query = "INSERT INTO users (username, email, password) VALUES(?,?,?)";

        $stmt = $this->db->prepare($query);

        $stmt->bind_param("sss", 
        $this-> username,
        $this-> email,
        $this-> password);

        if($stmt->execute()){
            return true;
        }
        return false;
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