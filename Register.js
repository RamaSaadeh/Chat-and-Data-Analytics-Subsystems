var green = "#2fe617";

function passwordClick() {
	$("#passwordDetails").slideDown();
}
function passwordClose() {
	$("#passwordDetails").slideUp();
}
function detailsInput(box) {
	colour = "#D2D2D2";
	document.getElementById(box).style.borderColor = colour;
	a = $("#password").val();
	//if password length is greater than or equal to 8 then make colour green else make colour red
	var colour = (a.length >= 8) ? green : "red";
	$("#length").css('color', colour);
	//this works in the same way for the other checks
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
	//check that password contains a special character
	var a = $("#password").val();
	for (let i = 0; i < a.length; i++) {
		if (a.charAt(i).match(/^[^a-zA-Z0-9]+$/)) return true;
	}
	return false;
}

function submitClick() {
	event.preventDefault();
	colour = "#D2D2D2";
	document.getElementById("password").style.borderColor = colour;
	$("#passwordDetails").slideUp();
	var a = document.getElementById("registerError");
	a.style.display = "none";
	var firstName = document.getElementById("firstName").value;
	var surname = document.getElementById("surname").value;
	var email = document.getElementById("email").value;
	var role = document.getElementById("role").value;
	var password = document.getElementById("password").value;
	//check all failure possibilities
	if (firstName.length == 0 || surname.length == 0 || email.length == 0 || !passwordComplete()) return;
	//check that email address is a make-it-all email address
	if (email.slice(email.length - 18).toLowerCase() != "@make-it-all.co.uk") {
		registerError.innerHTML = "Email address must be assigned to Make-It-All";
		colour = "red";
		document.getElementById("email").style.borderColor = colour;
		a.style.display = "block";
	}
	else {
		$.ajax({
			type: "POST",
			url: "Register.php",
			data: {
				//data to pass to php
				action: "check_email",
				email: email
			},
			success: function (response) {
				//if response means that email address is already in use
				if (response == "exists") {
					//show error message
					registerError.innerHTML = "Email address is already in use";
					colour = "red";
					document.getElementById("email").style.borderColor = colour;
					a.style.display = "block";
				}
				else {
					//successful
					$.ajax({
						type: "POST",
						url: "Register.php",
						data: {
							//data to pass to php
							action: "register_user",
							firstName: firstName,
							surname: surname,
							email: email,
							role: role,
							password: password
						}
					});
					//show success message
					registerError.style.color = "#2fe617";
					registerError.innerHTML = "Success!";
					a.style.display = "block";
				}
			},
			error:function(){
				alert("error");
			}
		});
	}
}

function passwordComplete() {
	//checks if all password parameters are successful
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