<?php
// Include your database configuration file
include "db_config.php";

// Create connection
$conn = new mysqli($servername, $username, $dbpassword, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User input (example)

$user_id = intval($_POST['user_id']);

//Posts
$sql = "UPDATE Posts SET UserID = 0 WHERE UserID = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

//PostLikes
$sql = "UPDATE PostLikes SET UserID = 0 WHERE UserID = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

//Comments
$sql = "UPDATE Comments SET UserID = 0 WHERE UserID = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

//CommentLikes
$sql = "UPDATE CommentLikes SET UserID = 0 WHERE UserID = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

//projects
$sql = "UPDATE projects SET leader_id = 0 WHERE leader_id = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

//project_staff
$sql = "DELETE FROM project_staff WHERE user_id = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

//task_staff
$sql = "DELETE FROM task_staff WHERE user_id = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();


// Prepare SQL statement
$sql = "DELETE FROM users WHERE user_id = ?;";
$stmt = $conn->prepare($sql);
// Bind parameters
$stmt->bind_param("i", $user_id);

// Execute SQL statement
if ($stmt->execute()) {
    // Query was successful
    echo "User deleted successfully.";
} else {
    // Query failed
    echo "Error deleting user: " . $stmt->error;
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();
?>