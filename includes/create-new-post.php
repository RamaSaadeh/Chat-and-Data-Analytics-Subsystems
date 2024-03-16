<?php

$servername = "phpmyadmin.sci-project.lboro.ac.uk";
$username = "team001";
$password = "p3NT9hXVPedkjhaqehjL";
$database = "team001";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $postName = $_POST['postname'];
    $postDescription = $_POST['description'];
    $currentDate = date("Y-m-d");
    $author = 'Manager'; // fetch author from login at a later date
    $query = "INSERT INTO posts (postName, postDescription, author, date) VALUES ('$postName', '$postDescription', '$author', '$currentDate')";
    
    if ($conn->query($query) === TRUE) {
        $postID = mysqli_insert_id($conn);

        // Assuming the selected tags are submitted as an array
        if (isset($_POST['tagselect']) && is_array($_POST['tagselect'])) {
            foreach ($_POST['tagselect'] as $selectedTag) {
                $tagQuery = "SELECT tagID FROM tagDetails WHERE tagName = '$selectedTag'";
                $result = $conn->query($tagQuery);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $tagID = $row['tagID'];

                    $insertQuery = "INSERT INTO postTags (postID, tagID) VALUES ($postID, $tagID)";
                    $conn->query($insertQuery);
                }
            }
        }
    }

    $conn->close();
}
header("Location: ../forums.php");

?> 