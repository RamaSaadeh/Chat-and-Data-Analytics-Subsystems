<?php


// // Retrieve the project ID sent via POST
$project_ID_toload = $_POST['ID'];

$servername = "localhost";
$username = "host";
$dbpassword = "Team017FTW!";
$database = "makeitall";

$conn = new mysqli($servername, $username, $dbpassword, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tasks_and_staff = array();


$sql = "SELECT proj_name FROM projects WHERE project_id='$project_ID_toload';";

$result = $conn->query($sql);

$projectname = "";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projectname = $row['proj_name'];
    }
}

array_push($tasks_and_staff, $projectname);


// SQL query that fetches all tasks from our specified project 
$sql = "SELECT task_id, task_name, hrs_remaining, status, deadline, notes FROM `tasks` WHERE project_id='$project_ID_toload';";

$result = $conn->query($sql);



$alltasks = array();

if ($result->num_rows > 0) {
    while ($task = $result->fetch_assoc()) {

        $assigned_to = find_staffassigned($conn, $project_ID_toload, $task['task_id']);

        $eachtask = array(
            $task['task_id'],
            $task['task_name'],
            $task['hrs_remaining'],
            $task['status'],
            $task['deadline'],
            $assigned_to,
            $task['notes']
        );
        
        // Add the each indiviudal $task to the $alltasks array
        array_push($alltasks, $eachtask);
    }
}


function find_staffassigned($conn, $project_ID_toload, $task_id) {

    $sql = "SELECT users.user_id, users.name FROM users JOIN task_staff ON task_staff.project_id = '$project_ID_toload' AND task_staff.task_id = '$task_id' AND users.user_id = task_staff.user_id;";

    $result = $conn->query($sql);


    $assigned_to = "";

    if ($result->num_rows > 0) {
        while ($user = $result->fetch_assoc()) {

            $assigned_to .= '#' . $user['user_id'] . ' - ' . $user['name'] . '<br>';

            }
    }

    //test
    // return "John Doe<br>John Doe";
    return $assigned_to;
}





////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// Now Load staff with same connection

// SQl query that finds the team leader for the given project
$sql = "SELECT leader_id FROM `projects` WHERE project_id = '$project_ID_toload';";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($temp = $result->fetch_assoc()) {
        $project_teamldr = $temp['leader_id'];
    }
}




// SQL query that fetches all tasks from our specified project 
$sql = "SELECT users.user_id, users.name, users.role, users.email FROM users JOIN project_staff ON project_staff.user_id = users.user_id WHERE project_staff.project_id = '$project_ID_toload';";

$result = $conn->query($sql);

$allstaff = array();

if ($result->num_rows > 0) {
    while ($staff = $result->fetch_assoc()) {



        $eachstaff = array(
            $staff['user_id'],
            $staff['name'],
            "",
            $staff['email'],
        );

        if ($staff['user_id'] == $project_teamldr) {
            $eachstaff[2] = "Team Leader";
        } else {
            $eachstaff[2] = $staff['role'];
        }
        
        // Add the each indiviudal $task to the $alltasks array
        array_push($allstaff, $eachstaff);
    }
}


array_push($tasks_and_staff, $alltasks);
array_push($tasks_and_staff, $allstaff);

echo json_encode($tasks_and_staff);





$conn->close();
?>