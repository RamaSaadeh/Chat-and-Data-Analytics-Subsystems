<?php
$commentID = $_GET["commentid"];
require_once("../../includes/dbh.inc.php");
// set commendID as unpinned
$sql = "UPDATE commentDetails SET commentPinned = '0' WHERE commentID = $commentID";
$query_run = mysqli_query($conn, $sql);
