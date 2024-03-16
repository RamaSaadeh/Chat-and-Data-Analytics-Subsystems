<?php
session_start();
$postID = $_GET['postID'];
$authorID = $_GET['authorID'];
$userID = $_SESSION['userid'];
$condition = $_GET['condition'];
?>

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
require_once("../../includes/dbh.inc.php");

if ($condition == 1) { //Top comments
    $sql = "SELECT commentDetails.*,  userDetails.userName, userDetails.userSurname, userDetails.userProfilePicture, IF (LDR IS NULL, 0, LDR) AS LDR
    FROM commentDetails 
    LEFT JOIN userDetails ON userDetails.userID = commentUserID 
    LEFT JOIN (SELECT commentLikeOrDislikeDetails.commentID as commentID, SUM(commentLikeOrDislikeDetails.commentLOD) as LDR
    FROM commentLikeOrDislikeDetails 
    GROUP BY commentLikeOrDislikeDetails.commentID) AS Q ON Q.commentID = commentDetails.commentID  WHERE commentDetails.postID = $postID 
    ORDER BY commentDetails.commentPinned DESC, LDR DESC;";
} else if ($condition == 2) { //Newest comments
    $sql = "SELECT commentDetails.*,  userDetails.userName, userDetails.userSurname, userDetails.userProfilePicture, IF (LDR IS NULL, 0, LDR) AS LDR 
    FROM commentDetails 
    LEFT JOIN userDetails ON userDetails.userID = commentUserID 
    LEFT JOIN (SELECT commentLikeOrDislikeDetails.commentID as commentID, SUM(commentLikeOrDislikeDetails.commentLOD) as LDR
    FROM commentLikeOrDislikeDetails 
    GROUP BY commentLikeOrDislikeDetails.commentID) AS Q ON Q.commentID = commentDetails.commentID  WHERE commentDetails.postID = $postID 
    ORDER BY commentDetails.commentPinned DESC, commentDateTime DESC;";
}
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