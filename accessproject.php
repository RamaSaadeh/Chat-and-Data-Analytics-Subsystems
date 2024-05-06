<html lang="en">
<head>
  <title>My Projects</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
  <link href="https://db.onlinewebfonts.com/c/77009ab521bc15b6e38fcc22dd5270f4?family=Churchward+Design+Bold" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/css/bootstrap.min.css">
  <link href="accessproject.css" rel="stylesheet"></script>
</head>

<body onload="checkLogin()">
		<!--Beginning of html for navbar-->
        <nav class="myNav">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="#">
                    <img src="logo.PNG" id="logo" />
                </a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link" id="dashboard" href="#">Dashboard</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Posts
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="all-posts.html">All Posts</a>
							<a class="dropdown-item" href="create-post.html">Create Post</a>
						</div>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="btnInviteUser" onclick="openForm()" href="#">Invite User</a>
					</li>
					<li class="nav-item dropdown" id="accountBox">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span id="profileIcon" class="material-symbols-outlined">account_circle</span>Account</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="profile.html">My Profile</a>
							<a class="dropdown-item logout" href="login.html">Logout</a>
						</div>
					</li>
				</ul>
			</div>
		</nav>
	</nav>

    <div class="popup-background" id="inviteuseropaquebg">
		<form class="inviteuser-form" action="javascript:getData()" method="get">
			<h1>Invite User</h1>

			<label for="email"><b>Email</b></label>
			<input type="text" placeholder="Enter Email" id="email" name="email">
			<label id="emailError" style="color: red; display: none">Email address is not valid</label>

			<button type="submit" class="btn" onclick="sendInvite()">Send Invite Link</button>
			<button type="button" class="btn cancel" onclick="closeForm()">Close</button>
		</form>
	</div>

    <!--My projects Box-->
    <div class = "container">

        <div class="myprojects-flex">
		
            <div class="title">My Projects</div>   
            <form id="form-selectproject" action="managerdash.html" method="get">
                
                <label>Select your project to launch</label>
                <select required id="select-proj" name="selected_project_ID">
                    <option value="" disabled selected>Select Project or Search by Typing</option>
                    <?php include "get_projects.php"; ?>
                </select>		
                <button type="submit" id="exploreproj-btn">Explore Project</button>

            </form>
        </div>

        <div class="or-flex">
            <div id="or-word">Or</div>
        </div>


        <div class="createproj-flex">
		
            <div class="title">Create New Project</div>   
                <form id="createnew_form" onsubmit="create_newproj()" method="get">
                    
                    <label>Enter new Project Name</label>
                    <input type="text" placeholder="Enter Project Name" id="newprojname" required>			
                    <button type="submit" id="newproj-btn">Create Project</button>

				</form>
            </div>
        </div>



    </div>
	

    <div id="emptypagefooter"></div>




<footer class="myFooter">
	<div class = "footer">
	<div class = "footer-content">
	<div class = "footer-section about">

		<h1 class ="footer-title"> Make-It-All</h1>

		<div class="contact-details">
			<span><i class="fas fa-phone"></i> &nbsp; 01509 888999</span>
			<span><i class="fas fa-envelope"></i> &nbsp; king@make-it-all.co.uk</span>
		</div>
	</div>

	</div>
	</div>
</footer>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="accessproject.js"></script>
	

</body>
</html>