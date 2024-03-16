<?php
session_start();
$postID = $_GET["postid"];
require_once("../includes/dbh.inc.php");
$sql = "DELETE FROM posts WHERE postID = '$postID'";
$query_run = mysqli_query($conn, $sql);
$sql = "DELETE FROM postLikeOrDislikeDetails WHERE postID = '$postID'";
$query_run = mysqli_query($conn, $sql);
