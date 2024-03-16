<?php

// get the taskID and hours from the header
$taskID = $_GET["id"];
$hours = $_GET["hours"];

// require the database and functions file so that they can be used
require_once("../includes/dbh.inc.php");
require_once("task-functions.inc.php");

// update the completed hours for the project
updateTaskHours($conn, $taskID, $hours);