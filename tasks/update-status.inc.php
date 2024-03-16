<?php

// get the taskID from the header 
$taskID = $_GET["id"];
// get the task status from the header and replace the '__' with ' '
$nextTaskStatus = str_replace('__', ' ', $_GET["status"]);

// require the database and the functions so that they can be used
require_once("../includes/dbh.inc.php");
require_once("task-functions.inc.php");

// update the task status
updateTaskStatus($conn, $nextTaskStatus, $taskID);