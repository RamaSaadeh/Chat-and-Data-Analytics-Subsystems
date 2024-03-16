<?php

// get all of the task information from the header
$taskID = $_GET["id"];
// replace '__' with ' '
$taskName = str_replace('__', ' ', $_GET["name"]);
$taskDesc = str_replace('__', ' ', $_GET["desc"]);
$taskStatus = str_replace('__', ' ', $_GET["status"]);
$taskDeadline = $_GET["deadline"];
$reqHours = $_GET["hours"];
$userID = $_GET["user"];
$projectID = $_GET["project"];

// require the database and the functions so that they can be used
require_once("../includes/dbh.inc.php");
require_once("task-functions.inc.php");
require_once("../includes/functions.inc.php");

// update the task in taskDetails
updateTask($conn, $taskID, $taskName, $taskDesc, $taskStatus, $taskDeadline, $reqHours);
// calculate the progress of the project
progressProject($conn, $projectID);

// get the current data of the task that is being edited
$sql = "SELECT TP.projectID, TU.userID, TD.taskID FROM taskDetails TD JOIN taskUser TU ON TD.taskID = TU.taskID JOIN taskProject TP ON TD.taskID = TP.taskID WHERE TU.taskID = $taskID;";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $currentUser = $row["userID"];
    $currentProject = $row["projectID"];
}

// declare the variables that keep track of whether the project and user are updated
$updatedProject = false;
$updatedUser = false;

// if the projectID is different to the current project
if ($$projectID != $currentProject) {
    // update taskProject with the new projectID
    updateTaskProject($conn, $taskID, $projectID);
    // set updatedProject to true
    $updatedProject = true;
}
// if the userID is different to the current user
if ($userID != $currentUser) {
    // update taskUser with the new userID
    updateTaskUser($conn, $taskID, $userID);
    // set updatedUser to true
    $updatedUser = true;
}

// if the user or the project have been updated
if ($updatedProject == true || $updatedUser == true) {
    // add a new entry to userProject
    addToUserProject($conn, $userID, $projectID);
} 
