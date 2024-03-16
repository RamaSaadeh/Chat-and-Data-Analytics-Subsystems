<?php
session_start();
?>

<div class="d-flex justify-content-between align-items-center gap-3">
    <span class="accountType">
        <?php echo ucfirst($_SESSION["role"]) ?> Account
    </span>
    <a href="/login.php?action=loggedOut" id="signOutButton" type="button" class="btn">Sign
        Out</a>
</div>

<script>


</script>

<div class="row">
    <div class="col-12 col-lg-5">
        <div class="d-flex pt-5 pb-3 justify-content-center justify-content-lg-start">
            <div class="w-auto accountProfileContainer d-flex position-relative">
                <?php
                $id = $_SESSION['userid']; //getting ID
                require("../includes/dbh.inc.php"); //geting connection
                $sql = "SELECT userProfilePicture FROM userDetails WHERE userID = $id"; //sql 
                $result = mysqli_query($conn, $sql); //result
                ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <img id="accountProfilePicture" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>" onerror="this.onerror=null; this.src=' account/logo.png'" />
                <?php } ?>

                <form id="imageForm" method="post" action="account/upload.php" enctype="multipart/form-data">
                    <label class="label">
                        <input id="fileInput" type="file" name="image" required />
                        <div id="cameraContainer" class="d-flex position-absolute"><i class="bi bi-camera d-flex"></i>
                        </div>
                    </label>
                </form>
            </div>
        </div>
        <div class="text-center text-lg-start"> <!--text center if below medium else text start-->
            <p id="accountName" class="m-0 py-1"><span id="accountFirstName">
                    <?php echo $_SESSION["username"] ?>
                </span><span id="accountLastName">
                    <?php echo $_SESSION["usersurname"] ?>
                </span></p>
            <p id="accountEmail" class="m-0 py-1">
                <?php echo $_SESSION["email"] ?>
            </p>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="pt-5 pb-3">
            <ul class="account-card-content p-0 m-0">
                <!-- user first name -->
                <li>
                    <div id="firstNameContainer" class="account-card-container">
                        <div class="account-card-header" tabindex="-1">
                            <span class="account-card-title">First Name</span>
                            <i class="account-card-icon bi bi-person-fill"></i>
                        </div>
                        <div class="account-card-body" tabindex="-1">
                            <span id="firstNameData" class="account-card-data">
                                <?php echo $_SESSION["username"]; ?>
                            </span>
                            <i id="firstNameChangeButton" class="account-card-change-icon bi bi-pencil-fill ms-2"></i>
                        </div>
                        <div id="firstNameSuccess" class="alert alert-dismissible fade show m-0 position-absolute h-100 w-100 p-2" hidden role="alert">
                            <div class="wrapper"> <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div id="firstNameLoader" class="loader" hidden></div>
                </li>
                <!-- user last name -->
                <li>
                    <div id="lastNameContainer" class="account-card-container">
                        <div class="account-card-header" tabindex="-1">
                            <span class="account-card-title">Last Name</span>
                            <i class="account-card-icon bi bi-person-fill"></i>
                        </div>
                        <div class="account-card-body" tabindex="-1">
                            <span id="lastNameData" class="account-card-data">
                                <?php echo $_SESSION["usersurname"]; ?>
                            </span>&nbsp;<i id="lastNameChangeButton" class="account-card-change-icon bi bi-pencil-fill ms-2"></i>
                        </div>
                        <div id="lastNameSuccess" class="alert alert-dismissible fade show m-0 position-absolute h-100 w-100 p-2" hidden role="alert">
                            <div class="wrapper"> <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div id="lastNameLoader" class="loader" hidden></div>
                </li>
            </ul>
            <ul class="account-card-content m-0" style="padding: 2rem 0 0 0">
                <li>
                    <div id="passwordContainer" class="account-card-container">
                        <div class="account-card-header" tabindex="-1">
                            <span class="account-card-title">Password</span>
                            <i class="account-card-icon bi bi-lock-fill"></i>
                        </div>
                        <div class="account-card-body" tabindex="-1">
                            <span class="account-card-data">
                                <?php echo str_repeat("*", strlen($_SESSION["password"])) ?>
                            </span>
                            <i id="passwordChangeButton" class="account-card-change-icon bi bi-pencil-fill ms-2"></i>
                        </div>
                        <div id="passwordSuccess" class="alert alert-dismissible fade show m-0 position-absolute h-100 w-100 p-2" hidden role="alert">
                            <div class="wrapper"> <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div id="passwordLoader" class="loader" hidden></div>
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

<div class="row pt-5">
    <span id="deleteAccountHeading">Delete Your Account</span>
    <span class="deleteAccountInfo">• Delete All Your Account Information</span>
    <span class="deleteAccountInfo">• Log You Out Of All Current Devices</span>
    <button id="deleteAccountButton" class="btn" data-bs-target="#deleteAccountModal" data-bs-toggle="modal">Delete
        Account</button>
</div>

<!-- remove account modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- modal header-->
            <div class="modal-header" style="background-color: white; color: var(--clr-error)">
                <div class="modal-header-container container-fluid text-center">
                    <!-- modal title-->
                    Are you sure?
                </div>
            </div>
            <!-- modal body -->
            <div class="modal-body">
                <span>To complete the deletion of your account, please type the characters you see below.</span>
                <?php
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 5; $i++) {
                    $randomString .= $characters[random_int(0, $charactersLength - 1)];
                }
                ?>
                <span class="d-block text-center p-2" style="font-style: italic; user-select: none">
                    <?php echo $randomString ?>
                </span>


                <!-- title form -->
                <input class="form-control mb-3" type="text" id="deleteCaptcha">
                <!-- modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="deleteAccount()" class="btn btn-danger" data-bs-dismiss="modal" id="deleteAccount" disabled>Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- confirm account modal -->
<div class="modal fade" id="confirmAccountModal" tabindex="-1" role="dialog" aria-labelledby="confirmAccountTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" id="confirmAccountModalContent">
            <!-- modal header-->
            <div class="modal-header" style="background-color: white; color: var(--clr-error)">
                <div class="modal-header-container container-fluid text-center">
                    <!-- modal title-->
                    Confirm Password
                </div>
            </div>
            <!-- modal body -->
            <div class="modal-body">
                <span>To edit your account details, please re-enter your password to confirm your identity.</span>
                <!-- password form -->
                <form>
                    <input class="form-control mb-3" type="password" id="accountConfirmPassword" required autocomplete="new-password">
                </form>
                <!-- modal footer -->
                <div class=" modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="confirmPassword()" class="btn btn-danger">Confirm</button>
                </div>
            </div>
            <div id="confirmPasswordSuccess" class="alert alert-dismissible fade show m-0 position-absolute h-100 w-100 p-2" hidden role="alert">
                <div class="wrapper"> <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                </div>
            </div>
            <div id="confirmPasswordLoader" class="loader" hidden></div>
        </div>
    </div>
</div>

<!-- change password modal -->
<div class="modal fade" id="passwordChangeModal" tabindex="-1" role="dialog" aria-labelledby="passwordChangeTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" id="passwordChangeModalContent">
            <!-- modal header-->
            <div class="modal-header" style="background-color: white; color: var(--clr-error)" id="passwordChangeModalHeader">
                <div class="modal-header-container container-fluid text-center">
                    <!-- modal title-->
                    Change Password
                </div>
            </div>
            <!-- modal body -->
            <div class="modal-body" id="passwordChangeModalBody">
                <span id="currentPasswordLabel">Current Password</span>
                <!-- current password form -->
                <input class="form-control mb-3" type="password" id="currentPassword" required>
                <span>New Password</span>
                <!-- new password form -->
                <input class="form-control mb-3" type="tezr" id="newPassword" required>
                <!-- password requirements -->
                <ul style="list-style-position: inside">
                    <li id="rule1Label" style="color: var(--clr-error)">7+ characters</li>
                    <li id="rule2Label" style="color: var(--clr-error)">Use at least 1 letter</li>
                    <li id="rule3Label" style="color: var(--clr-error)">Use at least 1 number</li>
                    <li id="rule4Label" style="color: var(--clr-green)">No spaces</li>
                </ul>
                <span>Confirm New Password</span>
                <!-- confirm new password form -->
                <input class="form-control mb-3" type="text" id="confirmNewPassword" required>
                <!-- modal footer -->
                <div class=" modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="updatePassword()" id="confirmNewPasswordButton" class="btn btn-success" disabled>Confirm</button>
                </div>
            </div>
            <!-- successful password change -->
            <div id="password_change_successful" class="alert alert-dismissible fade show m-0 position-absolute h-100 w-100 p-2" hidden role="alert">
                <div class="wrapper"> <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                </div>
            </div>
            <!-- faulty password change -->
            <div id="password_change_unsuccessful" class="alert alert-dismissible fade show m-0 position-absolute h-100 w-100 p-2" style="background-color: var(--clr-lightred) !important;" hidden role="alert">
                <div class="wrapper"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" style="box-shadow: inset 0px 0px 0px var(--clr-error); animation: fill__error 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" style="stroke: var(--clr-error) !important;" />
                        <path class="checkmark__check" fill="none" d="M14.1 14.1l23.8 23.8 m0,-23.8 l-23.8,23.8" />
                    </svg>
                </div>
            </div>
            <!-- modal spinner -->
            <div id="change_password_loader" class="loader" hidden></div>
        </div>
    </div>
</div>

<script>
    $(window).click(function(e) {
        if ("<?php echo $_SESSION["accounteditable"] ?>" != 1) { //if account is not allowed to be edited change action of upload image;
            if (e.target.id == "cameraContainer" || e.target.classList.contains("bi-camera")) {
                e.preventDefault()
                $('#confirmAccountModal').modal('show');
            }
        }
    });



    $('#deleteAccountModal').on('show.bs.modal', function(e) {
        if ("<?php echo $_SESSION["accounteditable"] ?>" != 1) {
            e.preventDefault();
            $('#confirmAccountModal').modal('show');
        }
    });

    $("#fileInput").change(function() { //on change
        $('form#imageForm').submit(); //submit form
    });


    $("#firstNameChangeButton").click(function() {
        if ("<?php echo $_SESSION["accounteditable"] ?>" != 1) { //if account is not allowed to be edited return;
            $('#confirmAccountModal').modal('show')
            return;
        }
        //if confirm button clicked, change first name
        if (this.classList.contains("bi-check-circle-fill")) {
            $('#firstNameContainer').css('opacity', '0.2'); //lower opacity
            $("#firstNameLoader").removeAttr('hidden');
            //0.5 second delay
            setTimeout(function() {
                $("#firstNameLoader").attr('hidden', true); //hide loader
                $('#firstNameContainer').css('opacity', '1'); //reset opacity
                $("#firstNameSuccess").removeAttr('hidden'); //show alert
            }, 500);
            //1.2 second delay
            setTimeout(function() {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "account/updateDB.php?type=username&data=" + $("#firstNameInput").val(), true);
                xhttp.send();
                xhttp.onload = () => {
                    $("#accountContent").load("account/contents.php");
                }
            }, 2500);
            return
        }

        //change edit button to confirm button
        this.classList.remove("bi-pencil-fill"); //remove pencil
        this.classList.add("bi-check-circle-fill"); //add tick
        this.style.color = "var(--clr-lightgreen)"; //change clr
        $("#firstNameData").replaceWith("<input id='firstNameInput' class='account-card-data p-0' style='width: " + $("#firstNameData").width() + "px; border: none transparent; outline: none;' value=" + $("#firstNameData").html() + "type='text' autocomplete='off'>" + "</input>");
        var val = $("#firstNameInput").val(); //store the value of the element
        $("#firstNameInput").val('') //clear the value of the element
        $("#firstNameInput").val(val.trim()); //set that value back.  
        $("#firstNameInput").focus(); //set cursor in text box
        $("#firstNameInput").keypress(function(e) {
            //if keycode is a spacebar
            if (e.keyCode == 32) {
                return false; //stop space
            }
        })
        document.addEventListener("click", (evt) => {
            let targetEl = evt.target; // clicked element      
            if (targetEl != document.getElementById("firstNameInput") && targetEl != document.getElementById("firstNameChangeButton")) {
                //if clicked outside of field, change back to orignal value
                $("#firstNameInput").replaceWith("<span id='firstNameData' class='account-card-data'>" + $("#accountFirstName").html() + "</span>"); //use account first name html as php session does not change in script
                this.classList.remove("bi-check-circle-fill"); //remove tick
                this.classList.add("bi-pencil-fill"); //add pencil
                this.style.color = "var(--clr-blue)"; //change clr to pending
            }
        });

    })

    $("#lastNameChangeButton").click(function() {
        if ("<?php echo $_SESSION["accounteditable"] ?>" != 1) { //if account is not allowed to be edited return;
            $('#confirmAccountModal').modal('show')
            return;
        }
        //if confirm button clicked, change last name
        if (this.classList.contains("bi-check-circle-fill")) {
            $('#lastNameContainer').css('opacity', '0.2'); //lower opacity
            $("#lastNameLoader").removeAttr('hidden');
            //0.5 second delay
            setTimeout(function() {
                $("#lastNameLoader").attr('hidden', true); //hide loader
                $('#lastNameContainer').css('opacity', '1'); //reset opacity
                $("#lastNameSuccess").removeAttr('hidden'); //show alert
            }, 500);
            //2.5 second delay
            setTimeout(function() {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "account/updateDB.php?type=usersurname&data=" + $("#lastNameInput").val(), true);
                xhttp.send();
                xhttp.onload = () => {
                    $("#accountContent").load("account/contents.php");
                }
            }, 2500);
            return
        }

        //change edit button to confirm button
        this.classList.remove("bi-pencil-fill"); //remove pencil
        this.classList.add("bi-check-circle-fill"); //add tick
        this.style.color = "var(--clr-lightgreen)"; //change clr
        $("#lastNameData").replaceWith("<input id='lastNameInput' class='account-card-data p-0' style='width: " + $("#lastNameData").width() + "px; border: none transparent; outline: none;' value=" + $("#lastNameData").html() + ">" + "</input>");
        $("#lastNameInput").focus(); //set cursor in text box
        var val = $("#lastNameInput").val(); //store the value of the element
        $("#lastNameInput").val('') //clear the value of the element
        $("#lastNameInput").val(val.trim()); //set that value back.  
        $("#lastNameInput").keypress(function(e) {
            //if keycode is a spacebar
            if (e.keyCode == 32) {
                return false; //stop space
            }
        })
        document.addEventListener("click", (evt) => {
            let targetEl = evt.target; // clicked element      
            if (targetEl != document.getElementById("lastNameInput") && targetEl != document.getElementById("lastNameChangeButton")) {
                //if clicked outside of field, change back to orignal value
                $("#lastNameInput").replaceWith("<span id='lastNameData' class='account-card-data'>" + $("#accountLastName").html() + "</span>"); //use account last name html as php session does not change in script
                this.classList.remove("bi-check-circle-fill"); //remove tick
                this.classList.add("bi-pencil-fill"); //add pencil
                this.style.color = "var(--clr-blue)"; //change clr to pending
            }
        });

    })

    $("#passwordChangeButton").click(function() {
        if ("<?php echo $_SESSION["accounteditable"] ?>" != 1) { //if account is not allowed to be edited return;
            // $('#confirmAccountModal').modal('show')
            // return;
        }
        $('#passwordChangeModal').modal('show'); //display change password modal
    })

    $("#deleteCaptcha").keyup(function() {
        if ($("#deleteCaptcha").val() == '<?php echo $randomString ?>') $("#deleteAccount").prop("disabled", false);
        else $("#deleteAccount").prop("disabled", true);
    })

    $("#deleteAccountModal").on("hidden.bs.modal", function() {
        $("#deleteCaptcha").val(""); //when modal closes reset input form
    })

    function deleteAccount() {
        window.location.replace("account/deleteAccount.php")
    }

    function confirmPassword() {
        $("#confirmPasswordLoader").removeAttr('hidden');
        //0.5 second delay
        setTimeout(function() {
            $("#confirmPasswordLoader").attr('hidden', true); //hide loader
            if ($("#accountConfirmPassword").val() == "<?php echo $_SESSION["password"] ?>") {
                $("#confirmPasswordSuccess").removeAttr('hidden'); //show alert
            };
        }, 500);
        //1.2 second delay
        setTimeout(function() {
            var xhttp = new XMLHttpRequest();
            xhttp.open("GET", "account/setAccountEditable.php", true);
            xhttp.send();
            xhttp.onload = () => {
                window.location.replace("account.php")
            }
        }, 2500);
        return
    }
    //
    //jQuery script for changing password modal
    //
    $("#newPassword").keydown(function() {
        this.type = "password" //changing type to type of inputs to password
    })
    $("#confirmNewPassword").keydown(function() {
        this.type = "password" //changing type to type of inputs to password
    })
    var alphabet = "abcdefghijklmnopqrstuvwxyz" //alphabet
    numbers = "0123456789" //numbers
    Rule1 = false; //7 or more characters
    Rule2 = false; //one character at least
    Rule3 = false; //one number at least
    Rule4 = true; //no space
    Rule5 = false; //password do not match
    $('#passwordChangeModal').on('show.bs.modal', function(e) {
        Rule1 = false; //7 or more characters
        Rule2 = false; //one character at least
        Rule3 = false; //one number at least
        Rule4 = true; //no space
        Rule5 = false; //passwords do not match
        $("#currentPassword").val("") //resetting input fields
        $("#newPassword").val("") //resetting input fields
        $("#confirmNewPassword").val("") //resetting input fields
        $("#currentPassword").css('outline', 'none') //resetting input fields
        $("#newPassword").css('outline', 'none') //resetting input fields
        $("#confirmNewPassword").css('outline', 'none') //resetting input fields
        $("#rule1Label").css('color', 'var(--clr-error)'); //resetting rule labels
        $("#rule2Label").css('color', 'var(--clr-error)'); //resetting rule labels
        $("#rule3Label").css('color', 'var(--clr-error)'); //resetting rule labels
        $("#rule4Label").css('color', 'var(--clr-green)'); //resetting rule labels
        $("#confirmNewPasswordButton").prop("disabled", true); //disable confirm button
    });
    $("#currentPassword").keyup(function() {
        //make sure outline is none
        $("#currentPassword").css('outline', 'none')
    })
    $("#newPassword").keyup(function() {
        //make sure outline is none
        $("#newPassword").css('outline', 'none')
        if (this.value.length >= 7) { //if password length greater than 7
            $("#rule1Label").css('color', 'var(--clr-green)');
            Rule1 = true;
        } else {
            $("#rule1Label").css('color', 'var(--clr-error)');
            Rule1 = false;
        }
        for (var i = 0; i < alphabet.length; i++) {
            if (this.value.includes(alphabet[i])) {
                $("#rule2Label").css('color', 'var(--clr-green)');
                Rule2 = true;
                break
            } else {
                $("#rule2Label").css('color', 'var(--clr-error)');
                Rule2 = false;
            }
        }
        for (var i = 0; i < numbers.length; i++) {
            if (this.value.includes(numbers[i])) {
                $("#rule3Label").css('color', 'var(--clr-green)');
                Rule3 = true;
                break
            } else {
                $("#rule3Label").css('color', 'var(--clr-error)');
                Rule3 = false;
            }
        }
        if (!checkWhitespace(this.value)) {
            $("#rule4Label").css('color', 'var(--clr-green)');
            Rule4 = true;
        } else {
            $("#rule4Label").css('color', 'var(--clr-error)');
            Rule4 = false;
        }
        if (this.value == $("#confirmNewPassword").val()) {
            Rule5 = true;
        } else {
            Rule5 = false;
        }

        if (Rule1 == true && Rule2 == true && Rule3 == true && Rule4 == true && Rule5 == true) {
            $("#confirmNewPasswordButton").prop("disabled", false);
        } else {
            $("#confirmNewPasswordButton").prop("disabled", true);
        }
    })
    $("#confirmNewPassword").keyup(function() {
        //make sure outline is none
        $("#confirmNewPassword").css('outline', 'none')
        if (this.value == $("#newPassword").val()) {
            Rule5 = true;
        } else {
            Rule5 = false;
        }

        if (Rule1 == true && Rule2 == true && Rule3 == true && Rule4 == true && Rule5 == true) {
            $("#confirmNewPasswordButton").prop("disabled", false);
        } else {
            $("#confirmNewPasswordButton").prop("disabled", true);
        }
    })
    //function to determine is password has a space inside it
    function checkWhitespace(str) {
        return /\s/.test(str);
    }
    //function to update users password
    function updatePassword() {
        var password = "<?php echo $_SESSION["password"] ?>";
        if ($("#newPassword").val() == password) { //cannot change password to current password
            $('#passwordChangeModalBody').css('opacity', '0.2'); //lower opacity
            $('#passwordChangeModalHeader').css('opacity', '0.2'); //lower opacity
            $("#change_password_loader").removeAttr('hidden');
            //0.5 second delay
            setTimeout(function() {
                $("#change_password_loader").attr('hidden', true); //hide loader
                $('#passwordChangeModalBody').css('opacity', '1'); //reset opacity
                $('#passwordChangeModalHeader').css('opacity', '1'); //reset opacity
                $("#password_change_unsuccessful").removeAttr('hidden'); //show alert
            }, 500);
            //2.5 second delay
            setTimeout(function() {
                $("#password_change_unsuccessful").attr('hidden', true); //hide alert
                $("#newPassword").val("") //reset new password input
                $("#confirmNewPassword").val("") //reset confirm new password input
                $("#currentPassword").css('outline', '1px solid var(--clr-green)') //setting outline for current password
                $("#newPassword").css('outline', '1px solid var(--clr-error)') //setting outline for current password
                $("#confirmNewPassword").css('outline', '1px solid var(--clr-error)') //setting outline for current password
                $("#rule1Label").css('color', 'var(--clr-error)'); //resetting rule labels
                $("#rule2Label").css('color', 'var(--clr-error)'); //resetting rule labels
                $("#rule3Label").css('color', 'var(--clr-error)'); //resetting rule labels
                $("#rule4Label").css('color', 'var(--clr-green)'); //resetting rule labels
                $("#confirmNewPasswordButton").prop("disabled", true); //disable confirm button
            }, 2500);
        } else if ($("#currentPassword").val() == password) { //correct current password
            $('#passwordChangeModalBody').css('opacity', '0.2'); //lower opacity
            $('#passwordChangeModalHeader').css('opacity', '0.2'); //lower opacity
            $("#change_password_loader").removeAttr('hidden');
            //0.5 second delay
            setTimeout(function() {
                $("#change_password_loader").attr('hidden', true); //hide loader
                $('#passwordChangeModalBody').css('opacity', '1'); //reset opacity
                $('#passwordChangeModalHeader').css('opacity', '1'); //reset opacity
                $("#password_change_successful").removeAttr('hidden'); //show alert
            }, 500);
            //2.5 second delay
            setTimeout(function() {
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "account/update_password.php?password=" + $("#newPassword").val(), true);
                xhttp.send();
                xhttp.onload = () => {
                    location.reload(); //reload page if password reset
                }
            }, 2500);
        } else {
            $('#passwordChangeModalBody').css('opacity', '0.2'); //lower opacity
            $('#passwordChangeModalHeader').css('opacity', '0.2'); //lower opacity
            $("#change_password_loader").removeAttr('hidden');
            //0.5 second delay
            setTimeout(function() {
                $("#change_password_loader").attr('hidden', true); //hide loader
                $('#passwordChangeModalBody').css('opacity', '1'); //reset opacity
                $('#passwordChangeModalHeader').css('opacity', '1'); //reset opacity
                $("#password_change_unsuccessful").removeAttr('hidden'); //show alert
            }, 500);
            //2.5 second delay
            setTimeout(function() {
                $("#password_change_unsuccessful").attr('hidden', true); //hide alert
                $("#currentPassword").val("") //reset current password input
                $("#currentPassword").css('outline', '1px solid var(--clr-error)') //setting outline for current password
                $("#newPassword").css('outline', '1px solid var(--clr-green)') //setting outline for current password
                $("#confirmNewPassword").css('outline', '1px solid var(--clr-green)') //setting outline for current password
            }, 2500);
        }

    }
</script>