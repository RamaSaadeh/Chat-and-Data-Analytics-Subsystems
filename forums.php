<?php
session_start(); //start session
$page = "forums";
$title = "Forums";
$_SESSION["title"] = $title;
$currentPage = "Forums";
if (!isset($_SESSION["valid"])) { //if session isn't valid take back to login page
    header("Location: login.php");
    exit();
} else if ($_SESSION["role"]) { //if user is a member include member header
    include_once('includes/header.inc.php');
}
?>
<!--linking to account css stylesheet-->
<link rel="stylesheet" href="forums/styles/forumsDisplay.css">

<script>
    removeBackgrounds();
    setBackgrounds(2);
</script>

<main class="container mw-75 p-3">
    <div class="shadow rounded d-flex my-3 m-0 p-3 gap-3 align-items-center" style="z-index: 10; position: sticky; top: 0; background: white;">
        <?php
        $userID = $_SESSION["userid"];
        require_once("includes/dbh.inc.php");
        $sql = "SELECT userProfilePicture FROM userDetails WHERE userID = $userID"; //sql 
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) { ?>
            <img height="35" width="35" style="border-radius: 50%;" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['userProfilePicture']); ?>" onerror="this.onerror=null; this.src='account/logo.png'" />
        <?php
        }
        ?>
        <div class="form-group d-flex" style="flex: 1 0 0;">
            <input id="forum__search__input" type="text" class="form-control p-2" placeholder="Start typing to search or create a post...">
        </div>
        <button type="button" class="btn btn-primary" style="font-size: 0.85rem" data-bs-toggle="modal" data-bs-target="#createForumModal">Create Post</button>
    </div>

    <div class="shadow rounded d-flex flex-md-row flex-column my-3 m-0 p-2 d-flex justify-content-between">
        <div class="d-flex justify-content-between">
            <button id="trendingButton" type="button" class="btn btn-primary" onclick="$('#forumContent').load('forums/forumsContent.php?condition=1'); this.classList.add('btn-primary'); document.getElementById('newButton').classList.remove('btn-primary'); document.getElementById('topButton').classList.remove('btn-primary'); document.getElementById('favouriteButton').classList.remove('btn-primary'); document.getElementById('myPostsButton').classList.remove('btn-primary');"><i class="bi bi-fire"></i> Trending</button>
            <button id="newButton" type="button" class="btn mx-2" onclick="$('#forumContent').load('forums/forumsContent.php?condition=2'); this.classList.add('btn-primary'); document.getElementById('trendingButton').classList.remove('btn-primary'); document.getElementById('topButton').classList.remove('btn-primary'); document.getElementById('favouriteButton').classList.remove('btn-primary'); document.getElementById('myPostsButton').classList.remove('btn-primary');"><i class="bi bi-stars"></i> New</button>
            <button id="topButton" type="button" class="btn" onclick="$('#forumContent').load('forums/forumsContent.php?condition=3'); this.classList.add('btn-primary'); document.getElementById('trendingButton').classList.remove('btn-primary'); document.getElementById('newButton').classList.remove('btn-primary'); document.getElementById('favouriteButton').classList.remove('btn-primary'); document.getElementById('myPostsButton').classList.remove('btn-primary');"><i class=" bi bi-trophy"></i> Top</button>
            <button id="favouriteButton" type="button" class="btn" onclick="$('#forumContent').load('forums/forumsContent.php?condition=4'); this.classList.add('btn-primary'); document.getElementById('trendingButton').classList.remove('btn-primary'); document.getElementById('newButton').classList.remove('btn-primary'); document.getElementById('topButton').classList.remove('btn-primary'); document.getElementById('myPostsButton').classList.remove('btn-primary');"><i class="bi bi-star-fill"></i> Favourites</button>
        </div>
        <button id="myPostsButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn"><i class="bi bi-person-fill"></i> My Posts</button>
        <div class="dropdown-menu mt-1" aria-labelledby="dropdownMenuButton">
            <button type="button" class="dropdown-item btn" onclick="$('#forumContent').load('forums/forumsContent.php?condition=5'); document.getElementById('myPostsButton').classList.add('btn-primary'); document.getElementById('trendingButton').classList.remove('btn-primary'); document.getElementById('newButton').classList.remove('btn-primary'); document.getElementById('topButton').classList.remove('btn-primary'); document.getElementById('favouriteButton').classList.remove('btn-primary');">Live</button>
            <button type="button" class="dropdown-item btn" onclick="$('#forumContent').load('forums/forumsContent.php?condition=6'); document.getElementById('myPostsButton').classList.add('btn-primary'); document.getElementById('trendingButton').classList.remove('btn-primary'); document.getElementById('newButton').classList.remove('btn-primary'); document.getElementById('topButton').classList.remove('btn-primary'); document.getElementById('favouriteButton').classList.remove('btn-primary');">Archived</button>
        </div>
    </div>


    <div id="forumContent">

        <?php
        require_once("includes/dbh.inc.php");
        //sql getting all forum posts
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
        ORDER BY `trendingValue` DESC;";


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
                    <div class=" forumHeader d-flex justify-content-between align-items-center gap-1"> <!--forum header-->
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
                                <button type="button" class="dropdown-item btn" onclick="archivePost('<?php echo $row['postID'] ?>')">Archive</button>
                                <script>
                                    //archive post script
                                    function archivePost($postID) {
                                        //UPDATING DATABASE
                                        var xhttp = new XMLHttpRequest();
                                        xhttp.open("GET", "forums/archivePost.php?postid=" + $postID, true); //archive post
                                        xhttp.send();
                                        xhttp.onload = () => {
                                            $('#forumContent').load('forums/forumsContent.php?condition=' + sectionBeingViewed());
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
                                    echo "<span class='forumTag' onclick='searchForTag(" . '"' . $tagArray[$i] . '"' . ")'>#" . $tagArray[$i] . "</span>";
                                    break;
                                } else { //otherwise
                                    echo " ";
                                    echo "<span class='forumTag' onclick='searchForTag(" . '"' . $tagArray[$i] . '"' . ")'>#" . $tagArray[$i] . "</span>";
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
                                    xhttp.open("GET", "forums/favouriteForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
                                    xhttp.send();
                                    xhttp.onload = () => {

                                    }
                                } else if (document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.contains("bi-star-fill")) { //already favourited
                                    document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.add("bi-star") //add unfilled star
                                    document.getElementById("favouriteIcon<?php echo $row['postID'] ?>").classList.remove("bi-star-fill") //remove filled star
                                    //UPDATING DATABASE
                                    var xhttp = new XMLHttpRequest();
                                    xhttp.open("GET", "forums/unfavouriteForum.php?postid=<?php echo $row['postID'] ?>", true); //undislike post
                                    xhttp.send();
                                    xhttp.onload = () => {

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
        <?php
        }
        ?>

        <div id="emptySearch" class="d-none flex-column justify-content-center align-items-center p-5">
            <i class="bi bi-search" style="font-size: 125px;"></i>
            <span class="text-center">No search results found.</span>
            <span class="text-center" style="font-weight: 300;">Make sure words are spelled correctly. Use less specific
                or different keywords.</span>
        </div>
    </div>

    <div class="modal fade" id="createForumModal" tabindex="-1" aria-labelledby="createForumModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <span class="h1">Create post</span>
                    <div class="titleInput py-2" style="position: relative;">
                        <input type="text" id="newForumTitle" class="form-control" placeholder="Title" maxlength="200" style="padding-right: 4.5rem;" autocomplete="off"></input>
                        <span id="newForumTitleCharacterCount" style="position: absolute; right:5px; top:50%; translate: 0 -50%"></span>
                    </div>
                    <div class="descriptionInput py-2">
                        <textarea id="newForumDesc" class="form-control" rows="6" placeholder="Description (optional)"></textarea>
                    </div>
                    <div class="tagInput py-2">
                        <textarea id="tagInputTextarea" class="form-control" rows="3" placeholder="Tags (optional)"></textarea>
                    </div>
                    <div class="footer py-2 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button id="postButton" type="button" class="btn btn-primary" onclick="addForum(); " data-bs-dismiss="modal" disabled>Post</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <span id="editPostID" hidden></span>
                    <span class="h1">Edit post</span>
                    <div class="titleInput py-2" style="position: relative;">
                        <input type="text" id="editPostTitle" class="form-control" placeholder="Title" maxlength="200" style="padding-right: 4.5rem;" autocomplete="off"></input>
                        <span id="editForumTitleCharacterCount" style="position: absolute; right:5px; top:50%; translate: 0 -50%"></span>
                    </div>
                    <div class="descriptionInput py-2">
                        <textarea id="editPostDesc" class="form-control" rows="6" placeholder="Description (optional)"></textarea>
                    </div>
                    <div class="tagInput py-2">
                        <textarea id="editPostTags" class="form-control" rows="3" placeholder="Tags (optional)"></textarea>
                    </div>
                    <div class="footer py-2 text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button id="editPostButton" type="button" class="btn btn-primary" onclick="updateForum($('#editPostID').html())" data-bs-dismiss="modal">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex flex-column p-2">
                        <span id="deletePostID" hidden></span>
                        <span class="h3">Delete post</span>
                        <span class="my-2">Delete your post permanently?</span>
                    </div>
                    <div class="d-flex justify-content-end p-2 gap-3">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="color: var(--clr-blue)">Close</button>
                        <button type="button" class="btn" data-bs-dismiss="modal" style="color: var(--clr-blue)" onclick="deletePost($('#deletePostID').html())">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        //when page reloaded make sure you are at top of page
        if (history.scrollRestoration) {
            history.scrollRestoration = 'manual';
        } else {
            window.onbeforeunload = function() {
                window.scrollTo(0, 0);
            }
        }

        function addForum() {
            //UPDATING DATABASE
            var xhttp = new XMLHttpRequest();
            var postname = $('#newForumTitle').val().trim().replace(/ /g, "_")
            var postdesc = $('#newForumDesc').val().trim().replace(/ /g, "_")
            var posttags = ($('#tagInputTextarea').val().trim().replace(/ /g, "_")).replace(/#/g, '')
            xhttp.open("GET", "forums/addForum.php?postname=" + postname + "&postdesc=" + postdesc + "&posttags=" + posttags, true); //add post
            xhttp.send();
            xhttp.onload = () => {
                $("#newButton").trigger("click");
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth',
                })
                //reset add modal
                $("#newForumTitle").val('')
                $("#newForumDesc").val('')
                $("#tagInputTextarea").val('')
            }
        }


        function updateForum(postid) {
            //UPDATING DATABASE
            var xhttp = new XMLHttpRequest();
            var postname = $('#editPostTitle').val().trim().replace(/ /g, "_")
            var postdesc = $('#editPostDesc').val().trim().replace(/ /g, "_")
            var posttags = ($('#editPostTags').val().trim().replace(/ /g, "_")).replace(/#/g, '')
            xhttp.open("GET", "forums/updateForum.php?postid=" + postid + "&postname=" + postname + "&postdesc=" + postdesc + "&posttags=" + posttags, true); //add post
            xhttp.send();
            xhttp.onload = () => {
                $('#forumContent').load('forums/forumsContent.php?condition=' + sectionBeingViewed());
            }
        }

        $('#createForumModal').on('show.bs.modal', function() {
            if ($('#forum__search__input').val() != "") {
                $('#newForumTitle').val($('#forum__search__input').val()) //setting value of title
                if ($('#newForumTitle').val() != "") {
                    $('#postButton').prop('disabled', false);
                }
            }
            //get length of title
            var length = $('#newForumTitle').val().length;
            $('#newForumTitleCharacterCount').html(length + "/200")
        })

        $('#newForumTitle').on("input", function() {
            //get length of title
            var length = $('#newForumTitle').val().length;
            $('#newForumTitleCharacterCount').html(length + "/200")
            if ($('#newForumTitle').val() != "") {
                $('#postButton').prop('disabled', false);
            } else {
                $('#postButton').prop('disabled', true);
            }
        })

        $('#tagInputTextarea').focus(function() {
            //get value of textarea
            if (this.value == "") {
                this.value = "#"
            }
        })

        $('#tagInputTextarea').focusout(function() {
            //get value of textarea
            if (this.value == "#") {
                this.value = ""
            }
        })

        $('#tagInputTextarea').keypress(function(e) {
            //get length of textarea
            var length = this.value.length;
            //getting last character of textarea
            var lastcharacter = this.value.substr(length - 1);
            if (lastcharacter == "#") {
                //if keycode is a spacebar
                if (e.keyCode == 32) {
                    return false; //stop space
                }
            }
            //if keycode is a spacebar
            if (e.keyCode == 32) {
                this.value = this.value + " " + "#"
                return false; //stop space
            }

            //if keycode is #
            if (e.keyCode == 35) {
                return false;
            }

            if (this.value.charAt(e.target.selectionStart) == "#") {
                return false;
            }
        })

        $('#tagInputTextarea').keyup(function(e) {
            //get length of textarea
            var length = this.value.length;
            var lastcharacter = this.value.substr(length - 1);
            if (lastcharacter == " ") {
                this.value = this.value.substring(0, length - 1);
            }
            if (lastcharacter == "") {
                this.value = "#"
            }
        })

        $('#forum__search__input').on("input", function() {
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
        })

        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var recipient = button.data('bs-whatever')
            var modal = $(this)
            modal.find('#deletePostID').text(recipient)

        })
        $('#editPostModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var recipient = button.data('bs-whatever')
            var title = button.data('bs-title')
            var desc = button.data('bs-desc')
            var tags = button.data('bs-tags')
            if (tags != "") {
                tags = "#" + tags.replace(",", " #");
            }

            var modal = $(this)
            modal.find('#editPostID').text(recipient)
            modal.find('#editPostTitle').val(title)
            modal.find('#editPostDesc').text(desc)
            modal.find('#editPostTags').text(tags)
            //get length of title
            var length = title.length;
            $('#editForumTitleCharacterCount').html(length + "/200")
        })

        $('#editPostTags').focus(function() {
            //get value of textarea
            if (this.value == "") {
                this.value = "#"
            }
        })

        $('#editPostTags').focusout(function() {
            //get value of textarea
            if (this.value == "#") {
                this.value = ""
            }
        })

        $('#editPostTags').keypress(function(e) {
            //get length of textarea
            var length = this.value.length;
            //getting last character of textarea
            var lastcharacter = this.value.substr(length - 1);
            if (lastcharacter == "#") {
                //if keycode is a spacebar
                if (e.keyCode == 32) {
                    return false; //stop space
                }
            }
            //if keycode is a spacebar
            if (e.keyCode == 32) {
                this.value = this.value + " " + "#"
                return false; //stop space
            }
            //if keycode is #
            if (e.keyCode == 35) {
                return false;
            }

            if (this.value.charAt(e.target.selectionStart) == "#") {
                return false;
            }
        })

        $('#editPostTags').keyup(function(e) {
            //get length of textarea
            var length = this.value.length;
            var lastcharacter = this.value.substr(length - 1);
            if (lastcharacter == " ") {
                this.value = this.value.substring(0, length - 1);
            }
            if (lastcharacter == "") {
                this.value = "#"
            }
        })

        $('#editPostTitle').on("input", function() {
            //get length of title
            var length = $('#editPostTitle').val().length;
            $('#editForumTitleCharacterCount').html(length + "/200")
            if ($('#editPostTitle').val() != "") {
                $('#editPostButton').prop('disabled', false);
            } else {
                $('#editPostButton').prop('disabled', true);
            }
        })

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
</main>



<?php

include_once('includes/footer.inc.php');

?>