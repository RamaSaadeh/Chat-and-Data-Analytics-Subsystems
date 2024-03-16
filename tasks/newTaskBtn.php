
<!-- This is the button and modal for creating a new task and is included in the tasks page -->

<div class=" modal fade" id="taskCreateModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 taskCreateHeader">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="create-task.inc.php" method="post" class="modal-body taskCreateBody" id="taskCreateBody">
                <!-- task name input -->
                <div class="taskCreateBodyAll taskCreateBody-Name">
                    <p class="taskCreateBodyTitle">Task Name</p>
                    <input type="text" class="taskCreateBodyInput taskCreateBodyInput-Name" id="createTaskName"
                        placeholder="Name" minlength="5" maxlength="32" name="createName" required>
                </div>
                <!-- task description input -->
                <div class="taskCreateBodyAll taskCreateBody-Desc">
                    <p class="taskCreateBodyTitle">Task Desc</p>
                    <textarea class="taskCreateBodyInput taskCreateBodyInput-Desc" id="createTaskDesc" minlength="5"
                        maxlength="512" placeholder="Description" name="createDescription" required></textarea>
                </div>
                <!-- task deadline input -->
                <div class="taskCreateBodyAll taskCreateBody-Date">
                    <p class="taskCreateBodyTitle">Task Deadline</p>
                    <input type="date" class="taskCreateBodyInput taskCreateBodyInput-Date" id="createtaskDate"
                        onkeydown="return false" placeholder="Deadline" name="createDate" required>
                </div>
                <!-- required hours input -->
                <div class="taskCreateBodyAll taskCreateBody-Hours">
                    <p class="taskCreateBodyTitle">Required Hours</p>
                    <input class="taskCreateBodyInput taskCreateBodyInput-Hours" type="number" min="1" step="1"
                        id="createTaskHours<?php echo $taskID ?>" name="createHours" placeholder="Hours"
                        value='<?php echo $hours ?>' required>
                </div>
                <!-- assigned user input -->
                <div class="taskCreateBodyAll taskCreateBody-User">
                    <p class="taskCreateBodyTitle">Assigned User</p>
                    <select class="taskCreateBodyInput taskCreateBodyInput-User" id="createTaskUser" name="createUserID"
                        required>
                        <option value="" disabled selected>Select a User</option>
                        <!-- users from the database are automatically inserted into this select -->
                        <?php
                        $userQuery = "SELECT userID, CONCAT(userName, ' ', userSurname) as name FROM userDetails;";
                        $userResult = $conn->query($userQuery);
                        while ($userRow = $userResult->fetch_assoc()) {
                            echo "<option value='" . $userRow['userID'] . "'>" . $userRow['userID'] . ' - ' . $userRow['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

            </form>
            <!-- buttons in the modal footer -->
            <div class="modal-footer border-0">
                <div class="row w-100 d-flex flex-row gap-3 flex-grow">
                    <div class="col">
                        <!-- button clears the form when it is clicked -->
                        <button type="button" class="btn btn-taskEditDelete btn-taskCancel w-100"
                            data-bs-dismiss="modal"
                            onclick="document.getElementById('taskCreateBody').reset();">Cancel</button>
                    </div>
                    <div class="col">
                        <!-- button submits the form -->
                        <button type="submit" name="submit" form="taskCreateBody" id="newTaskConfirm"
                            class="btn btn-taskEditDelete btn-taskCreateConfirm w-100">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // set the parent of the select to the modal
    $(document).ready(function () {
        $('.taskCreateBodyInput-User').select2({
            dropdownParent: $('#taskCreateModal')
        });
    });
</script>

<script>
    // when the modal is opened this is run
    document.getElementById("createTaskBtn").addEventListener("click", () => {
        // get the current date
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

        // set min date to today
        document.querySelector(".taskCreateBodyInput-Date").setAttribute("min", today);

        // set max to the project deadline
        var maxDate = "<?php echo getProjectDeadline($conn, $_GET['projectID']); ?>"
        document.querySelector(".taskCreateBodyInput-Date").setAttribute("max", maxDate);
        // if the deadline is in the past, set the task deadline to the project deadline
        if (document.querySelector(".taskCreateBodyInput-Date").getAttribute("max") < today) {
            document.querySelector(".taskCreateBodyInput-Date").setAttribute("value", maxDate);
        } 
        // else set the task deadline to today
        else {
            document.querySelector(".taskCreateBodyInput-Date").setAttribute("value", today);
        }
        // when the task is created this is run
        document.getElementById("newTaskConfirm").addEventListener("click", () => {
            // set max to the project deadline
            document.querySelector(".taskCreateBodyInput-Date").setAttribute("max", maxDate);
            // if project deadline is in the past
            if (document.querySelector(".taskCreateBodyInput-Date").getAttribute("max") < today) {
                // set min and value to project deadline
                document.getElementById("createtaskDate").setAttribute("min", maxDate);
                document.getElementById("createtaskDate").value = maxDate;
            }
            else {
                // if the input is in the past
                if (document.getElementById("createtaskDate").value < today) {
                    // set the value to today
                    document.getElementById("createtaskDate").setAttribute("min", today);
                    document.querySelector(".taskCreateBodyInput-Date").setAttribute("max", maxDate);
                    document.getElementById("createtaskDate").value = today;
                }

            }
        })

        // when a value is input this is run
        document.getElementById("createTaskDate").addEventListener("input", () => {

            // set the max date to the project deadline
            document.querySelector(".taskCreateBodyInput-Date").setAttribute("max", maxDate);
            // if the project deadline is in the past
            if (document.querySelector(".taskCreateBodyInput-Date").getAttribute("max") < today) {
                // set the min to the deadline, set the value to the deadline
                document.getElementById("createtaskDate").setAttribute("min", maxDate);
                document.getElementById("createtaskDate").value = maxDate;

            } 
            else {
                // if the input is in the past
                if (document.getElementById("createtaskDate").value < today) {
                    // set the value to today
                    document.getElementById("createtaskDate").setAttribute("min", today);
                    document.querySelector(".taskCreateBodyInput-Date").setAttribute("max", maxDate);
                    document.getElementById("createtaskDate").value = today;
                }

            }

        })
    })
</script>