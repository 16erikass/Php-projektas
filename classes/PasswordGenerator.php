<?php
class PasswordGenerator {
    private $lowercaseCount;
    private $uppercaseCount;
    private $numberCount;
    private $specialCount;

    public function __construct($lc, $uc, $num, $spec) {
        $this->lowercaseCount = (int)$lc;
        $this->uppercaseCount = (int)$uc;
        $this->numberCount = (int)$num;
        $this->specialCount = (int)$spec;
    }

    public function generate() {
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specials = '!@#$%^&*()._-=+';

        $password = '';
        
        for ($i = 0; $i < $this->lowercaseCount; $i++) $password .= $lower[random_int(0, strlen($lower) - 1)];
        for ($i = 0; $i < $this->uppercaseCount; $i++) $password .= $upper[random_int(0, strlen($upper) - 1)];
        for ($i = 0; $i < $this->numberCount; $i++) $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        for ($i = 0; $i < $this->specialCount; $i++) $password .= $specials[random_int(0, strlen($specials) - 1)];

        //Išmaišomi simboliai, kad tvarka nebūtų nuspėjama
        return str_shuffle($password);
    }
}
?>