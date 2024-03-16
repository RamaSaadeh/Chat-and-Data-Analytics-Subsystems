<?php
$commentID = $_GET["commentid"];
$postID = $_GET["postid"];
require_once("../../includes/dbh.inc.php");
//first set pinned = false for all comments in that post
$sql = "UPDATE commentDetails SET commentPinned = 0 WHERE postID = '$postID'";
$query_run = mysqli_query($conn, $sql);
// set commendID as pinned
$sql = "UPDATE commentDetails SET commentPinned = '1' WHERE commentID = $commentID";
$query_run = mysqli_query($conn, $sql);
