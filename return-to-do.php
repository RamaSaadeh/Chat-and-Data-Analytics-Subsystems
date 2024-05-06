<?php
include "db_config.php";

$user_id = intval($_POST['user_id']);

$conn = new mysqli($servername, $username, $dbpassword, $database);
$toDoItems = array();
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 // Set the appropriate headers to indicate JSON content
 header('Content-Type: application/json');


// Execute SQL queries to fetch users data
$sql1 = "SELECT user_id, item_id, description, checked, time_added FROM `todolist` WHERE user_id = $user_id AND checked = 0 ORDER BY time_added desc;";
$result1 = $conn->query($sql1);
$sql2 = "SELECT user_id, item_id, description, checked, time_added FROM `todolist` WHERE user_id = $user_id AND checked = 1 ORDER BY time_added asc;";
$result2 = $conn->query($sql2);



if ($result1->num_rows > 0) {
    // Fetch the results into an associative array
    while($row = $result1->fetch_assoc()) {
        // append data to staffData array
        $toDoItems[] = $row;
    }
} 
if ($result2->num_rows > 0) {
    // Fetch the results into an associative array
    while($row = $result2->fetch_assoc()) {
        // append data to staffData array
        $toDoItems[] = $row;
    }
} 

$jsonResponse = json_encode($toDoItems);



// Output the JSON response
echo $jsonResponse;



// Close the database connection
$conn->close();
?>
