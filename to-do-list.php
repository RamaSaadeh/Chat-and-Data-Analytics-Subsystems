<?php
session_start();
echo "<script>let userid = " . $_SESSION['userid'] . "</script>"; //used for adding item
//if logged in
if (!isset($_SESSION["valid"])) {
  //redirect if session is not valid
  header("Location: login.php");
  exit();
}
//setting titles and pages
$title = "To-Do";
$_SESSION["title"] = $title;
$currentPage = "To-Do";
// $page = "lists";
//setting header
if ($_SESSION["role"]) {
  include_once('includes/header.inc.php'); //member header
?>
  <script>
    removeBackgrounds()
    setBackgrounds(3)
  </script>
<?php
}
?>

<!--linking to account css stylesheet-->
<link rel="stylesheet" href="styles/todolist.css">

<script>
  //minified(see local file for better layout)
  function reloadPage() {
    //loading in counts
    $.get("to-do-list/counts/count-all.php", (function(t) {
      const n = t.split(",");
      document.getElementById("div-flag-count").innerHTML = n[0], document.getElementById("div-all-count").innerHTML = n[1], document.getElementById("div-today-count").innerHTML = n[2], document.getElementById("div-completed-count").innerHTML = n[3]
    }));
    //loading in chart 
    $("#my-list-statistics-2").load("to-do-list/counts/count-all-chart.php")
    //loading in tables
    $("#div-flag").load("to-do-list/tables/flagged-table.php"), $("#div-all").load("to-do-list/tables/all-table.php"), $("#div-today").load("to-do-list/tables/today-table.php"), $("#div-completed").load("to-do-list/tables/completed-table.php"), $("#modals").load("to-do-list/modals.php")
  }

  function hideClearAll() {
    document.getElementById("clear-all-button").style.display = 'none'
  }

  $(document).ready((function() {
    reloadPage();
    hideClearAll(); //when document is ready hide clear all button
  }));
</script>
<main>
  <!-- card container -->
  <div class="container p-0 h-auto overflow-hidden">
    <div class="row mx-1">
      <!-- control centre column-->
      <div class="col col-lg-6 m-0 py-3 d-flex h-auto">
        <div class="to-do-panels border-0 mx-auto rounded shadow d-flex flex-column p-3 w-100">
          <!-- first row of options-->
          <div class="row">
            <!-- flagged column -->
            <div class="col p-2">
              <nav>
                <div class="nav">
                  <div class="to-do-selection-panel border-0 mx-auto rounded shadow d-flex flex-column p-3 w-100" type="button" style="color: #000000" id="flag-panel">
                    <!-- panel header container -->
                    <div class="header-container">
                      <table class="mb-2">
                        <tbody>
                          <tr>
                            <td style="width: 1%">
                              <!-- icon container -->
                              <div class="icon-container-circular rounded-circle" style="background-color: #FFD966;">
                                <i class="fa fa-flag p-2" style="color: #FFFFFF"></i>
                              </div>
                            </td>
                            <!-- section counter -->
                            <td class="text-end">
                              <div id="div-flag-count"></div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- description -->
                    <p class="mt-4 mb-1">Flagged</p>
                    <!-- script for flag panel functionality -->
                    <script>
                      $("#flag-panel").mouseenter((function() {
                        document.getElementById("flag-panel").style.backgroundColor = "#ECECEC"
                      })), $("#flag-panel").mouseleave((function() {
                        document.getElementById("flag-panel").style.backgroundColor = "white"
                      })), $("#flag-panel").click((function() {
                        document.getElementById("nav-flag-tab").click(), document.getElementById("clear-all-button").style.display = 'none', document.getElementById("flag-panel").style.backgroundColor = "white", document.getElementById("nav-header").style.color = "#FFD966", document.getElementById("nav-header").innerHTML = "Flagged", document.getElementById("nav-footer-icon").style.color = "#FFD966", document.getElementById("nav-footer-text").style.color = "#FFD966";
                        if ($(window).width() <= 991) {
                          window.location = "#div-flag"
                        }
                        for (var e = document.getElementsByClassName("edit-item"), t = 0; t < e.length; t++) e[t].style.color = "#FFD966"
                      }));
                    </script>
                  </div>
                </div>
              </nav>
            </div>
            <div class="col p-2">
              <div class="to-do-selection-panel border-0 mx-auto rounded shadow d-flex flex-column p-3 w-100" type="button" style="color: #000000" id="all-panel">
                <!-- panel header container -->
                <div class="header-container">
                  <table class="mb-2">
                    <tbody>
                      <tr>
                        <td style="width: 1%">
                          <!-- icon container -->
                          <div class="icon-container-circular rounded-circle" style="background-color: #E97777;">
                            <i class="fa fa-archive p-2" style="color: #FFFFFF"></i>
                          </div>
                        </td>
                        <!-- section counter -->
                        <td class="text-end">
                          <div id="div-all-count"></div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- description -->
                <p class="mt-4 mb-1">Pending</p>

                <!-- script for all panel functionality -->
                <script>
                  $("#all-panel").mouseenter((function() {
                    document.getElementById("all-panel").style.backgroundColor = "#ECECEC"
                  })), $("#all-panel").mouseleave((function() {
                    document.getElementById("all-panel").style.backgroundColor = "white"
                  })), $("#all-panel").click((function() {
                    document.getElementById("nav-all-tab").click(), document.getElementById("clear-all-button").style.display = 'none', document.getElementById("all-panel").style.backgroundColor = "white", document.getElementById("nav-header").style.color = "#E97777", document.getElementById("nav-header").innerHTML = "Pending", document.getElementById("nav-footer-icon").style.color = "#E97777", document.getElementById("nav-footer-text").style.color = "#E97777"
                    if ($(window).width() <= 991) {
                      window.location = "#div-all"
                    }
                    for (var e = document.getElementsByClassName("edit-item"), l = 0; l < e.length; l++) e[l].style.color = "#E97777"
                  }));
                </script>

              </div>
            </div>
          </div>
          <div class="row">
            <div class="col p-2">
              <div class="to-do-selection-panel border-0 mx-auto rounded shadow d-flex flex-column p-3 w-100" type="button" style="color: #000000" id="today-panel">
                <!-- panel header container -->
                <div class="header-container">
                  <table class="mb-2">
                    <tbody>
                      <tr>
                        <td style="width: 1%">
                          <!-- icon container -->
                          <div class="icon-container-circular rounded-circle" style="background-color: #98A8F8;">
                            <i class="fa fa-calendar-o p-2" style="color: #FFFFFF"></i>
                          </div>
                        </td>
                        <!-- section counter -->
                        <td class="text-end">
                          <div id="div-today-count"></div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- description -->
                <p class="mt-4 mb-1">Today</p>

                <!-- script for today panel functionality -->
                <script>
                  $("#today-panel").mouseenter((function() {
                    document.getElementById("today-panel").style.backgroundColor = "#ECECEC"
                  })), $("#today-panel").mouseleave((function() {
                    document.getElementById("today-panel").style.backgroundColor = "white"
                  })), $("#today-panel").click((function() {
                    document.getElementById("nav-today-tab").click(), document.getElementById("clear-all-button").style.display = 'none', document.getElementById("today-panel").style.backgroundColor = "white", document.getElementById("nav-header").style.color = "#98A8F8", document.getElementById("nav-header").innerHTML = "Today", document.getElementById("nav-footer-icon").style.color = "#98A8F8", document.getElementById("nav-footer-text").style.color = "#98A8F8";
                    if ($(window).width() <= 991) {
                      window.location = "#div-today"
                    }
                    for (var e = document.getElementsByClassName("edit-item"), t = 0; t < e.length; t++) e[t].style.color = "#98A8F8"
                  }));
                </script>

              </div>
            </div>
            <div class="col p-2">
              <div class="to-do-selection-panel border-0 mx-auto rounded shadow d-flex flex-column p-3 w-100" type="button" style="color: #000000" id="completed-panel">
                <!-- panel header container -->
                <div class="header-container">
                  <table class="mb-2">
                    <tbody>
                      <tr>
                        <td style="width: 1%">
                          <!-- icon container -->
                          <div class="icon-container-circular rounded-circle" style="background-color: #D0E7D2;">
                            <i class="fa fa-check p-2" style="color: #FFFFFF"></i>
                          </div>
                        </td>
                        <!-- section counter -->
                        <td class="text-end">
                          <div id="div-completed-count"></div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- description -->
                <p class="mt-4 mb-1">Completed</p>

                <!-- script for completed panel functionality -->
                <script>
                  $("#completed-panel").mouseenter((function() {
                    document.getElementById("completed-panel").style.backgroundColor = "#ECECEC"
                  })), $("#completed-panel").mouseleave((function() {
                    document.getElementById("completed-panel").style.backgroundColor = "white"
                  })), $("#completed-panel").click((function() {
                    document.getElementById("nav-completed-tab").click(), document.getElementById("clear-all-button").style.display = 'block', document.getElementById("completed-panel").style.backgroundColor = "white", document.getElementById("nav-header").style.color = "#D0E7D2", document.getElementById("nav-header").innerHTML = "Completed", document.getElementById("nav-footer-icon").style.color = "#D0E7D2", document.getElementById("nav-footer-text").style.color = "#D0E7D2";
                    if ($(window).width() <= 991) {
                      window.location = "#div-completed"
                    }
                    for (var e = document.getElementsByClassName("edit-item"), t = 0; t < e.length; t++) e[t].style.color = "#D0E7D2"
                  }));
                </script>

              </div>
            </div>
          </div>
          <div class="row">
            <div class="col text-center">
              <p class="mt-3 mb-1 fs-2" style="font-weight: 600;">My List Statistics</p>
            </div>
          </div>
          <!--myLists row MINIFIED-->
          <div id=my-list-statistics-2 class="p-2" style=height:100%;overflow-y:auto></div>
        </div>
      </div>

      <!-- to do list column MINIFIED-->
      <div class="col m-0 py-3 d-flex h-auto">
        <div class="to-do-panels border-0 mx-auto rounded shadow d-flex flex-column p-3 w-100">
          <!--tab title MINIFIED -->
          <div id=nav-header style=font-weight:600;color:#E97777 class="px-3 display-5">Pending</div>
          <div id=clear-all-button-container style=height:3vh><button class="px-3 btn btn-link h6 mt-2 p-0" id=clear-all-button data-bs-target=#clearModal data-bs-toggle=modal style=text-decoration:none;font-weight:500;color:#98A8F8>Clear</button>
          </div>
          <!--navigation bar with tabs MINIFIED-->
          <nav hidden>
            <div class="mt-4 nav nav-tabs" id=nav-tab role=tablist><button aria-controls=nav-flag aria-selected=true class=nav-link data-bs-target=#nav-flag data-bs-toggle=tab hidden id=nav-flag-tab role=tab type=button>Flagged</button> <button aria-controls=nav-today aria-selected=true class=nav-link data-bs-target=#nav-today data-bs-toggle=tab hidden id=nav-today-tab role=tab type=button>Today</button>
              <button aria-controls=nav-all aria-selected=true class="nav-link active" data-bs-target=#nav-all data-bs-toggle=tab hidden id=nav-all-tab role=tab type=button>All</button> <button aria-controls=nav-completed aria-selected=true class=nav-link data-bs-target=#nav-completed data-bs-toggle=tab hidden id=nav-completed-tab role=tab type=button>Completed</button>
            </div>
          </nav>
          <!--navigation tab content MINIFIED-->
          <div class=tab-content id=nav-tabContent>
            <div class="fade p-3 tab-pane" id=nav-flag aria-labelledby=nav-flag-tab role=tabpanel>
              <div class="scrollable custom-scroll" id=div-flag style=height:65vh;overflow-y:auto></div>
            </div>
            <div class="fade p-3 tab-pane" id=nav-today aria-labelledby=nav-today-tab role=tabpanel>
              <div class=scrollable id=div-today style=height:65vh;overflow-y:auto></div>
            </div>
            <div class="fade p-3 tab-pane active show" id=nav-all aria-labelledby=nav-all-tab role=tabpanel>
              <div class=scrollable id=div-all style=height:65vh;overflow-y:auto></div>
            </div>
            <div class="fade p-3 tab-pane" id=nav-completed aria-labelledby=nav-completed-tab role=tabpanel>
              <div class=scrollable id=div-completed style=height:65vh;overflow-y:auto></div>
            </div>
          </div>
          <!--new item button container MINIFIED-->
          <div class="button-container d-grid px-3" data-bs-target=#addModal data-bs-toggle=modal type=button>
            <table>
              <tr>
                <td style=width:1%><i class="fa fa-plus-circle fs-4 me-2" id=nav-footer-icon style=color:#E97777></i>
                <td style=font-weight:600;color:#E97777 id=nav-footer-text>New Item
            </table>
          </div>
        </div>
      </div>
    </div>
</main>
<!-- modals div so jQuery can load -->
<span>
  <div id="modals"></div>
</span>
<!-- including footer -->
<?php include_once('includes/footer.inc.php'); ?>
<script>
  function deleteItem(e) {
    $("#delete-item").load("to-do-list/deleted.php?%20ID=" + e), setTimeout((function() {
      reloadPage()
    }), 500)
  }

  function addItem() {
    information = document.getElementById("new-to-do-information").value.trim().replace(/ /g, "_"), priority = document.getElementById("new-to-do-priority").value, "Low" == priority ? priority = 1 : "Medium" == priority ? priority = 2 : "High" == priority && (priority = 3), date = document.getElementById("new-to-do-datepicker").value, "" == date ? duedate = "0001-01-01" : (duedate = new Date(date), duedate = duedate.toISOString().split("T")[0]), notes = document.getElementById("new-to-do-notes").value.trim().replace(/ /g, "_"), flag = document.getElementById("new-to-do-flag-enabler").checked, 1 == flag ? flag = 1 : flag = 0, $("#add-item").load("to-do-list/new.php?%20ID=" + userid + "&%20INFORMATION=" + information + "&%20PRIORITY=" + priority + "&%20DUEDATE=" + duedate + "&%20NOTES=" + notes + "&%20FLAG=" + flag), setTimeout((function() {
      reloadPage()
    }), 500)
  }

  function updateItem(e) {
    information = document.getElementById("update-to-do-information-" + e).value.trim().replace(/ /g, "_"), priority = document.getElementById("update-to-do-priority-" + e).value, "Low" == priority ? priority = 1 : "Medium" == priority ? priority = 2 : "High" == priority && (priority = 3), date = document.getElementById("datepicker-" + e).value, "" == date ? duedate = "0001-01-01" : (duedate = new Date(date), duedate = duedate.toISOString().split("T")[0]), notes = document.getElementById("update-to-do-notes-" + e).value.trim().replace(/ /g, "_"), flag = document.getElementById("flag-enabler-" + e).checked, 1 == flag ? flag = 1 : flag = 0, $("#update-item").load("to-do-list/update.php?%20ID=" + e + "&%20INFORMATION=" + information + "&%20PRIORITY=" + priority + "&%20DUEDATE=" + duedate + "&%20NOTES=" + notes + "&%20FLAG=" + flag), setTimeout((function() {
      reloadPage()
    }), 500)
  }

  function clearAll() {
    $("#clear-all-items").load("to-do-list/clearall.php"), setTimeout(function() {
      reloadPage()
    }, 500)
  }
  $(document).on("click", ".priority-icons", (function() {
    setTimeout((function() {
      reloadPage()
    }), 500)
  }));
</script>
<span>
  <div id="delete-item"></div>
  <div id="update-item"></div>
  <div id="add-item"></div>
  <div id="clear-all-items"></div>
</span>