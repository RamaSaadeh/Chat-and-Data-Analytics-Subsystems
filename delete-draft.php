<?php

//connect to database
include 'db.php';

//check if PostID is set and is a valid integer
if (isset($_POST['postID']) && is_numeric($_POST['postID'])) {
    //sanitize input
    $postID = $_POST['postID'];
    
    //prepare SQL statement to delete the draft
    $sql = "DELETE FROM Posts WHERE PostID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postID);

    //execute the delete statement
    if ($stmt->execute()) {
        //draft deleted successfully
        $response = array("success" => true, "message" => "Draft deleted successfully.");
        http_response_code(200); 
    } else {
        //error occurred while deleting the draft
        $response = array("success" => false, "message" => "Error deleting draft.");
        http_response_code(500); 
    }

    //close prepared statement
    $stmt->close();
} else {
    //invalid or missing PostID
    $response = array("success" => false, "message" => "Invalid PostID.");
    http_response_code(400); 
}

//set response header as JSON
header('Content-Type: application/json');

//output the response
echo json_encode($response);

//close database connection
$conn->close();

?>
