<!-- Modal for Creating a new Project -->
<div class=" modal fade" id="ProjectCreateModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 projectCreateHeader">
                <!-- Close button for Modal -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Form for sending values submitted -->
            <form action="create-projects.inc.php" method="post" class="modal-body projectCreateBody"
                id="projectCreateBody">
                <div class="projectCreateBodyAll projectCreateBody-Name">
                    <!-- Project Name -->
                    <p class="projectCreateBodyTitle">Project Name</p>
                    <input type="text" class="projectCreateBodyInput projectCreateBodyInput-Name" id="createProjectName"
                        placeholder="Name" minlength="5" maxlength="32" name="createName" required>
                </div>

                <div class="projectCreateBodyAll projectCreateBody-Desc">
                    <!-- Project Description -->
                    <p class="projectCreateBodyTitle">Project Desc</p>
                    <textarea class="projectCreateBodyInput projectCreateBodyInput-Desc" id="createProjectDesc"
                        minlength="5" maxlength="512" placeholder="Description" name="createDescription"
                        required></textarea>
                </div>

                <div class="projectCreateBodyAll projectCreateBody-Leader">
                    <!-- Project Leader -->
                    <p class="projectCreateBodyTitle">Project Leader</p>
                    <select class="projectCreateBodyInput projectCreateBodyInput-User"
                        id="createprojectUser<?php echo $projectID ?>" name="createProjectUser" required>
                        <?php

                        // Retrieving all potential leaders
                        $userQuery = "SELECT userID, CONCAT(userName, ' ', userSurname) as name FROM userDetails WHERE userRole = 'leader';";
                        $userResult = $conn->query($userQuery);
                        while ($userRow = $userResult->fetch_assoc()) {
                            // Listing each leader as an option in the select input
                            echo "<option value='" . $userRow['userID'] . "'>" . $userRow['userID'] . ' - ' . $userRow['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="projectCreateBodyAll projectCreateBody-Date">
                    <!-- Project Deadline -->
                    <p class="projectCreateBodyTitle">Project Deadline</p>
                    <input type="date" class="projectCreateBodyInput projectCreateBodyInput-Date" id="createProjectDate"
                        onkeydown="return false" placeholder="Deadline" name="createDate" required>
                    <span id="createProjectDateWarning"></span>
                </div>

            </form>
            <div class="modal-footer border-0">
                <div class="row w-100 d-flex flex-row gap-3 flex-grow">
                    <div class="col">
                        <!-- Modal Cancel -->
                        <button type="button" class="btn btn-projectEditDelete btn-projectCancel w-100"
                            data-bs-dismiss="modal"
                            onclick="document.getElementById('projectCreateBody').reset();">Cancel</button>
                    </div>
                    <div class="col">
                        <!-- Modal Submit -->
                        <button type="submit" name="submit" form="projectCreateBody" id="newProjectConfirm"
                            class="btn btn-projectEditDelete btn-projectCreateConfirm w-100">Create</button>
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
        $('.projectCreateBodyInput-User').select2({
            dropdownParent: $('#ProjectCreateModal')
        });
    });
</script>


<!-- Makes it so that deadline date in create modal is: -->
<!-- Minimum value is today's date -->
<!-- Current Value is automatically today's date -->
<script>
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


    // Set Minimum value of deadline to today
    // Set value of deadline to today
    document.getElementById("createProjectDate").setAttribute("min", today);
    document.getElementById("createProjectDate").setAttribute("value", today);

    // Validation once confirm is clicked
    document.getElementById("newProjectConfirm").addEventListener("click", () => {

        // Check if deadline is less than today's date
        if (document.getElementById("createProjectDate").value < today) {

            // Set Minimum value of deadline to today
            // Set value of deadline to today
            document.getElementById("createProjectDate").setAttribute("min", today);
            document.getElementById("createProjectDate").value = today;
        }

    })

    // Validation once there is an input in the deadline
    document.getElementById("createProjectDate").addEventListener("input", () => {

        // Check if deadline is less than today's date
        if (document.getElementById("createProjectDate").value < today) {

            // Set Minimum value of deadline to today
            // Set value of deadline to today
            document.getElementById("createProjectDate").setAttribute("min", today);
            document.getElementById("createProjectDate").value = today;
        }

    })

</script>