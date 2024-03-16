<?php

if (isset($_POST['submit'])) {
    $comment = $_POST['comment'];
    $currentDate = date('Y-m-d');

    $servername = "phpmyadmin.sci-project.lboro.ac.uk";
    $username = "team001";
    $password = "p3NT9hXVPedkjhaqehjL";
    $database = "team001";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn = new mysqli($servername, $username, $password, $database);


    $query = "INSERT INTO commentDetails (comment, commentAuthor, date) VALUES ('$comment', 'Commenter', '$currentDate' )";
    $conn->query($query);

    $commentID = mysqli_insert_id($conn);
    $postID = $postID = urldecode($_GET['postID']);

    $query2 = "INSERT INTO postComments (commentID, postID) VALUES ('$commentID','$postID')";
    $conn->query($query2);

    $conn->close();
} 
header("Location: ../demo-post.php?postID=" . urlencode($postID));
?>
