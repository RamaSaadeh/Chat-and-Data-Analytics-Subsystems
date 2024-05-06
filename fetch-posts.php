<?php

header('Content-Type: application/json');

include 'db.php';

$userID = mysqli_real_escape_string($conn, $_GET['userID']); //get user id from request

//query to get valid post information from Posts table. ALso get formatted date
$sql = "SELECT p.PostID, p.Title, p.Content, 
        p.DateCreated, 
        DATE_FORMAT(p.DateCreated, '%M %d, %Y') as FormattedDateCreated, 
        p.DateLastModified,
        DATE_FORMAT(p.DateLastModified, '%M %d, %Y') as FormattedDateLastModified, 
        p.IsDraft, p.LikesCount, p.Topic, u.name as AuthorName,
        CASE WHEN pl.PostID IS NOT NULL THEN 'true' ELSE 'false' END as IsLiked
        FROM Posts p
        INNER JOIN users u ON p.UserID = u.user_id
        LEFT JOIN PostLikes pl ON p.PostID = pl.PostID AND pl.UserID = $userID
        WHERE p.IsDraft = 0
        ORDER BY p.PostID DESC";



$result = $conn->query($sql);

//store list of posts in an array
$posts = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        //ensure the IsLiked attribute is correctly read as a boolean 
        $row['IsLiked'] = $row['IsLiked'] === 'true' ? true : false;
        
        //determine which date to use and if the post has been edited (if last modified has value use this, else use date created)
        if (!empty($row['DateLastModified']) && $row['DateLastModified'] != $row['DateCreated']) {
            $row['DisplayDate'] = $row['FormattedDateLastModified'] . ' (edited)';
        } else {
            $row['DisplayDate'] = $row['FormattedDateCreated'];
        }
        unset($row['FormattedDateLastModified']); 
        
        $posts[] = $row; //add the row to the posts array

    }
    echo json_encode($posts); 
} else {
    echo json_encode([]); //if no posts found, return an empty array
}

//close database connection
$conn->close();
?>
