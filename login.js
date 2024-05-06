async function getData() {
	var a = document.getElementById("loginError");
	a.innerHTML = "Details could not be found";
	var email = document.getElementById("email").value;
	var password = document.getElementById("password").value;
	$.ajax({
		type: "POST",
		url: "login.php",
		data: {
			//pass data to php
			email: email,
			password: password
		},
		success: function (response) {
			if (response == "invalid") {
				a.style.display = "block";
			}
			else {
				a.style.display = "none";
				//split response into array
				var array = response.split("/");
				switch (array[1]) {
					//check role and redirect to appropriate page
					case 'Admin':
						array[1] = 'a';
						window.location.replace("AdminDashboard.html");
						break;
					case 'General Staff':
						array[1] = 'g';
						window.location.replace("userdash.html");
						break;
					case 'Manager':
						array[1] = 'm';
						window.location.replace("accessproject.php");
						break;
					case 'Leader':
						array[1] = 'l';
						window.location.replace("leader-dash.php");
						break;
				}
				let obj = { id: array[0], role: array[1], email: array[2] };
				//create session storage where active user details are stored
				sessionStorage.setItem("user", JSON.stringify(obj));
			}
		}
	});	
}

function clearSessionStorage() {
	let obj = { id: "none", role: "none", email: "none" };
	sessionStorage.setItem("user", JSON.stringify(obj));
}