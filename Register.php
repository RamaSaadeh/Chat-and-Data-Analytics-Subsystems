<?php 



	$action = $_POST['action'];
	
	$servername = "localhost";
	$username = "host";
	$dbpassword = "Team017FTW!";
	$database = "makeitall";

	$conn = new mysqli($servername, $username, $dbpassword, $database);

	if ($action == "check_email") {
		$email = $_POST['email'];
		//check if email already exists
		$stmt = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($emailCount);
        $stmt->fetch();
        $stmt->close();

        if ($emailCount > 0) {
            echo "exists";
        } else {
            echo "not_exists";
        }
	}

	else if ($action == "register_user") {
		$firstName = $_POST['firstName'];
		$surname = $_POST['surname'];
		$name = $firstName . " " . $surname;
		$email = $_POST['email'];
		$role = $_POST['role'];
		$password = hash('sha256', $_POST['password']);

		//add new user to database
		$stmt = $conn->prepare("INSERT INTO `users` (role, email, name, password) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssss", $role, $email, $name, $password);
		$stmt->execute();
	}
?>