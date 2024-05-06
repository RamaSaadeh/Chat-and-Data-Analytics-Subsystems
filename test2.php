<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "db2";
$username = "db2";
$password = "db2"; // Replace with the actual password
$dbname = "db2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
