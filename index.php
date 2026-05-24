<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/PasswordGenerator.php';
require_once 'classes/PasswordManager.php';

$database = new Database();
$db = $database->getConnection();
$userClass = new User($db);

$message = "";

//Registracijos ir Prisijungimo logika
if (isset($_POST['register'])) {
    $userClass->register($_POST['username'], $_POST['password']);
    $message = "Registracija sėkminga! Prisijunkite.";
}

if (isset($_POST['login'])) {
    $userData = $userClass->login($_POST['username'], $_POST['password']);
    if ($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['master_key'] = $userData['master_key'];
        header("Location: index.php");
        exit;
    } else {
        $message = "Blogi prisijungimo duomenys.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
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

    <?php if (!empty($message)) echo "<p style='color:blue'><b>$message</b></p>"; ?>

    <?php
    //jei vartotojas prisijunges
    if (isset($_SESSION['user_id'])) {
        $passManager = new PasswordManager($db, $_SESSION['user_id'], $_SESSION['master_key']);
        $generatedPassword = "";

        //Jei paspaudė "Saugoti į DB"
        if (isset($_POST['save_password'])) {
            $passManager->addPassword($_POST['title'], $_POST['gen_password']);
            echo "<p style='color:green;'>Slaptažodis sėkmingai išsaugotas!</p>";
        }

        //Jei paspaudė "Generuoti"
        if (isset($_POST['generate'])) {
            $generator = new PasswordGenerator($_POST['lc'], $_POST['uc'], $_POST['num'], $_POST['spec']);
            $generatedPassword = $generator->generate();
        }

        //Ištraukiami slaptažodžiai lentelės atvaizdavimui
        $myPasswords = $passManager->getPasswords();

        //itraukiame dashboard html faila
        include 'views/dashboard.php';

    //jei vartotojas neprisijunges
    } else {
        //itraukiame formu html failus
        include 'views/register.php';
        include 'views/login.php';
    }
    ?>

</body>
</html>