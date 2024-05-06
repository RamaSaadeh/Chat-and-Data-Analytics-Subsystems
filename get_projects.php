<?php

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);

// SQL query to fetch options from the database
$sql = "SELECT project_id, proj_name FROM `projects`;";
$result = $conn->query($sql);


// Generate values for each option in the dropdown
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . $row['project_id'] . '">' . $row['project_id'] .': '. $row['proj_name'] . '</option>';
}

echo '</select>';

$conn->close();
?>