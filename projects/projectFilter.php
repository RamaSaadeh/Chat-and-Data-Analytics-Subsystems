<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();


require_once("../includes/dbh.inc.php");
require_once("../includes/functions.inc.php");

// store value of sort from GET method
$sort = $_GET["sort"];
$sortDate = ", projectDetails.projectDate DESC";

// check value of sort in order to sort by the given sort value and another criteria
if ($sort == "`projectDate`ASC") {
    // date ascending and ID ascending
    $sort = "`projectDate` ASC, `projectID` ASC";
} elseif ($sort == "`projectDate`DESC") {
    // date descending and ID ascending
    $sort = "`projectDate` DESC, `projectID` ASC";
} elseif ($sort == "`projectProgress`ASC") {
    // progress ascending and date descending
    $sort = "`projectProgress` ASC" . $sortDate;
} elseif ($sort == "`projectProgress`DESC") {
    // progress descending and date descending
    $sort = "`projectProgress` DESC" . $sortDate;
}

// store value of date range from GET method
$dateFrom = $_GET["dateFrom"];
$dateTo = $_GET["dateTo"];

// store value of progress range from GET method
$progressFrom = $_GET["progressFrom"];
$progressTo = $_GET["progressTo"];

// store value of whether to show completed projects from GET method
$completed = $_GET["completed"];
$completedShown = "";

// Check if completed variable is true or false to show completed or not
if ($completed == "true") {
    $completedShown = "projectDetails.projectProgress = 100 AND";
} elseif ($completed == "false") {
    $completedShown = "projectDetails.projectProgress < 100 AND";
}


$userShown = "";

// check if the value of users in the GET method is 'empty' and is so make no change to the filtering for users
if ($_GET['users'] == "Empty") {

    $usersFormat = "in (userProject.userID)";
} else {

    // Store value of users from GET method
    $users = $_GET["users"];

    // Check if user picked all or not
    if ($users == "All") {
        // every user picked
        $usersFormat = "not in ('')";
    } else {
        // Specific users picked
        $usersFormat = "in ($users)";
    }

    // Show users based on userProject table
    $userShown = "userProject.projectID = projectDetails.projectID AND";
}


// Check if role is 'manager or not'
if ($_SESSION['role'] === 'manager') {
    $userID = $_SESSION['userid'];

    // Filter based on choices
    $sql = "SELECT DISTINCT projectDetails.projectID, projectDetails.projectLeaderID, projectDetails.projectName, projectDetails.projectDesc, projectDetails.projectDate, projectDetails.projectProgress, DATE_FORMAT(projectDetails.lastEdited, '%d-%m-%Y %T') as lastEditedFormat
        FROM projectDetails, userProject 
        WHERE projectDetails.projectDate BETWEEN '$dateFrom' AND '$dateTo' AND
        $completedShown
        projectDetails.projectProgress BETWEEN '$progressFrom' AND '$progressTo' AND
        $userShown
        projectDetails.projectLeaderID $usersFormat 
        ORDER BY $sort";
} else if ($_SESSION['role'] !== 'manager') {

    $userID = $_SESSION['userid'];

    // Filter based on choices
    $sql = "SELECT DISTINCT projectDetails.projectID, projectDetails.projectLeaderID, projectDetails.projectName, projectDetails.projectDesc, projectDetails.projectDate, projectDetails.projectProgress, DATE_FORMAT(projectDetails.lastEdited, '%d-%m-%Y %T') as lastEditedFormat
    FROM userProject, projectDetails, taskUser, taskProject
    WHERE taskUser.userID = $userID AND
        taskProject.projectID = projectDetails.projectID AND
        taskProject.taskID = taskUser.taskID AND
        userProject.projectID = projectDetails.projectID AND 
        projectDetails.projectDate BETWEEN '$dateFrom' AND '$dateTo' AND
            $completedShown
            projectDetails.projectProgress BETWEEN '$progressFrom' AND '$progressTo' AND
            $userShown
            projectDetails.projectLeaderID $usersFormat 
    ORDER BY $sort";
}


// Let 'display-projkects.inc.php' know that custom sql is being used
$_SESSION['filter'] = 1;
$_SESSION['sql'] = $sql;

require_once("display-projects.inc.php");
