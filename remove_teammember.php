<?php

$project_ID_toremovefrom = $_POST['projectID'];
$user_ID_to_remove = $_POST['userID'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);



// SQL query to fetch staff not currently in team from the database
$sql = "DELETE FROM project_staff WHERE project_id = '$project_ID_toremovefrom' AND user_id = '$user_ID_to_remove';";

$result = $conn->query($sql);


//SQL query that deletes the user off all tasks they're assigned to 
$sql = "DELETE FROM task_staff WHERE project_id = '$project_ID_toremovefrom' AND user_id = '$user_ID_to_remove';";

$result = $conn->query($sql);

$sql = "UPDATE projects SET leader_id = 0 WHERE project_id = '$project_ID_toremovefrom' AND leader_id = '$user_ID_to_remove';";

$result = $conn->query($sql);


$conn->close();

?>