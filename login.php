
<?php
// checks if user is logged in and if so redirects them to the dashboard
session_start();


if (
  isset($_SESSION["valid"]) && !isset($_GET["action"])
) {
  header("Location: dashboard.php?redirected=dashboard");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- title-->
  <title>Login</title>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <!-- favicons-->
  <link rel="apple-touch-icon" sizes="180x180" href="/content/favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/content/favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/content/favicon_io/favicon-16x16.png">
  <link rel="manifest" href="/content/favicon_io/site.webmanifest">

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous" />

  <!-- Bootstrap icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

  <!-- Custom CSS-->
  <link rel="stylesheet" href="styles/formValidation.css" />
  <link rel="stylesheet" href="styles/util.css">


</head>


<body class="bg-white overflow-auto">

  <?php
 // depending on user state creates an alert to display relevant message to user
  if (isset($_GET['action'])) {
    if ($_GET['action'] == "loggedOut") {
      ?>
      <div class="alert alert-success alert-dismissible fade show text-center m-0 position-absolute w-100" role="alert">
        <strong>Success!</strong> Successfully logged out.
      </div>
      <?php
    } else if ($_GET['action'] == "notLoggedIn") {
      ?>
        <div class="alert alert-danger alert-dismissible fade show text-center m-0 position-absolute w-100" role="alert">
          <strong>Error!</strong> Not logged in.
        </div>
      <?php
    } else if ($_GET['action'] == "accountDeleted") {
      ?>
          <div class="alert alert-success alert-dismissible fade show text-center m-0 position-absolute w-100" role="alert">
            <strong>Success!</strong> Account successfully deleted.
          </div>
      <?php
    }
    //destory session
    session_destroy();
  }

  ?>



  <main>
    <!--  login screen box-->
    <div class="row mx-1 align-items-center justify-content-center" style="height: 100vh;">
      <div class="col m-0 py-3 d-flex h-auto">
        <!-- Login Form-->
        <form action="includes/login.inc.php" method="post"
          class="needs-validation container col-16 col-sm-12 col-md-10 col-lg-8 mx-auto rounded shadow d-flex flex-column justify-content-center align-items-center gap-4 pb-5"
          style="background-color: #D2D2D2;">

          <!-- Top part, displays the Make-it-all logo -->
          <div class="title-container text-center pt-5 px-5">
            <img src="/content/img/logo.png" alt="logo-image" style="width: 100%; min-width: 300px" />
          </div>

          <!-- Email input box -->
          <div class=" row w-75">
            <div class="col d-flex align-items-center justify-content-center ">
              <div class="form-floating rounded w-100">
                <input type="email" name="email" class="form-control" id="formEmail" placeholder="name@example.com"
                  autocomplete="email" required />
                <label for="formEmail">Email</label>
              </div>
            </div>
          </div>

          <!-- Password input box-->
          <div class="row w-75">
            <div class="col d-flex align-items-center justify-content-center ">
              <div
                class="form-floating w-100 bg-white rounded position-relative d-flex justify-content-end align-items-center">
                <!-- unhide/hide password icon button-->
                <i class="bi bi-eye-slash position-absolute me-3" id="togglePassword" style="cursor: pointer"
                  onclick="const icon = document.querySelector('#togglePassword'); icon.classList.toggle('bi-eye-slash'); icon.classList.toggle('bi-eye')"></i>
                <input type="password" name="password" class="form-control pe-5" id="formPassword"
                  placeholder="Password" autocomplete="current-password" required />
                <label for="formPassword">Password</label>
              </div>
            </div>
          </div>
          <!-- Login button and Register link -->
          <div class="row w-75 mb-3">
            <!-- login button-->
            <div class="col d-flex flex-column align-items-center justify-content-center text-center gap-1">
              <button type="submit" name="submit" class="btn w-100 p-2" style="background-color: #000; color: #FFFFFF;">
                Login
              </button>
              <!-- register text and link-->
              <div class="lead fs-6">
                <p class="m-0">
                  Don't have an account?
                  <a href="register.php" class="text-decoration-none btn-link"><strong>Register</strong></a>
                </p>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- REDUNDANT (not needed)-->
      <div class="col m-0 d-flex d-none d-xxl-block p-0 w-25">
        <div class="border-0 mx-auto rounded d-flex flex-column w-75 ms-5">
          <!--svg image-->
          <img src="/content/img/login-image.svg" alt="login-image" />
        </div>
      </div>

    </div>




  </main>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
    crossorigin="anonymous"></script>

  <!-- custom JS to hide/unhide password-->
  <script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#formPassword");
    togglePassword.addEventListener("click", () => {
      // Toggle the type attribute using
      // getAttribure() method
      const type =
        password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);
      // Toggle the eye and bi-eye icon
      this.classList.toggle("bi-eye");
    });
  </script>

  <!-- Bootstrap JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz"
    crossorigin="anonymous"></script>
</body>

