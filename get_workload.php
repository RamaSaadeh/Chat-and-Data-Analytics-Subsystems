<?php

$project_ID_toload = $_POST['ID'];


$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);


// // SQL query to fetch number of tasks Completed
// $sql = "SELECT COUNT(DISTINCT task_id) AS count FROM tasks WHERE status = 'Completed' AND project_id = '$project_ID_toload';";

// $result = $conn->query($sql);

$sql = "SELECT users.user_id, users.name FROM users JOIN project_staff ON users.user_id = project_staff.user_id WHERE project_staff.project_id = '$project_ID_toload';";

$result = $conn->query($sql);


$staffhrs = array();
$staffinfo = array();


if ($result->num_rows > 0) {
    while ($staff = $result->fetch_assoc()) {

        $userhrs = find_staffhrs($conn, $project_ID_toload, $staff['user_id']);
        array_push($staffhrs, $userhrs);
        
        
        
        array_push($staffinfo, ($staff['user_id'] . '-  ' .  $staff['name']));
    }
}



function find_staffhrs($conn, $project_ID_toload, $user_id) {

    $sql = "SELECT SUM(hrs_remaining) as total_hrs FROM tasks JOIN task_staff ON tasks.task_id = task_staff.task_id AND tasks.project_id = task_staff.project_id WHERE task_staff.user_id = '$user_id' AND task_staff.project_id = '$project_ID_toload';";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($count = $result->fetch_assoc()) {

            $temp =  $count['total_hrs'];

            }
    }
    return $temp;
}


$temp = array();

array_push($temp, $staffhrs);
array_push($temp, $staffinfo);

echo json_encode($temp);
?>