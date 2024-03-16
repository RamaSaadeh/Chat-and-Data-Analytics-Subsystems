<?php
// session_start();
if (isset($_POST["submit"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];

    require_once("dbh.inc.php");
    require_once("functions.inc.php");

    loginUser($conn, $email, $password);
} else {
    header("location: ../login.php?error");
}
