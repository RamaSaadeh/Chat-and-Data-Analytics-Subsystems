var inviteForm = document.getElementById("inviteuseropaquebg");

function checkLogin() {
    try {
        var details = sessionStorage.getItem("user");
        var role = JSON.parse(details).role;
        switch (role) {
            case "a":
                window.location.replace("permission-denied.html");
                break;
            case "g":
                window.location.replace("permission-denied.html");
                break;
            case "m":
                window.location.replace("permission-denied.html");
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

function openForm() {
    inviteForm.style.display = "block";
}

function closeForm() {
    inviteForm.style.display = "none";
    document.getElementById("email").value = "";
    document.getElementById("emailError").style.display = "none";
}

function sendInvite() {
    var email = document.getElementById("email").value;
    var label = document.getElementById("emailError");
    if (email.slice(email.length - 18).toLowerCase() != "@make-it-all.co.uk") {
        label.innerHTML = "Email address is not valid";
        label.style.color = "red";
        label.style.display = "block";
    }
    else {
        label.innerHTML = "Invite sent!";
        label.style.color = "#2fe617";
        label.style.display = "block";
    }
}

var details = sessionStorage.getItem("user");
var id = JSON.parse(details).id;

$.ajax({
    type: "POST",
    url: "get_leader_projects.php",
    data: {
        id: id
    },
    success: function (response) {
        var projects = JSON.parse(response);
        //print projects as options in dropdown
        projects.forEach(function (project) {
            document.getElementById("select-proj").innerHTML += project;
        });
    }
});
