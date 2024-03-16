<?php
session_start();
$commentID = $_GET["commentid"];
require_once("../../includes/dbh.inc.php");
$sql = "DELETE FROM commentDetails WHERE commentID = '$commentID'";
$query_run = mysqli_query($conn, $sql);
$sql = "DELETE FROM commentLikeOrDislikeDetails WHERE commentID = '$commentID'";
$query_run = mysqli_query($conn, $sql);
