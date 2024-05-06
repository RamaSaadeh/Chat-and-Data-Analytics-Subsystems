<?php

$projectID = $_POST['projectID'];
$taskID = $_POST['taskID'];
$newtaskname = $_POST['name'];
$newtaskstatus = $_POST['status'];
$newhrsrem = $_POST['hrs'];
$newtaskdeadline = $_POST['deadline'];
$newtaskstaffarray = array();
$newtaskstaffarray= $_POST['staff'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);


//remove all staff currently working on this task- then rewrite the new version
$sql = "DELETE FROM task_staff WHERE task_id = '$taskID' AND project_id = '$projectID';";
$result = $conn->query($sql);


foreach($newtaskstaffarray as $user){
    $sql = "INSERT INTO task_staff (`project_id`, `task_id`, `user_id`) VALUES ('$projectID', '$taskID', '$user');";
    $result = $conn->query($sql);
}

//SQL query to fetch staff not currently in team from the database
$sql = "UPDATE tasks SET task_name = '$newtaskname', hrs_remaining = '$newhrsrem', status = '$newtaskstatus', deadline = '$newtaskdeadline'  WHERE project_id = '$projectID' AND task_id = '$taskID';";

$result = $conn->query($sql);

$conn->close();

?>