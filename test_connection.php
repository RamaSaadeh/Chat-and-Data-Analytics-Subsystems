<?php
echo "COB290 Database Connection";

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

// Create connection
$conn = new mysqli($servername, $username, $dbpassword, $database);

/*$servername = "localhost";
$username = "team017";
$dbpassword = "xngk4RgUqJxMjKX3EMak";
$database = "team017";

$conn = new mysqli($servername, $username, $dbpassword, $database);*/

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
