<?php
session_start();
$postID = $_GET["postid"];
$userID = $_SESSION["userid"];
require_once("../includes/dbh.inc.php");
$sql = "INSERT INTO postFavouriteDetails (postID, userID) VALUES ('$postID', '$userID')";
$query_run = mysqli_query($conn, $sql);
