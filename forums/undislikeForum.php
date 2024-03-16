<?php
session_start();
$postID = $_GET["postid"];
$userID = $_SESSION["userid"];
require_once("../includes/dbh.inc.php");
$sql = "DELETE FROM postLikeOrDislikeDetails WHERE postID = '$postID' AND userID = '$userID' ";
$query_run = mysqli_query($conn, $sql);
