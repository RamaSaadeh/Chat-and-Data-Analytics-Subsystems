<?php

// This page is run when the delete task button is pressed

if (isset($_GET["taskID"])) {

    // get the taskID and the projectID of the task that is being deleted
    $taskID = (int) $_GET['taskID'];
    $projectID = (int) $_GET['projectID'];
    $userID = (int) $_GET['userID'];

    require_once("../includes/dbh.inc.php");
    require_once("task-functions.inc.php");

    // count the number of tasks that a user has in a project
    $sql = "SELECT COUNT(tp.taskID) AS taskCount
    FROM taskProject tp
    JOIN userProject up ON tp.projectID = up.projectID
    WHERE up.userID = $userID AND up.projectID = $projectID";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $numTasks = $row['taskCount'];

    // if the users last task is being deleted, then remove them from the taskProject table
    if($numTasks == 1) {
        deleteFromUserProjectTask($conn, $userID, $projectID);
    }

    // call the required functions that delete the task information from the database
    deleteFromTaskDetails($conn, $taskID);
    deleteFromTaskUser($conn, $taskID);
    deleteFromTaskProject($conn, $taskID, $projectID);
    
} else {
    header("location: tasks.php?projectID=" . $projectID);
}
