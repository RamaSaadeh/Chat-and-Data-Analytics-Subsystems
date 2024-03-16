<?php

//starting session
session_start();
if (!isset($_SESSION["valid"])) {
    //redirect if session is not valid
    header("Location: login.php");
    exit();
}

?>



<table id="table" class="table table-borderless ">
    <tbody>
        <!--reading in all list-->
        <?php
        require_once("../../includes/dbh.inc.php");

        $todoOwnderID = $_SESSION["userid"];
        //sql statement
        $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoOwnderID AND todoCompletion = 0 ORDER BY todoPriority DESC;";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {

        ?>

            <!-- when clicking on row, display view modal -->
            <tr id="all-row-<?php echo $row["todoID"] ?>" data-bs-toggle="modal" data-bs-target="#viewModal-<?php echo $row["todoID"] ?>">
                <!-- priority data -->
                <td style="width: 1%" id="all-priority-cell-<?php echo $row["todoID"] ?>" data-bs-toggle="modal" data-bs-target="">
                    <?php
                    require_once("../functions.php");
                    $colour = getPriorityColour($row["todoPriority"]);
                    ?>
                    <i id="all-priority-icon-<?php echo $row["todoID"] ?>" class='priority-icons fa fa-circle-thin fs-5' style='color: #<?php echo $colour ?>'></i>
                    <?php

                    ?>
                </td>
                <!-- script to complete items (only allow for non completed) -->
                <?php
                ?>
                <script>
                    //getting colour of priority
                    colour<?php echo $row["todoID"] ?> = document.getElementById("all-priority-icon-<?php echo $row["todoID"] ?>").style.color;
                    //mouse clicked
                    $("#all-priority-icon-<?php echo $row["todoID"] ?>").click(function() {

                        //replacing icon with filled circle
                        document.getElementById('all-priority-cell-<?php echo $row["todoID"] ?>').innerHTML = "<i class='fa fa-circle fs-5' style='color:" + colour<?php echo $row["todoID"] ?> + "'></i >";
                        //delay completion by 1.5 seconds
                        setTimeout(function() {

                            $("#test").load("to-do-list/completed.php?%20ID=<?php echo $row["todoID"] ?>&%20SECTION=all");



                        }, 350);

                    })
                </script>

                <!-- information data -->
                <td class="d-inline-block text-truncate" style="max-width: calc(6rem + 12.5vw)">
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

                <!-- flag item data -->
                <td style="width: 1%" data-bs-toggle="modal" data-bs-target="">
                    <?php if ($row["todoFlag"] == 1) { ?>
                        <i class="fa fa-flag fs-5" style="color: #FFBF00"></i>
                    <?php
                    } ?>
                </td>
                <!-- edit item data -->
                <td style="width: 1%" data-bs-toggle="modal" data-bs-target="">
                    <i class="edit-item fa fa-info-circle fs-5" style="color: #808080;" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $row["todoID"] ?>"></i>
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