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
$item_id = intval($_POST['itemId']);
$user_id = intval($_POST['userId']);

// Prepare SQL statement
$sql = "DELETE FROM `todolist` WHERE user_id = ? AND item_id = ?;";
$stmt = $conn->prepare($sql);
// Bind parameters
$stmt->bind_param("ii", $user_id, $item_id);

// Execute SQL statement
if ($stmt->execute()) {
    // Query was successful
    echo "Row deleted successfully.";
} else {
    // Query failed
    echo "Error deleting row: " . $stmt->error;
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();
?>