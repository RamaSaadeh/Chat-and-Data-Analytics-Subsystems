<?php

// include database connetion and functions
include_once("../includes/dbh.inc.php");
include_once("../includes/functions.inc.php");

// get the session user and project
$projectID = $_SESSION["projectID"];
$userID = $_SESSION['userid'];
$searchPersonalTask = "AND tu.userID != $userID";

if ($isTaskPersonal) {
    $searchPersonalTask = "AND tu.userID = $userID";
}

// display all tasks if there is no filter
if ($_SESSION['filter'] == 0) {
    // display all tasks if the user is a manager
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
        ORDER BY  td.taskDeadline ASC";
    // only display the tasks assigned to the employee if the user is a 'member'
    } else if ($_SESSION['role'] === 'member') {
        $sql = "SELECT
        td.taskID,
        td.taskName,
        td.taskDesc,
        td.taskStatus,
        td.reqHours,
        td.taskDeadline,
        td.completeHours,
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
        ORDER BY td.taskDeadline ASC";

    // display all tasks if the employee is a team leader and they lead the project
    } else if ($_SESSION['role'] === 'leader' && checkProjectLeaderID($conn, $userID, $projectID)) {

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
        ORDER BY td.taskDeadline ASC";

    // only display the tasks assigned to the team leader if they do not lead the project
    } else if ($_SESSION['role'] === 'leader' && !checkProjectLeaderID($conn, $userID, $projectID)) {

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
        ORDER BY td.taskDeadline ASC";

    }
} elseif ($_SESSION['filter'] == 1) {
    $sql = $_SESSION['sql'];
}

$result = $conn->query($sql);

include("taskItem.php");
