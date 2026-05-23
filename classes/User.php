<?php
require_once 'Encryptor.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $plainPassword) {
        $password_hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        // Sugeneruojamas atsitiktinis 32 simbolių RAKTAS
        $masterKey = bin2hex(random_bytes(16)); 
        
        // Raktas užkoduojamas naudojant vartotojo įvestą PLAIN slaptažodį
        $encrypted_key = Encryptor::encrypt($masterKey, $plainPassword);

        $query = "INSERT INTO " . $this->table_name . " (username, password_hash, encrypted_key) VALUES (:username, :password_hash, :encrypted_key)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password_hash", $password_hash);
        $stmt->bindParam(":encrypted_key", $encrypted_key);
        
        return $stmt->execute();
    }

    public function login($username, $plainPassword) {
        $query = "SELECT id, password_hash, encrypted_key FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($plainPassword, $row['password_hash'])) {
                // Atkoduojame RAKTĄ su plain slaptažodžiu
                $masterKey = Encryptor::decrypt($row['encrypted_key'], $plainPassword);
                return [
                    'id' => $row['id'],
                    'master_key' => $masterKey
                ];
            }
        }
        return false;
    }

    // Slaptažodžio keitimas (Perkoduojamas RAKTAS)
    public function changePassword($userId, $oldPlainPassword, $newPlainPassword) {
        $query = "SELECT password_hash, encrypted_key FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($oldPlainPassword, $row['password_hash'])) {
            $masterKey = Encryptor::decrypt($row['encrypted_key'], $oldPlainPassword);
            $newEncryptedKey = Encryptor::encrypt($masterKey, $newPlainPassword);
            $newPasswordHash = password_hash($newPlainPassword, PASSWORD_DEFAULT);

            $updateQuery = "UPDATE " . $this->table_name . " SET password_hash = :hash, encrypted_key = :enc_key WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":hash", $newPasswordHash);
            $updateStmt->bindParam(":enc_key", $newEncryptedKey);
            $updateStmt->bindParam(":id", $userId);
            return $updateStmt->execute();
        }
        return false;
    }
}
?>