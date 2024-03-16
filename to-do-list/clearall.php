<?php
require_once("../includes/dbh.inc.php");
$sql = "DELETE FROM todoList WHERE todoCompletion = 1";
$query_run = mysqli_query($conn, $sql);
