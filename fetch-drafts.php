<?php

//connect to database through db.php
include 'db.php';

//hardcoded user ID for testing
$userID = mysqli_real_escape_string($conn, $_GET['userID']); //get user id from request

//SQL query to fetch drafts for the specified user
$sql = "SELECT PostID, Title, Content, DateCreated, DateLastModified, Topic FROM Posts WHERE UserID = ? AND IsDraft = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

//array to store fetched drafts
$drafts = array();

//fetch drafts and store them in the array
while ($row = $result->fetch_assoc()) {
    $dateCreated = $row['DateCreated'];
    $dateLastModified = $row['DateLastModified'];
    
    //if DateLastModified is NULL, use DateCreated instead
    if ($dateLastModified === NULL) {
        $dateToDisplay = $dateCreated;
        $isEdited = false; //as DateLastModified is NULL, the draft has not been edited
    } else {
        $isEdited = ($dateCreated != $dateLastModified);
        $dateToDisplay = $isEdited ? $dateLastModified : $dateCreated;
    }
    
    //format the date for display
    $formattedDate = date('d F Y \a\t H:i', strtotime($dateToDisplay)); // e.g., "18 February 2024 at 14:29"

    //construct the display string
    $displayString = "Last Modified: $formattedDate";

    //append '(edited)' if the draft has been modified
    if ($isEdited) {
        $displayString .= ' (edited)';
    }

    //add each draft to the drafts array
    $drafts[] = array(
        'postID' => $row['PostID'], 
        'title' => $row['Title'],
        'body' => $row['Content'],
        'topic' => $row['Topic'], 
        'lastModified' => $displayString 
    );
}

//close the database connection
$stmt->close();
$conn->close();

//prepare the response as JSON
header('Content-Type: application/json');
echo json_encode(array("drafts" => $drafts));
?>
