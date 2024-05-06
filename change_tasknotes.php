<?php

$project_ID_toadd = $_POST['projectID'];
$task_id = $_POST['taskid'];
$new_notes = $_POST['notes'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);


// SQL query to fetch staff not currently in team from the database
$sql = "UPDATE tasks SET notes = '$new_notes' WHERE project_id = '$project_ID_toadd' AND task_id = '$task_id';";

$result = $conn->query($sql);

$conn->close();

?>