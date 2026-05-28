<?php
//Klasė Encryptor, skirta saugiam duomenų užšifravimui ir dešifravimui
class Encryptor {
    
    //Privati konstanta, nurodanti naudojamą šifravimo algoritmą
    private const CIPHER = 'aes-256-cbc';

    //Viešas statinis metodas, skirtas duomenų užšifravimui
    public static function encrypt($data, $key) {
        
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER));
        
        //Paverčia vartotojo raktą į fiksuoto 256 bitų ilgio dvejetainį formatą naudojant SHA-256
        $hashKey = hash('sha256', $key, true); 
        
        //Užšifruoja tekstą ($data) naudojant algoritmą, sugeneruotą raktą ir atsitiktinį IV
        $encrypted = openssl_encrypt($data, self::CIPHER, $hashKey, 0, $iv);
       
        //Sujungia IV su užšifruotu tekstu ir viską konvertuoja į base64 tekstą
        return base64_encode($iv . $encrypted);
        
    }

    //Viešas statinis metodas, skirtas užšifruotų duomenų atstatymui
    public static function decrypt($data, $key) {
        
        //Atkoduoja base64 tekstą atgal į pirminį dvejetainį formatą
        $data = base64_decode($data);
        
        //Sužino, kokio ilgio buvo inicializacijos vektorius (IV)
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        
        //Iškerpa pačią pirmąją duomenų dalį, kurioje buvo paslėptas atsitiktinis IV
        $iv = substr($data, 0, $ivLength);
        
        //Iškerpa likusią duomenų dalį, kuri yra tikrasis užšifruotas tekstas
        $encrypted = substr($data, $ivLength);
        
        //Vėl sugeneruoja tą patį 256 bitų dvejetainį raktą iš vartotojo slaptažodžio, kad galėtų atsirakinti
        $hashKey = hash('sha256', $key, true);
        
        //Dešifruoja tekstą naudodamas iškirptą šifrą, sugeneruotą raktą bei surastą IV ir grąžina švarų tekstą
        return openssl_decrypt($encrypted, self::CIPHER, $hashKey, 0, $iv);
        
    }
    
}
?>