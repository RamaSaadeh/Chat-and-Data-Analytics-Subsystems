<?php

//starting session
session_start();
if (!isset($_SESSION["valid"])) {
    //redirect if session is not valid
    header("Location: login.php");
    exit();
}

?>



<table id="table" class="table table-borderless">
    <tbody>
        <!--reading in flag list-->
        <?php

        require_once("../../includes/dbh.inc.php");
        $todoOwnerID = $_SESSION["userid"];
        $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoOwnerID AND todoCompletion = 1;";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {

        ?>

            <!-- when clicking on row, display view modal -->
            <tr id="completed-row-<?php echo $row["todoID"] ?>" data-bs-toggle="modal" data-bs-target="#viewModal-<?php echo $row["todoID"] ?>">
                <!-- script for mouse hovering -->
                <script>
                    //mouse entering
                    $("#completed-row-<?php echo $row["todoID"] ?>").mouseenter(function() {
                        document.body.style.cursor = "pointer";
                        document.getElementById("completed-row-<?php echo $row["todoID"] ?>").style.backgroundColor = "#ECECEC";
                    })
                    //mouse leaving
                    $("#completed-row-<?php echo $row["todoID"] ?>").mouseleave(function() {
                        document.body.style.cursor = "default";
                        document.getElementById("completed-row-<?php echo $row["todoID"] ?>").style.backgroundColor = "white";
                    })
                </script>
                <!-- priority data -->
                <td style="width: 1%" id="priority-cell-<?php echo $row["todoID"] ?>" data-bs-toggle="modal" data-bs-target="">
                    <?php
                    require_once("../functions.php");
                    $colour = getPriorityColour($row["todoPriority"]);
                    ?>
                    <i id="priority-icon-<?php echo $row["todoID"] ?>" class='priority-icons fa fa-circle fs-5' style='color: #<?php echo $colour ?>'></i>
                    <?php

                    ?>
                </td>
                <!-- script to complete items (only allow for non completed) -->
                <?php
                ?>
                <script>
                    //getting colour of priority
                    colour<?php echo $row["todoID"] ?> = document.getElementById("priority-icon-<?php echo $row["todoID"] ?>").style.color;
                    //mouse clicked
                    $("#priority-icon-<?php echo $row["todoID"] ?>").click(function() {

                        //replacing icon with filled circle
                        document.getElementById('priority-cell-<?php echo $row["todoID"] ?>').innerHTML = "<i class='fa fa-circle-thin fs-5' style='color:" + colour<?php echo $row["todoID"] ?> + "'></i >";
                        //delay completion by 1.5 seconds
                        setTimeout(function() {

                            $("#test").load("to-do-list/uncompleted.php?%20ID=<?php echo $row["todoID"] ?>&%20SECTION=all");



                        }, 350);

                    })
                </script>

                <!-- information data -->
                <td data-bs-toggle="modal" data-bs-target="#viewModal-<?php echo $row["todoID"] ?>" class="d-inline-block text-truncate" style="max-width: calc(6rem + 12.5vw)">
                    <!--printing information-->
                    <?php echo $row["todoInformation"];
                    //if statement to determine if user has inputted notes
                    if ($row["todoNotes"] != "") {
                        echo "<p class='m-0 text-truncate' style='max-width: 100%;'><small>" . $row["todoNotes"] . "</small></p>";
                    }
                    //if statement to determine if user has selected a due date
                    if ($row["todoDueDate"] != "0001-01-01") {
                        require_once("../functions.php");
                        displayingDate($row["todoDueDate"]);
                    } ?>
                </td>
            </tr>


        <?php

        }

        ?>

    </tbody>
</table>
<h1>
    <div id="test"></div>
</h1>