<?php
require_once 'Encryptor.php';

//Klase User skirta vartotojo registracijai, prisijungimui ir duomenų valdymui
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db; //Priskiria gautą DB ryšį klasės vidiniam kintamajam
    }

    //Metodas naujo vartotojo registracijai
    public function register($username, $plainPassword) {
        //Užhešuoja slaptažodį
        $password_hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        //Sugeneruoja unikalų, atsitiktinį 32 simbolių pagrindinį raktą šio vartotojo duomenims saugoti
        $masterKey = bin2hex(random_bytes(16)); 
        
        //Užšifruoja sugeneruotą pagrindinį raktą, naudodamas vartotojo slaptažodį
        $encrypted_key = Encryptor::encrypt($masterKey, $plainPassword);

        $query = "INSERT INTO " . $this->table_name . " (username, password_hash, encrypted_key) VALUES (:username, :password_hash, :encrypted_key)";
        //Paruošia SQL užklausą
        $stmt = $this->conn->prepare($query);
        //Pririša vartotojo vardą prie SQL kintamojo :username
        $stmt->bindParam(":username", $username);
        //Pririša slaptažodžio hešą prie SQL kintamojo :password_hash
        $stmt->bindParam(":password_hash", $password_hash);
        //Pririša užšifruotą raktą prie SQL kintamojo :encrypted_key
        $stmt->bindParam(":encrypted_key", $encrypted_key);
        
        //Įvykdo užklausą ir grąžina true (jei pavyko) arba false
        return $stmt->execute();
    }

    //Metodas vartotojo prisijungimui tikrinti
    public function login($username, $plainPassword) {
        //SQL užklausa, ieškanti vieno vartotojo pagal jo unikalų vardą
        $query = "SELECT id, password_hash, encrypted_key FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        //Patikrina, ar duomenų bazėje buvo rastas toks vartotojas
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //Patikrina, ar įvestas slaptažodis sutampa su DB esančiu hešu
            if (password_verify($plainPassword, $row['password_hash'])) {
                //Sėkmės atveju, įvestu slaptažodžiu atšifruoja vartotojo pagrindinį raktą
                $masterKey = Encryptor::decrypt($row['encrypted_key'], $plainPassword);
                //Grąžina sėkmingo prisijungimo duomenis: ID ir veikiantį pagrindinį raktą
                return [
                    'id' => $row['id'],
                    'master_key' => $masterKey
                ];
            }
        }
        //Grąžina false, jei vartotojas nerastas arba slaptažodis neteisingas
        return false;
    }

    //Metodas vartotojo slaptažodžio keitimui
    public function changePassword($userId, $oldPlainPassword, $newPlainPassword) {
        $query = "SELECT password_hash, encrypted_key FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //Patikrina, ar pateiktas senasis slaptažodis yra teisingas
        if (password_verify($oldPlainPassword, $row['password_hash'])) {
            //Su senuoju slaptažodžiu laikinai atšifruoja pagrindinį raktą
            $masterKey = Encryptor::decrypt($row['encrypted_key'], $oldPlainPassword);
            //Iš naujo užšifruoja tą patį raktą, tik šįkart su naujuoju slaptažodžiu
            $newEncryptedKey = Encryptor::encrypt($masterKey, $newPlainPassword);
            //Sukuria naujojo slaptažodžio hešą saugojimui DB
            $newPasswordHash = password_hash($newPlainPassword, PASSWORD_DEFAULT);

            //SQL užklausa vartotojo duomenų atnaujinimui lentelėje
            $updateQuery = "UPDATE " . $this->table_name . " SET password_hash = :hash, encrypted_key = :enc_key WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":hash", $newPasswordHash);
            $updateStmt->bindParam(":enc_key", $newEncryptedKey);
            $updateStmt->bindParam(":id", $userId);
            //Įvykdo atnaujinimą ir grąžina true/false
            return $updateStmt->execute();
        }
        //Grąžina false, jei senasis slaptažodis buvo neteisingas
        return false;
    }
}
?>