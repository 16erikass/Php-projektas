<?php
require_once 'Encryptor.php';

class PasswordManager {
    private $conn;
    private $userId;
    private $masterKey;

    public function __construct($db, $userId, $masterKey) {
        $this->conn = $db;
        $this->userId = $userId;
        $this->masterKey = $masterKey;
    }

    public function addPassword($title, $plainPassword) {
        //Šifruojama su vartotojo atšifruotu raktu
        $encryptedPass = Encryptor::encrypt($plainPassword, $this->masterKey);
        
        $query = "INSERT INTO passwords (user_id, title, encrypted_password) VALUES (:uid, :title, :enc_pass)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->userId);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":enc_pass", $encryptedPass);
        return $stmt->execute();
    }

    public function getPasswords() {
        $query = "SELECT title, encrypted_password, created_at FROM passwords WHERE user_id = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->userId);
        $stmt->execute();

        $passwords = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['decrypted_password'] = Encryptor::decrypt($row['encrypted_password'], $this->masterKey);
            $passwords[] = $row;
        }
        return $passwords;
    }
}
?>