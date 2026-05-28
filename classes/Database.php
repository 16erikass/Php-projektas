<?php
//Apibrėžiama klasė Database, kuri bus atsakinga už ryšį su duomenų baze
class Database {
    private $host = "localhost";
    
    private $db_name = "password_manager_db";
    
    private $username = "root";
    
    private $password = "";
    
    //Viešas kintamasis, kuriame bus saugomas aktyvus ryšys (viešas, kad jį matytų kitos klasės)
    public $conn; 

    //Viešas metodas, kurį iškvietus sukuriamas ir grąžinamas DB ryšys
    public function getConnection() {
        
        //Tikrina, ar ryšys dar nebuvo sukurtas
        if (!$this->conn) {
            
            //Sukuria naują PDO objektą ir priskiria jį klasės kintamajam $this->conn
            $this->conn = new PDO(
                //Nurodo DB tipą (mysql), serverį, DB pavadinimą ir UTF-8 koduotę
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4", 
                $this->username, //Perduoda aukščiau nurodytą DB vartotojo vardą
                $this->password, //Perduoda aukščiau nurodytą DB slaptažodį
                
                [
                    //Nurodo PDO automatiškai mesti Exception (klaidą), jei įvyks bet koks SQL sutrikimas
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION 
                ]
            );
            
        }
        
        //Grąžina aktyvų ir paruoštą duomenų bazės ryšį SQL užklausoms vykdyti
        return $this->conn;
        
    }
    
}
?>