<?php

// if tasks exist in the query
if (mysqli_num_rows($result) != 0) {

    // include the database and functions so they can be used in the file
    include_once("../includes/dbh.inc.php");
    include_once("../includes/functions.inc.php");

    // for every row in the query result
    while ($row = $result->fetch_assoc()) {

        // get all the information about the task for each row
        $taskID = $row["taskID"];
        $taskName = $row["taskName"];
        $taskDesc = $row["taskDesc"];
        $taskStatus = $row["taskStatus"];
        $actualTaskStatus = $row["taskStatus"];
        $hours = $row["reqHours"];
        $completeHours = $row["completeHours"];
        $taskDeadline = $row["taskDeadline"];
        $taskDeadlineFormat = date_format(date_create($taskDeadline), "d/m/Y");
        $taskPersonID = $row["userID"];
        $userID = $_SESSION["userid"];
        $taskPersonName = $row["userName"];
        $projectID = $row["projectID"];

        $currentPage = "Project" . $projectID;

        // get todays date
        $today = date("Y/m/d");
        $today = strtotime($today);
        $taskDeadlineCheck = strtotime($taskDeadline);

        // set the $taskStatus variable to "Overdue" if the deadline is in the past
        if ($today > $taskDeadlineCheck && $taskStatus != "Complete") {
            $taskStatus = "Overdue";
        }

        // set the status class for the colour of the deadline container based on the task status
        $taskStatusClass;
        if ($taskStatus == "Backlog") {
            $taskStatusClass = "task-deadline-backlog-bg";
        } else if ($taskStatus == "In Progress") {
            $taskStatusClass = "task-deadline-progress-bg";
        } else if ($taskStatus == "Overdue") {
            $taskStatusClass = "task-deadline-overdue-bg";
        } else if ($taskStatus == "Complete") {
            $taskStatusClass = "task-deadline-complete-bg";
        }
        ?>
        <!-- Individual Tasks Container -->
        <li class="taskListItems" id="task<?php echo $taskID ?>">
            <div class="task-card container shadow rounded ">
                <div class="task-card-content w-100 h-100">
                    <div class="btn task-card-btn" tabindex="-1">
                        <div class="task-info-content flex-column justify-content-start w-100 text-start">
                            <!-- task ID -->
                            <span class="task-info-content-id" id="<?php echo $taskID ?>" style="
                            <?php
                            if ($_SESSION['role'] !== 'member') {
                                echo "right: 0";
                            } else if ($_SESSION['role'] === 'member') {
                                echo "left: 0";
                            }
                            ?>">
                                <?php echo $taskID ?>
                            </span>
                            <!-- task name -->
                            <span class="task-info-content-title" id="<?php echo $taskName ?>">
                                <?php echo ucfirst($taskName) ?>
                            </span>
                            <!-- user assigned to task -->
                            <span class="task-info-content-user" id="<?php echo $taskPersonName ?>">
                                <?php echo $taskPersonName ?>
                            </span>
                            <!-- task description -->
                            <span class="task-info-content-desc-short">
                                <?php echo ucfirst(truncateText($taskDesc, 50)) ?>
                            </span>
                        </div>
                        <!-- task deadline and hours information -->
                        <div class="task-deadline-content <?php echo $taskStatusClass ?>">
                            <div class="task-deadline-content-group">
                                <span class="task-deadline-content-title">Deadline</span>
                                <!-- task deadline -->
                                <span class="task-deadline-content-date">
                                    <?php echo $taskDeadlineFormat ?>
                                </span>
                            </div>
                            <!-- hours -->
                            <div class="hours-container">
                                <span class="hours-value">
                                    <?php echo $completeHours . '/' . $hours ?>
                                </span>
                                <span class="hours-caption">hr</span>
                            </div>
                        </div>
                    </div>
                    <?php
                    // display the button to advance the task status if the status is not complete and the user is the owner of the task
                    if ($_SESSION['role'] === 'member' || isTaskCurrentUser($conn, $taskID, $userID)) {
                        if ($taskStatus == "Complete") {
                        } else {
                            ?>
                            <button type="submit" class="task-info-content-transitionState btn"
                                data-current-state="<?php echo $actualTaskStatus ?>" id="taskTransitionBtn<?php echo $taskID ?>">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <?php
                        }
                    } else if ($_SESSION['role'] !== 'member') {
                    }
                    ?>
                    <?php
                    // allow the user to click on the hours and access the add worked hours modal if they are the task owerner and the task status is "In Progress"
                    if ($_SESSION['role'] === 'member' || isTaskCurrentUser($conn, $taskID, $userID)) {
                        if ($actualTaskStatus == "Complete" || $actualTaskStatus == "Backlog") {
                        } else {
                            ?>
                            <!-- button to add worked hours -->
                            <button class="complete-hours-edit text-center" id="editHoursBtn<?php echo $taskID ?>" type="button"
                                data-bs-toggle="modal" data-bs-target="#editHoursModal<?php echo $taskID ?>">
                            </button>
                            <?php
                        }
                    } else if ($_SESSION['role'] !== 'member') {
                    }
                    ?>
                    <!-- allows the user to click on the task and view its information -->
                    <button class="task-card-caption-overlay text-center m-0 w-100 h-100" id="taskModalBtn<?php echo $taskID ?>"
                        type="button" data-bs-toggle="modal" data-bs-target="#taskModal<?php echo $taskID ?>">

                    </button>
                </div>
            </div>
        </li>
        <!-- modal to display information about a task -->
        <div class="modal fade" id="taskModal<?php echo $taskID ?>" tabindex="-1" aria-labelledby="TaskModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- View Modal Header -->
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- View Modal Content -->
                    <div class="modal-body">
                        <div class="container d-flex flex-column px-5 text-center">
                            <div class="d-flex flex-column gap-2 my-3">
                                <!-- display task name -->
                                <p id="taskName">
                                    <?php echo ucwords($taskName) ?>
                                </p>
                                <!-- display task description -->
                                <p id="taskDesc">
                                    <?php echo $taskDesc ?>
                                </p>
                                <hr>
                                <!-- table to display information about the task -->
                                <table class="table table-borderless text-center" id="taskInfoTable">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Due:</th>
                                            <td>
                                                <?php echo $taskDeadlineFormat ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Status:</th>
                                            <td>
                                                <?php
                                                if ($taskStatus == "Overdue") {
                                                    echo $actualTaskStatus . " | " . $taskStatus;
                                                } else {
                                                    echo $taskStatus;
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Owner:</th>
                                            <td>
                                                <?php echo $taskPersonName . ' (' . $taskPersonID . ")" ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Hours:</th>
                                            <td>
                                                <?php echo $hours ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                    // if the user is a manager or the project team leader then display buttons to edit or delete a task 
                    if ($_SESSION['role'] === 'manager' || ($_SESSION['role'] === 'leader' && checkProjectLeaderID($conn, $_SESSION["userid"], $projectID))) {
                        ?>
                        <!-- View Modal Footer -->
                        <div class="modal-footer d-flex justify-content-center align-items-center">
                            <div class="row w-100 d-flex flex-row gap-3">
                                <div class="col">
                                    <!-- edit button -->
                                    <button type="button" class="btn btn-task btn-task-edit w-100" data-bs-toggle="modal"
                                        data-bs-target="#taskEditModal<?php echo $taskID ?>">Edit
                                    </button>
                                </div>
                                <div class="col">
                                    <!-- delete button -->
                                    <button type="button" class="btn btn-task btn-task-delete w-100" data-bs-toggle="modal"
                                        data-bs-target="#taskDeleteModal<?php echo $taskID ?>">Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        // create the modals to delete or edit the task if the user is a manager or the team leader of the project
        if ($_SESSION['role'] === 'manager' || ($_SESSION['role'] === 'leader' && checkProjectLeaderID($conn, $_SESSION["userid"], $projectID))) {
            ?>
            <div class="modal fade" id="taskDeleteModal<?php echo $taskID ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <!-- modal header -->
                        <div class="modal-header border-0 taskDeleteHeader">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <!-- bootstrap icon -->
                            <i class="bi bi-patch-exclamation taskDeleteHeaderIcon"></i>
                        </div>
                        <div class="modal-body taskDeleteBody">
                            <!-- contents of the modal asking the user to if they want to delete the task  -->
                            <p class="taskDeleteBodyTitle">Are You Sure?</p>
                            <p class="taskDeleteBodySubtitle">Are you sure you want to delete this task? This process cannot be
                                undone.</p>
                        </div>
                        <div class="modal-footer border-0">
                            <div class="row w-100 d-flex flex-row gap-3">
                                <div class="col">
                                    <!-- cancel button -->
                                    <button type="button" class="btn btn-taskEditDelete btn-taskCancel w-100"
                                        data-bs-target="#taskModal<?php echo $taskID ?>" data-bs-toggle="modal">Cancel</button>
                                </div>
                                <div class="col">
                                    <!-- delete button that opens "delete-task.inc.php" and appeands the task ID to the header -->
                                    <a type="button" class="btn btn-taskEditDelete btn-taskDeleteConfirm w-100"
                                        id="taskDeleteConfirm<?php echo $taskID ?>"
                                        href="delete-task.inc.php?taskID=<?php echo $taskID; ?>&projectID=<?php echo $projectID ?>&userID=<?php echo $userID ?>">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal to edit the task information -->
            <div class=" modal fade" id="taskEditModal<?php echo $taskID ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0 taskEditHeader">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body taskEditBody text-box-shadow-on" id="taskEditBody<?php echo $taskID ?>">
                            <!-- edit title -->
                            <div class="taskEditBodyAll taskEditBody-Name">
                                <p class="taskEditBodyTitle">Task Name</p>
                                <input type="text" class="taskEditBodyInput taskEditBodyInput-Name"
                                    id="editTaskName<?php echo $taskID ?>" placeholder="Name" minlength="5" maxlength="32"
                                    name="editName" value='<?php echo $taskName ?>' required>
                            </div>
                            <!-- edit description -->
                            <div class="taskEditBodyAll taskEditBody-Desc">
                                <p class="taskEditBodyTitle">Task Desc</p>
                                <textarea class="taskEditBodyInput taskEditBodyInput-Desc" id="editTaskDesc<?php echo $taskID ?>"
                                    minlength="5" maxlength="512" placeholder="Description" name="editDescription"
                                    required><?php echo $taskDesc ?></textarea>
                            </div>
                            <!-- edit status -->
                            <div class="taskEditBodyAll taskEditBody-Status">
                                <p class="taskEditBodyTitle">Task Status</p>
                                <select class="taskEditBodyInput taskEditBodyInput-Status" id="editTaskStatus<?php echo $taskID ?>"
                                    name="editStatus" required>
                                    <option value="Backlog">Backlog</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Complete">Complete</option>
                                </select>
                            </div>
                            <!-- edit deadline -->
                            <div class="taskEditBodyAll taskEditBody-Date">
                                <p class="taskEditBodyTitle">Task Deadline</p>
                                <input type="date" class="taskEditBodyInput taskEditBodyInput-Date"
                                    id="editTaskDate<?php echo $taskID ?>" onkeydown="return false" placeholder="Deadline"
                                    name="editDeadline" value=<?php echo $taskDeadline ?> max=<?php echo getProjectDeadline($conn, $projectID); ?> required>
                                <p class="taskEditBodyError" id="editTaskError<?php echo $taskID ?>"></p>
                            </div>
                            <!-- edit required hours -->
                            <div class="taskEditBodyAll taskEditBody-Hours">
                                <p class="taskEditBodyTitle">Required Hours</p>
                                <input class="taskEditBodyInput taskEditBodyInput-Hours" type="number" min="0" step="1"
                                    id="editTaskHours<?php echo $taskID ?>" name="editHours" placeholder="Hours"
                                    value='<?php echo $hours ?>' required>
                            </div>
                            <!-- edit assigned user -->
                            <div class="taskEditBodyAll taskEditBody-User">
                                <p class="taskEditBodyTitle">Assigned User</p>
                                <select class="taskEditBodyInput taskEditBodyInput-User<?php echo $taskID ?>"
                                    id="editTaskUser<?php echo $taskID ?>" name="editTaskUser" required>
                                    <?php
                                    // the users from the database are automatically populated into the select
                                    $userQuery = "SELECT userID, CONCAT(userName, ' ', userSurname) as name FROM userDetails;";
                                    $userResult = $conn->query($userQuery);
                                    while ($userRow = $userResult->fetch_assoc()) {
                                        echo "<option value='" . $userRow['userID'] . "'>" . $userRow['userID'] . ' - ' . $userRow['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- edit assigned project -->
                            <div class="taskEditBodyAll taskEditBody-Project">
                                <p class="taskEditBodyTitle">Assigned Project</p>
                                <select class="taskEditBodyInput taskEditBodyInput-Project<?php echo $taskID ?>"
                                    id="editTaskProject<?php echo $taskID ?>" name="project-ID-input" required>
                                    <?php
                                    // the projects from the database are automatically populated into the select
                                    $projectQuery = "SELECT * FROM projectDetails;";
                                    $projectResult = $conn->query($projectQuery);
                                    while ($projectRow = $projectResult->fetch_assoc()) {
                                        echo "<option value='" . $projectRow['projectID'] . "'>" . $projectRow['projectID'] . ' - ' . $projectRow['projectName'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <div class="row w-100 d-flex flex-row gap-3">
                                <div class="col">
                                    <!-- cancel button -->
                                    <button type="button" class="btn btn-taskEditDelete btn-taskCancel w-100"
                                        data-bs-target="#taskModal<?php echo $taskID ?>" data-bs-toggle="modal">Cancel</button>
                                </div>
                                <div class="col">
                                    <!-- edit confirm button -->
                                    <button type="button" class="btn btn-taskEditDelete btn-taskEditConfirm w-100"
                                        id="taskEditConfirm<?php echo $taskID ?>">Edit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal for adding 'worked' hours to a task  -->
            <div class="modal fade" id="editHoursModal<?php echo $taskID ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 editHoursHeader">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <i class="bi bi-clock editHoursHeaderIcon"></i>
                        </div>
                        <div class="modal-body editHoursBody">
                            <p class="editHoursBodyTitle">Add Hours</p>
                            <p class="taskHoursBodySubtitle">Use the slider below to add worked hours to the task.</p>
                            <div class="slidecontainer">
                                <!-- slider input -->
                                <input type="range" min="1" max="50" value="1" step="1" class="slider"
                                    id="completeHoursInput<?php echo $taskID ?>">
                                <hr>
                                <!-- p element that contains a span element that is updated when the value of the slider changes -->
                                <p class="taskHoursBodySubtitle">Hours: <span id="hours<?php echo $taskID ?>"></span></p>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <div class="row w-100 d-flex flex-row gap-3">
                                <div class="col">
                                    <!-- cancel button -->
                                    <button type="button" class="btn btn-taskEditDelete btn-taskCancel w-100"
                                        data-bs-dismiss="modal" data-bs-toggle="modal">Cancel</button>
                                </div>
                                <div class="col">
                                    <!-- confirm button -->
                                    <button type="button" class="btn btn-taskEditDelete btn-taskEditConfirm w-100"
                                        id="taskEditHoursConfirm<?php echo $taskID ?>">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // make the edit task modal the parent for the select2 inputs
                $(document).ready(function () {
                    $('.taskEditBodyInput-User<?php echo $taskID ?>').select2({
                        dropdownParent: $('#taskEditModal<?php echo $taskID ?>')
                    });
                    $('.taskEditBodyInput-Project<?php echo $taskID ?>').select2({
                        dropdownParent: $('#taskEditModal<?php echo $taskID ?>')
                    });
                });
            </script>

            <script>
                // get the slider and span elements
                var slider = document.querySelector("#completeHoursInput<?php echo $taskID ?>");
                var output = document.querySelector("#hours<?php echo $taskID ?>");
                output.textContent = slider.value;

                // when the value of the slider changes change the text content of the span element to the value of the slider
                slider.addEventListener("input", (event) => {
                    document.querySelector("#hours<?php echo $taskID ?>").textContent = event.target.value;
                })
            </script>

            <script>

                $(document).ready(function () {

                    $.ajaxSetup({
                        // Disable caching of AJAX responses
                        cache: false
                    });

                    // when the confirm button to edit a task is pressed, this code is run
                    $('#taskEditConfirm<?php echo $taskID ?>').click(function () {

                        // get the values from each of the inputs
                        var id = <?php echo $taskID ?>;
                        var name = document.querySelector("#editTaskName<?php echo $taskID ?>").value;
                        var desc = document.querySelector('#editTaskDesc<?php echo $taskID ?>').value;
                        var status = document.querySelector('#editTaskStatus<?php echo $taskID ?>').value;
                        var hours = parseInt(document.querySelector('#editTaskHours<?php echo $taskID ?>').value);
                        var user = parseInt(document.querySelector('#editTaskUser<?php echo $taskID ?>').value);
                        var project = parseInt(document.querySelector('#editTaskProject<?php echo $taskID ?>').value);

                        // for the inputs that contain spaces, replaces the ' ' with '__'
                        var nameReplaced = name.split(' ').join('__');
                        var descReplaced = desc.split(' ').join('__');
                        var statusReplaced = status.split(' ').join('__');

                        // get date elements
                        var date = document.querySelector("#editTaskDate<?php echo $taskID ?>").value;
                        var dateSelect = document.querySelector("#editTaskDate<?php echo $taskID ?>");
                        var dateError = document.querySelector("#editTaskError<?php echo $taskID ?>");

                        // get todays date in format yyyy-mm-dd
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth() + 1;
                        var yyyy = today.getFullYear();

                        if (dd < 10) {
                            dd = '0' + dd;
                        }

                        if (mm < 10) {
                            mm = '0' + mm;
                        }

                        today = yyyy + '-' + mm + '-' + dd;

                        // turn the task deadline into the date
                        <?php
                        $date = date_create($taskDeadline);
                        $dateFormat = date_format($date, "Y-m-d");
                        ?>

                        // if the date is before today and the date is not the current deadline
                        if (date < today && (new Date(date).getTime() != new Date("<?php echo $dateFormat ?>").getTime())) {
                            // set the select value to the original deadline and print and error message
                            dateSelect.value = "<?php echo $taskDeadline ?>";
                            dateError.innerText = "Deadline Error!\nSelect a date between today and the project deadline.";
                        }
                        // if the date is ok 
                        else {
                            // reload the tasks after loading "update-tasks.inc.php" with the new information in the header
                            $("#taskList").load(("update-tasks.inc.php?id=" + id + "&name=" + nameReplaced + "&desc=" + descReplaced + "&status=" + statusReplaced + "&deadline=" + date + "&hours=" + hours + "&user=" + user + "&project=" + project), function () {
                                $("#taskList").load("display-tasks.inc.php", function () {
                                    window.location.reload();
                                });
                            });
                            $('#taskEditModal<?php echo $taskID ?>').modal('toggle');
                            document.getElementById("task<?php echo $taskID ?>").scrollIntoView();
                            location.href = "#task<?php echo $taskID ?>";
                        }

                    })
                });
            </script>

            <script>

                $(document).ready(function () {

                    $.ajaxSetup({
                        // Disable caching of AJAX responses
                        cache: false
                    });

                    // when the add hours button is pressed this code is run
                    $('#taskEditHoursConfirm<?php echo $taskID ?>').click(function () {

                        // get the taskID
                        var id = <?php echo $taskID ?>;
                        // get the value from the slider
                        var hours = document.querySelector("#completeHoursInput<?php echo $taskID ?>").value;

                        // reload the tasks list after loading "update-hours.inc.php" with the new hours in the header
                        $("#taskList").load(("update-hours.inc.php?id=" + id + "&hours=" + hours), function () {
                            $("#taskList").load("display-tasks.inc.php", function () {
                                window.location.reload();
                            });
                        });

                        $('#editHoursModal<?php echo $taskID ?>').modal('toggle');

                        document.getElementById("task<?php echo $taskID ?>").scrollIntoView();
                        location.href = "#task<?php echo $taskID ?>";

                    })
                });
            </script>

            <script>
                // populate the status select in the edit modal with the current status 
                document.addEventListener("DOMContentLoaded", function () {
                    var taskStatus = "<?php echo $taskStatus; ?>";

                    var selectStatus = document.getElementById("editTaskStatus<?php echo $taskID ?>");

                    var matchingStatus = Array.from(selectStatus.options).find(function (status) {
                        return status.value === taskStatus;
                    });

                    if (matchingStatus) {
                        matchingStatus.selected = true;
                    }
                });
            </script>

            <script>
                // populate the user select in the edit modal with the current user 
                document.addEventListener("DOMContentLoaded", function () {
                    var userID = "<?php echo $taskPersonID; ?>";

                    var selectUser = document.getElementById("editTaskUser<?php echo $taskID ?>");

                    var matchingUser = Array.from(selectUser.options).find(function (user) {
                        return user.value === userID;
                    });

                    if (matchingUser) {
                        matchingUser.selected = true;
                    }
                });
            </script>

            <script>
                // populate the project select in the edit modal with the current project 
                document.addEventListener("DOMContentLoaded", function () {
                    var projectID = "<?php echo $projectID; ?>";

                    var selectProject = document.getElementById("editTaskProject<?php echo $taskID ?>");

                    var matchingProject = Array.from(selectProject.options).find(function (project) {
                        return project.value === projectID;
                    });

                    if (matchingProject) {
                        matchingProject.selected = true;
                    }
                });
            </script>
            <?php
        }
        ?>

        <script>

            // if the transition button exists
            if (document.querySelector("#taskTransitionBtn<?php echo $taskID ?>") != null) {
                // when the button is clicked
                document.querySelector("#taskTransitionBtn<?php echo $taskID ?>").addEventListener("click", () => {

                    <?php
                    // set the next task status
                    if ($actualTaskStatus == "Backlog" || $actualTaskStatus == "In Progress") {
                        if ($actualTaskStatus == "Backlog") {
                            $nextTaskStatus = "In__Progress";
                        } else if ($actualTaskStatus == "In Progress") {
                            $nextTaskStatus = "Complete";
                        }
                        ?>

                        // reload the tasks list after loading "update-status.inc.php" with the new status in the header
                        $("#taskList").load(("update-status.inc.php?id=" + "<?php echo $taskID ?>" + "&status=" + "<?php echo $nextTaskStatus ?>"), function () {
                            $("#taskList").load("display-tasks.inc.php", function () {
                                window.location.reload();
                            });
                        });

                        document.getElementById("task<?php echo $taskID ?>").scrollIntoView();
                        location.href = "#task<?php echo $taskID ?>";

                        <?php
                    } 
                    // if the task is complete
                    else if ($actualTaskStatus == "Complete") {
                        $nextTaskStatus = $actualTaskStatus;
                    }
                    ?>
                });
            }
        </script>
        <?php
    }
} 
// if there are no tasks in the query
else {

    include_once("newTaskItem.php");
}
?>