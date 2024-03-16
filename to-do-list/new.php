<?php
require_once("../includes/dbh.inc.php");
require_once("functions.php");


$userid = $_GET['ID'];
$information = modify($_GET['INFORMATION']);
$priority = $_GET['PRIORITY'];
$duedate = $_GET['DUEDATE'];
$notes = modify($_GET['NOTES']);
$flag = $_GET['FLAG'];

addToDo($conn, $information, $priority, $userid, $duedate, $notes, $flag);

function modify($str)
{
    return ucwords(str_replace("_", " ", $str));
}
