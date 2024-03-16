<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$userID = $_SESSION["userid"];
$postName = (modify($_GET["postname"]));
$postDesc = (modify($_GET["postdesc"]));
$commentDateAndTime =  date('Y-m-d H:i:s');

//get all existing tags 
$tagArray = explode("_", $_GET["posttags"]);
foreach ($tagArray as $key => $value) {
    $tagArray[$key] = ucfirst($value);
    if ($value == "") {
        unset($tagArray[$key]);
    }
}
$tagArray = array_unique($tagArray);
$tagString = str_replace(' ', '', implode(", ", $tagArray));


require_once("../includes/dbh.inc.php");
$sql = "INSERT INTO posts (postName, postDescription, authorID, postDateTime, postTags) VALUES (?, ?, ?, ?, ?);";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $postName, $postDesc, $userID, $commentDateAndTime, $tagString);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

function modify($str)
{
    return (str_replace("_", " ", $str));
}
