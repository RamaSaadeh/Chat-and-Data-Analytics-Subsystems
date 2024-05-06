<?php

//connect to database
include 'db.php';

//ensure the request is POST for updating data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //retrieve and sanitize input data
    $postId = isset($_POST['postID']) ? mysqli_real_escape_string($conn, $_POST['postID']) : '';
    $topic = isset($_POST['topic']) ? mysqli_real_escape_string($conn, $_POST['topic']) : '';
    $title = isset($_POST['title']) ? mysqli_real_escape_string($conn, $_POST['title']) : '';
    $content = isset($_POST['content']) ? mysqli_real_escape_string($conn, $_POST['content']) : '';

    //enforce character limits
    if (strlen($topic) > 80 || strlen($title) > 80 || strlen($content) > 1500) {
        echo json_encode(['success' => false, 'message' => 'Character limit exceeded']);
        exit; //stop execution if any limit is exceeded
    }

    //check if all required fields are provided
    if (!empty($postId) && !empty($topic) && !empty($title) && !empty($content)) {
        //SQL query to update post thats changed by user
         $sql = "UPDATE Posts SET Topic = ?, Title = ?, Content = ?, DateLastModified = NOW() WHERE PostID = ?";

        //prepare statement
        if ($stmt = mysqli_prepare($conn, $sql)) {
       
            mysqli_stmt_bind_param($stmt, "sssi", $topic, $title, $content, $postId);

            //execute statement
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update post']);
            }

            //close statement
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }

    //close database connection
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>
