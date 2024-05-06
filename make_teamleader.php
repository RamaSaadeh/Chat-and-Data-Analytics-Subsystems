<?php

$project_ID_toadd = $_POST['projectID'];
$user_ID_to_add = $_POST['userID'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);


// SQL query to fetch staff not currently in team from the database
$sql = "UPDATE projects SET leader_id = '$user_ID_to_add' WHERE project_id = '$project_ID_toadd';";

$result = $conn->query($sql);

$conn->close();

?>