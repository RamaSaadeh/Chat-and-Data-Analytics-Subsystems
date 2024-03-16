<?php
session_start();
$postID = $_GET["postid"];
require_once("../includes/dbh.inc.php");
$sql = "UPDATE posts SET postArchived = 0 WHERE postID = '$postID'";
$query_run = mysqli_query($conn, $sql);
