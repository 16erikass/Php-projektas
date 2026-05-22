<?php
class Encryptor {
    private const CIPHER = 'aes-256-cbc';

    public static function encrypt($data, $key) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER));
        $hashKey = hash('sha256', $key, true); // Užtikriname, kad raktas bus 256 bitų
        $encrypted = openssl_encrypt($data, self::CIPHER, $hashKey, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data, $key) {
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        $hashKey = hash('sha256', $key, true);
        return openssl_decrypt($encrypted, self::CIPHER, $hashKey, 0, $iv);
    }
}
?>