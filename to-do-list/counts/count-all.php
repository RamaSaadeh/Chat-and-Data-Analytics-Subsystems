<?php

//starting session
session_start();
if (!isset($_SESSION["valid"])) {
  //redirect if session is not valid
  header("Location: login.php");
  exit();
}

?>

<?php
require_once("../../includes/dbh.inc.php");
require_once("../functions.php");
// printing count for flagged
echo countFlagged($conn) . ",";
echo countAll($conn) . ",";
echo countToday($conn) . ",";
echo countCompleted($conn);
