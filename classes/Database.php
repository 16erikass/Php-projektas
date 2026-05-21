<?php
class Database {
    private $host = "localhost";
    private $db_name = "password_manager_db";
    private $username = "root"; // Pakeiskite į savo DB vartotoją
    private $password = "";     // Pakeiskite į savo DB slaptažodį
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Prisijungimo klaida: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>