<?php
//connect to database
include 'db.php';

header('Content-Type: application/json');

//check if the form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
 //escape user inputs for security
	$postID = mysqli_real_escape_string($conn, $_POST['id']); 

    
    $userID = mysqli_real_escape_string($conn, $_POST['userID']); //get user id from request
    $commentContent = mysqli_real_escape_string($conn, $_POST['comment']); //get comment content from request

    //insert comment into Comments table in database
    $sql = "INSERT INTO Comments (PostID, UserID, CommentContent) VALUES ('$postID', '$userID', '$commentContent')";
    if (mysqli_query($conn, $sql)) {
        //successful comment inserted
        echo json_encode(['success' => true]);
    } else {
        //error inserting comment
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    //method not allowed
    http_response_code(405); 
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
}

//close db connection
mysqli_close($conn);
?>
