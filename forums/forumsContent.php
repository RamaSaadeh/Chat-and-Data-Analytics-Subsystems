<?php
session_start();
$userID = $_SESSION["userid"];
$condition = $_GET["condition"];
require_once("../includes/dbh.inc.php");
if ($condition == 1) {
    $sql = "SELECT 
        posts.*, userDetails.userProfilePicture, userDetails.userName, userDetails.userSurname, 
        LDR,
        FORMAT((LDR/TIMESTAMPDIFF(SECOND, posts.postDateTime, NOW())), 10) AS trendingValue,
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
        WHERE posts.postArchived = 0
        GROUP BY posts.postID  
        ORDER BY trendingValue  DESC;";
} else if ($condition == 2) {
    $sql = "SELECT 
        posts.*, userDetails.userProfilePicture, userDetails.userName, userDetails.userSurname, 
        LDR, numberOfComments 
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
        WHERE posts.postArchived = 0
        GROUP BY posts.postID  
        ORDER BY `postDateTime`  DESC;";
} else if ($condition == 3) {
    $sql = "SELECT 
        posts.*, userDetails.userProfilePicture, userDetails.userName, userDetails.userSurname, 
        LDR, numberOfComments
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
        WHERE posts.postArchived = 0
        GROUP BY posts.postID  
        ORDER BY `LDR`  DESC;";
} else if ($condition == 4) {
    $sql = "SELECT 
    posts.*, userDetails.userProfilePicture, userDetails.userName, userDetails.userSurname, 
    LDR, numberOfComments
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
        INNER JOIN postFavouriteDetails ON posts.postID = postFavouriteDetails.postID AND postFavouriteDetails.userID = $userID
        WHERE posts.postArchived = 0
        GROUP BY posts.postID";
} else if ($condition == 5) {
    $sql = "SELECT 
        posts.*, userDetails.userProfilePicture, userDetails.userName, userDetails.userSurname, 
        LDR, numberOfComments
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
        WHERE posts.authorID = $userID AND posts.postArchived = 0
        GROUP BY posts.postID;";
?>
    <span class="display-6 text-center d-block">Live</span>
<?php
} else if ($condition == 6) {
    $sql = "SELECT 
        posts.*, userDetails.userProfilePicture, userDetails.userName, userDetails.userSurname, 
        LDR, numberOfComments
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
        WHERE posts.authorID = $userID AND posts.postArchived = 1
        GROUP BY posts.postID;";
?>
    <span class="display-6 text-center d-block">Archived</span>
<?php
}
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
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
?>
    <div class="forum__container shadow rounded d-flex my-3 m-0" onclick="if (!$(event.target).hasClass('bi') && !$(event.target).hasClass('btn') && !$(event.target).hasClass('forumTag') && !$(event.target).hasClass('dropdown-item') && !$(event.target).hasClass('LOD'))window.location.replace('forums/focusForum.php?postid=<?php echo $row['postID'] ?>')">
        <div class="LOD d-flex flex-column align-items-center p-3 d-inline-block justify-content-center" style="width: fit-content; background-color: #FAFAFA; box-shadow: 1px 0px 5px 1px rgba(0, 0, 0, 0.25); border-radius: 0.375rem 0 0 0.375rem;">
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
                        xhttp.open("GET", "forums/undislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
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
                            xhttp.open("GET", "forums/likeForum.php?postid=<?php echo $row['postID'] ?>", true); //like post
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
                        xhttp.open("GET", "forums/likeForum.php?postid=<?php echo $row['postID'] ?>", true); //like post
                        xhttp.send();
                    }
                } else {
                    //POST UNLIKED
                    this.classList.add("bi-hand-thumbs-up") //add unfilled hand
                    this.classList.remove("bi-hand-thumbs-up-fill") //remove filled hand
                    //UPDATING DATABASE
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("GET", "forums/unlikeForum.php?postid=<?php echo $row['postID'] ?>", true); //unlike post
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
                        xhttp.open("GET", "forums/unlikeForum.php?postid=<?php echo $row['postID'] ?>", true); //unlike post
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
                            xhttp.open("GET", "forums/dislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //dislike post
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
                        xhttp.open("GET", "forums/dislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //dislike post
                        xhttp.send();
                    }
                } else {
                    //POST UNDISLIKED
                    this.classList.add("bi-hand-thumbs-down") //add unfilled hand
                    this.classList.remove("bi-hand-thumbs-down-fill") //remove filled hand
                    //UPDATING DATABASE
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("GET", "forums/undislikeForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
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






        <div class="forum__content d-inline-block py-3 pe-3 w-100" style="padding-left: calc(0.5rem + 5vw); word-break: break-word;">
            <div class="forumHeader d-flex justify-content-between align-items-center gap-1"> <!--forum header-->
                <div>
                    <img id="" height="25" width="25" style="border-radius: 50%;" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>" onerror="this.onerror=null; this.src='account/logo.png'" />
                    <span class="published__details ms-1">
                        <?php echo $row['userName'] . " " . $row['userSurname'] . " | " . DATFrameCalculation($row['postDateTime']) ?>
                    </span>
                </div>
                <button id="moreBtn<?php echo $row['postID'] ?>" type="button" class="btn p-0 m-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="justify-self: start; align-self: flex-start;"><i class="moreBtn bi bi-three-dots-vertical p-1"></i></button>
                <div class="dropdown-menu mt-1" aria-labelledby="dropdownMenuButton">
                    <?php
                    if ($row['authorID'] == $userID) {
                    ?>
                        <button type="button" class="dropdown-item btn" data-bs-toggle="modal" data-bs-target="#editPostModal" data-bs-whatever="<?php echo $row['postID'] ?>" data-bs-title="<?php echo $row['postName'] ?>" data-bs-desc="<?php echo $row['postDescription'] ?>" data-bs-tags="<?php echo $row['postTags'] ?>">Edit</button>
                        <button type="button" class="dropdown-item btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-bs-whatever="<?php echo $row['postID'] ?>">Delete</button>
                        <script>
                            //delete post script
                            function deletePost($postID) {
                                //UPDATING DATABASE
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("GET", "forums/deletePost.php?postid=" + $postID, true); //unlike post
                                xhttp.send();
                                xhttp.onload = () => {
                                    $('#forumContent').load('forums/forumsContent.php?condition=' + sectionBeingViewed());
                                }
                            }
                        </script>
                        <?php
                        if ($condition != 6) {
                        ?>
                            <button type="button" class="dropdown-item btn" onclick="archivePost('<?php echo $row['postID'] ?>')">Archive</button>
                        <?php
                        } else {
                        ?>
                            <button type="button" class="dropdown-item btn" onclick="unarchivePost('<?php echo $row['postID'] ?>')">Unarchive</button>
                        <?php
                        }
                        ?>
                        <script>
                            //archive post script
                            function archivePost($postID) {
                                //UPDATING DATABASE
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("GET", "forums/archivePost.php?postid=" + $postID, true); //unlike post
                                xhttp.send();
                                xhttp.onload = () => {
                                    $('#forumContent').load('forums/forumsContent.php?condition=' + sectionBeingViewed());
                                }
                            }

                            //unarchive post script
                            function unarchivePost($postID) {
                                //UPDATING DATABASE
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("GET", "forums/unarchivePost.php?postid=" + $postID, true); //unlike post
                                xhttp.send();
                                xhttp.onload = () => {
                                    $('#forumContent').load('forums/forumsContent.php?condition=6');
                                }
                            }
                        </script>
                    <?php
                    } else {
                    ?>
                        <a class="dropdown-item" href="#">Report</a>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <!--forum title-->
            <div class="forumTitle h5 py-2">
                <?php echo $row['postName'] ?>
            </div>
            <!--forum desc-->
            <div class="forumDescription py-2">
                <?php echo $row['postDescription'] ?>
            </div>
            <!--forum tags-->
            <div class="forumTags py-2 small">
                <?php
                $tagArray = explode(",", $row['postTags']);
                if ($tagArray[0] != "") { //if a tag exists
                    for ($i = 0; $i <= count($tagArray) - 1; $i++) { //loop for length of tags
                        if (count($tagArray) == 0) { //if there is only one tag
                            echo "<span class='forumTag' onclick='searchForTag(" . '"'  . $tagArray[$i] . '"' . ")'>#" . $tagArray[$i] . "</span>";
                            break;
                        } else { //otherwise
                            echo " ";
                            echo "<span class='forumTag' onclick='searchForTag(" . '"'  . $tagArray[$i] . '"' . ")'>#" . $tagArray[$i] . "</span>";
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
                            xhttp.open("GET", "forums/favouriteForum.php?postid=<?php echo $row['postID'] ?>", true); //favourite post
                            xhttp.send();
                        } else if (document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.contains("bi-star-fill")) { //already favourited
                            document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.add("bi-star") //add unfilled star
                            document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.remove("bi-star-fill") //remove filled star
                            //UPDATING DATABASE
                            var xhttp = new XMLHttpRequest();
                            xhttp.open("GET", "forums/unfavouriteForum.php?postid=<?php echo $row['postID'] ?>", true); //unfavourite post
                            xhttp.send();
                            xhttp.onload = () => {
                                $('#forumContent').load('forums/forumsContent.php?condition=' + sectionBeingViewed());
                            }
                        }
                    })
                </script>
                <?php
                if ($row['numberOfComments'] == "") { //no comments
                ?>
                    <button id="commentsPost<?php echo $row['postID'] ?>" type="button" class="comments__btn btn p-0 ms-2" onclick="window.location.replace('forums/focusForum.php?postid=<?php echo $row['postID'] ?>')"><i class="bi bi-chat-right-text"></i> 0 Comments</button>
                <?php
                } else if ($row['numberOfComments'] == 1) { //1 comment
                ?>
                    <button id="commentsPost<?php echo $row['postID'] ?>" type="button" class="comments__btn btn p-0 ms-2" onclick="window.location.replace('forums/focusForum.php?postid=<?php echo $row['postID'] ?>')"><i class="bi bi-chat-right-text"></i> 1 Comment</button>
                <?php
                } else { //otherwise
                ?>
                    <button id="commentsPost<?php echo $row['postID'] ?>" type="button" class="comments__btn btn p-0 ms-2" onclick="window.location.replace('forums/focusForum.php?postid=<?php echo $row['postID'] ?>')"><i class="bi bi-chat-right-text"></i>
                        <?php echo $row['numberOfComments'] ?> Comments
                    </button>
                <?php
                }


                ?>
            </div>
        </div>
    </div>
<?php }
?>
<div id="emptySearch" class="flex-column justify-content-center align-items-center p-5" style="display: none !important">
    <i class="bi bi-search" style="font-size: 125px;"></i>
    <span class="text-center">No search results found.</span>
    <span class="text-center" style="font-weight: 300;">Make sure words are spelled correctly. Use less specific or different keywords.</span>
</div>


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


<script>
    //get value of search bar
    var input = $('#forum__search__input').val().toLocaleLowerCase();
    //setting variable for seeing if search returns a value
    var empty = true;
    $('.forumTitle').each(function() {
        var forumTitle = $(this).html().trim();
        if (forumTitle.toLocaleLowerCase().indexOf(input) >= 0) {
            $(this).parent().parent().attr('style', 'display: flex !important')
            empty = false;
        } else {
            $(this).parent().parent().attr('style', 'display: none !important')
        }
    })

    $('.forumTag').each(function() {
        var forumTag = $(this).html().trim();
        forumTag = forumTag.slice(1)
        if (forumTag.toLocaleLowerCase().indexOf(input) >= 0) {
            $(this).parent().parent().parent().attr('style', 'display: flex !important')
            empty = false;
        }
    })

    if (empty == true) {
        $("#emptySearch").attr('style', 'display: flex !important')
    } else {
        $("#emptySearch").attr('style', 'display: none !important')
    }

    function sectionBeingViewed() {
        if (document.getElementById('trendingButton').classList.contains('btn-primary')) {
            return 1
        } else if (document.getElementById('newButton').classList.contains('btn-primary')) {
            return 2
        } else if (document.getElementById('topButton').classList.contains('btn-primary')) {
            return 3
        } else if (document.getElementById('favouriteButton').classList.contains('btn-primary')) {
            return 4
        }
    }

    function searchForTag(tagName) {
            $('#forum__search__input').val(tagName);
            $('#forum__search__input').trigger('input')  
    }
</script>