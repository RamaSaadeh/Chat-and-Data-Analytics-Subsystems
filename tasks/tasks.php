<?php

// start session
session_start();


if (!isset($_SESSION["valid"])) {
    header("location: ../login.php?action=notLoggedIn");
    exit();
}

// require that database and the functions so that they can be used
require_once("../includes/dbh.inc.php");
require_once("task-functions.inc.php");

// if the project doesn't exist, then redirect to projects (stops user typing invalid id into address bar)
if (projectIDExist($conn, $_GET["projectID"]) == 0) {
    header("location: ../projects/projects.php");
    exit();
}

// get the current project from the header
$currentProject = $_GET['projectID'];

$page = "tasks";

// get the projectID from the header and set the session variable equal to it
$projectID = $_GET["projectID"];
$_SESSION["projectID"] = $projectID;

// get the project name and set the title and session variable to it
$projectName = getProjectName($conn, $projectID)["projectName"];
$title = $projectID . " - " . $projectName;
$_SESSION["title"] = $title;
$currentPage = ucwords($projectName);

// include the header
include_once('../includes/header.inc.php');
?>

<script>
    removeBackgrounds();
    setBackgrounds(1);
</script>

<!-- links for css -->
<link rel="stylesheet" type="text/css" href="styles/taskFilter.css" />
<link rel="stylesheet" type="text/css" href="styles/taskFilterMenu.css">
<link rel="stylesheet" type="text/css" href="styles/taskSearchedList.css">
<link rel="stylesheet" type="text/css" href="styles/taskListItems.css" />
<link rel="stylesheet" type="text/css" href="styles/taskListItemsModal.css">
<link rel="stylesheet" type="text/css" href="styles/taskCreate.css">
<link rel="stylesheet" type="text/css" href="styles/taskAccordion.css">
<link rel="stylesheet" type="text/css" href="../styles/tasks.css">

<main class="container mw-75 p-3" id="task">

    <?php

    // if the user is a manager or the user is the leader of the project then the code below will be executed
    if ($_SESSION["role"] == "manager" || ($_SESSION["role"] == "leader" && checkProjectLeaderID($conn, $_SESSION["userid"], $projectID))) {
        ?>
        <!-- accordian that contains useful information about the tasks in the project -->
        <div class="accordion" id="projectStatsAccordian">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        View Information
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#projectStatsAccordian">
                    <div class="accordion-body">
                        <div class="users-tab-content">
                            <div class="users-tab-content-item">
                                <?php
                                //sql statement to get the required hours and actual completed hours for all the tasks in the project
                                $sql = "SELECT SUM(td.completeHours) AS totalCompleteHours, SUM(td.reqHours) AS totalReqHours
                                    FROM taskDetails td
                                    JOIN taskProject tp ON td.taskID = tp.taskID
                                    WHERE tp.projectID = $currentProject;";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                // get the information from the query
                                $actualHours = $row["totalCompleteHours"];
                                $budgetedHours = $row["totalReqHours"];
                                // change the colour of the actual bar depening on if it is more or less than the required bar
                                if ($actualHours > $budgetedHours) {
                                    $actualBackground = '"#e97777"';
                                } else {
                                    $actualBackground = '"#bcdcbe"';
                                }
                                // format depening on if the captions are plural or not
                                if ($actualHours == 1) {
                                    $actualHoursFormat = $actualHours . " Actual Hour";
                                } else {
                                    $actualHoursFormat = $actualHours . " Actual Hours";
                                }
                                if ($budgetedHours == 1) {
                                    $budgetedHoursFormat = $budgetedHours . " Budgeted Hour";
                                } else {
                                    $budgetedHoursFormat = $budgetedHours . " Budgeted Hours";
                                }
                                ?>
                                <!-- div and canvas for the hours chart -->
                                <div class="users-tab-chart">
                                    <canvas id="hoursChart" width="100" height="100">
                                    </canvas>
                                </div>
                                <!-- chart captions -->
                                <div class="users-tab-chart-caption">
                                    <span class="users-caption">
                                        <?php echo $budgetedHoursFormat ?>
                                    </span>
                                    <span class="users-caption">
                                        <?php echo $actualHoursFormat ?>
                                    </span>
                                </div>
                            </div>

                            <div class="users-tab-content-item">
                                <?php
                                $today = date("Y-m-d");
                                // sql statement to count the total number of tasks for each status
                                $sql = "SELECT 
                                    SUM(CASE WHEN td.taskStatus = 'backlog' THEN 1 ELSE 0 END) AS backlogTasks,
                                    SUM(CASE WHEN td.taskStatus = 'in progress' THEN 1 ELSE 0 END) AS inProgressTasks,
                                    SUM(CASE WHEN td.taskStatus = 'complete' THEN 1 ELSE 0 END) AS completeTasks,
                                    (SELECT COUNT(td.taskID) 
                                    FROM 
                                        taskDetails td 
                                    JOIN 
                                        taskProject tp ON td.taskID = tp.taskID
                                    WHERE 
                                        tp.projectID = $currentProject AND taskDeadline < '$today' AND td.taskStatus != 'Complete')  AS overdueTasks
                                    FROM 
                                        taskDetails td
                                    JOIN 
                                        taskProject tp ON td.taskID = tp.taskID
                                    WHERE 
                                        tp.projectID = $currentProject;";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                // get the information from the query
                                $backlogTasks = $row["backlogTasks"];
                                $inProgressTasks = $row["inProgressTasks"];
                                $completeTasks = $row["completeTasks"];
                                $overdueTasks = $row["overdueTasks"];
                                $totalTasks = $backlogTasks + $inProgressTasks + $completeTasks;
                                // format depending on if the captions are plural or not
                                if ($backlogTasks == 1) {
                                    $backlogTasksFormat = $backlogTasks . " Backlogged Task";
                                } else {
                                    $backlogTasksFormat = $backlogTasks . " Backlogged Tasks";
                                }
                                if ($inProgressTasks == 1) {
                                    $inProgressTasksFormat = $inProgressTasks . " In Progress Task";
                                } else {
                                    $inProgressTasksFormat = $inProgressTasks . " In Progress Tasks";
                                }
                                if ($completeTasks == 1) {
                                    $completeTasksFormat = $completeTasks . " Complete Task";
                                } else {
                                    $completeTasksFormat = $completeTasks . " Complete Tasks";
                                }
                                if ($overdueTasks == 1) {
                                    $overdueTasksFormat = $overdueTasks . " Overdue Task";
                                } else {
                                    $overdueTasksFormat = $overdueTasks . " Overdue Tasks";
                                }
                                ?>
                                <!-- div and canvas for the status chart -->
                                <div class="users-tab-chart">
                                    <canvas id="statusChart" width="100" height="100">
                                    </canvas>
                                    <div class="users-tab-chart-content" style="z-index: 1;">
                                        <span class="users-tab-chart-primary-num" style="color:#354182 !important">
                                            <?php echo $totalTasks ?>
                                        </span>
                                    </div>
                                </div>
                                <!-- captions for the chart -->
                                <div class="users-tab-chart-caption">
                                    <span class="users-caption">
                                        <?php echo $backlogTasksFormat ?>
                                    </span>
                                    <span class="users-caption">
                                        <?php echo $inProgressTasksFormat ?>
                                    </span>
                                    <span class="users-caption">
                                        <?php echo $completeTasksFormat ?>
                                    </span>
                                    <span class="users-caption">
                                        <?php echo $overdueTasksFormat ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <!-- Filter Row -->
    <div class="filter-row container my-3 p-3 shadow rounded w-100 gap-1 gap-lg-3 justify-content-center">
        <div class="filter-mobile">
            <!-- button to open the filter menu -->
            <button class="filter-icon-btn filter-icon" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#filter-offcanvasRight" aria-controls="filter-offcanvasRight" title="Filter Tasks">
                <!-- filter icon -->
                <i class="filter-icon-filter bi bi-filter"></i>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvasRight"
                aria-labelledby="filter-offcanvasRightLabel" style="overflow-y: auto;">
                <div class="offcanvas-header" style="position: sticky; top:0; background: #fff; z-index: 999;">
                    <h5 class="container offcanvas-title" id="filter-offcanvasRightLabel">Filter By</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <!-- form -->
                <div class="offcanvas-body d-flex flex-column gap-5 justify-content-between justify-content-lg-start">
                    <!-- sort by  -->
                    <div id="sort" class="d-flex flex-column justify-content-between">
                        <span class="btn filter-btn filter-sort">Sort</span>
                        <div class="filter-collapse" id="filter-sort-content">
                            <div class="sort-content">
                                <input type="radio" name="sortChoice" id="sort-radio-one" value="`taskDeadline`ASC"
                                    checked>
                                <input type="radio" name="sortChoice" id="sort-radio-two" value="`taskDeadline`DESC">
                                <label for="sort-radio-one" class="radio-box sort-radio-first">
                                    <div class="sort-radio-title">
                                        <span class="sort-radio-icon"></span>
                                        <span>Date (Low-High)</span>
                                    </div>
                                </label>
                                <label for="sort-radio-two" class="radio-box sort-radio-second">
                                    <div class="sort-radio-title">
                                        <span class="sort-radio-icon"></span>
                                        <span>Date(High-Low)</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- filter by date section -->
                    <div id="date" class="d-flex flex-column justify-content-between">
                        <button class="btn filter-btn filter-date" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filter-date-content">
                            Date
                            <i class="bi bi-caret-down"></i>
                        </button>
                        <div class="filter-collapse collapse" id="filter-date-content"
                            data-bs-parent="#filter-offcanvasRight">
                            <!-- dates input -->
                            <div class="date-content">
                                <input type="date" class="date" id="date-from" name="date-from" onkeydown="return false"
                                    oninput="updateDateTo()" placeholder="Deadline Date" required>
                                <input type="date" class="date" id="date-to" name="date-to" onkeydown="return false"
                                    oninput="updateDateFrom()" placeholder="Deadline Date" required>
                            </div>
                        </div>
                    </div>

                    <!-- filter by users section -->
                    <div id="users" class="d-flex flex-column justify-content-between">
                        <button class="btn filter-btn filter-users" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filter-users-content">
                            Users
                            <i class="bi bi-caret-down"></i>
                        </button>
                        <div class="filter-collapse collapse" id="filter-users-content"
                            data-bs-parent="#filter-offcanvasRight">
                            <div class="users-content">
                                <!-- List of User's picked from checkbox -->
                                <div class="users-picked">
                                    <span class="users-picked-title">You selected:
                                        <span id="users-picked-list">
                                        </span>
                                    </span>
                                </div>
                                <div class="users-search">
                                    <div class="users-search-content">
                                        <i class="filter-icon-search bi bi-search"></i>
                                        <!-- filters the users based on the each letter -->
                                        <input id="input-search-id" type="search" class="users-input-search"
                                            onkeyup="filterList('input-search-id','user-search-list')" incremental
                                            placeholder="Search Users...">
                                    </div>
                                    <div id="user-search-list" class="users-search-output">
                                        <ul>

                                            <!-- Pick all users option -->
                                            <li class="itemListValues">
                                                <div>
                                                    <input type="checkbox" id="userPickAll" name="userID[]" value="All"
                                                        class="user-search-value">
                                                    <label for="userPickAll">All</label>
                                                </div>
                                            </li>
                                            <?php
                                            include_once("../includes/dbh.inc.php");
                                            // select all of the users
                                            $sql = 'SELECT * FROM userDetails ORDER BY userID ASC';

                                            $result = $conn->query($sql);

                                            while ($row = $result->fetch_assoc()) {

                                                // User's infromation
                                                $userID = $row["userID"];
                                                $userFirstName = $row["userName"];
                                                $userLastName = $row["userSurname"];

                                                $userName = $userFirstName . " " . $userLastName;

                                                $userEmail = $row["userEmail"];

                                                ?>

                                                <!-- list of users -->
                                                <li class="itemListValues">
                                                    <div>
                                                        <!-- checkbox to select the user -->
                                                        <input type="checkbox" id="user<?php echo $userID ?>"
                                                            name="userID[]" value="<?php echo $userID ?>"
                                                            class="user-search-value">
                                                        <!-- user is the label for the checkbox -->
                                                        <label for="user<?php echo $userID ?>">
                                                            <?php echo $userName ?>
                                                        </label>
                                                    </div>
                                                    <!-- User's ID -->
                                                    <span class="itemListValuesID">
                                                        <?php echo $userID ?>
                                                    </span>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- filter by status section -->
                    <div id="status" class="d-flex flex-column justify-content-between">
                        <button class="btn filter-btn filter-status" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filter-status-content">
                            Status
                            <i class="bi bi-caret-down"></i>
                        </button>
                        <div class="filter-collapse collapse" id="filter-status-content"
                            data-bs-parent="#filter-offcanvasRight">
                            <div class="status-content">
                                <!-- status filter options -->
                                <div class="status-container">
                                    <input class="status-checkbox" type="checkbox" id="status-check-one" value="Backlog"
                                        checked>
                                    <label for="status-check-one" class="status-box-label">
                                        Backlog
                                    </label>
                                </div>
                                <div class="status-container">
                                    <input class="status-checkbox" type="checkbox" id="status-check-two"
                                        value="In Progress" checked>
                                    <label for="status-check-two" class="status-box-label">
                                        In Progress
                                    </label>
                                </div>
                                <div class="status-container">
                                    <input class="status-checkbox" type="checkbox" id="status-check-three"
                                        value="Complete" checked>
                                    <label for="status-check-three" class="status-box-label">
                                        Complete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- buttons to apply or clear the filters -->
                    <div class="filter-btn-content">
                        <!-- apply button applies the chosen filters -->
                        <button class="d-block btn filter-btn-submit" id="filter-apply">
                            APPLY
                        </button>
                        <!-- reset button clears the filter menu -->
                        <button class="d-block btn filter-btn-reset" id="filter-reset">
                            RESET
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- form for searching tasks -->
        <form method="post" class="filter-container-search">
            <div class="filter-search-content">
                <i class="filter-icon-search bi bi-search"></i>
                <!-- input that filters the tasks based on the input after each letter -->
                <input id="filter-input-search-id" name="task_name" type="search" class="filter-input-search"
                    onkeyup="filterList('filter-input-search-id', 'filter-search-list')" incremental
                    placeholder="Search Task Titles...">
            </div>
        </form>
        <?php

        // if the user is a manager or a team leader that leads the project
        if ($_SESSION["role"] == "manager" || ($_SESSION["role"] == "leader" && checkProjectLeaderID($conn, $_SESSION["userid"], $projectID))) {
            ?>
            <!-- display the create new tasks button -->
            <div class="filter-container-new">
                <div class="col d-flex">
                    <button type="button" class="filter-icon-btn filter-icon w-auto h-auto" data-bs-toggle="modal"
                        data-bs-target="#taskCreateModal" title="Create A New Task" id="createTaskBtn"><i
                            class="filter-icon-filter bi bi-journal-plus"></i>
                    </button>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="filter-mobile">
            <button class="filter-icon-btn filter-icon text-decoration-none" onclick="redirectedTasks()"><i
                    class="filter-icon-filter bi bi-x-lg"></i></button>
        </div>
    </div>
    <!-- Content Row -->
    <div class="task-container">
        <div id="filter-search-list" class="task-search-output">
            <?php
            // if the user is a manager or a team leader that leads the project
            if ($_SESSION["role"] == "manager" || ($_SESSION["role"] == "leader" && checkProjectLeaderID($conn, $_SESSION["userid"], $projectID))) {
                ?>
                <!-- display accordian that splits the users personal tasks and everyone elses tasks -->
                <div class="accordion accordion-flush" id="taskItemAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#personalTaskCollapse" aria-expanded="true" aria-controls="collapseOne">
                                Your Tasks
                            </button>
                        </h2>
                        <div id="personalTaskCollapse" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <ul class="taskList" id="taskList">
                                    <?php
                                    // display the personal tasks for the user
                                    $_SESSION['filter'] = 0;
                                    $isTaskPersonal = true;
                                    include("display-tasks.inc.php")

                                        ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#everyoneElseTaskCollapse" aria-expanded="false"
                                aria-controls="collapseTwo">
                                Everyone Else's Tasks
                            </button>
                        </h2>
                        <div id="everyoneElseTaskCollapse" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <ul class="taskList" id="taskList2">
                                    <?php
                                    // display everyone elses tasks
                                    $_SESSION['filter2'] = 0;
                                    $isTaskPersonal = false;
                                    include("display-tasks.inc.php")

                                        ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            // if the user is a member or a team leader that is not leading the project
            else if ($_SESSION["role"] == "member" || ($_SESSION["role"] == "leader" && !checkProjectLeaderID($conn, $_SESSION["userid"], $projectID))) {
                ?>
                    <ul class="taskList" id="taskList">
                        <?php
                        // display the personal tasks of the user
                        $_SESSION['filter'] = 0;
                        $isTaskPersonal = true;
                        include_once("display-tasks.inc.php")

                            ?>
                    </ul>
                <?php
            }
            ?>
        </div>
    </div>
    <!-- button to scroll back up to the top of the screen -->
    <button onclick="topFunction()" id="scrollTop" title="Go to top"><i class="bi bi-arrow-up"></i></button>
    <?php
    // if the user is a manager or a team leader that leads the project
    if ($_SESSION["role"] == "manager" || ($_SESSION["role"] == "leader" && checkProjectLeaderID($conn, $_SESSION["userid"], $projectID))) {
        // include the button to create a new task
        include_once("newTaskBtn.php");
    }
    ?>
</main>

<script>

    // Pressing the return key doesn't force a reload
    $("#filter-input-search-id").keypress(
        function (event) {
            if (event.which == '13') {
                event.preventDefault();
            }
        });
</script>

<script>
    // redirect the user to the projects page
    function redirectedTasks() {
        <?php
        $_SESSION['redirectedTasks'] = true;
        ?>
        location.href = ' <?php echo '/projects/projects.php' . '?redirectedID=' . str_replace(' ', '', $projectID) . '#' . str_replace(' ', '', $projectID); ?>'
    }
</script>

<script>
    // get today's date
    var today = new Date();
    var dd = today.getDate();
    var ddT = today.getDate() + 1;
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    // Check if day is less than 10 to pad it with 0 eg. 1 -> 01 
    if (dd < 10) {
        dd = '0' + dd;
        ddT = '0' + ddT;
    }

    // Check if month is less than 10 to pad it with 0 eg. 9 -> 09
    if (mm < 10) {
        mm = '0' + mm;
    }

    // Put all the values together to makes today's date
    today = yyyy + '-' + mm + '-' + dd;

    // Put all the values together to makes tomorrow's date
    tomorrow = yyyy + '-' + mm + '-' + ddT;

    // get the highest and lowest dates of tasks from the database
    var dateTo = "<?php echo getHighestTaskDate($conn, $currentProject); ?>";
    var dateFrom = "<?php echo getLowestTaskDate($conn, $currentProject); ?>";

    // set the min and max dates for the date filter based on the highest and lowest task dates in the project
    document.querySelector("#date-from").setAttribute("min", dateFrom);
    document.querySelector("#date-from").setAttribute("value", dateFrom);
    document.querySelector("#date-from").setAttribute("max", dateTo);
    document.querySelector("#date-to").setAttribute("min", dateFrom);
    document.querySelector("#date-to").setAttribute("value", dateTo);

    // function to update max date choice
    function updateDateTo() {
        var minDate = document.getElementById("date-from").value;
        document.getElementById("date-to").setAttribute("min", minDate);
        document.getElementById("date-from").setAttribute("max", minDate)
    }

    // function to update min date choice
    function updateDateFrom() {
        var maxDate = document.getElementById("date-to").value;
        document.getElementById("date-from").setAttribute("max", maxDate);
    };

</script>

<script>
    // function that is used to filter the task items
    function filterTasks() {
        // get the filter values 
        var sortValue = document.querySelector('input[name="sortChoice"]:checked').value;
        var dateFrom = document.querySelector("#date-from").value;
        var dateTo = document.querySelector("#date-to").value;
        // get the values from all the user checkboxes that are checked
        const checked = document.querySelectorAll('.user-search-value[type="checkbox"]:checked');
        // make an array from all of the values
        var users = Array.from(checked).map(x => x.value);

        // set the value of the users filter
        if (users.length == 0) {
            users = "Empty";
        } else {
            users.forEach((e) => {
                if (e.value == "All") {
                    users = "All";
                }
            })
        }

        // get all of the status checkboxes that are checked
        const checkedStatus = document.querySelectorAll('.status-checkbox[type="checkbox"]:checked');
        // make an array of all the values of the checked check boxes
        var status = Array.from(checkedStatus).map(x => x.value);

        status = status.map(function (item) {
            return item.replace(/ /g, '__');
        });

        // display the tasks with the filter applied for personal and non-personal tasks
        $("#taskList").load("taskFilter.php?sort=" + sortValue + "&dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&users=" + users + "&status=" + status + "&isTaskPersonal=true");
        $("#taskList2").load("taskFilter.php?sort=" + sortValue + "&dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&users=" + users + "&status=" + status + "&isTaskPersonal=false");
    }

    // when apply is clicked
    $('#filter-apply').click(function () {

        // apply the tasks filter
        filterTasks()

        // button to close the off canvas element 
        let closeCanvas = document.querySelector('[data-bs-dismiss="offcanvas"]');
        closeCanvas.click();
    })

    // function to reset the filter menu when the reset button is pressed
    function resetTasks() {
        $('input[name=sortChoice]').prop('checked', false);
        // set all of the radio buttons and checkboxes back to their default state
        document.getElementById("sort-radio-one").checked = true;
        document.getElementById("date-from").value = dateFrom;
        document.getElementById("date-to").value = dateTo;
        document.querySelector("#status-check-one").checked = true;
        document.querySelector("#status-check-two").checked = true;
        document.querySelector("#status-check-three").checked = true;

        // deselect all of the users
        unCheckAll();

        // set the search fiel empty
        document.querySelector("#input-search-id").value = "";
        document.querySelectorAll(".itemListValues").forEach(element => {
            element.style.display = "none";
        });

        // Make 'all' visible | do all the neccessary procedures
        document.querySelectorAll(".itemListValues")[0].style.display = "flex"
        selectAll();

        // get the value to sort by from the document
        var sortValue2 = document.querySelector('input[name="sortChoice"]:checked').value;
        // get the date filters from the document
        var dateFrom2 = document.querySelector("#date-from").value;
        var dateTo2 = document.querySelector("#date-to").value;

        // get the values from all the user checkboxes that are checked
        const checked2 = document.querySelectorAll('.user-search-value[type="checkbox"]:checked');
        // make an array from all of the values
        var users2 = Array.from(checked2).map(x => x.value);

        // set the value of the users filter
        if (users2.length == 0) {
            users2 = "Empty";
        } else {
            checked2.forEach((e) => {
                if (e.value == "All") {
                    users2 = "All";
                }
            })
        }

        // get all of the values of the status check boxes that are checked
        const checkedStatus2 = document.querySelectorAll('.status-checkbox[type="checkbox"]:checked');
        // make an array from the values from these checkboxes
        var status2 = Array.from(checkedStatus2).map(x => x.value);

        status2 = status2.map(function (item) {
            return item.replace(/ /g, '__');
        });

        // display the tasks with the filter applied for personal and non-personal tasks
        $("#taskList").load("taskFilter.php?sort=" + sortValue2 + "&dateFrom=" + dateFrom2 + "&dateTo=" + dateTo2 + "&users=" + users2 + "&status=" + status2 + "&isTaskPersonal=true");
        $("#taskList2").load("taskFilter.php?sort=" + sortValue2 + "&dateFrom=" + dateFrom2 + "&dateTo=" + dateTo2 + "&users=" + users2 + "&status=" + status2 + "&isTaskPersonal=false");
    }

    // when reset is clicked
    $('#filter-reset').click(function () {

        // reset the filter menu
        resetTasks()

        // button to close the off canvas
        let closeCanvas = document.querySelector('[data-bs-dismiss="offcanvas"]');
        closeCanvas.click();
    })

</script>

<script>

    // set values to variables
    var input2 = document.getElementById('input-search-id'); // search input field
    var ul2 = document.getElementById("user-search-list"); // list of searched users
    var li2 = ul2.getElementsByTagName('li'); // individual list items

    // Set 'all' checkbox to show
    li2[0].style.display = "flex";

    // on input in search field
    input2.addEventListener('input', (e) => {
        // if the input length is less than 0, hide every list item
        if (e.currentTarget.value.length > 0) { } else {
            for (i = 0; i < li2.length; i++) {
                li2[i].style.display = "none";
            }
        }
    });

    // on search in search field
    input2.addEventListener("search", (element) => {
        // if input length is 0 show the first item - 'All'
        if (element.currentTarget.value.length == 0) {
            document.querySelectorAll(".itemListValues")[0].style.display = "flex"
        }
    })

    // function that checks every checkbox
    // removes every selected value in list
    function checkAll() {
        document.querySelectorAll(".user-search-value").forEach((e) => {
            e.checked = true;
        })
        document.getElementById("users-picked-list").querySelectorAll(".users-picked-values").forEach(element => {
            element.remove();
        });

    }

    // function that unchecks every checkbox
    // removes every selected value in list
    function unCheckAll() {
        document.querySelectorAll(".user-search-value").forEach((e) => {
            e.checked = false;
        })

        document.getElementById("users-picked-list").querySelectorAll(".users-picked-values").forEach(element => {
            element.remove();
        });
    }

    // checks for if the all checkbox is clicked
    document.querySelector("#userPickAll").addEventListener("click", () => {

        // checks everything if checked
        // uncheck everythign if unchecked
        if (document.querySelector('#userPickAll').checked) {
            checkAll();
        } else {
            unCheckAll();
        }

    })

    // set values to variables
    var input3 = document.getElementById('filter-input-search-id'); // search input field
    var ul3 = document.getElementById("filter-search-list"); // list of searched projects
    var li3 = ul3.getElementsByTagName('li'); // individual list items

    // on input in search field
    input3.addEventListener('input', (e) => {
        // if the input length is less than 0, show every list item
        if (e.currentTarget.value.length > 0) { } else {
            for (i = 0; i < li3.length; i++) {
                li3[i].style.display = "flex";
            }
        }
    });

    // function for flitering a given list with a given input
    function filterList(userInput, userUl) {

        var input, filter, ul, li, label, i, txtValue;
        input = document.getElementById(userInput); // text the user types
        var startPointer;
        ul = document.getElementById(userUl); // list of items
        li = ul.getElementsByTagName('li'); // individual list items

        // Checking if the filtering is being used for the user search or task search
        if (input.id == "input-search-id") {
            startPointer = 1;
        }
        else if (input.id == "filter-input-search-id") {
            startPointer = 0;
        }

        // if input is not empty, set everything to toUpperCase
        // makes search case insensitive
        if (input) {
            filter = input.value.toUpperCase();
        } else {
            filter = "";
        }

        li[0].style.display = "flex"; // make first element always visible - 'all'

        // check if input text is not empty
        if (input.value.length > 0) {

            for (i = startPointer; i < li.length; i++) {

                // user search
                if (userInput == "input-search-id") {
                    // get user's name
                    label = li[i].getElementsByTagName("label")[0];
                    txtValue = label.textContent || label.innerText;
                }
                // project item search
                else if (userInput == "filter-input-search-id") {
                    // get task's name and id
                    label = li[i].getElementsByTagName("span")[1];
                    label2 = li[i].getElementsByTagName("span")[0];
                    txtValue = label.textContent || label.innerText;
                    txtValue2 = label2.textContent || label2.innerText;
                }

                // split filter by spaces
                let filters = filter.split(" ")

                filters = filters.filter(f => f.length)

                let shouldDisplay = true
                // test each filter and store true only if string contains all filter
                filters.forEach(filt => {
                    shouldDisplay = shouldDisplay && txtValue.toUpperCase().includes(filt);
                })

                if (userInput == "filter-input-search-id") {

                    // test each filter and store true only if string contains all filter
                    let shouldDisplay2 = true
                    filters.forEach(filt => {
                        filt = filt.replace(/\s/g, '')
                        txtValue2 = txtValue2.replace(/\s/g, '')
                        shouldDisplay2 = shouldDisplay2 && txtValue2 == filt;
                    })

                    // update visibility
                    // set visible if the string include all filters
                    // or if there is no filter
                    li[i].style.display = (shouldDisplay || shouldDisplay2 || filters.length === 0) ? "flex" : "none";
                }
                else if (userInput == "input-search-id") {
                    // update visibility
                    // set visible if the string include all filters
                    // or if there is no filter
                    li[i].style.display = (shouldDisplay || filters.length === 0) ? "flex" : "none";
                }
            }
        }
        // make sure 'all' is always visible even when deleting characters
        else if (input.value.length = 1) {
            li[0].style.display = "flex";
        }
        // if the length of the user's input is 0 or less than 0 somehow
        else {
            for (i = 0; i < li.length; i++) {

                if (userInput == "input-search-id") {
                    li[i].style.display = "none";
                } else if (userInput == "filter-input-search-id") {
                    li[i].style.display = "flex";
                }


            }
        }
    }
</script>

<script>
    let mybutton = document.getElementById("scrollTop");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function () {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
</script>

<script>
    const userCheckbox = document.querySelectorAll('.user-search-value');
    const usersList = document.querySelector("#users-picked-list");

    // Loop through the checkboxes of users
    userCheckbox.forEach((e) => {
        // when a change occurs on a given checkbox
        e.addEventListener("change", () => {
            const searchResults = usersList.querySelectorAll(".users-picked-values");
            const userID = e.id;  // userID of given checkbox
            var label = e.nextElementSibling.innerText; // getting user's name for Checked box


            var checkedValue;

            // Check if all was selected or not
            document.querySelectorAll("#userPickAll").forEach(element => {
                if (element.checked != undefined) {
                    checkedValue = element.checked
                }

            });

            // if the element was checked
            if (e.checked) {
                if (checkedValue == true && e.value != "All") {
                } else {

                    // if name is not already written in selection list, write it
                    if (!searchResults.forEach((name) => {
                        if (name.innerText === label) {
                            return true
                        }
                    })) {

                        // Setting tag
                        let tag = "a";

                        // Setting class
                        let className = "users-picked-values";

                        // Creating the elements 
                        let elem = document.createElement(tag);

                        // Give element ID
                        elem.id = userID;

                        // Give element function for onclick
                        elem.setAttribute("onclick", uncheckValue);
                        elem.onclick = uncheckValue;

                        // Give element text and class
                        elem.innerText = label;
                        elem.classList.add(className);

                        // Add the element to the body 
                        usersList.appendChild(elem);

                    }
                }


            }
            // if the element was unchecked
            else if (!e.checked) {

                // if the all box wasn't checked but the all box was selected
                if (checkedValue == true && e.value != "All") {

                    // uncheck the all box
                    document.querySelectorAll(".user-search-value")[0].checked = false;
                    $("#userPickAll").prop("checked", false);

                    document.querySelectorAll(".user-search-value").forEach(element => {
                        if (element.checked == true) {

                            // Setting label as name of element
                            var label = element.nextElementSibling.innerText;

                            // Setting tag
                            let tag = "a";

                            // Setting class
                            let className = "users-picked-values";

                            // Creating the elements 
                            let elem = document.createElement(tag);

                            // Give element ID
                            elem.id = "user" + element.value;

                            // Give element function for onclick
                            elem.setAttribute("onclick", uncheckValue);
                            elem.onclick = uncheckValue;

                            // Give element text and class
                            elem.innerText = label;
                            elem.classList.add(className);

                            // Add the element to the body 
                            usersList.appendChild(elem);

                        }
                    });;
                }

                // Check in the names of checked boxes for if:
                //      the name of the item and the checkbox match and the id's match up
                //      the name of the item is all 
                searchResults.forEach(name => {
                    if (name.innerText === label && userID === name.id || name.innerText == "All") {
                        name.remove();
                    }
                })



            }
        })
    })
</script>

<script>
    function uncheckValue() {
        // loops through the checkboxes
        userCheckbox.forEach((box) => {
            // if the unchecked value is 'All', everything is unchecked and the value all in the text is removed
            if (this.innerText == "All") {
                unCheckAll();
                document.querySelectorAll(".users-picked-values").forEach((e) => {
                    e.innerText = ""
                    e.remove();
                    document.getElementById("input-search-id").value = "";
                })
            }
            // If the id of the clicked element matches the id of the checked box then:
            // it is unchecked 
            // The name of it in text is removed
            else if (this.id === box.id) {
                box.checked = false;
                this.innerText = "";
                this.remove();
                document.getElementById("input-search-id").value = "";
            }
        })
    }
</script>


<script>

    // ensure that the canvases are loaded before trying to render the charts
    document.addEventListener('DOMContentLoaded', function () {
        // get the canvas elements
        var hours = document.querySelector('#hoursChart').getContext('2d');
        var status = document.querySelector('#statusChart').getContext('2d');

        // options for the bar chart
        var optionsBar = {
            indexAxis: 'y',
            title: {
                display: false
            },
            scales: {
                y: {
                    ticks: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
            },
            animation: {
                duration: 750,
                animateRotate: false,
                animateScale: true
            }

        };

        // options for the doughnut chart
        var optionsDoughnut = {
            title: {
                display: false
            },
            plugins: {
                legend: {
                    display: false
                },
            },
            animation: {
                duration: 750,
                animateRotate: false,
                animateScale: true
            }

        };

        // create the hours chart
        var hoursChart = new Chart(hours, {
            type: 'bar',
            data: {
                labels: [
                    'Budgeted',
                    'Actual',
                ],
                datasets: [{
                    data: [<?php echo $budgetedHours ?>, <?php echo $actualHours ?>],
                    backgroundColor: [
                        '#98A8F8', // Completed
                        <?php echo $actualBackground ?> // Pending
                    ],
                    hoverOffset: 4,
                }]
            },
            options: optionsBar
        });

        // create the status chart
        var statusChart = new Chart(status, {
            type: 'doughnut',
            data: {
                labels: [
                    'Backlogged',
                    'In Progress',
                    'Complete',
                    'Overdue'
                ],
                datasets: [{
                    data: [<?php echo $backlogTasks ?>, <?php echo $inProgressTasks ?>, <?php echo $completeTasks ?>, <?php echo $overdueTasks ?>],
                    backgroundColor: [
                        '#5B5B5B', // Held
                        '#bcdcbe', // Pending
                        '#98a8f8', // Success
                        '#e97777' // Error
                    ],
                    hoverOffset: 4,
                    cutout: "67.5%",
                }]
            },
            options: optionsDoughnut
        });
    });
</script>

<script>

    function selectAll() {
        // Initalise the checking of the all checkbox
        // Check the 'All' checkbox
        document.getElementById("userPickAll").checked = true;

        // Check all the checkboxes
        checkAll();

        // Setting tag
        let tag = "a";

        // Setting class
        let className = "users-picked-values";

        // Creating the elements 
        let elem = document.createElement(tag);

        // Give element ID
        elem.id = "userPickAll";

        // Give element function for onclick
        elem.setAttribute("onclick", uncheckValue);
        elem.onclick = uncheckValue;

        // Give element text and class
        elem.innerText = "All";
        elem.classList.add(className);

        // Add the element to the body 
        usersList.appendChild(elem);
    }

    selectAll();


</script>

<?php
// include the footer
include_once('../includes/footer.inc.php');
?>