<?php
session_start();
$commentID = $_GET["commentid"];
$userID = $_SESSION["userid"];
require_once("../../includes/dbh.inc.php");
$sql = "INSERT INTO commentLikeOrDislikeDetails (commentID, userID, commentLOD) VALUES ('$commentID', '$userID', '1')";
$query_run = mysqli_query($conn, $sql);
