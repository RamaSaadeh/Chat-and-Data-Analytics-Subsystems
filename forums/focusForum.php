<?php
session_start(); //start session
$currentPage = "Forums";
if (!isset($_SESSION["valid"])) { //if session isn't valid take back to login page
    header("Location: ../login.php");
    exit();
} else if ($_SESSION["role"]) { //if user is a member include member header
    include_once('../includes/header.inc.php');
}
?>
<!--linking to account css stylesheet-->
<link rel="stylesheet" href="styles/forumsDisplay.css">
<script>
    removeBackgrounds();
    setBackgrounds(2);
</script>

<?php
require_once("../includes/dbh.inc.php");
$userID = $_SESSION['userid'];
$postID = $_GET["postid"];
//sql getting all forum posts
$sql = "SELECT 
        posts.*, userDetails.userProfilePicture, userDetails.userName, userDetails.userSurname, 
        LDR,
        numberOfComments
        FROM posts 
            INNER JOIN userDetails ON posts.authorID = userDetails.userID 
            LEFT JOIN (SELECT postLikeOrDislikeDetails.postID as postID, SUM(postLikeOrDislikeDetails.postLOD) as LDR
                    FROM postLikeOrDislikeDetails 
                    GROUP BY postLikeOrDislikeDetails.postID
                    ) AS Q ON Q.postID = posts.postID 
            LEFT JOIN (SELECT commentDetails.postID as postID, COUNT(commentDetails.commentID) as numberOfComments
            FROM commentDetails 
            GROUP BY commentDetails.postID
            ) AS R ON R.postID = posts.postID
        WHERE posts.postID = $postID
        GROUP BY posts.postID";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    //determining post likes
    $sql2 = "SELECT postID, group_concat(userID SEPARATOR ',') as userID, group_concat(postLOD SEPARATOR ',') as postLOD FROM postLikeOrDislikeDetails WHERE postID = " . $row['postID'] . " AND userID GROUP BY postID;";
    $result2 = $conn->query($sql2);
    $LDR = 0; //LDR is Like to Dislike ratio
    $liked = false;
    $disliked = false;
    while ($row2 = $result2->fetch_assoc()) {
        $LODArray = explode(",", $row2['postLOD']);
        $UserIDArray = explode(",", $row2['userID']);
        if ($LODArray[0] != "") { //if a like exists
            for ($i = 0; $i <= count($LODArray) - 1; $i++) { //loop for length of likes
                if ($UserIDArray[$i] == $_SESSION['userid']) {
                    if ($LODArray[$i] == 1) {
                        $liked = true;
                    } else if ($LODArray[$i] == -1) {
                        $disliked = true;
                    }
                }
                $LDR = $LDR + $LODArray[$i];
            }
        }
    }
    $authorID = $row['authorID'];
?>
    <main class="container mw-75 p-3">
        <div class="shadow rounded d-flex my-3 m-0">
            <div class="d-flex flex-column align-items-center p-3 d-inline-block justify-content-center" style="width: fit-content; background-color: #FAFAFA; box-shadow: 1px 0px 5px 1px rgba(0, 0, 0, 0.25); border-radius: 0.375rem 0 0 0.375rem;">
                <!-- Like Forum Button -->
                <?php
                if ($liked == true) {
                ?>
                    <i id="likePost<?php echo $row['postID'] ?>" class="forum__like__btn bi bi-hand-thumbs-up-fill fs-5" style="color: var(--clr-green); cursor: pointer"></i>
                <?php
                } else if ($liked == false) {
                ?>
                    <i id="likePost<?php echo $row['postID'] ?>" class="forum__like__btn bi bi-hand-thumbs-up fs-5" style="color: var(--clr-green); cursor: pointer"></i>
                <?php
                }
                ?>
                <!-- LDR Display -->
                <?php
                if ($LDR >= 0) {
                    echo '<span id="LDRPost' . $row['postID'] . '" class="py-1" style="color: var(--clr-green)"> ' . $LDR . ' </span>'; //positive LDR
                } else {
                    echo '<span id="LDRPost' . $row['postID'] . '" class="py-1" style="color: var(--clr-red)"> ' . $LDR . ' </span>'; //negative LDR
                }
                ?>
                <!-- Dislike Forum Button -->
                <?php
                if ($disliked == true) {
                ?>
                    <i id="dislikePost<?php echo $row['postID'] ?>" class="forum__dislike__btn bi bi-hand-thumbs-down-fill fs-5" style="color: var(--clr-red); cursor: pointer"></i>
                <?php
                } else if ($disliked == false) {
                ?>
                    <i id="dislikePost<?php echo $row['postID'] ?>" class="forum__dislike__btn bi bi-hand-thumbs-down fs-5" style="color: var(--clr-red); cursor: pointer"></i>
                <?php
                }
                ?>
            </div>

            <script>
                $('#likePost<?php echo $row['postID'] ?>').click(function() { //liked button clicked
                    if (this.classList.contains("bi-hand-thumbs-up")) {
                        //POST LIKED
                        this.classList.remove("bi-hand-thumbs-up") //remove unfilled hand
                        this.classList.add("bi-hand-thumbs-up-fill") //add filled hand
                        if (document.getElementById("dislikePost<?php echo $row['postID'] ?>").classList.contains("bi-hand-thumbs-down-fill")) { //DISLIKE TO LIKE
                            document.getElementById("dislikePost<?php echo $row['postID'] ?>").classList.add("bi-hand-thumbs-down") //add unfilled hand
                            document.getElementById("dislikePost<?php echo $row['postID'] ?>").classList.remove("bi-hand-thumbs-down-fill") //remove filled hand
                            //UPDATING DATABASE
                            var xhttp = new XMLHttpRequest();
                            xhttp.open("GET", "undislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
                            xhttp.send();
                            xhttp.onload = () => {
                                $("#LDRPost<?php echo $row['postID'] ?>").html(parseInt($("#LDRPost<?php echo $row['postID'] ?>").html()) + 2); //LDR updated on page
                                if ($("#LDRPost<?php echo $row['postID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                    $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-green)")
                                } else {
                                    $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-red)")
                                }
                                //UPDATING DATABASE
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("GET", "likeForum.php?postid=<?php echo $row['postID'] ?>", true); //like post
                                xhttp.send();
                            }
                        } else { //NULL TO LIKE
                            $("#LDRPost<?php echo $row['postID'] ?>").html(parseInt($("#LDRPost<?php echo $row['postID'] ?>").html()) + 1); //LDR updated on page
                            if ($("#LDRPost<?php echo $row['postID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-green)")
                            } else {
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-red)")
                            }
                            //UPDATING DATABASE
                            var xhttp = new XMLHttpRequest();
                            xhttp.open("GET", "likeForum.php?postid=<?php echo $row['postID'] ?>", true); //like post
                            xhttp.send();
                        }
                    } else {
                        //POST UNLIKED
                        this.classList.add("bi-hand-thumbs-up") //add unfilled hand
                        this.classList.remove("bi-hand-thumbs-up-fill") //remove filled hand
                        //UPDATING DATABASE
                        var xhttp = new XMLHttpRequest();
                        xhttp.open("GET", "unlikeForum.php?postid=<?php echo $row['postID'] ?>", true); //unlike post
                        xhttp.send();
                        xhttp.onload = () => {
                            $("#LDRPost<?php echo $row['postID'] ?>").html(parseInt($("#LDRPost<?php echo $row['postID'] ?>").html()) - 1); //LDR updated on page
                            if ($("#LDRPost<?php echo $row['postID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-green)")
                            } else {
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-red)")
                            }
                        }
                    }
                });
                $('#dislikePost<?php echo $row['postID'] ?>').click(function() { //dislike button clicked
                    if (this.classList.contains("bi-hand-thumbs-down")) {
                        //POST DISLIKED
                        this.classList.remove("bi-hand-thumbs-down") //remove unfilled hand
                        this.classList.add("bi-hand-thumbs-down-fill") //add filled hand
                        if (document.getElementById("likePost<?php echo $row['postID'] ?>").classList.contains("bi-hand-thumbs-up-fill")) { //LIKE TO DISLIKE
                            document.getElementById("likePost<?php echo $row['postID'] ?>").classList.add("bi-hand-thumbs-up") //add unfilled hand
                            document.getElementById("likePost<?php echo $row['postID'] ?>").classList.remove("bi-hand-thumbs-up-fill") //remove filled hand
                            //UPDATING DATABASE
                            var xhttp = new XMLHttpRequest();
                            xhttp.open("GET", "unlikeForum.php?postid=<?php echo $row['postID'] ?>", true); //unlike post
                            xhttp.send();
                            xhttp.onload = () => {
                                $("#LDRPost<?php echo $row['postID'] ?>").html(parseInt($("#LDRPost<?php echo $row['postID'] ?>").html()) - 2); //LDR updated on page
                                if ($("#LDRPost<?php echo $row['postID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                    $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-green)")
                                } else {
                                    $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-red)")
                                }
                                //UPDATING DATABASE
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("GET", "dislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //dislike post
                                xhttp.send();
                            }
                        } else { //NULL TO DISLIKE
                            $("#LDRPost<?php echo $row['postID'] ?>").html(parseInt($("#LDRPost<?php echo $row['postID'] ?>").html()) - 1); //LDR updated on page
                            if ($("#LDRPost<?php echo $row['postID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-green)")
                            } else {
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-red)")
                            }
                            //UPDATING DATABASE
                            var xhttp = new XMLHttpRequest();
                            xhttp.open("GET", "dislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //dislike post
                            xhttp.send();
                        }
                    } else {
                        //POST UNDISLIKED
                        this.classList.add("bi-hand-thumbs-down") //add unfilled hand
                        this.classList.remove("bi-hand-thumbs-down-fill") //remove filled hand
                        //UPDATING DATABASE
                        var xhttp = new XMLHttpRequest();
                        xhttp.open("GET", "undislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
                        xhttp.send();
                        xhttp.onload = () => {
                            $("#LDRPost<?php echo $row['postID'] ?>").html(parseInt($("#LDRPost<?php echo $row['postID'] ?>").html()) + 1); //LDR updated on page
                            if ($("#LDRPost<?php echo $row['postID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-green)")
                            } else {
                                $("#LDRPost<?php echo $row['postID'] ?>").css("color", "var(--clr-red)")
                            }
                        }
                    }
                });
            </script>



            <div class="d-inline-block py-3 pe-3 w-100" style="padding-left: calc(0.5rem + 5vw); word-break: break-word;">
                <div class=" forumHeader d-flex justify-content-between gap-1"> <!--forum header-->
                    <div>
                        <img id="" height="25" width="25" style="border-radius: 50%;" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>" onerror="this.onerror=null; this.src='account/logo.png'" />
                        <span class="published__details">
                            <?php echo $row['userName'] . " " . $row['userSurname'] . " | " . DATFrameCalculation($row['postDateTime']) ?>
                        </span>
                    </div>
                    <button type="button" class="btn p-0 m-0" style="justify-self: start; align-self: flex-start;" onclick="window.location.replace('../forums.php')"><i class="forum__exit__btn bi bi-x-lg"></i></button>
                </div>
                <div class="forumTitle h5 py-2"> <!--forum title-->
                    <?php echo $row['postName'] ?>
                </div>
                <div class="forumDescription py-2"> <!--forum desc-->
                    <?php echo $row['postDescription'] ?>
                </div>
                <div class="forumTags py-2 small"> <!--forum tags-->
                    <?php
                    $tagArray = explode(",", $row['postTags']);
                    $tagColour = explode(",", $row['tagColour']);
                    if ($tagArray[0] != "") { //if a tag exists
                        for ($i = 0; $i <= count($tagArray) - 1; $i++) { //loop for length of tags
                            if (count($tagArray) == 0) { //if there is only one tag
                                echo "<span class='forumTagFocus'>#" . $tagArray[$i] . "</span>";
                                break;
                            } else { //otherwise
                                echo " ";
                                echo "<span class='forumTagFocus'>#" . $tagArray[$i] . "</span>";
                            }
                        }
                    }
                    ?>
                </div>
                <div class="forumFooter pt-2"> <!--forum footer-->
                    <?php
                    $sql3 = "SELECT userID FROM postFavouriteDetails WHERE userID = " . $_SESSION['userid'] . " AND postID = " . $row['postID'];
                    $result3 = $conn->query($sql3);
                    $favourited = false;
                    while ($row3 = $result3->fetch_assoc()) {
                        if ($row3['userID'] == $_SESSION['userid']) {
                            $favourited = true;
                        }
                    }
                    ?>
                    <?php
                    if ($favourited == false) {
                    ?>
                        <button id="favouritePost<?php echo $row['postID'] ?>" type="button" class="favourite__btn btn p-0"><i id="favouriteIcon<?php echo $row['postID'] ?>" class="bi bi-star"></i> Favourite</button>

                    <?php
                    } else if ($favourited == true) {
                    ?>
                        <button id="favouritePost<?php echo $row['postID'] ?>" type="button" class="favourite__btn btn p-0"><i id="favouriteIcon<?php echo $row['postID'] ?>" class="bi bi-star-fill"></i> Favourite</button>

                    <?php
                    }
                    ?>
                    <script>
                        $('#favouritePost<?php echo $row['postID'] ?>').click(function() { //favourite button clicked
                            if (document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.contains("bi-star")) { //not favourited already
                                document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.add("bi-star-fill") //add filled star
                                document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.remove("bi-star") //remove unfilled star
                                //UPDATING DATABASE
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("GET", "comments/favouriteForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
                                xhttp.send();
                                xhttp.onload = () => {

                                }
                            } else if (document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.contains("bi-star-fill")) { //already favourited
                                document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.add("bi-star") //add unfilled star
                                document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.remove("bi-star-fill") //remove filled star
                                //UPDATING DATABASE
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("GET", "comments/unfavouriteForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
                                xhttp.send();
                                xhttp.onload = () => {

                                }
                            }
                        })
                    </script>
                    <?php
                    if ($row['numberOfComments'] == "") { //no comments
                    ?>
                        <button id="commentsPost<?php echo $row['postID'] ?>" type="button" class="comments__btn btn p-0 ms-2"><i class="bi bi-chat-right-text"></i> 0 Comments</button>
                    <?php
                    } else if ($row['numberOfComments'] == 1) { //1 comment
                    ?>
                        <button id="commentsPost<?php echo $row['postID'] ?>" type="button" class="comments__btn btn p-0 ms-2"><i class="bi bi-chat-right-text"></i> 1 Comment</button>
                    <?php
                    } else { //otherwise
                    ?>
                        <button id="commentsPost<?php echo $row['postID'] ?>" type="button" class="comments__btn btn p-0 ms-2"><i class="bi bi-chat-right-text"></i>
                            <?php echo $row['numberOfComments'] ?> Comments
                        </button>
                    <?php
                    }


                    ?>
                </div>
            </div>
        </div>
    <?php } //closing while loop
    ?>
    <div class="shadow rounded my-3 m-0 p-3">
        <span>Comment as <?php echo $_SESSION['username'] . " " . $_SESSION['usersurname'] ?>
        </span>
        <div id="add__comment__container" style="border-radius: 0.375rem;">
            <div class="form-group">
                <textarea class="form-control" id="comment__textarea" rows="5" placeholder="Add a comment" style="border-bottom: none; border-radius: 0.375rem 0.375rem 0 0"></textarea>
            </div>
            <script>
                $("#comment__textarea").focus(function() { //focus in textarea
                    $("#comment__textarea").css("border-color", "#dee2e6")
                    $("#comment__textarea").css("box-shadow", "none")
                    $("#add__comment__container").css("box-shadow", "0 0 0 0.25rem rgba(150, 166, 248, 0.25)")
                    $("#add__comment__container").css("transition", "box-shadow 0.5s cubic-bezier(0.445, 0.05, 0.55, 0.95);")
                })
                $("#comment__textarea").focusout(function() { //focus out of textarea
                    $("#add__comment__container").css("box-shadow", "")
                    $("#add__comment__container").css("transition", "")
                })
            </script>
            <div id="commentTextareaFooter" class="text-end p-1" style="height: 40px; background-color: #F5F5F5; border: 1px solid #dee2e6; border-radius: 0 0 0.375rem 0.375rem; border-top: none;">
                <button id="commentBtn" type="button" class="btn px-4 btn-primary" style="border-radius: 1.25rem !important; font-size: smaller;" disabled>Comment</button>
            </div>
        </div>
        <!--comment button script-->
        <script>
            $("#commentBtn").click(function() {
                var commentValue = $("#comment__textarea").val().trim().replace(/ /g, "_");
                //UPDATING DATABASE
                var xhttp = new XMLHttpRequest();
                xhttp.open("GET", "addComment.php?postid=<?php echo $postID ?>&commentvalue=" + commentValue, true); //unlike post
                xhttp.send();
                xhttp.onload = () => {
                    $("#comment__textarea").val("") //reset textarea value
                    $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=2')
                }
            })

            $("#comment__textarea").keyup(function() {
                var commentValue = $("#comment__textarea").val().trim().replace(/ /g, "_");
                if (commentValue.length > 0) {
                    $("#commentBtn").prop("disabled", false);
                } else {
                    $("#commentBtn").prop("disabled", true);
                }
            })
        </script>
        <!--displaying comments on post-->
        <div id="commentsContainer" class="d-flex flex-column mt-3" style="word-break: break-word;">
            <div class="d-flex align-items-center gap-3">
                <span id="numberOfComments" class="h5"></span>
                <button type="button" class="btn d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bi bi-filter-left h4 m-0"></i>
                    <span>Sort by</span>
                </button>
                <!--drop down menu for sorting comments-->
                <div class="dropdown-menu mt-1" aria-labelledby="dropdownMenuButton">
                    <button type="button" class="dropdown-item btn" onclick="$('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=1')">Top comments</button>
                    <button type="button" class="dropdown-item btn" onclick="$('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=2')">Newest first</button>
                </div>
            </div>
            <?php
            require_once("../includes/dbh.inc.php");
            $sql = "SELECT commentDetails.*,  userDetails.userName, userDetails.userSurname, userDetails.userProfilePicture, IF (LDR IS NULL, 0, LDR) AS LDR
            FROM commentDetails 
            LEFT JOIN userDetails ON userDetails.userID = commentUserID 
            LEFT JOIN (SELECT commentLikeOrDislikeDetails.commentID as commentID, SUM(commentLikeOrDislikeDetails.commentLOD) as LDR
            FROM commentLikeOrDislikeDetails 
            GROUP BY commentLikeOrDislikeDetails.commentID) AS Q ON Q.commentID = commentDetails.commentID  WHERE commentDetails.postID = $postID 
            ORDER BY commentDetails.commentPinned DESC, commentDetails.commentDateTime DESC;";
            $result = $conn->query($sql);
            echo "<script>$('#numberOfComments').html('" . mysqli_num_rows($result) . " Comments')</script>";
            while ($row = $result->fetch_assoc()) {
                //determining comment likes
                $sql2 = "SELECT commentID, group_concat(userID SEPARATOR ',') as userID, group_concat(commentLOD SEPARATOR ',') as commentLOD FROM commentLikeOrDislikeDetails WHERE commentID = " . $row['commentID'] . " AND userID GROUP BY commentID;";
                $result2 = $conn->query($sql2);
                $LDR = 0; //LDR is Like to Dislike ratio
                $liked = false;
                $disliked = false;
                while ($row2 = $result2->fetch_assoc()) {
                    $LODArray = explode(",", $row2['commentLOD']);
                    $UserIDArray = explode(",", $row2['userID']);
                    if ($LODArray[0] != "") { //if a like exists
                        for ($i = 0; $i <= count($LODArray) - 1; $i++) { //loop for length of likes
                            if ($UserIDArray[$i] == $_SESSION['userid']) {
                                if ($LODArray[$i] == 1) {
                                    $liked = true;
                                } else if ($LODArray[$i] == -1) {
                                    $disliked = true;
                                }
                            }
                            $LDR = $LDR + $LODArray[$i];
                        }
                    }
                }
            ?>
                <div class="entireComment" style="border-top: 1px solid #dee2e6">
                    <?php
                    //if comment is pinned
                    if ($row['commentPinned'] == 1) {
                    ?>
                        <div class="d-flex align-items-center gap-2 px-1 mt-2">
                            <i class="bi bi-pin-angle-fill"></i>
                            <span>Pinned Answer</span>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="d-flex py-2">
                        <div class="d-flex flex-column d-inline-block" style="width: fit-content;">
                            <img height="25" width="25" style="border-radius: 50%;" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>" onerror="this.onerror=null; this.src='account/logo.png'" />
                        </div>
                        <div class="d-inline-block w-100 ps-2">
                            <div class="commentHeader d-flex justify-content-between gap-1 mb-2">
                                <div class="commentUserDisplay">
                                    <span>
                                        <?php echo $row['userName'] . " " . $row['userSurname'] ?>
                                    </span>
                                    <span style="font-weight: lighter;">
                                        <?php
                                        if ($row['commentEdited'] == 0) { //not edited
                                            echo " · " . DATFrameCalculation($row['commentDateTime']);
                                        } else { //edited
                                            echo " · " . DATFrameCalculation($row['commentDateTime']) . " (edited)";
                                        }
                                        ?>
                                    </span>
                                </div>
                                <button id="moreBtn<?php echo $row['commentID'] ?>" type="button" class="btn p-0 m-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="justify-self: start; align-self: flex-start;"><i class="moreBtn bi bi-three-dots-vertical p-1"></i></button>
                                <div class="dropdown-menu mt-1" aria-labelledby="dropdownMenuButton">
                                    <?php
                                    if ($row['commentUserID'] == $userID) {
                                    ?>
                                        <button type="button" class="dropdown-item btn" onclick="editComment(<?php echo $row['commentID'] ?>)">Edit</button>
                                        <script>
                                            //edit comment script
                                            function editComment($commentID) {
                                                var preEditCommentValue = $("#commentValue" + $commentID).html().trim();
                                                $("#commentValue" + $commentID).html('<textarea id="editCommentTextarea' + $commentID + '" class="form-control" style="border-bottom: none; border-radius: 0.375rem 0.375rem 0 0"></textarea>');
                                                //get cursor to focus inside textarea
                                                $("#editCommentTextarea" + $commentID).focus().val(preEditCommentValue)
                                                focusTextArea($commentID)
                                                //hide more button
                                                $("#moreBtn" + $commentID).prop("hidden", true);
                                                $("#editCommentFooter" + $commentID).prop("hidden", false); //show footer
                                                $("#doneBtn" + $commentID).prop("hidden", false); //show done button
                                                $("#cancelBtn" + $commentID).prop("hidden", false); //show cancel button
                                                var scrollheight = $("#editCommentTextarea" + $commentID).prop('scrollHeight') - 60
                                                if (scrollheight != 0) {
                                                    var rows = Math.ceil(scrollheight / 25)
                                                    $("#editCommentTextarea" + $commentID).attr('rows', 2 + rows);
                                                } else {
                                                    $("#editCommentTextarea" + $commentID).attr('rows', 2);
                                                }

                                                //focus textarea script
                                                $("#editCommentTextarea" + $commentID).focus(function() { //focus in textarea
                                                    focusTextArea($commentID)
                                                })
                                                $("#editCommentTextarea" + $commentID).focusout(function() { //focus out of textarea
                                                    $("#viewCommentContainer" + $commentID).css("box-shadow", "")
                                                    $("#viewCommentContainer" + $commentID).css("transition", "")
                                                })
                                            }

                                            function focusTextArea($commentID) {
                                                $("#editCommentTextarea" + $commentID).css("border-color", "#dee2e6")
                                                $("#editCommentTextarea" + $commentID).css("box-shadow", "none")
                                                $("#viewCommentContainer" + $commentID).css("box-shadow", "0 0 0 0.25rem rgba(150, 166, 248, 0.25)")
                                                $("#viewCommentContainer" + $commentID).css("transition", "box-shadow 0.5s cubic-bezier(0.445, 0.05, 0.55, 0.95);")
                                            }
                                        </script>
                                        <button type="button" class="dropdown-item btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-bs-whatever="<?php echo $row['commentID'] ?>">Delete</button>
                                        <script>
                                            //delete comment script
                                            function deleteComment($commentID) {
                                                //UPDATING DATABASE
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.open("GET", "comments/deleteComment.php?commentid=" + $commentID, true); //unlike post
                                                xhttp.send();
                                                xhttp.onload = () => {
                                                    $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=1')
                                                }
                                            }
                                        </script>
                                        <?php
                                        if ($row['commentPinned'] == 1 && $userID == $authorID) { //if comment is already pinned
                                        ?>
                                            <button type="button" class="dropdown-item btn" onclick="unpinComment(<?php echo $row['commentID'] ?>)">Unpin</button>
                                        <?php
                                        } else if ($userID == $authorID) { //otherwise
                                        ?>
                                            <button type="button" class="dropdown-item btn" onclick="pinComment(<?php echo $row['commentID'] ?>)">Pin</button>
                                        <?php
                                        }
                                        ?>
                                        <script>
                                            //pin comment script
                                            function pinComment($commentID) {
                                                //UPDATING DATABASE
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.open("GET", "comments/pinComment.php?postid=<?php echo $postID ?>" + "&commentid=" + $commentID, true); //pin comment
                                                xhttp.send();
                                                xhttp.onload = () => {
                                                    $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=1')
                                                }
                                            }
                                            //unpin comment script
                                            function unpinComment($commentID) {
                                                //UPDATING DATABASE
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.open("GET", "comments/unpinComment.php?postid=<?php echo $postID ?>" + "&commentid=" + $commentID, true); //pin comment
                                                xhttp.send();
                                                xhttp.onload = () => {
                                                    $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=1')
                                                }
                                            }
                                        </script>
                                    <?php
                                    } else {
                                    ?>
                                        <a class="dropdown-item" href="#">Report</a>
                                        <?php
                                        if ($row['commentPinned'] == 1 && $userID == $authorID) { //if comment is already pinned
                                        ?>
                                            <button type="button" class="dropdown-item btn" onclick="unpinComment(<?php echo $row['commentID'] ?>)">Unpin</button>
                                        <?php
                                        } else if ($userID == $authorID) { //otherwise
                                        ?>
                                            <button type="button" class="dropdown-item btn" onclick="pinComment(<?php echo $row['commentID'] ?>)">Pin</button>
                                        <?php
                                        }
                                        ?>
                                        <script>
                                            //pin comment script
                                            function pinComment($commentID) {
                                                //UPDATING DATABASE
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.open("GET", "comments/pinComment.php?postid=<?php echo $postID ?>" + "&commentid=" + $commentID, true); //pin comment
                                                xhttp.send();
                                                xhttp.onload = () => {
                                                    $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=1')
                                                }
                                            }
                                            //unpin comment script
                                            function unpinComment($commentID) {
                                                //UPDATING DATABASE
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.open("GET", "comments/unpinComment.php?postid=<?php echo $postID ?>" + "&commentid=" + $commentID, true); //pin comment
                                                xhttp.send();
                                                xhttp.onload = () => {
                                                    $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=1')
                                                }
                                            }
                                        </script>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div id="viewCommentContainer<?php echo $row['commentID'] ?>" class="d-flex flex-column me-1" style="border-radius: 0.375rem;">
                                <span id="commentValue<?php echo $row['commentID'] ?>">
                                    <?php echo $row['commentValue'] ?>
                                </span>
                                <div id="editCommentFooter<?php echo $row['commentID'] ?>" class="p-1 text-end" style="height: 40px; background-color: #F5F5F5; border: 1px solid #dee2e6; border-radius: 0 0 0.375rem 0.375rem; border-top: none;" hidden>
                                    <button id="cancelBtn<?php echo $row['commentID'] ?>" type="button" class="btn px-4 btn-secondary" style="border-radius: 1.25rem !important; font-size: smaller;" onclick="cancelEdit(<?php echo $row['commentID'] ?>)" hidden>Cancel</button>
                                    <script>
                                        //cancel edit comment script
                                        function cancelEdit($commentID) {
                                            $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=1')
                                        }
                                    </script>
                                    <button id="doneBtn<?php echo $row['commentID'] ?>" type="button" class="btn px-4 btn-primary" style="border-radius: 1.25rem !important; font-size: smaller;" onclick="confirmEdit(<?php echo $row['commentID'] ?>)" hidden>Done</button>
                                    <script>
                                        //confirm edit comment script
                                        function confirmEdit(commentID) {
                                            var commentValue = $("#editCommentTextarea" + commentID).val().trim().replace(/ /g, "_");
                                            //UPDATING DATABASE
                                            var xhttp = new XMLHttpRequest();
                                            xhttp.open("GET", "comments/editComment.php?commentid=" + commentID + "&commentvalue=" + commentValue, true); //confirm edit
                                            xhttp.send();
                                            xhttp.onload = () => {
                                                $('#commentsContainer').load('comments/displayComments.php?postID=<?php echo $postID ?>&authorID=<?php echo $authorID ?>&condition=2')
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                            <div class="pt-2 small">
                                <!-- Like Forum Button -->
                                <?php
                                if ($liked == true) {
                                ?>
                                    <i id="likeComment<?php echo $row['commentID'] ?>" class="forum__like__btn bi bi-hand-thumbs-up-fill fs-5" style="color: var(--clr-green); cursor: pointer"></i>
                                <?php
                                } else if ($liked == false) {
                                ?>
                                    <i id="likeComment<?php echo $row['commentID'] ?>" class="forum__like__btn bi bi-hand-thumbs-up fs-5" style="color: var(--clr-green); cursor: pointer"></i>
                                <?php
                                }
                                ?>
                                <!-- LDR Display -->
                                <?php
                                if ($LDR >= 0) {
                                    echo '<span id="LDRComment' . $row['commentID'] . '" class="py-1 mx-1" style="color: var(--clr-green)"> ' . $LDR . ' </span>'; //positive LDR
                                } else {
                                    echo '<span id="LDRComment' . $row['commentID'] . '" class="py-1 mx-1" style="color: var(--clr-red)"> ' . $LDR . ' </span>'; //negative LDR
                                }
                                ?>
                                <!-- Dislike Forum Button -->
                                <?php
                                if ($disliked == true) {
                                ?>
                                    <i id="dislikeComment<?php echo $row['commentID'] ?>" class="forum__dislike__btn bi bi-hand-thumbs-down-fill fs-5" style="color: var(--clr-red); cursor: pointer"></i>
                                <?php
                                } else if ($disliked == false) {
                                ?>
                                    <i id="dislikeComment<?php echo $row['commentID'] ?>" class="forum__dislike__btn bi bi-hand-thumbs-down fs-5" style="color: var(--clr-red); cursor: pointer"></i>
                                <?php
                                }
                                ?>
                            </div>
                            <script>
                                $('#likeComment<?php echo $row['commentID'] ?>').click(function() { //liked button clicked
                                    if (this.classList.contains("bi-hand-thumbs-up")) {
                                        //POST LIKED
                                        this.classList.remove("bi-hand-thumbs-up") //remove unfilled hand
                                        this.classList.add("bi-hand-thumbs-up-fill") //add filled hand
                                        if (document.getElementById("dislikeComment<?php echo $row['commentID'] ?>").classList.contains("bi-hand-thumbs-down-fill")) { //DISLIKE TO LIKE
                                            document.getElementById("dislikeComment<?php echo $row['commentID'] ?>").classList.add("bi-hand-thumbs-down") //add unfilled hand
                                            document.getElementById("dislikeComment<?php echo $row['commentID'] ?>").classList.remove("bi-hand-thumbs-down-fill") //remove filled hand
                                            //UPDATING DATABASE
                                            var xhttp = new XMLHttpRequest();
                                            xhttp.open("GET", "comments/undislikeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //undislike comment
                                            xhttp.send();
                                            xhttp.onload = () => {
                                                $("#LDRComment<?php echo $row['commentID'] ?>").html(parseInt($("#LDRComment<?php echo $row['commentID'] ?>").html()) + 2); //LDR updated on page
                                                if ($("#LDRComment<?php echo $row['commentID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                                    $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-green)")
                                                } else {
                                                    $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-red)")
                                                }
                                                //UPDATING DATABASE
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.open("GET", "comments/likeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //like comment
                                                xhttp.send();
                                            }
                                        } else { //NULL TO LIKE
                                            $("#LDRComment<?php echo $row['commentID'] ?>").html(parseInt($("#LDRComment<?php echo $row['commentID'] ?>").html()) + 1); //LDR updated on page
                                            if ($("#LDRComment<?php echo $row['commentID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-green)")
                                            } else {
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-red)")
                                            }
                                            //UPDATING DATABASE
                                            var xhttp = new XMLHttpRequest();
                                            xhttp.open("GET", "comments/likeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //like comment
                                            xhttp.send();
                                        }
                                    } else {
                                        //POST UNLIKED
                                        this.classList.add("bi-hand-thumbs-up") //add unfilled hand
                                        this.classList.remove("bi-hand-thumbs-up-fill") //remove filled hand
                                        //UPDATING DATABASE
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("GET", "comments/unlikeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //unlike comment
                                        xhttp.send();
                                        xhttp.onload = () => {
                                            $("#LDRComment<?php echo $row['commentID'] ?>").html(parseInt($("#LDRComment<?php echo $row['commentID'] ?>").html()) - 1); //LDR updated on page
                                            if ($("#LDRComment<?php echo $row['commentID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-green)")
                                            } else {
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-red)")
                                            }
                                        }
                                    }
                                });
                                $('#dislikeComment<?php echo $row['commentID'] ?>').click(function() { //dislike button clicked
                                    if (this.classList.contains("bi-hand-thumbs-down")) {
                                        //POST DISLIKED
                                        this.classList.remove("bi-hand-thumbs-down") //remove unfilled hand
                                        this.classList.add("bi-hand-thumbs-down-fill") //add filled hand
                                        if (document.getElementById("likeComment<?php echo $row['commentID'] ?>").classList.contains("bi-hand-thumbs-up-fill")) { //LIKE TO DISLIKE
                                            document.getElementById("likeComment<?php echo $row['commentID'] ?>").classList.add("bi-hand-thumbs-up") //add unfilled hand
                                            document.getElementById("likeComment<?php echo $row['commentID'] ?>").classList.remove("bi-hand-thumbs-up-fill") //remove filled hand
                                            //UPDATING DATABASE
                                            var xhttp = new XMLHttpRequest();
                                            xhttp.open("GET", "comments/unlikeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //unlike comment
                                            xhttp.send();
                                            xhttp.onload = () => {
                                                $("#LDRComment<?php echo $row['commentID'] ?>").html(parseInt($("#LDRComment<?php echo $row['commentID'] ?>").html()) - 2); //LDR updated on page
                                                if ($("#LDRComment<?php echo $row['commentID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                                    $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-green)")
                                                } else {
                                                    $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-red)")
                                                }
                                                //UPDATING DATABASE
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.open("GET", "comments/dislikeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //dislike comment
                                                xhttp.send();
                                            }
                                        } else { //NULL TO DISLIKE
                                            $("#LDRComment<?php echo $row['commentID'] ?>").html(parseInt($("#LDRComment<?php echo $row['commentID'] ?>").html()) - 1); //LDR updated on page
                                            if ($("#LDRComment<?php echo $row['commentID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-green)")
                                            } else {
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-red)")
                                            }
                                            //UPDATING DATABASE
                                            var xhttp = new XMLHttpRequest();
                                            xhttp.open("GET", "comments/dislikeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //dislike comment
                                            xhttp.send();
                                        }
                                    } else {
                                        //POST UNDISLIKED
                                        this.classList.add("bi-hand-thumbs-down") //add unfilled hand
                                        this.classList.remove("bi-hand-thumbs-down-fill") //remove filled hand
                                        //UPDATING DATABASE
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("GET", "comments/undislikeComment.php?commentid=<?php echo $row['commentID'] ?>", true); //undislike comment
                                        xhttp.send();
                                        xhttp.onload = () => {
                                            $("#LDRComment<?php echo $row['commentID'] ?>").html(parseInt($("#LDRComment<?php echo $row['commentID'] ?>").html()) + 1); //LDR updated on page
                                            if ($("#LDRComment<?php echo $row['commentID'] ?>").html() >= 0) { //making sure colours are correct for LDR
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-green)")
                                            } else {
                                                $("#LDRComment<?php echo $row['commentID'] ?>").css("color", "var(--clr-red)")
                                            }
                                        }
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <div id="emptyComments" class="flex-column justify-content-center align-items-center p-5" style="display: none !important">
        <i class="bi bi-chat-right" style="font-size: 125px;"></i>
        <span class="text-center">No comments found.</span>
        <span class="text-center" style="font-weight: 300;">Be the first one to comment.</span>
    </div>
    </main>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex flex-column p-2">
                        <span id="deleteCommentID" hidden></span>
                        <span class="h3">Delete comment</span>
                        <span class="my-2">Delete your comment permanently?</span>
                    </div>
                    <div class="d-flex justify-content-end p-2 gap-3">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="color: var(--clr-blue)">Close</button>
                        <button type="button" class="btn" data-bs-dismiss="modal" style="color: var(--clr-blue)" onclick="deleteComment($('#deleteCommentID').html())">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var recipient = button.data('bs-whatever')
            var modal = $(this)
            modal.find('#deleteCommentID').text(recipient)
        })
    </script>

    <?php
    function DATFrameCalculation($publishDate)
    {
        $todaysDateAndTime = date('Y-m-d H:i:s');
        $start_datetime = new DateTime($todaysDateAndTime);
        $diff = $start_datetime->diff(new DateTime($publishDate));
        $total_minutes = ($diff->days * 24 * 60);
        $total_minutes += ($diff->h * 60);
        $total_minutes += $diff->i;
        if ($total_minutes < 1) { //if less than a minute ago
            if ($diff->s == 1) { //1 second ago {
                return "1 second ago";
            } else { //otherwise
                return $diff->s . " seconds ago";
            }
        } else if ($total_minutes < 60) { //if between 1 and 60 minutes
            if ($total_minutes == 1) { //1 minute ago
                return "1 minute ago";
            } else { //otherwise
                return $total_minutes . " minutes ago";
            }
        } else if ($total_minutes < 1440) { //if between 1 and 24 hours 
            if ($total_minutes < 120) { //one hour ago
                return "1 hour ago";
            } else { //otherwise
                return $diff->h . " hours ago";
            }
        }
        if ($diff->days == 1) { //one day ago
            return "yesterday";
        } else if ($diff->m == 0 && $diff->y == 0) { //up to a month
            return $diff->days . " days ago";
        }
        if ($diff->m == 1 && $diff->y == 0) { //one month ago
            return "1 month ago";
        } else if ($diff->y == 0) { //up to a year
            return $diff->m . " months ago";
        }
        if ($diff->y == 1) { //1 year ago
            return "1 year ago";
        } else {
            return $diff->y . " years ago";
        }
    }
    ?>

    <?php

    include_once('../includes/footer.inc.php');

    ?>