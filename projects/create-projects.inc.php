<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Check if the information was sent by a button with the name "submit"
// Security Reasons
if (isset($_POST["submit"])) {

    // Get The values from the form
    $name = $_POST["createName"];
    $description = $_POST["createDescription"];
    $date = $_POST["createDate"];
    $leaderID = $_POST["createProjectUser"];

    require_once("../includes/dbh.inc.php");
    require_once("../includes/functions.inc.php");

    // Create Project in database and add leader | Projects to userProjects Table
    createProject($conn, $name, $description, $date, $leaderID);
    addUserProject($conn, $leaderID);

} else {
    // Return back to Project page
    header("location: projects.php");
}
