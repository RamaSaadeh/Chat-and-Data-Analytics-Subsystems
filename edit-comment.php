<?php

//connect to database
include 'db.php';

//check if the request is a POST request and has the data it needs
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentID']) && isset($_POST['newContent'])) {
    //sanitize and validate input data
    $commentID = mysqli_real_escape_string($conn, $_POST['commentID']);
    $newContent = mysqli_real_escape_string($conn, $_POST['newContent']);
    $dateCreated = mysqli_real_escape_string($conn, $_POST['dateCreated']);
    $currentDate = date('Y-m-d H:i:s'); // Get current date and time

    //prepare and execute the SQL query to update the comment content and LastModified field
    $updateQuery = "UPDATE Comments SET CommentContent = '$newContent', LastModified = '$currentDate' WHERE CommentID = '$commentID'";
    $result = mysqli_query($conn, $updateQuery);

    if ($result) {
        //comment updated successfully
        $fetchQuery = "SELECT LastModified FROM Comments WHERE CommentID = '$commentID'";
        $fetchResult = mysqli_query($conn, $fetchQuery);
        $row = mysqli_fetch_assoc($fetchResult);
        $lastModified = $row['LastModified'];

        echo json_encode([
            'success' => true, 
            'message' => 'Comment updated successfully.',
            'lastModified' => $lastModified
        ]);
    } else {
        // Error updating comment
        echo json_encode(['success' => false, 'message' => 'Failed to update comment.']);
    }
} else {
    //invalid request or missing data
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing data.']);
}

//close database connection
$conn->close();
?>
