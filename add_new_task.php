<?php

$project_ID_toadd = $_POST['projectID'];
$newtaskID = $_POST['taskID'];
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


foreach($newtaskstaffarray as $user){
    $sql = "INSERT INTO task_staff (`project_id`, `task_id`, `user_id`) VALUES ('$project_ID_toadd', '$newtaskID', '$user');";
    $result = $conn->query($sql);
}

$sql = "INSERT INTO tasks (`task_id`, `project_id`, `task_name`, `hrs_remaining`, `status`, `deadline`, `notes`) VALUES ('$newtaskID', '$project_ID_toadd', '$newtaskname', '$newhrsrem', '$newtaskstatus', '$newtaskdeadline', '');";
$result = $conn->query($sql);

$conn->close();
?>