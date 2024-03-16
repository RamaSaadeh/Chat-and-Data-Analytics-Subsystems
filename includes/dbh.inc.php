<?php

$serverName = "localhost";
$dBUsername = "team001";
$dBPassword = "p3NT9hXVPedkjhaqehjL";
$dBName = "team001";

$conn = mysqli_connect($serverName, $dBUsername, $dBPassword, $dBName);  

if (!$conn) {
    die("Connection failed". mysqli_connect_error());
}

?>