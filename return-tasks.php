<?php

// Include your database configuration file
include "db_config.php";

// Create connection
$conn = new mysqli($servername, $username, $dbpassword, $database);
$user_id = intval($_POST['user_id']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/*$sql = "SELECT DISTINCT tasks.*, task_staff.user_id, projects.proj_name
FROM tasks
INNER JOIN task_staff ON tasks.task_id = task_staff.task_id
INNER JOIN projects ON tasks.project_id = projects.project_id
WHERE task_staff.user_id = $user_id;";*/
$sql = "SELECT DISTINCT tasks.*, task_staff.user_id, projects.proj_name
        FROM tasks
        INNER JOIN task_staff ON tasks.task_id = task_staff.task_id AND tasks.project_id = task_staff.project_id
        INNER JOIN projects ON tasks.project_id = projects.project_id
        WHERE task_staff.user_id = $user_id;";


$result = $conn->query($sql);

$user_tasks = array();

if ($result->num_rows > 0) {
    // Fetch the results into an associative array
    while($row = $result->fetch_assoc()) {
        // append data to staffData array
        $user_tasks[] = $row;
    }
} 

echo json_encode($user_tasks);

// Close the database connection
$conn->close();
?>