<?php

$new_projname = $_POST['name'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);



// SQL query to fetch all projects, so we know what next ID available is
$sql = "SELECT project_id FROM projects ORDER BY project_id;";

$result = $conn->query($sql);

$counter = 1;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        if($counter != (int)$row['project_id']){
            break;
        } else{
            $counter = $counter + 1;
        }
    }
}


// SQL to enter new project into database
$sql = "INSERT INTO projects (project_id, proj_name, leader_id) VALUES ('$counter', '$new_projname', '0');";

$result = $conn->query($sql);

echo json_encode($counter);

$conn->close();
?>
