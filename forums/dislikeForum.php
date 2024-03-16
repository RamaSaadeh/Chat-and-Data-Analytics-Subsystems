<?php
session_start();
$postID = $_GET["postid"];
$userID = $_SESSION["userid"];
require_once("../includes/dbh.inc.php");
$sql = "INSERT INTO postLikeOrDislikeDetails (postID, userID, postLOD) VALUES ('$postID', '$userID', '-1')";
$query_run = mysqli_query($conn, $sql);
