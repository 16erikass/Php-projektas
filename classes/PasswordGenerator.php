<?php
//Klasė PasswordGenerator, skirta slaptažodžių kūrimui pagal nurodytus kriterijus
class PasswordGenerator {
    //Kintamasis nurodantis, kiek mažųjų raidžių reikės slaptažodyje
    private $lowercaseCount;
    //Kintamasis nurodantis, kiek didžiųjų raidžių reikės slaptažodyje
    private $uppercaseCount;
    //Kintamasis nurodantis, kiek skaičių reikės slaptažodyje
    private $numberCount;
    //Kintamasis nurodantis, kiek specialiųjų simbolių reikės slaptažodyje
    private $specialCount;

    public function __construct($lc, $uc, $num, $spec) {
        //Paverčia į int ir priskiria mažųjų raidžių kintamajam
        $this->lowercaseCount = (int)$lc;
        //Paverčia į int ir priskiria didžiųjų raidžių kintamajam
        $this->uppercaseCount = (int)$uc;
        //Paverčia į int ir priskiria skaičių kintamajam
        $this->numberCount = (int)$num;
        //Paverčia į int ir priskiria specialiųjų simbolių kintamajam
        $this->specialCount = (int)$spec;
    }

    //Metodas, kuris sugeneruoja ir grąžina slaptažodžio tekstą
    public function generate() {
        //Kintamasis, kuriame saugomas visų galimų mažųjų raidžių sąrašas
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        //Kintamasis, kuriame saugomas visų galimų didžiųjų raidžių sąrašas
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //Kintamasis, kuriame saugomas visų galimų skaičių sąrašas
        $numbers = '0123456789';
        //Kintamasis, kuriame saugomas visų galimų specialiųjų simbolių sąrašas
        $specials = '!@#$%^&*()._-=+';

        //tuščias kintamasis, kuriame po truputį bus surenkamas slaptažodis
        $password = '';
        
        for ($i = 0; $i < $this->lowercaseCount; $i++) $password .= $lower[random_int(0, strlen($lower) - 1)];
        for ($i = 0; $i < $this->uppercaseCount; $i++) $password .= $upper[random_int(0, strlen($upper) - 1)];
        for ($i = 0; $i < $this->numberCount; $i++) $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        for ($i = 0; $i < $this->specialCount; $i++) $password .= $specials[random_int(0, strlen($specials) - 1)];

        //Naudojant  funkciją str_shuffle, visi surinkti simboliai atsitiktine tvarka išmaišomi ir grąžinami
        return str_shuffle($password);
    }
    
}
?>