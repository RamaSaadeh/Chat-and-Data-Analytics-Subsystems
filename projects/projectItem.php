<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Check if there are any results
if (mysqli_num_rows($result) != 0) {

    // loading necessary files
    include_once("../includes/dbh.inc.php");
    include_once("../includes/functions.inc.php");
    include_once("project-functions.inc.php");

    // For as many results, do:
    while ($row = $result->fetch_assoc()) {



        // Store values from SQL in variables
        $projectID = $row["projectID"];
        $projectLeaderID = $row["projectLeaderID"];
        $projectLeaderName = getProjectLeaderName($conn, $projectLeaderID)[0];
        $projectName = $row["projectName"];
        $projectDesc = $row["projectDesc"];
        $projectDate = $row["projectDate"];
        $projectDateFormat = date_format(date_create($projectDate), "d/m/Y");

        $lastEdited = $row["lastEditedFormat"];

        $projectProgress = progressProject($conn, $projectID);

        // echo "<script> alert('$projectProgress')</script>";

        $today = date("Y/m/d");

        $today = strtotime($today);
        $projectDateCheck = strtotime($projectDate);
        $actualProjectStatus = "Ongoing";

        // Compare today's date with projectDeadline to work out status
        if ($today > $projectDateCheck) {
            $projectStatus = "Overdue";
        }
        if ($projectProgress == 100) {
            $projectStatus = "Completed";
        } elseif (($projectProgress < 100) and ($today <= $projectDateCheck)) {
            $projectStatus = "Ongoing";
        }


        $projectStatusClass;

        // Based on project status get a class name
        if ($projectStatus == "Completed") {
            $projectStatusClass = "project-deadline-complete-bg";
        } else if ($projectStatus == "Ongoing") {
            $projectStatusClass = "project-deadline-ongoing-bg";
        } else if ($projectStatus == "Overdue") {
            $projectStatusClass = "project-deadline-overdue-bg";
        }

        // Work out progress of project To use with CSS
        $progress = progressProject($conn, $projectID);
        $progressCircle = $progress * 3.6;



        ?>
        <!-- Individual Projects Container -->
        <li class="projectListItems" id="project<?php echo $projectID ?>">

            <?php
            // Check if User came from tasks page
            if (
                $redirectedID == $projectID
            ) {
                ?>
                <script>
                    // Give style then after 1s remove it
                    document.getElementById("project<?php echo $projectID ?>").style.boxShadow = "0 0 0 0.35rem rgba(0, 0, 0, 0.25)";
                    document.getElementById("project<?php echo $projectID ?>").style.transition = "2.5s ease";
                    document.getElementById("project<?php echo $projectID ?>").style.borderRadius = "0.5rem";
                    setTimeout(function () {
                        document.getElementById("project<?php echo $projectID ?>").style.boxShadow = "none";
                    }, 1000);
                </script>
                <?php
            }
            ?>


            <div class="project-card container shadow rounded ">
                <div class="project-card-content w-100 h-100">
                    <div class="btn project-card-btn" tabindex="-1">
                        <div class="project-info-content flex-column justify-content-start w-100 text-start">
                            <!-- Project ID -->
                            <span class="project-info-content-id" id="<?php echo $projectID ?>">
                                <?php echo $projectID ?>
                            </span>
                            <!-- Project Name -->
                            <span class="project-info-content-title" id="projectNameTitle">
                                <?php echo $projectName ?>
                            </span>
                            <!-- Project Leader's Name -->
                            <span class="project-info-content-leader">
                                <?php echo $projectLeaderName ?>
                            </span>
                            <!-- Project Description, truncated to 50 letters -->
                            <span class="project-info-content-desc-short">
                                <?php echo truncateText($projectDesc, 50) ?>
                            </span>
                        </div>
                        <!-- Project Deadline Section -->
                        <div class="project-deadline-content <?php echo $projectStatusClass ?>">
                            <div class="project-deadline-content-group">
                                <span class="project-deadline-content-title">Deadline</span>
                                <!-- Project Deadline -->
                                <span class="project-deadline-content-date">
                                    <?php echo $projectDateFormat ?>
                                </span>
                            </div>
                            <!-- Project Progress Donut Chart -->
                            <div class="progress-container">
                                <div class="circular-progress" style="--progress: <?php echo $progressCircle ?>deg"
                                    data-status=<?php echo $projectStatus ?>>
                                    <!-- Project Progress -->
                                    <span class="progress-value" data-progress=<?php echo $progress ?>></span>
                                </div>
                            </div>


                        </div>
                    </div>
                    <!-- Project Information button - Leads to modal  -->
                    <button class="btn project-info-btn" type="button" id="projectModalBtn<?php echo $projectID ?>"
                        type="button" data-bs-toggle="modal" data-bs-target="#ProjectModal<?php echo $projectID ?>"><i
                            class="bi bi-info"></i>
                    </button>
                    <!-- Button that leads to The Tasks involved in that Project -->
                    <a class="project-card-caption-overlay text-center m-0 w-100 h-100"
                        href=" <?php echo "/tasks/tasks.php?projectID= $projectID"; ?>">
                    </a>
                </div>
            </div>
        </li>

        <!-- Project Information Modal -->
        <div class="modal fade" id="ProjectModal<?php echo $projectID ?>" tabindex="-1" aria-labelledby="ProjectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Close Modal Button -->
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- View Modal Content -->
                    <div class="modal-body">

                        <div class="container d-flex flex-column px-5 text-center">
                            <div class="d-flex flex-column gap-2 my-3">

                                <!-- Project Last Edited Time - dd/mm/yy h:s:ms -->
                                <p class="my-1 h-100 text-nowrap" id="projectLastEdited">
                                    Last Edited:
                                    <span>
                                        <?php echo $lastEdited ?>
                                    </span>
                                </p>

                                <!-- Project Name -->
                                <p id="projectName">
                                    <?php echo ucwords($projectName) ?>
                                </p>

                                <!-- Project Description -->
                                <p id="projectDescription">
                                    <?php echo $projectDesc ?>
                                </p>
                                <hr>

                                <!-- Table of Information -->
                                <table class="table table-borderless text-center" id="projectInfoTable">
                                    <tbody>
                                        <tr>
                                            <!-- Project Date -->
                                            <th scope="row">Due:</th>
                                            <td>
                                                <?php echo $projectDateFormat ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <!-- Project Status -->
                                            <th scope="row">Status:</th>
                                            <td>
                                                <?php
                                                // Check if status is overdue so show both that it is overdue and it's status otherwise
                                                if ($taskStatus == "Overdue") {
                                                    echo $actualProjectStatus . " | " . $projectStatus;
                                                } else {
                                                    echo $projectStatus;
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <!-- Project Leader (name | id) -->
                                            <th scope="row">Owner:</th>
                                            <td>
                                                <?php echo $projectLeaderName . ' (' . $projectLeaderID . ")" ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <!-- Project Progress -->
                                            <th scope="row">Progress:</th>
                                            <td>
                                                <?php echo $projectProgress . '%' ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Only for managers -->
                    <!-- Buttons for editing and deleting a project -->
                    <?php if ($_SESSION['role'] === 'manager') { ?>
                        <div class="modal-footer d-flex justify-content-center align-items-center">
                            <div class="row w-100 d-flex flex-row gap-3">
                                <div class="col">
                                    <button type="button" class="btn btn-project btn-project-edit w-100" data-bs-toggle="modal"
                                        data-bs-target="#ProjectEditModal<?php echo $projectID ?>">Edit
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-project btn-project-delete w-100" data-bs-toggle="modal"
                                        data-bs-target="#ProjectDeleteModal<?php echo $projectID ?>">Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>

        <!-- Project Deletion Modal -->
        <div class="modal fade" id="ProjectDeleteModal<?php echo $projectID ?>" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">

                    <!-- Close Modal Button -->
                    <div class="modal-header border-0 projectDeleteHeader">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <i class="bi bi-patch-exclamation projectDeleteHeaderIcon"></i>
                    </div>

                    <!-- Confirmation Message -->
                    <div class="modal-body projectDeleteBody">
                        <p class="projectDeleteBodyTitle">Are You Sure?</p>
                        <p class="projectDeleteBodySubtitle">Are you sure you want to delete this project? The process cannot be
                            undone.</p>
                    </div>

                    <!-- Modal Cancelation & Deletion Buttons -->
                    <div class="modal-footer border-0">
                        <div class="row w-100 d-flex flex-row gap-3">
                            <div class="col">
                                <button type="button" class="btn btn-projectEditDelete btn-projectCancel w-100"
                                    data-bs-target="#ProjectModal<?php echo $projectID ?>"
                                    data-bs-toggle="modal">Cancel</button>
                            </div>
                            <div class="col">
                                <a type="button" class="btn btn-projectEditDelete btn-projectDeleteConfirm w-100"
                                    id="projectDeleteConfirm<?php echo $projectID ?>"
                                    href="delete-projects.inc.php? ID=<?php echo $projectID; ?>">Delete</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Edit Modal -->
        <div class=" modal fade" id="ProjectEditModal<?php echo $projectID ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">

                    <!-- Close Modal Button -->
                    <div class="modal-header border-0 projectEditHeader">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body projectEditBody" id="projectEditBody<?php echo $projectID ?>">

                        <!-- Edit Project Name -->
                        <div class="projectEditBodyAll projectEditBody-Name">
                            <p class="projectEditBodyTitle">Project Name</p>

                            <input type="text" class="projectEditBodyInput projectEditBodyInput-Name"
                                id="editProjectName<?php echo $projectID ?>" placeholder="Name" minlength="5" maxlength="32"
                                name="editName" value='<?php echo $projectName ?>' required>
                        </div>

                        <!-- Edit Project Description -->
                        <div class="projectEditBodyAll projectEditBody-Desc">
                            <p class="projectEditBodyTitle">Project Desc</p>
                            <textarea class="projectEditBodyInput projectEditBodyInput-Desc"
                                id="editProjectDesc<?php echo $projectID ?>" minlength="5" maxlength="512"
                                placeholder="Description" name="editDescription" required><?php echo $projectDesc ?></textarea>
                        </div>

                        <!-- Edit Project Deadline -->
                        <div class="projectEditBodyAll projectEditBody-Date">
                            <p class="projectEditBodyTitle">Project Date</p>
                            <input type="date" class="projectEditBodyInput projectEditBodyInput-Date"
                                id="editProjectDate<?php echo $projectID ?>" onkeydown="return false" placeholder="Deadline"
                                name="editDate" value=<?php echo $projectDate ?> required>
                            <p class="projectEditBodyError" id="editProjectError<?php echo $projectID ?>"></p>
                        </div>

                        <!-- Edit Project Leader -->
                        <div class="projectEditBodyAll projectEditBody-Leader">
                            <p class="projectEditBodyTitle">Project Leader</p>
                            <select class="projectEditBodyInput projectEditBodyInput-User<?php echo $projectID ?>"
                                id="editprojectUser<?php echo $projectID ?>" name="editprojectUser" required>
                                <?php

                                // Retrieving all potential leaders
                                $userQuery = "SELECT *, CONCAT(userName, ' ', userSurname) as name FROM userDetails WHERE userRole = 'leader';";
                                $userResult = $conn->query($userQuery);
                                while ($userRow = $userResult->fetch_assoc()) {

                                    // Listing each leader as an option in the select input
                                    if ($userRow["userID"] == checkProjectLeaderID($conn, $userRow["userID"], $projectID)) {
                                        // Select Current Leader
                                        echo "<option selected value='" . $userRow['userID'] . "'>" . $userRow['userID'] . ' - ' . $userRow['name'] . "</option>";
                                    } else {
                                        echo "<option value='" . $userRow['userID'] . "'>" . $userRow['userID'] . ' - ' . $userRow['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>





                    </div>

                    <!-- Buttons for Confirming and Cancelling The editing of a project -->
                    <div class="modal-footer border-0">
                        <div class="row w-100 d-flex flex-row gap-3">
                            <div class="col">
                                <button type="button" class="btn btn-projectEditDelete btn-projectCancel w-100"
                                    data-bs-target="#ProjectModal<?php echo $projectID ?>"
                                    data-bs-toggle="modal">Cancel</button>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-projectEditDelete btn-projectEditConfirm w-100"
                                    id="projectEditConfirm<?php echo $projectID ?>">Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Sets Parent of Select as the modal
            // Display purposes | Allows select to show on modal instead of body
            $(document).ready(function () {
                $('.projectEditBodyInput-User<?php echo $projectID ?>').select2({
                    dropdownParent: $('#ProjectEditModal<?php echo $projectID ?>')
                });
            });
        </script>

        <script>

            // When page first loads
            $(document).ready(function () {

                $.ajaxSetup({
                    // Disable caching of AJAX responses
                    cache: false
                });

                // When Project edit confirm button is clicked
                // Validation Purposes
                $('#projectEditConfirm<?php echo $projectID ?>').click(function () {

                    // Get Value in Edit Modal of each input
                    var id = <?php echo $projectID ?>;
                    var name = document.querySelector("#editProjectName<?php echo $projectID ?>").value;
                    var desc = document.querySelector('#editProjectDesc<?php echo $projectID ?>').value;
                    var leader = parseInt(document.querySelector('#editprojectUser<?php echo $projectID ?>').value);

                    // Replace the space with '__' to allow it to be used for GET
                    var nameReplaced = name.split(' ').join('__');
                    var descReplaced = desc.split(' ').join('__');

                    var date = document.querySelector("#editProjectDate<?php echo $projectID ?>").value;
                    var dateSelect = document.querySelector("#editProjectDate<?php echo $projectID ?>");

                    var dateError = document.querySelector("#editProjectError<?php echo $projectID ?>");

                    // Get today's date
                    var today = new Date();
                    var dd = today.getDate();
                    var mm = today.getMonth() + 1;
                    var yyyy = today.getFullYear();

                    // Check if day is less than 10 to pad it with 0 eg. 1 -> 01 
                    if (dd < 10) {
                        dd = '0' + dd;
                    }

                    // Check if month is less than 10 to pad it with 0 eg. 9 -> 09
                    if (mm < 10) {
                        mm = '0' + mm;
                    }

                    // Put all the values together to makes today date
                    today = yyyy + '-' + mm + '-' + dd;

                    <?php
                    // Format project Date in a Readable way
                    // e.g. 25/10/2024 - 2024/10/12
                    $date = date_create($projectDate);
                    $dateFormat = date_format($date, "Y-m-d");
                    ?>

                    // Check if date is:
                    //      less than today 
                    //      not equal to project deadline 
                    if (date < today && (new Date(date).getTime() != new Date("<?php echo $dateFormat ?>").getTime())) {
                        // Set deadline date to project deadline
                        dateSelect.value = "<?php echo $projectDate ?>";

                        // Error message
                        dateError.innerText = "Deadline Error!\nSelect the Original Deadline or Pick a Current Date";
                    } else {

                        // update project table with new  information
                        $("#projectList").load(("update-projects.inc.php?id=" + id + "&name=" + nameReplaced + "&desc=" + descReplaced + "&date=" + date + "&leader=" + leader), function () {

                            // display project items again
                            $("#projectList").load("display-projects.inc.php", function () {

                                // filter the project items
                                filterProjects();

                                // reload window
                                window.location.reload();
                            });
                        });

                        // Close Edit Projects Modal
                        $('#ProjectEditModal<?php echo $projectID ?>').modal('toggle');

                        <?php
                        $_SESSION["redirectedTasks"] = true;
                        ?>

                        // Scroll to the project after project
                        document.getElementById("<?php echo $projectID ?>").scrollIntoView();
                        location.href = "?redirectedID=<?php echo $projectID ?>#<?php echo $projectID ?>";
                    }



                })
            });
        </script>

        <script>


            var buttonClick = document.getElementById("projectModalBtn<?php echo $projectID ?>");

            // When clicked, set project's minimum date to today's date in the edit modal
            buttonClick.addEventListener(("click"), () => {

                document.getElementById("editProjectDate<?php echo $projectID ?>").setAttribute("min", today);

            })
        </script>


        <?php
    }
} else {

    // Load the new project Item Warning if there are no results
    include_once("newProjectItem.php");
}
?>