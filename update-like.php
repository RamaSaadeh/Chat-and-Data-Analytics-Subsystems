<?php
//connect to database
include 'db.php';

//check if request method is a post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //get post ID from the request
    $postID = mysqli_real_escape_string($conn, $_POST['postID']); //get post id from request
    $userID = mysqli_real_escape_string($conn, $_POST['userID']); //get user id from request

   
    
    $newLikeCount = 0; //initialize the variable to store the new like count

    //check if the user has already liked the post
    $checkQuery = "SELECT * FROM PostLikes WHERE PostID = '$postID' AND UserID = '$userID'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        //if user has previously liked the post unlike the post
        $unlikeQuery = "DELETE FROM PostLikes WHERE PostID = '$postID' AND UserID = '$userID'";
        mysqli_query($conn, $unlikeQuery);

        //decrement the LikesCount in the Posts table
        $updateQuery = "UPDATE Posts SET LikesCount = LikesCount - 1 WHERE PostID = '$postID'";
        mysqli_query($conn, $updateQuery);
    } else {
        //user has not liked the post, so like it
        $insertQuery = "INSERT INTO PostLikes (PostID, UserID) VALUES ('$postID', '$userID')";
        mysqli_query($conn, $insertQuery);

        //increment the LikesCount in the Posts table
        $updateQuery = "UPDATE Posts SET LikesCount = LikesCount + 1 WHERE PostID = '$postID'";
        mysqli_query($conn, $updateQuery);
    }

    //query the new like count to return it
    $countQuery = "SELECT LikesCount FROM Posts WHERE PostID = '$postID'";
    $countResult = mysqli_query($conn, $countQuery);
    if ($row = mysqli_fetch_assoc($countResult)) {
        $newLikeCount = $row['LikesCount'];
    }

    //check if request was successful
    if ($countResult) {
        echo json_encode(['success' => true, 'newLikeCount' => $newLikeCount]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to retrieve updated like count']);
    }
} else {
    //if request method is not POST, return error
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
