<!DOCTYPE html>
<html lang="en">

<head>
  <title>Register</title>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="/content/favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/content/favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/content/favicon_io/favicon-16x16.png">
  <link rel="manifest" href="/content/favicon_io/site.webmanifest">

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous" />
  <!-- Bootstrap icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="styles/formValidation.css" />
  <link rel="stylesheet" href="styles/util.css">
</head>

<body class="bg-white overflow-auto">

  <main>
    <div class="row mx-1 align-items-center justify-content-center" style="height: 100vh;">
      <div class="col m-0 py-3 d-flex h-auto">
        <form action="includes/register.inc.php" method="post" onsubmit="return comparePwd()"
          class="form container col-16 col-sm-12 col-md-10 col-lg-8 mx-auto rounded shadow d-flex flex-column justify-content-center align-items-center gap-4 pb-5"
          style="background-color: #D2D2D2;">
          <div class="title-container text-center pt-5 px-5">
            <!--display titles-->
            <img src="/content/img/logo.png" alt="logo-image" style="width: 100%; min-width: 300px" />
          </div>
          <!-- First Name -->
          <div class="row w-75">
            <div class="col">
              <div class="form-floating rounded">
                <input type="text" class="form-control" name="name" id="formFirstName" placeholder="John"
                  autocomplete="given-name" required />
                <label for="formFirstName">Name</label>
              </div>
            </div>
          </div>
          <!-- Last Name -->
          <div class="row w-75">
            <div class="col">
              <div class="form-floating rounded">
                <input type="text" class="form-control" name="surname" id="formLastName" placeholder="Doe"
                  autocomplete="family-name" required />
                <label for="formLastName">Surname</label>
              </div>
            </div>
          </div>


          <!-- Email -->
          <div class="row w-75">
            <div class="col">
              <div class="form-floating rounded">
                <input type="email" name="email" class="form-control" id="formEmail" placeholder="name@example.com"
                  autocomplete="email" pattern="[^@\s]+@make-it-all.co.uk" required />
                <label for="formEmail">Email</label>
              </div>
            </div>
          </div>
          <!-- Password -->
          <div class="row w-75">
            <div class="col">
              <div
                class="form-floating bg-white rounded position-relative d-flex justify-content-end align-items-center">
                <i class="bi bi-eye-slash position-absolute me-3" id="togglePassword" style="cursor: pointer"></i>
                <input type="password" class="formPassword form-control pe-5" name="password" id="formPassword"
                  placeholder="Password" minlength="8" autocomplete="new-password"
                  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" required />
                <label for="formPassword">Password</label>
              </div>
            </div>
          </div>
          <!-- Confirm Password -->
          <div class="row w-75">
            <div class="col">
              <div
                class="form-floating bg-white rounded position-relative d-flex justify-content-end align-items-center">
                <i class="bi bi-eye-slash position-absolute me-3" id="toggleConfirmPassword"
                  style="cursor: pointer"></i>
                <input type="password" class="confirmPassword form-control pe-5" name="confirmPassword"
                  id="confirmPassword" placeholder="Password" minlength="8" autocomplete="new-password"
                  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" required />
                <label for="confirmPassword">Confirm Password</label>
              </div>
            </div>
          </div>
          <!-- Others -->
          <div class="row w-75 mb-3">
            <div class="col d-flex flex-column align-items-center justify-content-center text-center gap-1">
              <!-- Submit Button -->
              <button type="submit" name="submit" class="btn w-100" style="background-color: #000; color: #FFFFFF;">
                Register
              </button>
              <!-- Redirect link to login page -->
              <div class="lead fs-6">
                <p class="m-0">
                  Already have an account?
                  <a href="login.php" class="text-decoration-none btn-link"><strong>Login</strong></a>
                </p>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="col m-0 d-flex d-none d-xxl-block p-0 w-25">
        <div class="border-0 mx-auto rounded d-flex flex-column w-75 ms-5">
          <!--svg image-->
          <img src="/content/img/register-image.svg" alt="register-image" />
        </div>
      </div>
    </div>
  </main>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
    crossorigin="anonymous"></script>

  <script>
    const togglePassword = document.querySelector("#togglePassword");
    const toggleConfirmPassword = document.querySelector(
      "#toggleConfirmPassword"
    );
    const password = document.querySelector("#formPassword");
    const confirmPassword = document.querySelector("#confirmPassword");

    togglePassword.addEventListener("click", () => {
      // Toggle the type attribute using
      // getAttribute() method
      const type =
        password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);
      // Toggle the eye and bi-eye icon
      togglePassword.classList.toggle("bi-eye");
    });

    toggleConfirmPassword.addEventListener("click", () => {
      // Toggle the type attribute using
      // getAttribute() method
      const type =
        confirmPassword.getAttribute("type") === "password" ?
          "text" :
          "password";
      confirmPassword.setAttribute("type", type);
      // Toggle the eye and bi-eye icon
      toggleConfirmPassword.classList.toggle("bi-eye");
    });
  </script>

  <script>

  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz"
    crossorigin="anonymous"></script>
  <script src="sources/formValidation.js"></script>
</body>

</html>
