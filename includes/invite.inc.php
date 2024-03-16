<?php

session_start();

if (!isset($_POST["invite"])) {
    if ($_SESSION["title"] === "Dashboard") {
        header('location: ../dashboard.php?error=invalidCredentials');
        exit();
    } else if ($_SESSION["title"] === "Home") {
        header('location: ../homepage.php?error=invalidCredentials');
        exit();
    } else if ($_SESSION["title"] === "Tasks") {
        header('location: ../manager-tasks.php?error=invalidCredentials');
        exit();
    } else if ($_SESSION["title"] === "Projects") {
        header('location: ../manager-projects.php?error=invalidCredentials');
        exit();
    } else if ($_SESSION["title"] === "To-Do") {
        header('location: to-do-list.inc.php?error=invalidCredentials');
        exit();
    } else {
        header('location: ../login.php?error=invalidCredentials');
        exit();
    }
}

$name = $_SESSION["username"] . " " . $_SESSION["usersurname"];
$email = $_SESSION["email"];

$downIcon = "\u{FE3E}";


#Receive user input
$emailAddress = $_POST['email'];

#Send email

$emailSubject = "Welcome to Make-It-All | A Referral from $name";

$emailBody = "<html><body class='center'>";
$emailBody .= "<style>\n
  * {\n
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;\n
  }\n
  
  body{
    width:100%;
  }\n

  p {\n
    text-align: start;\n
    margin: 0;\n
  }\n
  
  .container {\n
    padding: 1rem 1rem;\n
    width: fit-content;\n
  }
  .center {\n
    display: flex;\n
    flex-direction: column;\n
    justify-content: center;\n
    /* align-items: center; */\n
  }\n
  .spacing {\n
    line-height: 150%;\n
  }\n

  .btn{
    display: block;
    width: fit-content;
    padding: 1rem 2rem;
    
    border: white solid 1px;
    
    border-radius: 10px;
    
    background-color: #354182;
    color: white;
    transition: all 0.25s ease-in;

    transition: all 0.25s ease-in;
  }\n

  .btn:hover {
    background-color: transparent;
    border: #354182 solid 1px;
    color: #354182;
  }\n

  .btn-center {\n
    width: 100%;\n
    display: flex;\n
    flex-direction: column;\n
    align-items: center;\n
    justify-content: center;\n
  }\n

  .footer{\n
    opacity: 75%;\n
    font-size: 0.85rem;\n
  }\n

  
  

</style>\n
<div class='container center spacing'>\n
  <p>Hi There,</p>\n
  <br />\n
  <p>\n
    We're excited to invite you to join our website, where the adventure
    begins! 🚀
  </p>\n
  <p>\n
    It's the perfect place to connect, explore, and share your passions with
    like-minded folks.
  </p>\n
  <p>\n
    Click the link below to jump in and start your journey today. Join now:
    <br />\n
    <div class='container btn-center' >\n
            <a class='btn'
                href='http://team001.sci-project.lboro.ac.uk/register.php'
                target='_blank'
                style='text-decoration: none'
                >
                Register A New Account
            </a
              >\n
        </div>\n
  </p>\n

  <div class='footer'>\n
      <p>Best regards,</p>\n
      <br>
      <p>
          Daniel Adekunle-Adenusi
          <br>
          Make-It-All Internal Affairs
      </p>
  </div>\n
</div>\n";
$emailBody .= "</body></html>";

// $headers = array(
//     "From: danieladenusi26@gmail.com",
//     "Reply-To: danieladenusi26@gmail.com",
//     "X-Mailer: PHP/" . PHP_VERSION
// );
// $headers = implode("\r\n", $headers);

$header = "From: d.adekunle-adenusi-22@student.lboro.ac.uk\r\n";
$header .= "Reply-To: noreply@gmail.com \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-Type: text/html; charset=UTF-8\r\n";
$header .= "X-Priority: 1\r\n";

// $sent = mail($emailAddress, $emailSubject, $emailMessage);
$sent = mail($emailAddress, $emailSubject, $emailBody, $header);

#Thank user or notify them of a problem
if ($sent) {

    if ($_SESSION["title"] === "Dashboard") {
        header('location: ../dashboard.php?error=none?email=sent');
        exit();
    } else if ($_SESSION["title"] === "Home") {
        header('location: ../homepage.php?error=none?email=sent');
        exit();
    } else if ($_SESSION["title"] === "Tasks") {
        header('location: ../manager-tasks.php?error=none?email=sent');
        exit();
    } else if ($_SESSION["title"] === "Projects") {
        header('location: ../manager-projects.php?error=none?email=sent');
        exit();
    } else if ($_SESSION["title"] === "To-Do") {
        header('location: to-do-list.inc.php?error=none?email=sent');
        exit();
    }

} else {

    header('location: ../dashboard.php?error=emailNotSent');
    exit();
}
?>