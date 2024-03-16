<?php

// Get the name of the project leader based on the project leader's id
function getProjectLeaderName($conn, $projectLeaderID)
{
    $sql = "SELECT CONCAT(userDetails.userName, ' ', userDetails.userSurname) as name, projectDetails.projectID 
    FROM userDetails, projectDetails 
    WHERE projectDetails.projectLeaderID = $projectLeaderID and userDetails.userID = $projectLeaderID;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = $result->fetch_assoc();

    return [$row["name"], $row["projectID"]];
}

// Check if the given user is the project leader of the given project
function checkProjectLeaderID($conn, $userID, $projectID)
{
    $sql = "SELECT projectLeaderID 
    FROM projectDetails
    WHERE projectID = $projectID AND projectLeaderID = $userID";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = $result->fetch_assoc();

    // If soemthing is retrieved then the person is the project leader
    // If not, then they aren't
    if (mysqli_num_rows($result) != 0) {
        $val = $row["projectLeaderID"];
    } else {
        $val = 0;
    }

    return $val;
}