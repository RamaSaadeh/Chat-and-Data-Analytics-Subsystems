<?php

//db connection details
$servername = "localhost";
$username = "host";
$password = "Team017FTW!";
$dbname = "makeitall";

//create connection
$conn = new mysqli($servername, $username, $password, $dbname);

//check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>
