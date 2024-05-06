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
$item_id = intval($_POST['item_id']);
$user_id = intval($_POST['user_id']);
$description = $_POST['description'];
$status = $_POST['current_status'];

$sql = "";

if ($status == '0'){
    // unchecked -> check
    $sql = "DELETE FROM `todolist` WHERE user_id = $user_id AND item_id = $item_id";
    $result1 = $conn->query($sql);

    if ($result1) {
        $sql = "INSERT INTO todolist (item_id, user_id, description, checked) VALUES ($item_id, $user_id, '$description', 1)";
        $result2 = $conn->query($sql);

        if ($result2) {
            $response = array("success" => true, "message" => "Query successful");
        } else {
            $response = array("success" => false, "message" => "Insert failed: " . mysqli_error($conn));
        }
    } else {
        $response = array("success" => false, "message" => "Delete failed: " . mysqli_error($conn));
    }
} elseif ($status == '1'){
    // checked -> unchecked
    $sql = "DELETE FROM `todolist` WHERE user_id = $user_id AND item_id = $item_id";
    $result1 = $conn->query($sql);

    if ($result1) {
        $sql = "INSERT INTO todolist (item_id, user_id, description, checked) VALUES ($item_id, $user_id, '$description', 0)";
        $result2 = $conn->query($sql);

        if ($result2) {
            $response = array("success" => true, "message" => "Query successful");
        } else {
            $response = array("success" => false, "message" => "Insert failed: " . mysqli_error($conn));
        }
    } else {
        $response = array("success" => false, "message" => "Delete failed: " . mysqli_error($conn));
    }
} else {
    $response = array("success" => false, "message" => "status was not type character");
}

echo json_encode($response);

// Close the database connection
$conn->close();
?>
