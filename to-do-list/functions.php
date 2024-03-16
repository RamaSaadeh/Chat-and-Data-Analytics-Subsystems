<?php

function countFlagged($conn)
{
    //setting count as 0
    $count = 0;
    //getting user ID
    $todoID = $_SESSION["userid"];
    //sql statement
    $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoID AND todoCompletion = 0 AND todoFlag = 1;";
    //getting result
    $result = $conn->query($sql);
    //going through every result adding one to the count each time
    while ($row = $result->fetch_assoc()) {
        $count = $count + 1;
    }
    return $count;
}



function countAll($conn)
{
    //setting count as 0
    $count = 0;
    //getting user ID
    $todoID = $_SESSION["userid"];
    //sql statement
    $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoID AND todoCompletion = 0;";
    //getting result
    $result = $conn->query($sql);
    //going through every result adding one to the count each time
    while ($row = $result->fetch_assoc()) {
        $count = $count + 1;
    }
    return $count;
}

function countToday($conn)
{
    //setting count as 0
    $count = 0;
    //getting user ID
    $todoID = $_SESSION["userid"];
    //getting todays date
    $todaysDate = date("Y-m-d"); //todays date
    //sql statement
    $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoID AND todoCompletion = 0 AND todoDueDate = '$todaysDate';";
    //getting result
    $result = $conn->query($sql);
    //going through every result adding one to the count each time
    while ($row = $result->fetch_assoc()) {
        $count = $count + 1;
    }
    return $count;
}

function countCompleted($conn)
{
    //setting count as 0
    $count = 0;
    //getting user ID
    $todoID = $_SESSION["userid"];
    //sql statement
    $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoID AND todoCompletion = 1;";
    //getting result
    $result = $conn->query($sql);
    //going through every result adding one to the count each time
    while ($row = $result->fetch_assoc()) {
        $count = $count + 1;
    }
    return $count;
}

function displayingDate($date)
{
    $todaysDate = date("Y-m-d");
    $todaysDateFormatted = date_create(date("Y-m-d")); //todays date
    $yesterdaysDate = date_format(date_modify($todaysDateFormatted, "-1 day"), "Y-m-d");
    $todaysDateFormatted = date_create(date("Y-m-d")); //refreshing todays date (otherwise +2 days required)
    $tomorrowsDate = date_format(date_modify($todaysDateFormatted, "+1 day"), "Y-m-d");
    //formatting due date if it needs to be displayed
    $dueDate_pre_format = date_create($date);
    $dueDate = date_format($dueDate_pre_format, "d/m/Y");
    //comparing today's date to due date
    if ($todaysDate == $date) { //due date is today
        echo "<p class='m-0' style=''><small>Today</small></p>";
    } elseif ($yesterdaysDate == $date) { //due date yesterday
        echo "<p class='m-0' style='color: #CC3232'><small>Yesterday</small></p>";
    } elseif ($tomorrowsDate == $date) { //due date tomorrow
        echo "<p class='m-0' style=''><small>Tomorrow</p><small>";
    } elseif ($todaysDate > $date) { //due date tomorrow
        echo "<p class='m-0' style='color: #CC3232'><small>" . $dueDate . "</small></p>";
    } else { //other due dates
        echo "<p class='m-0' style=''><small>" . $dueDate . "</p><small>";
    }
}

function addToDo($conn, $todoInformation, $todoPriority, $userID, $todoDueDate, $todoNotes, $todoFlag)
{

    $sql = "INSERT INTO todoList (todoInformation, todoPriority, todoCompletion, todoOwnerID, todoDueDate, todoNotes, todoFlag) VALUES (?, ?, 0, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $todoInformation, $todoPriority, $userID, $todoDueDate, $todoNotes, $todoFlag);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function updateToDo($conn, $todoInformation, $todoPriority, $todoID, $todoDueDate, $todoNotes, $todoFlag)
{
    $sql = "UPDATE todoList SET todoInformation = '$todoInformation', todoPriority = '$todoPriority', todoDueDate = '$todoDueDate', todoNotes = '$todoNotes', todoFlag = '$todoFlag' WHERE todoID = $todoID";
    mysqli_query($conn, $sql);
}


function getPriorityColour($priority)
{
    if ($priority == 1) {
        return "99C140";
    } elseif ($priority == 2) {
        return "E7B416";
    } elseif ($priority == 3) {
        return "DB7B2B";
    }
}


function getPriorityNumber($priority)
{
    if ($priority == "Low") {
        return 1;
    } elseif ($priority == "Medium") {
        return 2;
    } elseif ($priority == "High") {
        return 3;
    }
}

function getPriorityText($priority)
{
    if ($priority == 1) {
        return "Low";
    } elseif ($priority == 2) {
        return "Medium";
    } elseif ($priority == 3) {
        return "High";
    }
}
