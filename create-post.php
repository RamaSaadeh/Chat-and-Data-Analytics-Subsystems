<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

//connect to database
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    echo "isDraft received: " . (isset($_POST['isDraft']) ? $_POST['isDraft'] : 'Not Set');

	//retrieve relevant topic, title and body fields  
    $topic = $_POST['topic'];
    $title = $_POST['title'];
    $body = $_POST['body'];
    $isDraft = (isset($_POST['isDraft']) && $_POST['isDraft'] === '0') ? 0 : 1;
    $userID = mysqli_real_escape_string($conn, $_POST['userID']); //get user id from request

    //SQL query to insert record into Posts table when user creates a post
    $sql = "INSERT INTO Posts (UserID, Topic, Title, Content, IsDraft) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $userID, $topic, $title, $body, $isDraft);
    


    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    //close db connection
    $stmt->close();
    $conn->close();
	
}
?>
