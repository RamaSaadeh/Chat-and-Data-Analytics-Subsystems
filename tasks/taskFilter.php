<?php

// start a session
session_start();

// require the database and task functions to allow them to be used in the file
require_once("../includes/dbh.inc.php");
require_once("task-functions.inc.php");

// get the projectID and userID from the session
$projectID = (int) $_SESSION["projectID"];
$userID = $_SESSION['userid'];

// get the sort method from the header
$sort = $_GET["sort"];

// format the sort method ready for the sql query
if ($sort == "`taskDeadline`ASC") {
    $sort = "`taskDeadline` ASC, `taskID` ASC";
} elseif ($sort == "`taskDeadline`DESC") {
    $sort = "`taskDeadline` DESC, `taskID` ASC";
}

// get the date from and date to filters from the header
$dateFrom = $_GET["dateFrom"];
$dateTo = $_GET["dateTo"];

// format the users filter readt for the sql query
$userShown = "";
if ($_GET['users'] == "Empty") {
    $usersFormat = "IN (tu.userID)";
} else {

    $users = $_GET["users"];

    if ($users == "All") {
        $usersFormat = "NOT IN ('')";
    } else {
        $usersFormat = "IN ($users)";
    }

    $userShown = "tu.taskID = td.taskID AND";
}

// get the status filter from the header
$status = $_GET["status"];

// remove the '__' from the string and replace them with ' '
$status = str_replace('__', ' ', $status);

$statusArray = explode(",", $status);

// format the status into the correct format for the sql query
$quotedWordsArray = array_map(function ($word) {
    return '"' . $word . '"';
}, $statusArray);
// Join the words back into a string
$statusFormatQuoted = implode(",", $quotedWordsArray);
$statusFormat = "IN ($statusFormatQuoted)";

// get value from the header
$isTaskPersonal = $_GET["isTaskPersonal"];

//format the personal task section of the query
$searchPersonalTask = "AND tu.userID != $userID";
if (($isTaskPersonal) == "true") {
    $searchPersonalTask = "AND tu.userID = $userID";
}

// sql query if the user is a manager
if ($_SESSION['role'] === 'manager') {
    $sql = "SELECT
    td.taskID,
    td.taskName,
    td.taskDesc,
    td.taskStatus,
    td.reqHours,
    td.completeHours,
    td.taskDeadline,
    tu.userID AS userID,
    CONCAT(ud.userName, ' ',userSurname) AS userName,
    tp.projectID,
    pd.projectName
    FROM
        taskDetails td
    JOIN
        taskUser tu ON td.taskID = tu.taskID $searchPersonalTask
    JOIN
        userDetails ud ON tu.userID = ud.userID
    JOIN
        taskProject tp ON td.taskID = tp.taskID AND tp.projectID = $projectID
    JOIN
        projectDetails pd ON tp.projectID = pd.projectID
    WHERE $userShown tu.userID $usersFormat AND td.taskStatus $statusFormat AND td.taskDeadline BETWEEN '$dateFrom' AND '$dateTo' 
    ORDER BY $sort;";
} 
// sql query for teamleaders if they are the leader of the project
else if ($_SESSION['role'] === 'leader' && checkProjectLeaderID($conn, $userID, $projectID)) {
    $sql = "SELECT
    td.taskID,
    td.taskName,
    td.taskDesc,
    td.taskStatus,
    td.reqHours,
    td.completeHours,
    td.taskDeadline,
    tu.userID AS userID,
    CONCAT(ud.userName, ' ',userSurname) AS userName,
    tp.projectID,
    pd.projectName
    FROM
        taskDetails td
    JOIN
        taskUser tu ON td.taskID = tu.taskID $searchPersonalTask
    JOIN
        userDetails ud ON tu.userID = ud.userID
    JOIN
        taskProject tp ON td.taskID = tp.taskID AND tp.projectID = $projectID
    JOIN
        projectDetails pd ON tp.projectID = pd.projectID
    WHERE $userShown tu.userID $usersFormat AND td.taskStatus $statusFormat AND td.taskDeadline BETWEEN '$dateFrom' AND '$dateTo' 
    ORDER BY $sort;";
} 
// sql query for teamleaders if they are not the leader of the project 
else if ($_SESSION['role'] === 'leader' && !checkProjectLeaderID($conn, $userID, $projectID)) {
    $sql = "SELECT
    td.taskID,
    td.taskName,
    td.taskDesc,
    td.taskStatus,
    td.reqHours,
    td.completeHours,
    td.taskDeadline,
    tu.userID AS userID,
    CONCAT(ud.userName, ' ',userSurname) AS userName,
    tp.projectID,
    pd.projectName
    FROM
        taskDetails td
    JOIN
        taskUser tu ON td.taskID = tu.taskID $searchPersonalTask
    JOIN
        userDetails ud ON tu.userID = ud.userID
    JOIN
        taskProject tp ON td.taskID = tp.taskID AND tp.projectID = $projectID
    JOIN
        projectDetails pd ON tp.projectID = pd.projectID
    WHERE $userShown tu.userID $usersFormat AND td.taskStatus $statusFormat AND td.taskDeadline BETWEEN '$dateFrom' AND '$dateTo' 
    ORDER BY  $sort;";
} 
// sql query for members
else if ($_SESSION['role'] === 'member') {
    $sql = "SELECT
    td.taskID,
    td.taskName,
    td.taskDesc,
    td.taskStatus,
    td.reqHours,
    td.completeHours,
    td.taskDeadline,
    tu.userID AS userID,
    CONCAT(ud.userName, ' ',userSurname) AS userName,
    tp.projectID,
    pd.projectName
    FROM
        taskDetails td
    JOIN
        taskUser tu ON td.taskID = tu.taskID
    JOIN
        userDetails ud ON tu.userID = ud.userID AND ud.userID = $userID
    JOIN
        taskProject tp ON td.taskID = tp.taskID AND tp.projectID = $projectID
    JOIN
        projectDetails pd ON tp.projectID = pd.projectID
    WHERE tu.userID = $userID AND td.taskStatus $statusFormat AND td.taskDeadline BETWEEN '$dateFrom' AND '$dateTo' 
    ORDER BY $sort;";
}

// set the session filter variable to 1
$_SESSION['filter'] = 1;
// set the session sql variable to the new query based on the filters and role of the user
$_SESSION['sql'] = $sql;

// display the tasks
require("display-tasks.inc.php");
