<?php

$project_ID_toremove = $_POST['projectID'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);


// SQL query to fetch staff not currently in team from the database
$sql = "DELETE FROM project_staff WHERE project_id = '$project_ID_toremove';";

$result = $conn->query($sql);


// SQL query to fetch staff not currently in team from the database
$sql = "DELETE FROM projects WHERE project_id = '$project_ID_toremove';";

$result = $conn->query($sql);


//SQL query that deletes the user off all tasks they're assigned to 
$sql = "DELETE FROM task_staff WHERE project_id = '$project_ID_toremove';";

$result = $conn->query($sql);


//SQL query that deletes the user off all tasks they're assigned to 
$sql = "DELETE FROM tasks WHERE project_id = '$project_ID_toremove';";

$result = $conn->query($sql);

$conn->close();

?>