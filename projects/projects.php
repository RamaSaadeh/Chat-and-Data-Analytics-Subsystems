<?php

session_start();


?>

<?php

if (!isset($_SESSION["valid"])) {
    header("location: ../login.php?action=notLoggedIn");
    exit();
}

?>

<?php

$page = "projects";
$redirectedID;

if ($_SESSION['role']) {
    $title = "Projects";
    $_SESSION["title"] = $title;
    $currentPage = "Projects";
    if ($_SESSION["redirectedTasks"] == true) {
        $redirectedID = $_GET["redirectedID"];
        $_SESSION["redirectedTasks"] = false;
    } else {
        header("projects.php");
    }
    include_once('../includes/header.inc.php');
    ?>
    <script>
        removeBackgrounds();
        setBackgrounds(1);
    </script>
    <link rel="stylesheet" type="text/css" href="styles/projectFilter.css" />
    <link rel="stylesheet" type="text/css" href="styles/projectFilterMenu.css">
    <link rel="stylesheet" type="text/css" href="styles/projectSearchedList.css">
    <link rel="stylesheet" type="text/css" href="styles/projectListItems.css" />
    <link rel="stylesheet" type="text/css" href="styles/projectListItemsModal.css">
    <link rel="stylesheet" type="text/css" href="styles/projectCreate.css">

    <?php
}


?>


<main class="container mw-75 p-3" id="project">
    <!-- Filter Row -->
    <div class="filter-row container my-3 p-3 shadow rounded w-100 gap-1 gap-lg-3 justify-content-center">

        <div class="filter-mobile">
            <!-- button to open the filter menu -->
            <button class="filter-icon-btn filter-icon" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#filter-offcanvasRight" aria-controls="filter-offcanvasRight" title="Filter Projects">
                <!-- filter icon -->
                <i class="filter-icon-filter bi bi-filter"></i>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="filter-offcanvasRight"
                aria-labelledby="filter-offcanvasRightLabel" style="overflow-y: auto;">
                <div class="offcanvas-header" style="position: sticky; top:0; background: #fff; z-index: 999;">
                    <h5 class="container offcanvas-title" id="filter-offcanvasRightLabel">Filter By</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <!-- <form> -->
                <div class="offcanvas-body d-flex flex-column gap-5 justify-content-between justify-content-lg-start">
                    <!-- sort by  -->
                    <div id="sort" class="d-flex flex-column justify-content-between">
                        <span class="btn filter-btn filter-sort">Sort</span>
                        <div class="filter-collapse" id="filter-sort-content">
                            <div class="sort-content">
                                <input type="radio" name="sortChoice" id="sort-radio-one" value="`projectDate`ASC"
                                    checked>
                                <input type="radio" name="sortChoice" id="sort-radio-two" value="`projectDate`DESC">
                                <input type="radio" name="sortChoice" id="sort-radio-three"
                                    value="`projectProgress`ASC">
                                <input type="radio" name="sortChoice" id="sort-radio-four"
                                    value="`projectProgress`DESC">
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
                                <label for="sort-radio-three" class="radio-box sort-radio-third">
                                    <div class="sort-radio-title">
                                        <span class="sort-radio-icon"></span>
                                        <span>Progress(Low-High)</span>
                                    </div>
                                </label>
                                <label for="sort-radio-four" class="radio-box sort-radio-fourth">
                                    <div class="sort-radio-title">
                                        <span class="sort-radio-icon"></span>
                                        <span>Progress(High-Low)</span>
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
                            <!-- date input -->
                            <div class="date-content">
                                <input type="date" class="date" id="date-from" name="date-from" onkeydown="return false"
                                    oninput="updateDateTo()" placeholder="Deadline Date" required value=<?php echo $projectDate ?>>
                                <input type="date" class="date" id="date-to" name="date-to" onkeydown="return false"
                                    oninput="updateDateFrom()" placeholder="Deadline Date" required value=<?php echo $projectDate ?>>
                            </div>
                        </div>
                    </div>

                    <!-- filter by progress section -->
                    <div id="progress" class="d-flex flex-column justify-content-between">
                        <button class="btn filter-btn filter-progress" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filter-progress-content">
                            Progress
                            <i class="bi bi-caret-down"></i>
                        </button>
                        <div class="filter-collapse collapse" id="filter-progress-content"
                            data-bs-parent="#filter-offcanvasRight">
                            <div class="progress-content">
                                <?php
                                $min = 1;
                                $max = 99;
                                ?>
                                <div class="progress-div-select form-floating">
                                    <!-- Select of choices from 0-100 -->
                                    <select class="form-select progress-select" id="progress-from"
                                        aria-label="Progress From Select" name="progress-from">
                                        <option value="0" selected>0</option>
                                        <?php
                                        for ($i = 1; $i <= $max; $i++) {
                                            ?>
                                            <!-- each option has value equivelant to it's number -->
                                            <option value="<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                        <option value="100">100</option>
                                    </select>
                                    <label class="progress-label" for="progress-from">From</label>
                                </div>
                                <!-- Select of choices from 0-100 -->
                                <div class="progress-div-select form-floating">
                                    <select class="form-select progress-select" id="progress-to"
                                        aria-label="Progress To Select" name="progress-to">
                                        <option value="0">0</option>
                                        <?php
                                        for ($i = $min; $i <= $max; $i++) {
                                            ?>
                                            <!-- each option has value equivelant to it's number -->
                                            <option value="<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                        <option value="100" selected>100</option>
                                    </select>
                                    <label class="progress-label" for="progress-to">to</label>
                                </div>
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
                                        <!--  filters the users based on the each letter  -->
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

                                            // select all of the potential project leaders
                                            $sql = 'SELECT * FROM userDetails WHERE userRole = "leader" ORDER BY userID ASC';

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
                                                        <!-- user's name is the label for the checkbox -->
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

                    <!-- Checkbox to decide to show completed projects only -->
                    <div id="completed" class="d-flex flex-row w-100 completed-content">
                        <div class="d-flex gap-2 justify-content-center align-items-center">
                            <input type="checkbox" name="completed" id="completedCheckbox">
                            <label for="completedCheckbox">Show Completed Only</label>
                        </div>
                        <i class="bi bi-eye-slash completed-icon" id="complete-icon"></i>
                    </div>

                    <!-- buttons to apply or clear the filters -->
                    <div class="filter-btn-content">
                        <!-- apply button applies the chosen filters -->
                        <button class="d-block btn filter-btn-submit" id="filter-apply">
                            APPLY
                        </button>
                        <!-- reset buttony clears the filter menu -->
                        <button class="d-block btn filter-btn-reset" id="filter-reset">
                            RESET
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- form to search for projects -->
        <form method="post" class="filter-container-search">
            <div class="filter-search-content">
                <i class="filter-icon-search bi bi-search"></i>
                <!-- input that filters the projects based on the each letter -->
                <input id="filter-input-search-id" name="project_name" type="search" class="filter-input-search"
                    onkeyup="filterList('filter-input-search-id', 'filter-search-list')" incremental
                    placeholder="Search Project Titles...">
            </div>
        </form>

        <?php

        //  Only the manager can create a new project
        if ($_SESSION["role"] === "manager") {

            ?>
            <div class="filter-container-new">
                <div class="col d-flex">
                    <button type="button" class="filter-icon-btn filter-icon w-auto h-auto" data-bs-toggle="modal"
                        data-bs-target="#ProjectCreateModal" title="Create A New Project"><i
                            class="filter-icon-filter bi bi-folder-plus"></i>
                    </button>
                </div>
            </div>

            <?php
        }
        ?>

    </div>

    <!-- Content Row -->
    <div class="project-container">

        <div id="filter-search-list" class="project-search-output">
            <ul class="projectList" id="projectList">
                <?php

                // display the projects of the user
                $_SESSION['filter'] = 0;
                include_once("display-projects.inc.php");

                ?>


            </ul>
        </div>
    </div>

    <!-- button to scroll back up to the top of the screen -->
    <button onclick="topFunction()" id="scrollTop" title="Go to top"><i class="bi bi-arrow-up-short"></i></button>
    <?php

    // only show option to create new tasks if the user is a manager
    if ($_SESSION["role"] === "manager") {
        // include the button to create a new project
        include_once("newProjectBtn.php");
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

    // get the highest and lowest dates of projects from the database
    var dateTo = "<?php echo getHighestProjectDate($conn); ?>";
    var dateFrom = "<?php echo getLowestProjectDate($conn); ?>";

    // set the min and max dates for the date filter based on the highest and lowest project dates
    document.getElementById("date-from").setAttribute("min", dateFrom);
    document.getElementById("date-from").setAttribute("value", dateFrom);
    document.getElementById("date-from").setAttribute("max", dateTo);
    document.getElementById("date-to").setAttribute("min", dateFrom);
    document.getElementById("date-to").setAttribute("value", dateTo);

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

    // Filter's progress-from based on progress-to 
    // when progress-from is n, progress-to is n...100
    // when progress-to is n and progress-from selects a higher number x, progress-to becomes x
    $("#progress-from").on("change", function () {

        var valFrom = $(this).val();

        var $selectedFrom = $(this).find("option:selected");
        var $availableFrom = $selectedFrom.add($selectedFrom.nextAll()).clone();

        var selected2From = $("#progress-to").find("option:selected").val();

        $("#progress-to")
            .html($availableFrom)
            .find("option[value='" + selected2From + "']")
            .prop("selected", true);

    });
</script>


<script>
    // function that is used to filter the project items
    function filterProjects() {

        // get the value to sort by from the document
        var sortValue = document.querySelector('input[name="sortChoice"]:checked').value;

        // get the date filters from the document
        var dateFrom = document.querySelector("#date-from").value;
        var dateTo = document.querySelector("#date-to").value;

        // get the progress filters from the document
        var progressFrom = document.querySelector("#progress-from").value;
        var progressTo = document.querySelector("#progress-to").value;

        // get if there should only be completed projects shown
        var completed = document.querySelector("#completedCheckbox").checked;

        // get the values from all the user checkboxes that are checked
        const checked = document.querySelectorAll('.user-search-value[type="checkbox"]:checked');
        // make an array from the values from these checkboxes
        var users = Array.from(checked).map(x => x.value);

        // set the value of the users filter
        if (users.length == 0) {
            users = "Empty";
        } else {
            checked.forEach((e) => {
                if (e.value == "All") {
                    users = "All";
                }
            })
        }

        // display the projects with the filter
        $("#projectList").load("projectFilter.php?sort=" + sortValue + "&dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&progressFrom=" + progressFrom + "&progressTo=" + progressTo + "&users=" + users + "&completed=" + completed);

    }

    function resetProjects() {
        $('input[name=sortChoice]').prop('checked', false);
        document.getElementById("sort-radio-one").checked = true;

        document.getElementById("date-from").value = dateFrom;
        document.getElementById("date-to").value = dateTo;

        document.querySelector("#progress-from").value = 0;
        document.querySelector("#progress-to").value = 100;

        document.querySelector("#completedCheckbox").checked = false;

        // deselect all of the users
        unCheckAll();

        // set the search fiel empty
        document.querySelector("#input-search-id").value = "";
        document.querySelectorAll(".itemListValues").forEach(element => {
            element.style.display = "none";
        });

        // Make 'all' visible | do all the neccessary procedures
        document.querySelectorAll(".itemListValues")[0].style.display = "flex";
        selectAll();

        // get the value to sort by from the document
        var sortValue2 = document.querySelector('input[name="sortChoice"]:checked').value;

        // get the date filters from the document
        var dateFrom2 = document.querySelector("#date-from").value;
        var dateTo2 = document.querySelector("#date-to").value;

        // get the progress filters from the document
        var progressFrom2 = document.querySelector("#progress-from").value;
        var progressTo2 = document.querySelector("#progress-to").value;

        // get if there should only be completed projects shown
        var completed2 = document.querySelector("#completedCheckbox").checked;

        // get the values from all the user checkboxes that are checked
        const checked2 = document.querySelectorAll('.user-search-value[type="checkbox"]:checked');
        // make an array from the values from these checkboxes
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

        // display the projects with the filter applied
        $("#projectList").load("projectFilter.php?sort=" + sortValue2 + "&dateFrom=" + dateFrom2 + "&dateTo=" + dateTo2 + "&progressFrom=" + progressFrom2 + "&progressTo=" + progressTo2 + "&users=" + users2 + "&completed=" + completed2);

    }

    // function that filters projects on click
    $('#filter-apply').click(function () {

        // apply the project filter
        filterProjects()

        // button to close the off canvas element 
        let closeCanvas = document.querySelector('[data-bs-dismiss="offcanvas"]');
        closeCanvas.click();

    })

    // function that resets projects on click
    $('#filter-reset').click(function () {

        // reset the filter menu
        resetProjects()

        // button to close the off canvas element
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

        // set values to variables
        var input, filter, ul, li, label, i, txtValue;
        input = document.getElementById(userInput); // text the user types
        var startPointer;
        ul = document.getElementById(userUl); // list of items
        li = ul.getElementsByTagName('li'); // individual list items

        // Checking if the filtering is being used for the user search or project search
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
                    // get project's name and id
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
        }
        else {
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
            const userID = e.id; // userID of given checkbox
            var label = e.nextElementSibling.innerText; // getting user's name for Checked box

            var checkedValue;

            // Check if all was selected or deselected
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
            // If the unchecked element is 'All', every checkbox is unchecked and the value all in the text is removed
            if (this.innerText == "All") {
                unCheckAll();
                document.querySelectorAll(".users-picked-values").forEach((e) => {
                    e.innerText = ""
                    e.remove();
                })
            }
            // If the id of the clicked element matches the id of the checked box then:
            // it is unchecked 
            // The name of it in text is removed
            else if (this.id === box.id) {
                box.checked = false;

                this.innerText = "";
                this.remove();
            }
        })



    }
</script>

<script>
    const completedCheckbox = document.querySelector("#completedCheckbox");

    // Toggle icon when the complete checkbox is checked and unchecked
    completedCheckbox.addEventListener("input", () => {
        const icon = document.querySelector('#complete-icon');

        icon.classList.toggle('bi-eye-slash');
        icon.classList.toggle('bi-eye')
    })

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

include_once('../includes/footer.inc.php');

?>