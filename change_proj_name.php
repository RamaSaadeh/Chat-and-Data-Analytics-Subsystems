<?php

$project_ID = $_POST['projectID'];
$new_projname = $_POST['projname'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);


// SQL query to fetch staff not currently in team from the database
$sql = "UPDATE projects SET proj_name = '$new_projname' WHERE project_id = '$project_ID';";

$result = $conn->query($sql);

$conn->close();

?>