<?php
session_start();
$password = $_GET["password"];

require_once("../includes/dbh.inc.php");
$id = $_SESSION['userid'];

//chaning session value
$_SESSION["password"] = $password;
$password_hashed = password_hash($password, PASSWORD_DEFAULT);
$sql = "UPDATE userDetails SET userPword = '$password_hashed' WHERE userID = $id";
$query_run = mysqli_query($conn, $sql);
