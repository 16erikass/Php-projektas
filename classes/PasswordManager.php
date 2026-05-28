<?php
require_once 'Encryptor.php';

//Klasė PasswordManager skirta slaptažodžių valdymui
class PasswordManager {
    //Kintamasis duomenų bazės ryšiui saugoti
    private $conn;
    //Kintamasis prisijungusio vartotojo ID saugoti
    private $userId;
    //Kintamasis pagrindiniam šifravimo raktui saugoti
    private $masterKey;

    public function __construct($db, $userId, $masterKey) {
        $this->conn = $db; //Priskiria DB ryšį klasės kintamajam
        $this->userId = $userId; //Priskiria vartotojo ID klasės kintamajam
        $this->masterKey = $masterKey; //Priskiria pagrindinį raktą klasės kintamajam
    }

    //Metodas naujam slaptažodžiui užšifruoti ir įrašyti į DB
    public function addPassword($title, $plainPassword) {
        //Užšifruoja atvirą slaptažodį naudojant pagrindinį raktą
        $encryptedPass = Encryptor::encrypt($plainPassword, $this->masterKey);
        
        //SQL užklausa duomenų įrašymui
        $query = "INSERT INTO passwords (user_id, title, encrypted_password) VALUES (:uid, :title, :enc_pass)";
        //Paruošia SQL užklausą
        $stmt = $this->conn->prepare($query);
        //Pririša vartotojo ID prie užklausos kintamojo :uid
        $stmt->bindParam(":uid", $this->userId);
        //pririša pavadinimą prie užklausos kintamojo :title
        $stmt->bindParam(":title", $title);
        //pririša užšifruotą slaptažodį prie užklausos kintamojo :enc_pass
        $stmt->bindParam(":enc_pass", $encryptedPass);
        //Įvykdo užklausą ir grąžina true (jei pavyko) arba false
        return $stmt->execute();
    }

    //Metodas visų vartotojo slaptažodžių paėmimui ir atšifravimui
    public function getPasswords() {
        $query = "SELECT title, encrypted_password, created_at FROM passwords WHERE user_id = :uid";
        //Paruošia SQL užklausą
        $stmt = $this->conn->prepare($query);
        //pririša vartotojo ID
        $stmt->bindParam(":uid", $this->userId);
        //Įvykdo duomenų paėmimo užklausą
        $stmt->execute();

        //Sukuria tuščią masyvą rezultatams kaupti
        $passwords = [];
        //Ciklas veikia, kol iš DB po vieną paimama atrinkta eilutė
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //Atšifruoja slaptažodį ir sukuria naują stulpelį decrypted_password
            $row['decrypted_password'] = Encryptor::decrypt($row['encrypted_password'], $this->masterKey);
            $passwords[] = $row;
        }
        return $passwords;
    }
}
?>