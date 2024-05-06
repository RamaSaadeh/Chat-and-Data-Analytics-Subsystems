<?php

header('Content-Type: application/json');
//connect to database
include 'db.php';



if (isset($_GET['id'])) {
    $postID = mysqli_real_escape_string($conn, $_GET['id']); //get post id from request
    $userID = mysqli_real_escape_string($conn, $_GET['userID']); //get user id from request

    //sql statement to get all comments related to specific post
    $sql = "SELECT c.CommentID, c.PostID, c.UserID, c.CommentContent, c.Likes, c.LastModified, c.DateCreated, u.name AS AuthorName,
            (c.UserID = $userID) AS IsUserOwner,
            CL.UserID IS NOT NULL AS HasLiked,
            (SELECT role FROM users WHERE user_id = $userID) AS IsAdmin
            FROM Comments c
            INNER JOIN users u ON c.UserID = u.user_id
            LEFT JOIN CommentLikes CL ON c.CommentID = CL.CommentID AND CL.UserID = $userID
            WHERE c.PostID = '$postID'";


    //execute query
    $result = $conn->query($sql);

    //check if query was successful
    if ($result) {
        //initialize an array to store comments
        $comments = array();

  
        while ($row = $result->fetch_assoc()) {
            //add each comment to the comments array
            $row['IsUserOwner'] = $row['IsUserOwner'] == 1 ? true : false;
            $row['HasLiked'] = $row['HasLiked'] == 1 ? true : false;
            $row['IsEdited'] = $row['LastModified'] != $row['DateCreated'];
            $row['IsAdmin'] = ($row['IsAdmin'] === 'Admin');
            $comments[] = $row;
        }
       
        //close the connection
        $conn->close();

        //return comments as JSON
        echo json_encode($comments);
    } else {
        //return empty array if failure occurs
        echo json_encode([]);
    }
} else {
    //if postID is not provided, return an error message
    echo json_encode(['error' => 'No postID provided']);
}
?>
