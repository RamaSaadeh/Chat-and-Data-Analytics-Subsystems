<?php
require_once("../includes/dbh.inc.php");
$id = $_GET['ID'];
$sql = "DELETE FROM todoList WHERE todoID = $id";
$query_run = mysqli_query($conn, $sql);
