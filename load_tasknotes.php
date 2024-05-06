<?php

// // Retrieve the project ID sent via POST
$project_ID_toload = $_POST['ID'];
$task_id = $_POST['taskid'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query that fetches all tasks from our specified project 
$sql = "SELECT notes FROM `tasks` WHERE project_id='$project_ID_toload' AND task_id='$task_id';";

$result = $conn->query($sql);

$notes = "";

while ($row = $result->fetch_assoc()) {
    $notes .= $row['notes'];
}

echo json_encode($notes);

?>