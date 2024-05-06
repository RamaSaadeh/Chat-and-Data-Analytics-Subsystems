<?php

header('Content-Type: application/json');
include 'db.php';

$userID = mysqli_real_escape_string($conn, $_POST['userID']); //get user id from request

//check for POST request and required parameters
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentID'])) {
    $commentID = mysqli_real_escape_string($conn, $_POST['commentID']);
    
    //check if the user has already liked the comment
    $checkLike = "SELECT * FROM CommentLikes WHERE CommentID = '$commentID' AND UserID = '$userID'"; 
    $checkResult = mysqli_query($conn, $checkLike);
    
    if (mysqli_num_rows($checkResult) > 0) {
        //user has liked the comment before, so unlike it
        $deleteLike = "DELETE FROM CommentLikes WHERE CommentID = '$commentID' AND UserID = '$userID'";
        mysqli_query($conn, $deleteLike);
        
        //decrement likes count in Comments table
        $decrementLikes = "UPDATE Comments SET Likes = Likes - 1 WHERE CommentID = '$commentID'";
        mysqli_query($conn, $decrementLikes);
        
        $response = ['success' => true, 'message' => 'Comment unliked successfully.'];
    } else {
        //user hasn't liked the comment before, so like it
        $addLike = "INSERT INTO CommentLikes (CommentID, UserID) VALUES ('$commentID', '$userID')";
        mysqli_query($conn, $addLike);
        
        //increment Likes count in Comments table
        $incrementLikes = "UPDATE Comments SET Likes = Likes + 1 WHERE CommentID = '$commentID'";
        mysqli_query($conn, $incrementLikes);
        
        $response = ['success' => true, 'message' => 'Comment liked successfully.'];
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing comment ID.']);
}

//close db connection
$conn->close();
?>
