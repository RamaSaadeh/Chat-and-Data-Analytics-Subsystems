<?php

require_once("../includes/dbh.inc.php");
$id = $_GET['ID'];
$section = $_GET['SECTION'];
$sql = "UPDATE todoList SET todoCompletion = '1' WHERE todoID = $id";
$query_run = mysqli_query($conn, $sql);
