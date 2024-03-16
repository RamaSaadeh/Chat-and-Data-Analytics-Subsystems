<?php

//starting session
session_start();
if (!isset($_SESSION["valid"])) {
  //redirect if session is not valid
  header("Location: login.php");
  exit();
}


?>



<!-- add modal form -->
<form method="post">
  <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true" data-bs-backdrop='static'>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <!-- modal header-->
        <div class="modal-header" style="background-color: #354182">
          <div class="modal-header-container container-fluid text-center">
            <!-- modal title-->
            <p class="to-do-list-title h4 m-0" style="color: #FFFFFF; font-weight: 600" id="addModalTitle">New
              Item</p>
          </div>
        </div>
        <!-- modal body -->
        <div class="modal-body">
          <!-- title form -->
          <div class="form-group">
            <textarea class="form-control" id="new-to-do-information" rows="1" placeholder="Title">New Reminder</textarea>
          </div>
          <!-- notes form -->
          <div class="form-group pt-2">
            <textarea class="form-control" id="new-to-do-notes" rows="3" placeholder="Notes"></textarea>
          </div>
          <!-- date form -->
          <div class="form-group mt-2">
            <table class="table table-borderless">
              <tr>
                <td class="align-middle p-0" py-1 style="width: 1%;">
                  <!-- calender icon container-->
                  <div class="icon-container rounded-circle align-middle" style="background-color: #CC3232;">
                    <i class="fa fa-calendar p-2" style="color: #FFFFFF;"></i>
                  </div>
                </td>
                <!-- date textfield-->
                <td class="align-middle py-0"><input class="form-control" type="text" id="new-to-do-datepicker" disabled readonly placeholder="Date">
                </td>
                <!-- date on/off switch-->
                <td class="align-middle py-0 pe-0" style="width: 1%;">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="new-to-do-dateenabler">
                  </div>
                </td>
              </tr>
            </table>
          </div>
          <script>
            $((function() {
              $("#new-to-do-datepicker").datepicker()
            })), $("#new-to-do-dateenabler").on("change", (function() {
              if ($(this).is(":checked")) {
                $("#new-to-do-datepicker").prop("disabled", !1);
                let tempDate = new Date
                let e = [tempDate.getMonth() + 1, tempDate.getDate(), tempDate.getFullYear()].join('/');
                document.getElementById("new-to-do-datepicker").value = e
              } else $("#new-to-do-datepicker").prop("disabled", !0), document.getElementById("new-to-do-datepicker").value = ""
            })), $("#new-to-do-information").on("keyup", (function() {
              "" == $.trim($("#new-to-do-information").val()) ? $("#addToDo").prop("disabled", !0) : $("#addToDo").prop("disabled", !1)
            }));
          </script>
          <!-- flag form -->
          <div class="form-group mt-2">
            <table class="table table-borderless">
              <tr>
                <td class="py-0 ps-0" style="width: 1%;">
                  <!-- flag icon container -->
                  <div class="icon-container-circular rounded-circle" style="background-color: #FFBF00;">
                    <i class="fa fa-flag p-2" style="color: #FFFFFF"></i>
                  </div>
                </td>
                <!-- date textfield-->
                <td class="align-middle py-0">
                  <p class="m-0">Flag</p>
                </td>
                <!-- date on/off switch-->
                <td class="align-middle py-0 pe-0" style="width: 1%;">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="new-to-do-flag-enabler">
                  </div>
                </td>
              </tr>
            </table>
          </div>
          <!-- priority form -->
          <div class="form-group">
            <label for="new-to-do-priority">Priority</label>
            <select class="form-control" id="new-to-do-priority">
              <option>Low</option>
              <option>Medium</option>
              <option>High</option>
            </select>
          </div>

          <!-- modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" onclick="addItem()" class="btn btn-primary" name="addToDo" id="addToDo" data-bs-dismiss="modal">Add</button>
          </div>
        </div>
      </div>
    </div>
</form>
</div>
<!--reading in all list-->
<?php
require_once("../includes/dbh.inc.php");
$todoID = $_SESSION["userid"];
$sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoID ORDER BY todoPriority DESC;";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) { ?>
  <!-- all view modals -->
  <div class="modal fade" id="viewModal-<?php echo $row['todoID'] ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-bs-backdrop='static'>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <!-- modal header-->
        <div class="modal-header" style="background-color: #354182">
          <div class="modal-header-container container-fluid text-center">
            <!-- modal title-->
            <p class="to-do-list-title h4 m-0" style="color: #FFFFFF; font-weight: 600" id="addModalTitle">View
              Information</p>
          </div>
        </div>
        <!-- modal body -->
        <div class="modal-body">
          <!-- title form -->
          <div class="form-group">
            <p>Title</p>
            <p class="form-control text-break">
              <?php echo $row['todoInformation'] ?>
            </p>
          </div>
          <!-- notes form -->
          <div class="form-group">
            <p>Notes</p>
            <p class="form-control text-break">
              <?php
              if ($row['todoNotes'] == "") {
                echo "n/a";
              } else {
                echo $row['todoNotes'];
              }
              ?>
            </p>
          </div>
          <!-- date form -->
          <div class="form-group">
            <p>Date</p>
            <p class="form-control" placeholder="Date">
              <?php
              if ($row['todoDueDate'] == "0001-01-01") {
                echo "n/a";
              } else {
                $todoDueDate_pre_format = date_create($row["todoDueDate"]);
                $todoDueDate = date_format($todoDueDate_pre_format, "d/m/Y");
                echo $todoDueDate;
              }
              ?>
            </p>
          </div>
          <!-- flag form -->
          <div class="form-group">
            <p>Flag</p>
            <p class="form-control" placeholder="Flag">
              <?php
              if ($row['todoFlag'] == "0") {
                echo "n/a";
              } else {
                echo "Flagged";
              }
              ?>
            </p>
          </div>

          <!-- priority form -->
          <div class="form-group">
            <p>Priority</p>
            <p class="form-control">
              <?php
              require_once("functions.php");
              echo getPriorityText($row["todoPriority"]);
              ?>
            </p>
          </div>

        </div>
        <!-- modal footer -->
        <div class="modal-footer d-flex justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- all edit modals -->
  <form method="post">
    <div class="modal fade" id="editModal-<?php echo $row['todoID'] ?>" tabindex="-1" role="dialog" aria-labelledby="updateTitle" aria-hidden="true" data-bs-backdrop='static'>
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <!-- modal header-->
          <div class="modal-header" style="background-color: #354182">
            <div class="modal-header-container container-fluid text-center">
              <!-- modal title-->
              <p class="to-do-list-title h4 m-0" style="color: #FFFFFF; font-weight: 600" id="updateTitle">Details</p>
            </div>
          </div>
          <!-- modal body -->
          <div class="modal-body">
            <!-- title form -->
            <div class="form-group">
              <!-- id is used to check whether used has entered a title -->
              <textarea class="form-control" id="update-to-do-information-<?php echo $row['todoID'] ?>" rows="1" placeholder="Title"><?php echo $row['todoInformation']; ?></textarea>
            </div>
            <script>
              $("#update-to-do-information-<?php echo $row['todoID'] ?>").on("keyup", (function() {
                "" == $.trim($("#update-to-do-information-<?php echo $row['todoID'] ?>").val()) ? $("#updateToDo-<?php echo $row['todoID'] ?>").prop("disabled", !0) : $("#updateToDo-<?php echo $row['todoID'] ?>").prop("disabled", !1)
              }));
            </script>
            <!-- notes form -->
            <div class="form-group pt-2">
              <textarea class="form-control" rows="3" id="update-to-do-notes-<?php echo $row['todoID'] ?>" placeholder="Notes"><?php echo $row["todoNotes"]; ?></textarea>
            </div>
            <!-- date form -->
            <div class="form-group mt-2">
              <table class="table table-borderless">
                <tr>
                  <td class="align-middle p-0" py-1 style="width: 1%;">
                    <!-- calender icon container-->
                    <div class="icon-container rounded-circle align-middle" style="background-color: #CC3232;">
                      <i class="fa fa-calendar p-2" style="color: #FFFFFF;"></i>
                    </div>
                  </td>
                  <!-- date textfield-->
                  <td class="align-middle py-0"><input class="form-control" type="text" id="datepicker-<?php echo $row['todoID'] ?>" disabled readonly placeholder="Date">
                  </td>
                  <!-- date on/off switch-->
                  <td class="align-middle py-0 pe-0" style="width: 1%;">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" id="date-enabler-<?php echo $row['todoID'] ?>">
                    </div>
                  </td>
                </tr>
              </table>
            </div>
            <!--script for date form functionality-->
            <script>
              $((function() {
                $("#datepicker-<?php echo $row['todoID'] ?>").datepicker()
              })), $("#date-enabler-<?php echo $row['todoID'] ?>").on("change", (function() {
                if ($(this).is(":checked")) {
                  $("#datepicker-<?php echo $row['todoID'] ?>").prop("disabled", !1);
                  let tempDate = new Date
                  let e = [tempDate.getMonth() + 1, tempDate.getDate(), tempDate.getFullYear()].join('/');
                  document.getElementById("datepicker-<?php echo $row['todoID'] ?>").value = e
                } else $("#datepicker-<?php echo $row['todoID'] ?>").prop("disabled", !0), document.getElementById("datepicker-<?php echo $row['todoID'] ?>").value = ""
              })), $("#new-to-do-information").on("keyup", (function() {
                "" == $.trim($("#new-to-do-information").val()) ? $("#addToDo").prop("disabled", !0) : $("#addToDo").prop("disabled", !1)
              }));
            </script>
            <?php
            //if statement to determine if user has selected a due date
            if ($row["todoDueDate"] != "0001-01-01") {
            ?>
              <script>
                $("#date-enabler-<?php echo $row['todoID'] ?>").prop("checked", !0).trigger("change"), $("#datepicker-<?php echo $row['todoID'] ?>").prop("disabled", !1);
              </script>
            <?php
              $todoDueDate_pre_format = date_create($row["todoDueDate"]);
              $todoDueDate = date_format($todoDueDate_pre_format, "m/d/Y");
              //setting textbox due date
              echo "<script>document.getElementById('datepicker-" . $row["todoID"] . "').value = " . "'$todoDueDate'" . "</script>";
            } ?>
            <!-- flag form -->
            <div class="form-group mt-2">
              <table class="table table-borderless">
                <tr>
                  <td class="py-0 ps-0" style="width: 1%;">
                    <!-- flag icon container -->
                    <div class="icon-container-circular rounded-circle" style="background-color: #FFBF00;">
                      <i class="fa fa-flag p-2" style="color: #FFFFFF"></i>
                    </div>
                  </td>
                  <!-- date textfield-->
                  <td class="align-middle py-0">
                    <p class="m-0">Flag</p>
                  </td>
                  <!-- date on/off switch-->
                  <td class="align-middle py-0 pe-0" style="width: 1%;">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" id="flag-enabler-<?php echo $row['todoID'] ?>">
                    </div>
                  </td>
                </tr>
              </table>
            </div>
            <?php
            //if statement to determine if user has selected a flag
            if ($row["todoFlag"] == 1) {
            ?>
              <script>
                $("#flag-enabler-<?php echo $row['todoID'] ?>").prop('checked', true).trigger('change');
              </script>
            <?php
            }
            ?>
            <!-- priority form -->
            <div class="form-group my-2">
              <label for="update-to-do-priority">Priority</label>
              <select class="form-control" id="update-to-do-priority-<?php echo $row['todoID'] ?>">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
              </select>
            </div>
            <script>
              document.getElementById("update-to-do-priority-<?php echo $row['todoID'] ?>").selectedIndex = <?php echo $row['todoPriority'] ?> - 1;
            </script>
          </div>

          <!-- modal footer -->
          <div class="modal-footer d-flex justify-content-center">
            <!-- invisible id-->
            <input type="hidden" name="update-to-do-id" value="<?php echo $row['todoID'] ?>"></input>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" onclick="deleteItem(<?php echo $row['todoID'] ?>)" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
            <button type="button" onclick="updateItem(<?php echo $row['todoID'] ?>)" id="updateToDo-<?php echo $row['todoID'] ?>" class="btn btn-primary" data-bs-dismiss="modal">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="clearModal" tabindex="-1" aria-labelledby="clearModalLabel" aria-hidden="true" data-bs-backdrop='static'>
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <!-- modal header-->
        <div class="modal-header" style="background-color: #354182">
          <div class="modal-header-container container-fluid text-center">
            <!-- modal title-->
            <p class="h4 m-0" style="color: #FFFFFF; font-weight: 600" id="addModalTitle">Clear All</p>
          </div>
        </div>
        <div class="modal-body text-nowrap">
          Are you sure you want to clear all your completed to-do's?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" onclick="clearAll()" data-bs-dismiss="modal" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
  </div>
<?php } ?>