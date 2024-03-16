<?php
session_start();
$commentID = $_GET["commentid"];
$userID = $_SESSION["userid"];
require_once("../../includes/dbh.inc.php");
$sql = "DELETE FROM commentLikeOrDislikeDetails WHERE commentID = '$commentID' AND userID = '$userID' ";
$query_run = mysqli_query($conn, $sql);
