<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);


// Check if the information was sent by a GET method with ID name
// Security Reasons
if (isset($_GET["ID"])) {

    $id = $_GET['ID'];

    require_once("../includes/dbh.inc.php");
    require_once("../includes/functions.inc.php");

    // Delete The project From the table Associated wiht that ID
    // ProjectDetails | userProject | taskProject
    deleteProject($conn, $id);
} else {

    // Return back to Project page
    header("location: projects.php");
}
