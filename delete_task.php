<?php

$projectID = $_POST['projectID'];
$taskID = $_POST['taskID'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);


//remove all staff currently working on this task- then rewrite the new version
$sql = "DELETE FROM task_staff WHERE task_id = '$taskID' AND project_id = '$projectID';";
$result = $conn->query($sql);



//SQL query to fetch staff not currently in team from the database
$sql = "DELETE FROM tasks WHERE project_id = '$projectID' AND task_id = '$taskID';";

$result = $conn->query($sql);

$conn->close();

?>