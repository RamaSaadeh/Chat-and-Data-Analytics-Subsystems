<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

function emailExists($conn, $email)
{
    $sql = "SELECT * FROM userDetails WHERE userEmail = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../register.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($resultData);

    if ($row) {
        return $row;
    } else {
        $result = false;
        return $result;
    }
}
;


function createUser($conn, $name, $surname, $email, $password)
{

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = "member";

    $sql = "INSERT INTO userDetails (userRole, userName, userSurname, userEmail, userPword) VALUES (?, ?, ?, ?, ?) ";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../register.php?error=statementFailed");
        exit();
    } else {
    }

    mysqli_stmt_bind_param($stmt, "sssss", $role, $name, $surname, $email, $password_hashed);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../login.php?error=none");
    exit();
}
;

function loginUser($conn, $email, $password)
{

    $emailExists = emailExists($conn, $email);

    if ($emailExists === false) {
        header("location: ../login.php?InvalidEmail");
        exit();
    }

    $password_hashed = $emailExists["userPword"];
    $check_password = password_verify($password, $password_hashed);

    if ($check_password === false) {
        header("location: ../login.php?InvalidPassword");
        exit();
    } else if ($check_password === true) {
        session_start();
        $_SESSION["valid"] = true;
        $_SESSION["userid"] = $emailExists["userID"];
        $_SESSION["username"] = $emailExists["userName"];
        $_SESSION["usersurname"] = $emailExists["userSurname"];
        $_SESSION["email"] = $emailExists["userEmail"];
        $_SESSION["role"] = $emailExists["userRole"];
        $_SESSION["password"] = $password;
        $_SESSION["accounteditable"] = false;

        header("location: ../dashboard.php?success=loggedIn?" . $_SESSION['valid']);


        exit();
    }
}

function countToDo($conn, $type)
{

    $count = 0;
    $todoID = $_SESSION["userid"];


    if ($type == 0 || $type == 1) {
        $sql = "SELECT * FROM todoList WHERE todoCompletion=$type AND todoOwnerID = $todoID ORDER BY todoPriority DESC;";
    } else {
        $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoID ORDER BY todoPriority DESC;";
    }

    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $count = $count + 1;
    }
    return $count;
}

function truncateText($string, $limit = 50, $break = "", $pad = "...")
{
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit)
        return $string;

    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

function submitTask($conn, $taskName, $taskDesc, $taskProgress, $reqHours)
{
    $sql = "INSERT INTO taskDetails (taskName, taskDesc, taskProgress, reqHours) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: tasks.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssss", $taskName, $taskDesc, $taskProgress, $reqHours);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}


function createProject($conn, $name, $description, $date, $leaderID)
{

    $dateTime = Date("Y-m-d H:i:s");

    $sql = "INSERT INTO projectDetails (projectLeaderID, projectName, projectDesc, projectDate, projectProgress, lastEdited) VALUES (?, ?, ?, ?, 0, ?);";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sssss", $leaderID, $name, $description, $date, $dateTime);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}

function addUserProject($conn, $leaderID)
{

    $projectID = returnProjectID($conn);

    $sql = "INSERT INTO userProject (userID, projectID) VALUES (?, ?);";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $leaderID, $projectID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../projects/projects.php?projectCreated");
    exit();
}

function returnProjectID($conn)
{
    $sql = "SELECT projectID FROM projectDetails ORDER BY projectID DESC LIMIT 1 ;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $projectID = mysqli_fetch_assoc($resultData)["projectID"];

    return $projectID;
}

function dropProjectUsers($conn, $projectID)
{
    $sql = "DELETE FROM userProject WHERE projectID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $projectID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function updateProjectUser($conn, $projectID, $userIDs)
{
    dropProjectUsers($conn, $projectID);

    if (!empty($userIDs)) {
        foreach ($userIDs as $userID) {
            $sql = "INSERT INTO userProject (userID, projectID) VALUES (?, ?);";

            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../projects/projects.php?error=statementFailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, "ss", $userID, $projectID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } else {
    }

    header("location: ../projects/projects.php?error=none");
    exit();
}

function updateNewProjectUser($conn, $userIDs)
{
    $projectID = returnProjectID($conn);
    dropProjectUsers($conn, $projectID["projectID"]);

    if (!empty($userIDs)) {
        foreach ($userIDs as $userID) {
            $sql = "INSERT INTO userProject (userID, projectID) VALUES (?, ?);";

            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("location: ../projects/projects.php?error=statementFailed");
                exit();
            }

            mysqli_stmt_bind_param($stmt, "ss", $userID, $projectID["projectID"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } else {
    }

    header("location: ../projects/projects.php?error=none");
    exit();
}

function updateProject($conn, $name, $description, $date, $id, $leaderID)
{

    $sql = "SELECT projectLeaderID FROM projectDetails WHERE projectID = $id LIMIT 1";

    $result = $conn->query($sql);

    $row = $result->fetch_assoc();

    $oldLeaderID = $row["projectLeaderID"];

    $sql2 = "DELETE FROM userProject WHERE projectID = ? AND userID = ?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql2)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $id, $oldLeaderID);
    mysqli_stmt_execute($stmt);

    $dateTime = Date("Y-m-d H:i:s");

    $sql3 = "UPDATE projectDetails SET projectProgress = 0, projectDesc = ?, projectName = ?, projectDate = ?, lastEdited = ?, projectLeaderID = ? WHERE projectID = ?";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql3)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $description, $name, $date, $dateTime, $leaderID, $id);
    mysqli_stmt_execute($stmt);


    addUserProject($conn, $leaderID);

    exit();
}

function deleteProject($conn, $projectID)
{

    $sql = "DELETE FROM projectDetails WHERE projectID =?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $projectID);
    mysqli_stmt_execute($stmt);

    deleteFromUserProject($conn, $projectID);
    deleteTaskProject($conn, $projectID);


    mysqli_stmt_close($stmt);

    header("location: ../projects/projects.php?error=none");

    exit();
}
function deleteFromUserProject($conn, $projectID)
{

    $sql = "DELETE FROM userProject WHERE projectID =?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $projectID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getTaskID($conn, $projectID)
{
    $sql = "SELECT taskID FROM taskProject WHERE projectID =?;";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $projectID);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $arr = array();

    while ($row = mysqli_fetch_assoc($resultData)) {
        array_push($arr, $row["taskID"]);
    }

    return $arr;


}

function deleteTaskProject($conn, $projectID)
{

    $taskIDs = getTaskID($conn, $projectID);

    foreach ($taskIDs as $taskID) {
        $sql = "DELETE FROM taskProject WHERE projectID = $projectID;";

        $sql .= "DELETE FROM taskUser WHERE taskID = $taskID;";

        $sql .= "DELETE FROM taskDetails WHERE taskID = $taskID;";

        $result = mysqli_multi_query($conn, $sql);

        while (mysqli_next_result($conn)) {
            ;
        }
    }
}


function progressProject($conn, $projectID)
{

    $complete = 0;
    $notComplete = 0;
    $total = 0;
    $progress = 0;

    $sql = "SELECT (SELECT SUM(taskDetails.reqHours) as complete FROM taskProject, taskDetails WHERE
                                    taskProject.taskID = taskDetails.taskID AND taskDetails.taskStatus = 'Complete' AND
                                    taskProject.projectID = $projectID) as Complete, SUM(taskDetails.reqHours) as notComplete FROM
                                    taskProject, taskDetails WHERE taskProject.taskID = taskDetails.taskID AND
                                    taskDetails.taskStatus != 'Complete' AND taskProject.projectID = $projectID;";


    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $complete = $row["Complete"];
        $notComplete = $row["notComplete"];
    }

    $total = $complete + $notComplete;
    if ($total != 0) {
        $progress = floor(($complete / $total) * 100);
    } else {
        $progress = 0;
    }

    $sql2 = "UPDATE projectDetails SET projectProgress=? WHERE projectID = ?";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql2)) {
        header("location: ../projects/projects.php?error=statementFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $progress, $projectID);
    mysqli_stmt_execute($stmt);


    return $progress;
}

function getHighestProjectDate($conn)
{
    $sql = "SELECT projectDate FROM `projectDetails` ORDER BY `projectDate` DESC LIMIT 1;";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row["projectDate"];
}


function getLowestProjectDate($conn)
{
    $sql = "SELECT projectDate FROM `projectDetails` ORDER BY `projectDate` ASC LIMIT 1;";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row["projectDate"];
}
