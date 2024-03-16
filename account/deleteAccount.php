<?php
session_start(); //start session
if (!isset($_SESSION["valid"])) { //if session isn't valid take back to login page
    header("Location: login.php");
    exit();
}

$id = $_SESSION['userid'];
require_once("../includes/dbh.inc.php");
$sql = "DELETE FROM userDetails WHERE userID='$id';";
$query_run = mysqli_query($conn, $sql);

header("Location: ../login.php?action=accountDeleted");
