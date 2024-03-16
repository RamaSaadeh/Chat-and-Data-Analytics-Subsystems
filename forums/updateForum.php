<?php
session_start();
$userID = $_SESSION["userid"];
$postID = $_GET["postid"];
$postName = modify($_GET["postname"]);
$postDesc = modify($_GET["postdesc"]);


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
$sql = "UPDATE posts SET postName = ?, postDescription = ?, postTags = ? WHERE postID = $postID";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "sss", $postName, $postDesc, $tagString);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

function modify($str)
{
    return (str_replace("_", " ", $str));
}
