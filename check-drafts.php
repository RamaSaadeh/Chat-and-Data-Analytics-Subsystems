<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

//connect to database
include 'db.php';

$userID = mysqli_real_escape_string($conn, $_GET['userID']); //get user id from request

//query for counting number of drafts user has
$sql = "SELECT COUNT(*) AS draftCount FROM Posts WHERE UserID = ? AND IsDraft = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

//prepare the response as JSON
header('Content-Type: application/json');
echo json_encode(array("draftCount" => $row['draftCount']));

$stmt->close();
$conn->close();

?>
