<?php

// start the session
session_start();

// if user is not logged in send them to the login page
if (!isset($_SESSION["valid"]) || !isset($_SESSION)) {
  header("Location: login.php");
  exit();
}

?>

<?php

// set the title and session variables
$page = "home";
$title = "Dashboard";
$_SESSION["title"] = $title;
$currentPage = "Dashboard";
// get the userID of the user who is currently logged in
$userID = $_SESSION["userid"];

// inclide the header
include_once('includes/header.inc.php');
?>

<script>
  removeBackgrounds();
  setBackgrounds(0);
</script>

<main class="container" id="dashboard">
  <div class="row filter">
    <!-- buttons to show personal and company statistics -->
    <ul class="nav nav-underline nav-filter" role="tablist">
      <li class="nav-item" role="presentation">
        <!-- button to show personal statistics -->
        <button class="nav-link active nav-filter-link" id="personal-tab" data-bs-toggle="tab"
          data-bs-target="#personal-tab-pane" type="button" role="tab">Personal Statistics</button>
      </li>
      <?php
      // if the user is a member
      if ($_SESSION["role"] == "member") {
        // do nothing
      } else {
        // display the company statistics button
        ?>
        <li class="nav-item" role="presentation">
          <button class="nav-link nav-filter-link" id="everyoneElse-tab" data-bs-toggle="tab"
            data-bs-target="#everyoneElse-tab-pane" type="button" role="tab">

            <?php
            // change the button text depending on the role of the user
            if ($_SESSION["role"] == "leader") {
              echo "Your Project's Statistics";
            } else if ($_SESSION["role"] == "manager") {
              echo "Company Statistics";
            }
            ?>
          </button>
        </li>
        <?php
      }
      ?>
    </ul>

    <div>
      <?php
      // require the database and the functions so that they can be used in the file
      require_once("includes/dbh.inc.php");
      require_once("includes/functions.inc.php");

      // get todays date
      $today = date("Y-m-d");

      // select the number of backlogged and overdue tasks
      $sql = "SELECT COUNT(taskID) as backlogTasksCount, (SELECT COUNT(taskID) FROM taskDetails WHERE taskStatus != 'Complete' AND taskDeadline < '$today') AS overdueTasksCount FROM taskDetails WHERE taskStatus = 'Backlog'";
      $result = $conn->query($sql);
      $row = $result->fetch_assoc();

      // put the sql query results into variables to be used later on
      $backlogTasksCount = $row["backlogTasksCount"];
      $overdueTasksCount = $row["overdueTasksCount"];

      // select the number of projects and overdue projects
      $sql = "SELECT COUNT(projectID) AS amountProjectsCount, (SELECT COUNT(projectID) FROM projectDetails WHERE projectProgress != 100 AND projectDate < '$today') AS overdueProjectsCount FROM projectDetails;";
      $result = $conn->query($sql);
      $row = $result->fetch_assoc();

      // put the results of the sql query into variables to be used later
      $amountProjectsCount = $row["amountProjectsCount"];
      $overdueProjectsCount = $row["overdueProjectsCount"];

      // select the number of backlogged and overdue tasks that are specific to the user
      $sql = "SELECT COUNT(taskDetails.taskID) as personalBacklogTasksCount,
       (SELECT COUNT(taskDetails.taskID) 
       FROM taskDetails, taskUser
       WHERE taskStatus != 'Complete' AND 
       taskDeadline < '$today' AND taskUser.userID = $userID AND 
       taskUser.taskID = taskDetails.taskID) AS personalOverdueTasksCount,
       (SELECT COUNT(taskDetails.taskID) 
       FROM taskDetails, projectDetails, taskProject
       WHERE projectDetails.projectLeaderID = $userID AND
       projectDetails.projectID = taskProject.projectID AND
       taskProject.taskID = taskDetails.taskID AND
       taskDetails.taskStatus = 'Backlog') AS leaderBacklogTasksCount,
       (SELECT COUNT(taskDetails.taskID) 
       FROM taskDetails, projectDetails, taskProject
       WHERE projectDetails.projectLeaderID = $userID AND
       projectDetails.projectID = taskProject.projectID AND
       taskProject.taskID = taskDetails.taskID AND
       taskStatus != 'Complete' AND 
       taskDeadline < '$today') AS leaderOverdueTasksCount 
       FROM taskDetails, taskUser 
       WHERE taskUser.userID = $userID AND taskUser.taskID = taskDetails.taskID AND taskDetails.taskStatus = 'Backlog'";

      $result = $conn->query($sql);
      $row = $result->fetch_assoc();

      // put the sql query result into variables to be used later
      $personalBacklogTasksCount = $row["personalBacklogTasksCount"];
      $personalOverdueTasksCount = $row["personalOverdueTasksCount"]; #
      $leaderBacklogTasksCount = $row["leaderBacklogTasksCount"];
      $leaderOverdueTasksCount = $row["leaderOverdueTasksCount"];

      // select the number of projects and oversur projects that are specific to the user
      $sql = "SELECT COUNT(projectDetails.projectID) AS personalAmountProjectsCount,
      (SELECT COUNT(projectDetails.projectID) 
        FROM projectDetails, userProject 
        WHERE projectDetails.projectProgress != 100 AND
        projectDetails.projectDate < '$today' AND
        projectDetails.projectID = userProject.projectID AND
        userProject.userID = $userID) AS personalOverdueProjectsCount,
        (SELECT COUNT(projectID) 
        FROM projectDetails 
        WHERE projectLeaderID = $userID ) AS leaderAmountProjectsCount,
        (SELECT COUNT(projectDetails.projectID) 
        FROM projectDetails, userProject 
        WHERE projectDetails.projectProgress != 100 AND
        projectDetails.projectDate < '$today' AND
        projectDetails.projectID = userProject.projectID AND
        projectLeaderID = $userID) AS leaderOverdueProjectsCount
       FROM projectDetails, userProject 
       WHERE projectDetails.projectID = userProject.projectID AND userProject.userID = $userID  ";
      $result = $conn->query($sql);
      $row = $result->fetch_assoc();

      // put the sql result into variables to be used later
      $personalAmountProjectsCount = $row["personalAmountProjectsCount"];
      $personalOverdueProjectsCount = $row["personalOverdueProjectsCount"];
      $leaderAmountProjectsCount = $row["leaderAmountProjectsCount"];
      $leaderOverdueProjectsCount = $row["leaderOverdueProjectsCount"];
      ?>
    </div>
    <!-- information to be displayed in the personal tab -->
    <div class="tab-content" id="personalTabContent">
      <div class="tab-pane fade show active" id="personal-tab-pane" role="tabpanel" aria-labelledby="personal-tab"
        tabindex="-1">
        <div class="top-section">
          <div class="top-section-item" style="--bg-clr: var(--clr-urgent)">
            <i class="bi  bi-pause-fill"></i>
            <div class=" top-section-item-text">
              <!-- display backlogged tasks -->
              <Span>Backlogged Tasks<br>
                <span class="top-section-item-caption">
                  <?php echo $personalBacklogTasksCount ?>
                </span>
              </Span>
            </div>
          </div>
          <div class="top-section-item" style="--bg-clr: var(--clr-error)">
            <i class="bi bi-calendar2-x"></i>
            <div class="top-section-item-text">
              <!-- display overdue tasks -->
              <Span>Overdue Tasks<br>
                <span class="top-section-item-caption">
                  <?php echo $personalOverdueTasksCount ?>
                </span>
              </Span>
            </div>
          </div>
          <?php
          // if user is not a manager
          if ($_SESSION["role"] !== "manager") {
            ?>
            <!-- display the number of projects that they are involed in -->
            <div class="top-section-item" style="--bg-clr: var(--clr-pending)">
              <i class="bi bi-bar-chart-line-fill"></i>
              <div class=" top-section-item-text">
                <Span>Amount of Projects <br>
                  <span class="top-section-item-caption">
                    <?php echo $personalAmountProjectsCount ?>
                  </span>
                </Span>
              </div>
            </div>
            <?php
          }
          ?>
          <?php
          // if user is not a manager
          if ($_SESSION["role"] !== "manager") {
            ?>
            <!-- display the number of projects they are in that are overdue -->
            <div class="top-section-item" style="--bg-clr: var(--clr-sucess)">
              <i class="bi bi-calendar2-x"></i>
              <div class=" top-section-item-text">
                <Span>Overdue Projects <br>
                  <span class="top-section-item-caption">
                    <?php echo $personalOverdueProjectsCount ?>
                  </span>
                </Span>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <div class="tab-pane fade" id="everyoneElse-tab-pane" role="tabpanel" aria-labelledby="everyoneElse-tab"
        tabindex="-1">
        <div class="top-section">
          <?php
          // if the user is a team leader
          if ($_SESSION["role"] === "leader") {
            ?>
            <!-- display the number of backlogged tasks -->
            <div class="top-section-item" style="--bg-clr: var(--clr-urgent)">
              <i class="bi  bi-pause-fill"></i>
              <div class=" top-section-item-text">
                <Span>
                  Backlogged Tasks
                  <br>
                  <span class="top-section-item-caption">
                    <?php echo $leaderBacklogTasksCount; ?>
                  </span>
                </Span>
              </div>
            </div>
            <?php
          }
          ?>
          <?php
          // if the user is a team leader
          if ($_SESSION["role"] === "leader") {
            ?>
            <!-- display the number of overdue tasks -->
            <div class="top-section-item" style="--bg-clr: var(--clr-error)">
              <i class="bi bi-calendar2-x"></i>
              <div class="top-section-item-text">
                <Span>Overdue Tasks<br>
                  <span class="top-section-item-caption">
                    <?php echo $leaderOverdueTasksCount; ?>
                  </span>
                </Span>
              </div>
            </div>
            <?php
          } ?>
          <div class="top-section-item" style="--bg-clr: var(--clr-pending)">
            <i class="bi bi-bar-chart-line-fill"></i>
            <div class=" top-section-item-text">
              <Span>
                <?php if ($_SESSION["role"] === "leader") {
                  echo "Projects Led";
                } 
                else if ($_SESSION["role"] === "manager") {
                  echo "Amount of Projects";
                }
                ?>
                <br>
                <span class="top-section-item-caption">
                  <?php
                  // if the user is a manager display the number of projects
                  if ($_SESSION["role"] == "manager") {
                    echo $amountProjectsCount;
                  } 
                  // else display the number of projects that the user is leading
                  else {
                    echo $leaderAmountProjectsCount;
                  } ?>
                </span>
              </Span>
            </div>
          </div>
          <div class="top-section-item" style="--bg-clr: var(--clr-sucess)">
            <i class="bi bi-calendar2-x"></i>
            <div class=" top-section-item-text">
              <Span>
                <?php if ($_SESSION["role"] === "leader") {
                  echo "Overdue Projects";
                } 
                // else if the user is a manager display the number of overdue projects
                else if ($_SESSION["role"] === "manager") {
                  echo "Overdue Projects";
                }
                ?>
                <br>
                <span class="top-section-item-caption">
                  <?php
                  // if the user is a manager display the number of overdue projects
                  if ($_SESSION["role"] == "manager") {
                    echo $overdueProjectsCount;
                  } 
                  // else if the user is a leader display the number of overdue projects that they are leading
                  else {
                    echo $leaderOverdueProjectsCount;
                  } ?>
                </span>
              </Span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <!-- Users -->
  <?php
  // if the user is a manager display content about the users
  if ($_SESSION["role"] === "manager") {
    ?>
    <div class="row pb-3">
      <p class="todoHeader">
        Users
      </p>
      <div class="users-tab-content">
        <div class="users-tab-content-item">
          <?php
          // sql statement to select the number of users that have tasks
          $sql = "SELECT COUNT(userID) as WithTasks FROM taskUser;";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();

          // put the result of the sql query in a variable to be used later
          $withTasks = $row["WithTasks"];

          // format the caption depending on whether the users are plural or not
          if ($withTasks == 1) {
            $withTasksFormat = $withTasks . " User With Tasks";
          } else {
            $withTasksFormat = $withTasks . " Users With Tasks";
          }

          // sql statement to select the number of users who are in a project and don't have any tasks
          $sql = "select count(userID) as WithoutTasks from userDetails where userID NOT IN (select userDetails.userID from userDetails, taskUser where userDetails.userID = taskUser.userID );";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();

          // put the result of the sql query in a variable to be used later
          $withoutTasks = $row["WithoutTasks"];

          // format the caption depending on whether the users are plural or not
          if ($withoutTasks == 1) {
            $withoutTasksFormat = $withoutTasks . " User Without Tasks";
          } else {
            $withoutTasksFormat = $withoutTasks . " Users Without Tasks";
          }

          // sql statement to select the number of users that are available (not in a project and therefor also have no tasks)
          $sql = "select count(userID) as Available from userDetails where userID NOT IN (select userDetails.userID from userDetails, userProject where userDetails.userID = userProject.userID );";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();

          // put the result of the sql query in a variable to be used later
          $available = $row["Available"];

          // format depending on whether the user is plural or not
          if ($available == 1) {
            $availableFormat = $available . " User Not In Project";
          } else {
            $availableFormat = $available . " Users Not In A Project";
          }

          // calculate the total number of tasks 
          $userTasksTotal = $withTasks + $withoutTasks + $available;

          // determine the colour for the text in the center of the doughnut chart
          $withTasksColor;
          if ($withTasks < $userTasksTotal / 2) {
            $withTasksColor = "#e97777";
          } else if ($withTasks == $userTasksTotal / 2) {
            $withTasksColor = "#98a8f8";
          } else if ($withTasks > $userTasksTotal / 2) {
            $withTasksColor = "#bcdcbe";
          }
          ?> 

          <!-- div for the first users chart -->
          <div class="users-tab-chart">
            <!-- chart canvas -->
            <canvas id="userChart1" width="100" height="100">
            </canvas>
            <!-- display user information in the center of the chart -->
            <div class="users-tab-chart-content">
              <!-- users with tasks -->
              <span class="users-tab-chart-primary-num" style="color: <?php echo $withTasksColor ?> !important">
                <?php echo $withTasks ?>
              </span>
              <hr>
              <span class="users-tab-chart-secondary-num">
                <!-- total users -->
                <?php echo $userTasksTotal ?>
              </span>
            </div>
          </div>
          <!-- chart captions -->
          <div class="users-tab-chart-caption">
            <span class="users-caption">
              <!-- users with tasks -->
              <?php echo $withTasksFormat ?>
            </span>
            <span class="users-caption">
              <!-- users without tasks -->
              <?php echo $withoutTasksFormat ?>
            </span>
            <span class="users-caption">
              <!-- available users -->
              <?php echo $availableFormat ?>
            </span>
          </div>
        </div>
        
        <div class="users-tab-content-item">
          <?php

          // sql statement to select the number of users
          $sql = "SELECT COUNT(userID) as userCount FROM userDetails WHERE userInvited = 0;";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();

          // store the result of the sql query in a variable to be used later
          $userCount = $row["userCount"];

          // format the user caption to be plural or not
          if ($userCount == 1) {
            $userCountFormat = $userCount . " User";
          } else {
            $userCountFormat = $userCount . " Users";
          }

          // select the number of users who have been invited
          $sql = "SELECT COUNT(userID) as userInvitedCount FROM userDetails WHERE userInvited = 1;";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();

          //store the result of the sql query in a variable to be used later
          $userInvitedCount = $row["userInvitedCount"];

          // format the caption to be plural or not
          if ($userInvitedCount == 1) {
            $userInvitedCountFormat = $userInvitedCount . " Invited User";
          } else {
            $userInvitedCountFormat = $userInvitedCount . " Invited Users";
          }

          // calculate the total number of users
          $userCountTotal = $userCount + $userInvitedCount;

          // calulate the colur based on the number of users
          $userCountColor;
          if ($userCount < $userCountTotal / 2) {
            $userCountColor = "#e97777";
          } else if ($userCount == $userCountTotal / 2) {
            $userCountColor = "#98a8f8";
          } else if ($userCount > $userCountTotal / 2) {
            $userCountColor = "#bcdcbe";
          }
          ?>

          <!-- div for the second users chart -->
          <div class="users-tab-chart">
            <!-- chart canvas -->
            <canvas id="userChart2" width="100" height="100">
            </canvas>
            <!-- display the user information in the center of the chart -->
            <div class="users-tab-chart-content">
              <!-- users -->
              <span class="users-tab-chart-primary-num" style="color: <?php echo $userCountColor ?> !important">
                <?php echo $userCount ?>
              </span>
              <hr>
              <!-- total users -->
              <span class="users-tab-chart-secondary-num">
                <?php echo $userCountTotal ?>
              </span>
            </div>
          </div>
          <!-- chart captions -->
          <div class="users-tab-chart-caption">
            <!-- users -->
            <span class="users-caption">
              <?php echo $userCountFormat ?>
            </span>
            <!-- invited users -->
            <span class="users-caption">
              <?php echo $userInvitedCountFormat ?>
            </span>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <div class="row todo">
    <p class="todoHeader">
      To-Do
    </p>
    <!-- display users to-do items -->
    <ul class="todoContent">
      <!--reading in flag list-->
      <?php
      $todoOwnderID = $_SESSION["userid"];
      // sql statement to select the to-do's that are specific to the user that is logged in
      $sql = "SELECT * FROM todoList WHERE todoOwnerID = $todoOwnderID AND todoCompletion = 0 ORDER BY todoPriority DESC, todoID ASC LIMIT 3;";
      $result = $conn->query($sql);
      // if the reault has no rows then display the message below
      if (mysqli_num_rows($result) == 0) {
        ?>
        <div
          class="noProjectItem projectListItems d-flex flex-grow flex-column text-center align-items-center gap-0 justify-content-center p-3">
          <i class="bi bi-patch-exclamation" style="font-size: 5rem; color: var(--clr-error);"></i>
          <div class="d-flex flex-column gap-1" style="color: var(--clr-placeholder);">
            <span style="color: var(--clr-grey600);font-size: 4rem; letter-spacing: 0.5px;">NO TO-DO'S FOUND</span>
            <a href="to-do-list.php" class="text-decoration-none d-inline-block justify-content-center">
              <button class="btn btn-outline-secondary">
                Try
                Creating a new
                To-Do
              </button>
            </a>
          </div>
        </div>
        <?php
      } 
      // else if the query does contain data then display the to-do items
      else {
        while ($row = $result->fetch_assoc()) {

          // get the information about the to-do's
          $todoID = $row["todoID"];
          $todoTitle = $row["todoInformation"];
          $todoPriority = $row["todoPriority"];
          $todoCompletion = $row["todoCompletion"];
          $todoOwnerID = $row["todoOwnerID"];
          $todoDate = $row["todoDueDate"];
          $todoDesc = $row["todoNotes"];

          // set the colur based on the priority of the to-do
          $todoBGC;
          if ($todoPriority == 3) {
            $todoBGC = "#db7b2b";
          } else if ($todoPriority == 2) {
            $todoBGC = "#e7b416";
          } else if ($todoPriority == 1) {
            $todoBGC = "#99c140";
          } 
          
          if ($todoDate == "0000-00-00") {
            $todoDate = "No Deadline";
          }

          ?>
          <!-- display the to-do items in a list -->
          <li class=" todoItems" id="toDo<?php $todoID ?>">
            <div class="todo-card">
              <div class="todo-card-header" tabindex="-1" style="background-color: <?php echo "$todoBGC" ?>;">
                <div class="todo-card-header-left">
                  <i class="bi bi-patch-exclamation-fill todo-card-header-icon"></i>
                  <span class="todo-card-header-deadline">
                    <?php echo $todoDate ?>
                  </span>
                </div>
                <a class="btn todo-card-header-btn" href="to-do-list.php">
                  View
                </a>
              </div>
              <div class="todo-card-body" tabindex="-1">
                <span class="todo-card-body-title" id="todoNameTitle">
                  <?php echo $todoTitle ?>
                </span>
                <span class="todo-card-body-desc">
                  <?php echo truncateText($todoDesc, 50); ?>
                </span>
                <span class="todo-card-body-id" id="<?php echo $todoID ?>">
                  <?php echo $todoID ?>
                </span>
              </div>
            </div>
          </li>
          <?php
        }
      }
      ?>
    </ul>

  </div>
  </div>
</main>

<?php
// if the user is a manager get the canvas elements to render the charts
if ($_SESSION["role"] == "manager") {
  ?>
  <script>
    // get canvas elements
    var user1 = document.getElementById('userChart1').getContext('2d');
    var user2 = document.getElementById('userChart2').getContext('2d');

    // set the options for the doughnut charts
    var options = { 
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
      // maintainAspectRatio: true,

    };

    // create a new chart to be rendered (users tasks chart)
    var userChart1 = new Chart(user1, {
      type: 'doughnut',
      data: {
        labels: [
          'Users With Tasks',
          'Users Without Tasks',
          'Users Not In A Project'
        ],
        datasets: [{
          data: [<?php echo $withTasks ?>, <?php echo $withoutTasks ?>, <?php echo $available ?>],
          backgroundColor: [
            '#BCDCBE', // Completed
            '#98A8F8', // Pending
            '#E97777' // Overdue
          ],
          hoverOffset: 4,
          cutout: "67.5%",

        }]
      },
      options: options
    });

     // create a new chart to be rendered (users/users invited chart)
    var userChart2 = new Chart(user2, {
      type: 'doughnut',
      data: {
        labels: [
          'Users',
          'Invited Users'
        ],
        datasets: [{
          data: [<?php echo $userCount ?>, <?php echo $userInvitedCount ?>],
          backgroundColor: [
            '#BCDCBE', // Completed
            '#98A8F8', // Pending
          ],
          hoverOffset: 4,
          cutout: "67.5%",

        }]
      },
      options: options
    });

  </script>
  <?php
}
?>

<?php
// include the footer
include_once('includes/footer.inc.php');
?>