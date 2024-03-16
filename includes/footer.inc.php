<?php

session_start();

if (!isset($_SESSION["valid"])) {
    header("Location: ../login.php?error=notLoggedIn");
    exit();
}

// get user name and role
$userName = $_SESSION["username"] . " " . $_SESSION["usersurname"];
$userRole = $_SESSION["role"];


?>

<!--linking to account css stylesheet-->
<link rel="stylesheet" href="../styles/footer.css">

<footer class="container-fluid d-flex justify-content-end footer-bg position-absolute top-100" id="footer">

    <div class="container ">

        <div class="row d-flex flex-row align-items-center justify-content-center text-center py-5"
            style="color: white">

            <!-- company social links -->
            <span class="social-links fs-4 p-3 d-flex gap-5 justify-content-center">
                <a href="#" class="bi bi-instagram" style="color: white;"></a>
                <a href="#" class="bi bi-twitter-x" style="color: white;"></a>
                <a href="#" class="bi bi-youtube" style="color: white;"></a>
                <a href="#" class="bi bi-linkedin" style="color: white;"></a>
                <a href="#" class="bi bi-facebook" style="color: white;"></a>
            </span>

            <button type="button" class="btn"
                style="color: white; text-decoration: underline; width: fit-content; border: none"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">Invite A
                Colleague</button>
            <div class="offcanvas offcanvas-bottom h-auto p-0" tabindex="-1" id="offcanvasBottom"
                aria-labelledby="offcanvasBottomLabel">
                <div class="offcanvas-header p-4 justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-4 pb-5 pt-1">
                    <div class="row">
                        <div class="footerImageColumn col d-none d-md-block">
                            <!--svg image-->
                            <img src="..//content/img/footer-image.svg" alt="footer-image"
                                style="max-width: 400px; width: auto;" />
                        </div>
                        <div class="col-md-6 col-12 d-flex flex-column text-start">
                            <span class="display-5 py-3" style="font-weight: 600;">Invite a Colleague</span>
                            <span class="h4 py-3 m-0" style="color: #808080; letter-spacing: 0.6px;">Name
                                (Optional)</span>
                            <div style="position: relative;">
                                <input type="text" class="form-control h-2" placeholder="E.g John"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-person"
                                    style="position: absolute; left:10px; top: 50%; translate: 0 -50%"></i>
                            </div>
                            <span class="h4 py-3 m-0" style="color: #808080; letter-spacing: 0.6px;">Email</span>
                            <div style="position: relative;">
                                <input type="email" class="form-control h-2" placeholder="E.g John@make-it-all.co.uk"
                                    style="padding-left: 2.5rem;">
                                <i class="bi bi-envelope"
                                    style="position: absolute; left:10px; top: 50%; translate: 0 -50%"></i>
                            </div>
                            <span class="py-3" style="color: #E97777">Enter Invited Member’s Email Address</span>
                            <span class="text-end">
                                <button type="button" class="btn"
                                    style="background: #354182; width: fit-content; color: white; font-weight: 200">Send
                                    Invitation</button>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>


    </div>
</footer>


<!-- Bootstrap JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
    crossorigin="anonymous"></script>


</body>

</html>