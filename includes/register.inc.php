<?php

// error_reporting(E_ALL); ini_set('display_errors', 1);

if (isset($_POST["submit"])) {

    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_confirmation = $_POST["confirmPassword"];

    require_once("dbh.inc.php");
    require_once("functions.inc.php");

    if (emailExists($conn, $email)) {
        header("location: ../register.php?error=emailExistsAlready");
        exit();
    }

    createUser($conn, $name, $surname, $email, $password);


} else {
    header("location: ../register.php?error");
}

?>