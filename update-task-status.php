<?php

// Include your database configuration file
include "db_config.php";

// Create connection
$conn = new mysqli($servername, $username, $dbpassword, $database);
$status = $_POST['status'];
$task_id = $_POST['taskId'];
$project_id = $_POST['projectId'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind the update statement
$sql = "UPDATE tasks SET status = ? WHERE task_id = ? AND project_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $status, $task_id, $project_id);

// Execute the statement
if ($stmt->execute()) {
    $response = array("success" => true, "message" => "Query successful");
} else {
    $response = array("success" => false, "message" => "Update failed: " . $conn->error);
}

// Close the prepared statement
$stmt->close();

// Send JSON response
echo json_encode($response);

// Close the database connection
$conn->close();
?>