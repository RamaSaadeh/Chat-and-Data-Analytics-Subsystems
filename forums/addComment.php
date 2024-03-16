<?php
session_start();
$postID = $_GET["postid"];
$userID = $_SESSION["userid"];
$commentValue = modify($_GET["commentvalue"]);

$commentDateAndTime =  date('Y-m-d H:i:s');
require_once("../includes/dbh.inc.php");
$sql = "INSERT INTO commentDetails (postID, commentUserID, commentValue, commentDateTime) VALUES (?, ?, ?, ?)";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $postID, $userID, $commentValue, $commentDateAndTime);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

function modify($str)
{
    return (str_replace("_", " ", $str));
}
