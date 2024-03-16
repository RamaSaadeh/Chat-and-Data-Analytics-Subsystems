<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// store values from GET method
$id = $_GET["id"];
$name = $_GET["name"];
$description = $_GET["desc"];
$date = $_GET["date"];
$leaderID = $_GET["leader"];

// Replace __ with spaces
$name = str_replace('__', ' ', $name);
$description = str_replace('__', ' ', $description);

require_once("../includes/dbh.inc.php");
require_once("../includes/functions.inc.php");

// Update project tables based on given variables
updateProject($conn, $name, $description, $date, $id, $leaderID);
