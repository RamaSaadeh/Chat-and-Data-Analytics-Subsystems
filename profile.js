//get role from session storage
var details = sessionStorage.getItem("user");
var role = JSON.parse(details).role;

//ensure that user is logged in to grant access to this page else redirect them to login.html
function checkLogin() {
    try {
        var details = sessionStorage.getItem("user");
        var role = JSON.parse(details).role;
        switch (role) {
            case "a":
                break;
            case "g":
                break;
            case "m":
                break;
            case "l":
                break;
            default:
                window.location.replace("login.html");
                break;
        }
    }
    catch {
        window.location.replace("login.html");
    }
}

var email = JSON.parse(details).email;

var id = JSON.parse(details).id;

function toggle(event, tab) {
    //switching between tabs

    var i = 0;
    var tabcontent;
    var btnTabs;

    document.getElementById("password").value = "";
    document.getElementById("confirm").value = "";
    document.getElementById("successMessage").style.display = "none";
    document.getElementById("error").style.display = "none";

    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    btnTabs = document.getElementsByClassName("btnTabs");
    for (i = 0; i < btnTabs.length; i++) {
        btnTabs[i].className = btnTabs[i].className.replace(" active", "");
    }

    document.getElementById(tab).style.display = "block";
    event.currentTarget.className += " active";
}

var green = "#2fe617";

function passwordClick() {
    $("#passwordDetails").slideDown();
}
function passwordClose() {
    $("#passwordDetails").slideUp();
}

function detailsInput(box) {
    //same checks as register page
    colour = "#D2D2D2";
    document.getElementById(box).style.borderColor = colour;
    a = $("#password").val();
    var colour = (a.length >= 8) ? green : "red";
    $("#length").css('color', colour);

    colour = (a.toUpperCase() != a) ? green : "red";
    $("#lowercase").css('color', colour);

    colour = (a.toLowerCase() != a) ? green : "red";
    $("#uppercase").css('color', colour);

    colour = (/\d/.test(a)) ? green : "red";
    $("#number").css('color', colour);

    var match = specialCheck();
    colour = (match) ? green : "red";
    $("#special").css('color', colour);

    var b = $("#confirm").val();
    colour = (a == b) ? green : "red";
    $("#match").css('color', colour);
}

function specialCheck() {
    //checks if password contains a special character
    var a = $("#password").val();
    for (let i = 0; i < a.length; i++) {
        if (a.charAt(i).match(/^[^a-zA-Z0-9]+$/)) return true;
    }
    return false;
}

function submitClick() {
    colour = "#D2D2D2";
    document.getElementById("password").style.borderColor = colour;
    var complete = passwordComplete();
    //hide messages
    var a = document.getElementById("successMessage");
    a.style.display = "none";
    var b = document.getElementById("error");
    b.style.display = "none";
    if (!complete) return;
    var details = sessionStorage.getItem("user");
    var id = JSON.parse(details).id;
    //check if new password == old password when attempting to change password
    $.ajax({
        type: "POST",
        url: "profile.php",
        data: {
            action: "change_password",
            id: id,
            password: document.getElementById("password").value
        },
        success: function (response) {
            if (response == "invalid") {
                b.style.display = "block";
            }
            else {
                $("#passwordDetails").slideUp();
                a.style.display = "block";
            }
        },
        error: function () {
            alert("error");
        }
    });
}

function passwordComplete() {
    //check if all password parameters are successful
    const colours = [];
    colours[0] = document.getElementById("length").style.color;
    colours[1] = document.getElementById("lowercase").style.color;
    colours[2] = document.getElementById("uppercase").style.color;
    colours[3] = document.getElementById("number").style.color;
    colours[4] = document.getElementById("special").style.color;
    colours[5] = document.getElementById("match").style.color;
    if (!colours.includes("red")) return true;
    $("#passwordDetails").slideDown();
    colour = "red";
    document.getElementById("password").style.borderColor = colour;
    document.getElementById("confirm").style.borderColor = colour;
    return false;
}