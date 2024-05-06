<?php
	$action = $_POST['action'];

	$servername = "localhost";
	$username = "host";
	$dbpassword = "Team017FTW!";
	$database = "makeitall";

	$conn = new mysqli($servername, $username, $dbpassword, $database);

	if ($action == "change_password") {
		$id = $_POST['id'];
		//hashes password for security
		$password = hash('sha256', $_POST['password']);

		//get current password
		$stmt = $conn->prepare("SELECT password FROM `users` WHERE user_id = ?");
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$stmt->bind_result($existing_password);
		$stmt->fetch();
		$stmt->close();

		if ($password == $existing_password) {
			echo "invalid";
		}
		else {
			//if new password is different, update password in database
			$stmt = $conn->prepare("UPDATE `users` SET password = ? WHERE user_id = ?");
			$stmt->bind_param("ss", $password, $id);
			$stmt->execute();
			$stmt->close();
			echo "valid";
		}
	}

	else if ($action == "get_projects") {
		$id = $_POST['id'];

		//get number of projects that current user is part of
		$stmt = $conn->prepare("SELECT COUNT(project_id) FROM `project_staff` WHERE user_id = ?");
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$stmt->bind_result($num_projects);
		$stmt->fetch();
		$stmt->close();
		if ($num_projects == 0) {
			echo "none";
		}
		else {
			//if current user is part of at least one project, get project ids in order
			$stmt = $conn->prepare("SELECT project_id FROM `project_staff` WHERE user_id = ? ORDER BY project_id");
			$stmt->bind_param("s", $id);
			$stmt->execute();
			$stmt->bind_result($projects);
			while ($stmt->fetch()) {
				$conn2 = new mysqli($servername, $username, $dbpassword, $database);
				//get name and leader status for every project that the current user is part of
				$stmt2 = $conn2->prepare("SELECT proj_name, leader_id FROM `projects` WHERE project_id = ?");
				$stmt2->bind_param("s", $projects);
				$stmt2->execute();
				$stmt2->bind_result($name, $leader);
				$stmt2->fetch();
				$stmt2->close();
				if ($id == $leader) {
					echo "$name-L/";
				}
				else {
					echo "$name/";
				}
			}
			$stmt->close();
		}
	}

	else if ($action == "get_posts") {
		$id = $_POST['id'];

		include 'db.php';

		//get post details for current user
		$sql = "SELECT PostID, Title, DateCreated, IsDraft, LikesCount, Topic
				FROM Posts
				WHERE IsDraft = 0 AND UserID = ?
				ORDER BY DateCreated DESC";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$posts = array();
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$posts[] = $row;
			}
			echo json_encode($posts);
		}
		else {
			echo json_encode([]);
		}
		$stmt->close();
	}

	else if ($action == "get_comments") {
		$id = $_POST['id'];

		include 'db.php';

		//get comment details for current user
		$sql = "SELECT p.PostID, p.Title, p.Topic, c.CommentContent, c.Likes, c.LastModified
				FROM Comments c
				INNER JOIN Posts p ON c.PostID = p.PostID
				WHERE c.UserID = ?
				ORDER BY c.LastModified DESC";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$posts = array();
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$posts[] = $row;
			}
			echo json_encode($posts);
		}
		else {
			echo json_encode([]);
		}
		$stmt->close();
	}
?>