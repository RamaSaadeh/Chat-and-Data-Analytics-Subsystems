<?php

include 'db.php'; //connect to database

//check if the request is a "POST" method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //sanitize input
    $postId = isset($_POST['postId']) ? mysqli_real_escape_string($conn, $_POST['postId']) : '';

    if (!empty($postId)) {
        
        mysqli_begin_transaction($conn);

        try {
            
            //delete comment likes associated with comments deleted
            $deleteCommentLikesSql = "DELETE FROM CommentLikes WHERE CommentID IN (SELECT CommentID FROM Comments WHERE PostID = '$postId')";
            if (!mysqli_query($conn, $deleteCommentLikesSql)) {
                throw new Exception('Failed to delete comment likes');
            }

            //delete comments associated to specific post
            $deleteCommentsSql = "DELETE FROM Comments WHERE PostID = '$postId'";
            if (!mysqli_query($conn, $deleteCommentsSql)) {
                throw new Exception('Failed to delete comments');
            }

            //delete likes associated to that post
            $deleteLikesSql = "DELETE FROM PostLikes WHERE PostID = '$postId'";
            if (!mysqli_query($conn, $deleteLikesSql)) {
                throw new Exception('Failed to delete likes');
            }

            //delete the post
            $deletePostSql = "DELETE FROM Posts WHERE PostID = '$postId'";
            if (!mysqli_query($conn, $deletePostSql)) {
                throw new Exception('Failed to delete post');
            }

            //if everything is fine, commit the transaction
            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Post and related data deleted successfully']);
        } catch (Exception $e) {
            //if error occurs rollback changes
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Post ID not provided']);
    }

    //close the database connection
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>
