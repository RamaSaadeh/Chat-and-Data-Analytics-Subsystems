<?php

$project_ID_toload = $_POST['ID'];


$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);

$quantities = [0,0,0,0];


// SQL query to fetch number of tasks Completed
$sql = "SELECT COUNT(DISTINCT task_id) AS count FROM tasks WHERE status = 'Completed' AND project_id = '$project_ID_toload';";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    while ($value = $result->fetch_assoc()) {

        $quantities[0] = (int)$value['count'];
    }
}

// SQL query to fetch number of tasks OnTrack
$sql = "SELECT COUNT(DISTINCT task_id) AS count FROM tasks WHERE status = 'On Track' AND project_id = '$project_ID_toload';";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    while ($value = $result->fetch_assoc()) {

        $quantities[1] = (int)$value['count'];
    }
}

// SQL query to fetch number of tasks Overdue
$sql = "SELECT COUNT(DISTINCT task_id) AS count FROM tasks WHERE status = 'Overdue' AND project_id = '$project_ID_toload';";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    while ($value = $result->fetch_assoc()) {

        $quantities[2] = (int)$value['count'];
    }
}

// SQL query to fetch number of tasks Not Started
$sql = "SELECT COUNT(DISTINCT task_id) AS count FROM tasks WHERE status = 'Not Started' AND project_id = '$project_ID_toload';";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    while ($value = $result->fetch_assoc()) {

        $quantities[3] = (int)$value['count'];
    }
}



echo json_encode($quantities);

$conn->close();

?>