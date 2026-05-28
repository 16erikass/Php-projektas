<?php
session_start();

require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/PasswordGenerator.php';
require_once 'classes/PasswordManager.php';

//Sukuria duomenų bazės objektą ir pasiima aktyvų ryšį
$database = new Database();
$db = $database->getConnection();

//Sukuria vartotojo valdymo objektą, perduodant DB ryšį
$userClass = new User($db);

$message = "";

//Patikrina, ar buvo paspaustas registracijos mygtukas
if (isset($_POST['register'])) {
    //Užregistruoja naują vartotoją su įvestais duomenimis
    $userClass->register($_POST['username'], $_POST['password']);
    $message = "Registracija sėkminga! Prisijunkite.";
}

//Patikrina, ar buvo paspaustas prisijungimo mygtukas
if (isset($_POST['login'])) {
    //Tikrina duomenis ir bando prijungti vartotoją
    $userData = $userClass->login($_POST['username'], $_POST['password']);
    if ($userData) {
        //Sėkmės atveju, sesijoje išsaugo vartotojo ID ir jo atšifruotą pagrindinį raktą
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['master_key'] = $userData['master_key'];
        //Atgaivina puslapį, kad suveiktų prisijungusio būsena
        header("Location: index.php");
        exit;
    } else {
        $message = "Blogi prisijungimo duomenys.";
    }
}

//Patikrina, ar nuorodoje buvo paspausta „Atsijungti“ (logout=1)
if (isset($_GET['logout'])) {
    //Sunaikina visus sesijos duomenis serveryje
    session_destroy();
    //Nukreipia atgal į švarų pradinį puslapį
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Slaptažodžių generatorius</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 20px;">

    <?php //Jei kintamasis „message“ nėra tuščias, išveda mėlyną pranešimą ekrane
    if (!empty($message)) echo "<p style='color:blue'><b>$message</b></p>"; ?>

    <?php
    //Patikrina, ar vartotojas šiuo metu yra sėkmingai prisijungęs
    if (isset($_SESSION['user_id'])) {
        //Sukuria slaptažodžių valdymo objektą šiam konkrečiam vartotojui
        $passManager = new PasswordManager($db, $_SESSION['user_id'], $_SESSION['master_key']);
        $generatedPassword = "";

        //Patikrina, ar vartotojas paspaudė „Saugoti į DB“
        if (isset($_POST['save_password'])) {
            //Užšifruoja ir išsaugo naują slaptažodį duomenų bazėje
            $passManager->addPassword($_POST['title'], $_POST['gen_password']);
            echo "<p style='color:green;'>Slaptažodis sėkmingai išsaugotas!</p>";
        }

        //Patikrina, ar vartotojas paspaudė „Generuoti“
        if (isset($_POST['generate'])) {
            //Sukuria generatoriaus objektą su įvestais simbolių kiekiais
            $generator = new PasswordGenerator($_POST['lc'], $_POST['uc'], $_POST['num'], $_POST['spec']);
            //Sugeneruoja atsitiktinį slaptažodį
            $generatedPassword = $generator->generate();
        }

        //Ištraukia visus išsaugotus ir jau atšifruotus šio vartotojo slaptažodžius iš DB
        $myPasswords = $passManager->getPasswords();

        //Įkelia pagrindinį prisijungusio vartotojo aplinkos (angloje Dashboard) HTML vaizdą
        include 'views/dashboard.php';

    //Jei vartotojas nėra prisijungęs
    } else {
        //Įkelia registracijos ir prisijungimo formų HTML vaizdus
        include 'views/register.php';
        include 'views/login.php';
    }
    ?>

</body>
</html>