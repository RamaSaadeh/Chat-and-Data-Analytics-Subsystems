<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include_once("../includes/dbh.inc.php");
include_once("../includes/functions.inc.php");


// Check if the filter option wasn't chosen
if ($_SESSION['filter'] == 0) {

    // check if individual is a manager or not
    if ($_SESSION['role'] === 'manager') {
        // SQL for manager's view
        $sql = "SELECT *, DATE_FORMAT(lastEdited, '%d-%m-%Y %T') as lastEditedFormat FROM projectDetails WHERE projectProgress < 100 ORDER BY projectDate ASC, projectID ASC";
    } else if ($_SESSION['role'] !== 'manager') {

        $userID = $_SESSION['userid'];

        // SQL for everyone else's view
        $sql = "SELECT projectDetails.projectID, projectLeaderID, projectName, projectDesc, projectDate, projectProgress, DATE_FORMAT(lastEdited, '%d-%m-%Y %T') as lastEditedFormat 
        FROM userProject, projectDetails 
        WHERE userProject.userID = $userID
        AND userProject.projectID = projectDetails.projectID AND projectDetails.projectProgress < 100 
        ORDER BY projectDetails.projectDate ASC, projectDetails.projectID ASC;";
    }
} elseif ($_SESSION['filter'] == 1) {

    // SQL is gotten from the session for filtering purposes
    $sql = $_SESSION['sql'];
}

// get results from the query
$result = $conn->query($sql);

// Load the individual projects
include_once("projectItem.php");

