<?php
session_start(); //start session
$page = "Account";
$title = "Account";
$_SESSION["title"] = $title;
$currentPage = "Manage Account";
if (!isset($_SESSION["valid"])) { //if session isn't valid take back to login page
    header("Location: login.php");
    exit();
} else if ($_SESSION["role"]) { //if user is a member include member header
    include_once('includes/header.inc.php');
}

?>

<!--linking to account css stylesheet-->
<link rel="stylesheet" href="styles/account.css">
<!--page container-->
<div id="accountContent">
    <div class="d-flex justify-content-between align-items-center gap-3">
        <span class="accountType">
            <?php echo ucfirst($_SESSION["role"]) ?> Account
        </span>
        <a href="../login.php?action=loggedOut" id="signOutButton" type="button" class="btn">Sign Out</a>
    </div>

    <div class="row">
        <div class="col-12 col-lg-5">
            <div class="d-flex pt-5 pb-3 justify-content-center justify-content-lg-start">
                <div class="w-auto accountProfileContainer d-flex position-relative">
                    <?php
                    $id = $_SESSION['userid']; //getting ID
                    require_once("includes/dbh.inc.php"); //geting connection
                    $sql = "SELECT userProfilePicture FROM userDetails WHERE userID = $id"; //sql 
                    $result = mysqli_query($conn, $sql); //result
                    ?>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <img id="accountProfilePicture" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>" />
                    <?php } ?>

                    <form id="imageForm" method="post" action="account/upload.php" enctype="multipart/form-data">
                        <label class="label">
                            <input id="fileInput" type="file" name="image" required />
                            <div id="cameraContainer" class="d-flex position-absolute"><i class="bi bi-camera d-flex"></i></div>
                        </label>
                    </form>
                </div>
            </div>
            <div class="text-center text-lg-start"> <!--text center if below medium else text start-->
                <p id="accountName" class="m-0 py-1" style="text-overflow: ellipsis; overflow: hidden">
                    <?php echo $_SESSION["username"] . " " . $_SESSION["usersurname"]; ?>
                </p>
                <p id="accountEmail" class="m-0 py-1">
                    <?php echo $_SESSION["email"] ?>
                </p>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="pt-5 pb-3">
                <ul class="account-card-content p-0 m-0">
                    <li>
                        <div class="account-card-container">
                            <div class="account-card-header" tabindex="-1">
                                <span class="account-card-title">First Name</span>
                                <i class="account-card-icon bi bi-person-fill"></i>
                            </div>
                            <div class="account-card-body" tabindex="-1">
                                <span class="account-card-data">
                                    <?php echo $_SESSION["username"] ?>
                                </span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="account-card-container">
                            <div class="account-card-header" tabindex="-1">
                                <span class="account-card-title">Last Name</span>
                                <i class="account-card-icon bi bi-person-fill"></i>
                            </div>
                            <div class="account-card-body" tabindex="-1">
                                <span class="account-card-data">
                                    <?php echo $_SESSION["usersurname"] ?>
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="account-card-content m-0" style="padding: 2rem 0 0 0">
                    <li>
                        <div class="account-card-container">
                            <div class="account-card-header" tabindex="-1">
                                <span class="account-card-title">Password</span>
                                <i class="account-card-icon bi bi-lock-fill"></i>
                            </div>
                            <div class="account-card-body" tabindex="-1">
                                <span class="account-card-data">********</span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="account-card-container">
                            <div class="account-card-header" tabindex="-1">
                                <span class="account-card-title">ID</span>
                                <i class="account-card-icon bi bi-person-lines-fill"></i>
                            </div>
                            <div class="account-card-body" tabindex="-1">
                                <span class="account-card-data">
                                    <?php echo $_SESSION["userid"] ?>
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>



</div>

<script>
    $("#fileInput").change(function() { //on change
        $('form#imageForm').submit(); //submit form
    });
    $("#accountContent").load("account/contents.php");

    //if page is reloaded make account not editable
    if (performance.navigation.type == performance.navigation.TYPE_RELOAD) {
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "account/setAccountNotEditable.php", true);
        xhttp.send();
    }
</script>

<?php
include_once('includes/footer.inc.php');
?>