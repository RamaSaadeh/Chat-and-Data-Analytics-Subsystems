<?php

// function to add a new task to the taskDetails table in the database
function addTask($conn, $taskName, $taskDescription, $taskHours, $taskDeadline)
{

    $sql = "INSERT INTO taskDetails (taskName, taskDesc, reqHours, taskDeadline) VALUES (?, ?, ?, ?);";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssss", $taskName, $taskDescription, $taskHours, $taskDeadline);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}

// function to add add information to the taskUser table in the database
function addToTaskUser($conn, $userID)
{
    $sql = "INSERT INTO taskUser (taskID, userID) VALUES (?, ?);";

    // get the taskID of the most recently created task
    $query = "SELECT taskID FROM taskDetails ORDER BY taskID DESC LIMIT 1;";
    $result = $conn->query($query);
    while ($row = mysqli_fetch_assoc($result)) {
        $taskID = $row["taskID"];
    }

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $taskID, $userID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// function to add add information to the userProject table in the database
function addToUserProject($conn, $userID, $projectID)
{
    // check if the user is not already in the userProject table for the given project
    $query = "SELECT * FROM userProject WHERE userID = $userID AND projectID = $projectID";
    $result = $conn->query($query);

    // if they are not in the project, add them
    if ($result->num_rows == 0) {
        $sql = "INSERT INTO userProject (userID, projectID) VALUES (?, ?);";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: tasks.php?error=statementFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $userID, $projectID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// function to add add information to the taskProject table in the database
function addToTaskProject($conn, $projectID)
{
    // get the taskID of the most recently created task
    $query = "SELECT taskID FROM taskDetails ORDER BY taskID DESC LIMIT 1;";
    $result = $conn->query($query);
    while ($row = mysqli_fetch_assoc($result)) {
        $taskID = $row["taskID"];
    }

    // add the taskID with the projectID to the taskProject table
    $sql = "INSERT INTO taskProject (taskID, projectID) VALUES (?, ?);";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $taskID, $projectID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);


    header("location: tasks.php?projectID=" . $projectID);
    exit();
}

// function to delete task information from the taskDetails table
function deleteFromTaskDetails($conn, $taskID)
{
    // delete the task from the table
    $sql = "DELETE FROM taskDetails WHERE taskID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);


}

// function to delete infromation from the taskUser table
function deleteFromTaskUser($conn, $taskID)
{
    // delete the task from the table
    $sql = "DELETE FROM taskUser WHERE taskID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// function to delete a task from the taskProject table
function deleteFromTaskProject($conn, $taskID, $projectID)
{
    // delete the task from the table
    $sql = "DELETE FROM taskProject WHERE taskID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("location: tasks.php?projectID=" . $projectID);
    exit();
}

// function to delete a user from the userProject table
function deleteFromUserProjectTask($conn, $userID, $projectID)
{
    // delete the user from the table
    $sql = "DELETE FROM userProject WHERE userID = ? AND projectID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $userID, $projectID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// function to update the deatils of a task
function updateTask($conn, $taskID, $taskName, $taskDescription, $taskStatus, $taskDeadline, $reqHours)
{   
    // update the task details
    $sql = "UPDATE taskDetails SET taskName = ?, taskDesc = ?, taskStatus = ?, reqHours = ?, taskDeadline = ? WHERE taskID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $taskName, $taskDescription, $taskStatus, $reqHours, $taskDeadline, $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}

// function to update the taskProject table when the assigned project is changed
function updateTaskProject($conn, $taskID, $projectID)
{   
    // change the projectID
    $sql = "UPDATE taskProject SET projectID = ? WHERE taskID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $projectID, $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}

// function to update the taskUser table when the assigned user is changed
function updateTaskUser($conn, $taskID, $userID)
{
    // change the userID
    $sql = "UPDATE taskUser SET userID = ? WHERE taskID =?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $userID, $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}

// function to get the highest task date
function getHighestTaskDate($conn, $projectID)
{
    $sql = "SELECT taskDetails.taskID, taskDetails.taskDeadline, taskProject.projectID FROM taskDetails, taskProject WHERE taskDetails.taskID = taskProject.taskID AND taskProject.projectID = $projectID ORDER BY taskDeadline DESC LIMIT 1;";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row["taskDeadline"];
}

// function to get the lowest task date
function getLowestTaskDate($conn, $projectID)
{
    $sql = "SELECT taskDetails.taskID, taskDetails.taskDeadline, taskProject.projectID FROM taskDetails, taskProject WHERE taskDetails.taskID = taskProject.taskID AND taskProject.projectID = $projectID ORDER BY taskDeadline ASC LIMIT 1;";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row["taskDeadline"];
}

// function to determine if a projectID exists in the database
function projectIDExist($conn, $projectID)
{
    $sql = "SELECT projectID FROM projectDetails WHERE projectID = $projectID";

    $result = $conn->query($sql);
    $val = 0;

    if (mysqli_num_rows($result) != 0) {
        $val = 1;
    } else {
        $val = 0;
    }

    return $val;
}

// function to return the name of a given project
function getProjectName($conn, $projectID)
{
    $sql = "SELECT projectName FROM projectDetails WHERE projectID = $projectID";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row;
}

// function to return the deadline of a given project
function getProjectDeadline($conn, $projectID)
{
    $sql = "SELECT projectDate FROM projectDetails WHERE projectID = $projectID";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['projectDate'];
}

// function to return true if a user is the team leader of a project and false otherwise
function checkProjectLeaderID($conn, $userID, $projectID)
{
    // select the user from the database
    $sql = " SELECT projectLeaderID 
    FROM projectDetails
    WHERE projectID = $projectID AND projectLeaderID = $userID";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = $result->fetch_assoc();

    // if there are not 0 rows then the user is the leader of the project
    if (mysqli_num_rows($result) != 0) {
        $val = true;
    } 
    // else they are not
    else {
        $val = false;
    }

    return $val;
}

// function that moves the task status onwards
function updateTaskStatus($conn, $nextTaskStatus, $taskID)
{
    // update the task status
    $sql = "UPDATE taskDetails SET taskStatus = ? WHERE taskID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $nextTaskStatus, $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// function that returns true if the task belongs to the user and false otherwise
function isTaskCurrentUser($conn, $taskID, $userID)
{   
    // select the user ID and taskID from the database
    $sql = "SELECT userID, taskID 
    FROM taskUser
    WHERE taskID = $taskID AND userID = $userID";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = $result->fetch_assoc();

    // if there are not 0 rows then the user is the owner of the task 
    if (mysqli_num_rows($result) != 0) {
        $val = true;
    } else {
        $val = false;
    }

    return $val;
}

// function to add completed hours to a task 
function updateTaskHours($conn, $taskID, $hours)
{
    // add the number of completed hours to the number of hours that have already been completed 
    $sql = "UPDATE taskDetails SET completeHours = completeHours + ? WHERE taskID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $hours, $taskID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}