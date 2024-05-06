<?php

header('Content-Type: application/json');

//connect to database
include 'db.php'; 

//check if it is a "POST" request method and get comment id
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['commentID'])) {
    $commentID = mysqli_real_escape_string($conn, $_POST['commentID']);
    

    //query to delete the specific relevant records in CommentLikes table
    $deleteLikesSql = "DELETE FROM CommentLikes WHERE CommentID = ?";
    $stmt = $conn->prepare($deleteLikesSql);
    $stmt->bind_param("i", $commentID);
    if (!$stmt->execute()) {
    
        echo json_encode(['success' => false, 'message' => 'Failed to delete comment likes.']);
        exit;
    }

    //query to delete the comment from the Comments table
    $deleteCommentSql = "DELETE FROM Comments WHERE CommentID = ?";
    $stmt = $conn->prepare($deleteCommentSql);
    $stmt->bind_param("i", $commentID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Comment deleted successfully.']);
    } else {
   
        echo json_encode(['success' => false, 'message' => 'Failed to delete comment.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

//close db connection
$conn->close();
?>
