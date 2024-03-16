<?php
session_start();

// check if an image file was uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image = $_FILES['image']['tmp_name'];
    $data = addslashes(file_get_contents($image));

    require_once("../includes/dbh.inc.php");
    $id = $_SESSION['userid'];
    $sql = "UPDATE userDetails SET userProfilePicture = '$data' WHERE userID = $id";
    $query_run = mysqli_query($conn, $sql);
    header("Location: ../account.php");
}
