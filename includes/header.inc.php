<?php
session_start();

if (!isset($_SESSION['valid'])) {

    header("Location: ../login.php");
    exit();
}

$_SESSION['signout'] = false;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        <?php echo $title; ?>
    </title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no,  maximum-scale=1" />

    <link rel="apple-touch-icon" sizes="180x180" href="/content/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/content/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/content/favicon_io/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/content/favicon_io/android-chrome-192x192.png">
    <link rel="manifest" href="../content/favicon_io/site.webmanifest">
    <!-- Font awsome fot icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- css file import -->
    <link rel="stylesheet" href="../styles/util.css">
    <link rel="stylesheet" href="../styles/dashboard/dbToDoItems.css">
    <link rel="stylesheet" href="../styles/dashboard/dbFilterChart.css">
    <link rel="stylesheet" href="../styles/dashboard/dbTop.css">
    <link rel="stylesheet" href="../styles/dashboard/dbNav.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.css"
        integrity="sha512-BB0bszal4NXOgRP9MYCyVA0NNK2k1Rhr+8klY17rj4OhwTmqdPUQibKUDeHesYtXl7Ma2+tqC6c7FzYuHhw94g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Date picker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body>
    <header>

        <div class="container-fluid d-flex align-items-end justify-content-center"
            style="background-color: #354182; height: 9vh;">
            <!-- Title -->
            <div class="display-4" style="font-weight: 700; color: #ffffff">
                Make-It-All
            </div>
        </div>
        <div class="container-fluid d-flex align-items-center justify-content-center"
            style="background-color: #354182; height: 6vh;">
            <!-- Current Page -->
            <div class="h4" style="font-weight: 500; color: #ffffff">
                <?php echo $currentPage ?>
            </div>
        </div>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-md navbar-dark d-flex flex-column" style="background-color: #ffffff">
            <div class="container justify-content-center text-center ">
                <!-- Hamburger for mobile view -->
                <button type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" class="navbar-toggler"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" id="navbar-toggler">
                    <span><i class="bi bi-list" style="color: #354182; font-size: calc(1.3rem + .6vw);"
                            id="hamburgerIcon"></i></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center text-center w-25" id="navbarNav">
                    <!-- Navbar links -->
                    <ul class="navbar-nav gap-4 justify-content-center align-items-center mt-3 mt-md-0">
                        <!-- Dashboard -->
                        <li class="nav-item"><a id="dashboard-link" href="/dashboard.php"
                                class="nav-link rounded  p-0 p-md-2 py-2"
                                style="color: #354182; font-weight: 600;white-space: nowrap;"><i
                                    class="bi bi-house me-0 me-md-2"></i>Dashboard</a></li>

                        <!-- Projects -->
                        <li class="nav-item"><a id="projects-link" href="/projects/projects.php"
                                class="nav-link rounded p-0 p-md-2 py-2"
                                style="color: #354182; font-weight: 600;white-space: nowrap;"><i
                                    class="bi bi-file-earmark-text me-0 me-md-2"></i>Projects</a></li>

                        <!-- Forums -->
                        <li class="nav-item"><a id="forums-link" href="/forums.php"
                                class="nav-link rounded p-0 p-md-2 py-2"
                                style="color: #354182; font-weight: 600;white-space: nowrap;"><i
                                    class="bi bi-chat-dots me-0 me-md-2"></i>Forums</a></li>

                        <!-- To-Do -->
                        <li class="nav-item"><a id="to-do-list-link" href="/to-do-list.php"
                                class="nav-link rounded p-0 p-md-2 py-2"
                                style="color: #354182; font-weight: 600; white-space: nowrap;"><i
                                    class="bi bi-list-ul me-0 me-md-2"></i>To Do List</a></li>
                        <hr class="w-75 m-0">

                        <!-- Accounts -->
                        <li class="nav-item dropdown">
                            <!-- Desktop Button -->
                            <button id="account-link" href="/account.php" class="nav-link rounded"
                                onclick="openDropdown()">

                                <div class="accountNavPhoto d-flex justify-content-center align-items-center gap-2">
                                    <?php
                                    $id = $_SESSION['userid']; //getting ID
                                    require_once("/var/www/team-projects-part-2-team-01/includes/dbh.inc.php"); //geting connection
                                    $sql = "SELECT userProfilePicture FROM userDetails WHERE userID = $id"; //sql 
                                    $result = mysqli_query($conn, $sql); //result
                                    ?>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <img id="" height="45" width="45" style="border-radius: 50%;"
                                            src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>"
                                            onerror="this.onerror=null; this.src='/account/logo.png'" />
                                    <?php } ?>
                                </div>

                            </button>
                            <!-- Dropdown | Mobile View -->
                            <div class="dropdown-content" style="" id="navDropdownContent">
                                <?php
                                $id = $_SESSION['userid']; //getting ID
                                require_once("/var/www/team-projects-part-2-team-01/includes/dbh.inc.php"); //geting connection
                                $sql = "SELECT userName,userSurname, userRole, userProfilePicture FROM userDetails WHERE userID = $id"; //sql 
                                $result = mysqli_query($conn, $sql); //result
                                ?>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <!-- Dropdown Information | Mobile view information -->
                                    <div class="d-flex text-start flex-row gap-4 align-items-center">
                                        <img id="" height="50" width="50" style="border-radius: 50%;"
                                            src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>"
                                            onerror="this.onerror=null; this.src='/account/logo.png'" />
                                        <div class="d-flex text-start flex-column justify">
                                            <span class="m-0 p-0 dropdown-content-img-title">
                                                <?php echo ucfirst($row['userName']) . " " . substr(ucfirst($row['userSurname']), 0, 1) . "." ?>
                                            </span>
                                            <span class="m-0 p-0 dropdown-content-img-subtitle">
                                                <?php echo ucfirst($row['userRole']) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php } ?>
                                <hr class="d-none d-md-inline-block w-100">
                                <!-- Dropdown button | Mobile button -->
                                <div class="d-flex flex-column gap-3">
                                    <!-- Account Page -->
                                    <a href="../account.php" type="button" class="dropdownBtn" id="dropdownAccountBtn">
                                        <div class="dropdownAccountContent">
                                            <div class="dropdownAccountIconContainer">
                                                <i class="bi bi-person-fill-gear dropdownAccountIcon"
                                                    id="dropdownIcon1"></i>
                                            </div>
                                            <span>Manage Account</span>
                                        </div>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                    <!-- Signout button -->
                                    <a href="/login.php?action=loggedOut" type="button" class="dropdownBtn"
                                        id="dropdownSignOutBtn">
                                        <div class="dropdownAccountContent">
                                            <div class="dropdownAccountIconContainer">
                                                <i class="bi bi-box-arrow-right dropdownAccountIcon"
                                                    id="dropdownIcon2"></i>
                                            </div>
                                            <span>Sign Out</span>
                                        </div>
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid" style="background-color: #354182; height: 1vh;">
        </div>

    </header>

    <script>
        const navbarNav = document.getElementById("navbarNav");
        const navbarToggler = document.getElementById("navbar-toggler");
        const hamburgerIcon = document.getElementById("hamburgerIcon");
        const manageAccount = document.getElementById("dropdownAccountBtn");
        const logOut = document.getElementById("dropdownSignOutBtn");
        const navDropdownContent = document.getElementById('navDropdownContent');

        let dropdownContent = [manageAccount, logOut]

        // Tabbing through the dropdown causes the dropdown to open
        dropdownContent.forEach(e => {
            e.addEventListener("focusin", () => {
                if (navDropdownContent.classList.contains("dropdown-content-shown")) {
                    // dropdown already opened
                } else {
                    // make dropdown shown
                    navDropdownContent.classList.add("dropdown-content-shown")
                }
            })

        });

        // clicking outside of the dropdown closes it
        window.addEventListener('click', function (e) {
            if (document.getElementById('navDropdownContent').contains(e.target) || document.getElementById('account-link').contains(e.target)) {
                // Clicked in box
            } else {
                navDropdownContent.classList.remove("dropdown-content-shown")
            }
        });

        // Tabbing outside the dropdown causes the dropdown to close
        window.addEventListener('focusin', function (e) {
            if (document.getElementById('navDropdownContent').contains(e.target)) {
                // Clicked in box
            } else {
                navDropdownContent.classList.remove("dropdown-content-shown")
                // make dropdown hidden
            }
        });



        // Transitions on mobile screen for the navbar
        navbarToggler.addEventListener("click", () => {
            if (navbarToggler.getAttribute('aria-expanded') == "true") {
                document.getElementById("navbar-toggler").scrollIntoView({
                    behavior: 'smooth'
                });

                // change hamburger icon to close icon when opened
                hamburgerIcon.classList.remove("bi-list")
                hamburgerIcon.classList.add("bi-x-square")
                setTimeout(() => {
                    hamburgerIcon.classList.add("hamburger-show");
                }, 0);

            } else if (navbarToggler.getAttribute('aria-expanded') == "false") {
                // scroll back up to the top of the screen
                window.scrollTo(0, 0, {
                    behavior: 'smooth'
                });
                hamburgerIcon.classList.remove("hamburger-show");
                hamburgerIcon.classList.remove("bi-x-square")
                hamburgerIcon.classList.add("bi-list")

            }

        });


        function openDropdown() {
            // make dropdown shown
            navDropdownContent.classList.toggle("dropdown-content-shown");
        }

        // remove the background on unactive nav links
        function removeBackgrounds() {
            var navLinks = document.getElementsByClassName('nav-link'); // get all nav link
            for (var i = 0; i < navLinks.length; i++) {
                navLinks[i].style.backgroundColor = "#FFFFFF";
            }
        }

        // sets the background on the active nav link
        function setBackgrounds($i) {
            var navLinks = document.getElementsByClassName('nav-link'); // get all nav link
            navLinks[$i].style.backgroundColor = "#D9DEFF";
            navLinks[$i].classList.add("navItemSelected");
        }
    </script>
    <!-- Manager js file -->
    <script src="../sources/header-manager.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>