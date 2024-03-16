<?php

// This page is reached after the form to create a new task is submitted

session_start();
$_SESSION['projectID'];

if (isset($_POST["submit"])) {

    // get elements from the form
    $taskName = $_POST['createName'];
    $taskDescription = $_POST['createDescription'];
    $taskDeadline = $_POST['createDate'];
    $taskHours = $_POST['createHours'];
    $userID = $_POST['createUserID'];
    $projectID = $_SESSION['projectID'];

    require_once("/var/www/team-projects-part-2-team-01/includes/dbh.inc.php");
    require_once("task-functions.inc.php");

    // call functions to update the respective tables in the database with the new task information
    addTask($conn, $taskName, $taskDescription, $taskHours, $taskDeadline);
    addToTaskUser($conn, $userID);
    addToUserProject($conn, $userID, $projectID);
    addToTaskProject($conn, $projectID);

} else {
    header("location: tasks.php?projectID=" . $projectID);
}
