<?php

header('Content-Type: application/json');

//connect to database
include 'db.php';


//check if the Post ID is in the request
if (isset($_GET['id'])) {
    //sanitize the input to prevent SQL injection
    $userID = mysqli_real_escape_string($conn, $_GET['userID']); //get user id from request
    $postId = mysqli_real_escape_string($conn, $_GET['id']);
    
    //query to retrieve the details of the specific post based on its ID
    $sql = "SELECT p.PostID, p.Title, p.Content, 
            DATE_FORMAT(p.DateCreated, '%M %d, %Y') as FormattedDateCreated, 
            p.DateLastModified, p.IsDraft, p.LikesCount, p.Topic, u.name AS AuthorName,
            (p.UserID = ?) AS IsUserOwner,
            EXISTS(SELECT 1 FROM PostLikes pl WHERE pl.PostID = p.PostID AND pl.UserID = ?) AS IsLiked,
            (SELECT role FROM users WHERE user_id = ?) AS IsAdmin
            FROM Posts p
            INNER JOIN users u ON p.UserID = u.user_id
            WHERE p.PostID = ?";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $userID, $userID, $userID, $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc(); //fetch the post details

        //convert the IsUserOwner and IsLiked values from integer/exists result to boolean
        $post['IsUserOwner'] = (bool)$post['IsUserOwner'];
        $post['IsLiked'] = (bool)$post['IsLiked'];
        $post['IsAdmin'] = ($post['IsAdmin'] === 'Admin');

        //format DateLastModified if it's not NULL and different from DateCreated
        if (!is_null($post['DateLastModified'])) {
            //check if DateLastModified is different from DateCreated
            if ($post['DateLastModified'] !== $post['DateCreated']) {
                $formattedDateLastModified = date('F d, Y', strtotime($post['DateLastModified'])) . " (edited)";
                $post['DateLastModified'] = $formattedDateLastModified;
            } else {
                $post['DateLastModified'] = $post['FormattedDateCreated']; //use DateCreated if dates are the same
            }
        } else {
            $post['DateLastModified'] = $post['FormattedDateCreated']; //use DateCreated if DateLastModified is NULL
        }

        //prepare and output the response
        echo json_encode([
            'success' => true,
            'data' => $post
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Post not found']); //if post not found, return error
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Post ID not provided']); //if post id not provided from url, return an error
}

//close database connection
$conn->close();
?>
