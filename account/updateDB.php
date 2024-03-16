<?php
session_start();
$type = $_GET["type"];
$data = $_GET["data"];

require_once("../includes/dbh.inc.php");
$id = $_SESSION['userid'];


if ($type == "username") {
    //chaning session value
    $_SESSION["username"] = $data;
    $sql = "UPDATE userDetails SET userName = '$data' WHERE userID = $id";
    $query_run = mysqli_query($conn, $sql);
} else if ($type == "usersurname") {
    //chaning session value
    $_SESSION["usersurname"] = $data;
    $sql = "UPDATE userDetails SET userSurname = '$data' WHERE userID = $id";
    $query_run = mysqli_query($conn, $sql);
}
