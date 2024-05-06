//access different pages
dashboard.addEventListener("click", function () {
	var details = sessionStorage.getItem("user");
	var role = JSON.parse(details).role;
	var a = document.getElementById("dashboard");
	switch (role) {
		case "a":
			a.href = "AdminDashboard.html";
			break;
		case "g":
			a.href = "userdash.html";
			break;
		case "m":
			a.href = "accessproject.php";
			break;
		case "l":
			a.href = "leader-dash.php";
			break;
		default:
			a.href = "#";
	}
});

var currentSearchTerm = '';
var currentTopic = 'all'; // signifies empty topic initially

var inviteForm = document.getElementById("inviteuseropaquebg");
	
function openForm() {
	inviteForm.style.display = "block";
}

//close invite form
function closeForm() {
	inviteForm.style.display = "none";
	document.getElementById("email").value = "";
	document.getElementById("emailError").style.display = "none";
}
	
//send invite form
function sendInvite() {
	var email = document.getElementById("email").value;
	var label = document.getElementById("emailError");
	if (email.slice(email.length - 18).toLowerCase() != "@make-it-all.co.uk") {
		label.innerHTML = "Email address is not valid";
		label.style.color = "red";
		label.style.display = "block";
	}
	else {
		label.innerHTML = "Invite sent!";
		label.style.color = "#2fe617";
		label.style.display = "block";
	}
}

//check user is logged in to an account on users table
function checkLogin() {
  try {
      var details = sessionStorage.getItem("user");
      var role = JSON.parse(details).role;
      switch (role) {
          case "a":
              break;
          case "g":
              break;
          case "m":
              break;
          case "l":
              break;
          default:
              window.location.replace("login.html");
              break;
      }
  }
  catch {
      window.location.replace("login.html");
  }
}

$(document).ready(function() {

  var details = sessionStorage.getItem("user");
  var userID = JSON.parse(details).id;
    //display dynamic posts from the database

    $.ajax({
        url: "fetch-posts.php",
        type: "GET",
        dataType: "json", 
        data: {userID: userID},
		success: function (posts) {
			if (posts.length == 0) {
				$('.all-content').append("<br><br><h3>There are currently no posts!</h3>");
			}
            posts.forEach(function(post) {
                var postClass = post.Topic.toLowerCase().replace(/\s+/g, '-');
                var postHTML = `
                    <div class="post ${postClass}" data-date-created="${post.DateCreated}">
                        <div class="media-body">
                            <h3 class="post-topic">${post.Topic}</h3>
                            <h2 class="post-title">${post.Title}</h2>
                            <p class="preview-text">${post.Content}</p>
                            <div class="post-read-more">
                                <a href="single-post.html?id=${post.PostID}" class="btn">More</a>
                            </div>
                        </div>
                        <div class="comment-metadata">
                            <div class="comment-user-date">
                                <i class="far fa-user">${post.AuthorName}</i> &nbsp;
                                <i class="far fa-calendar">${post.DisplayDate}</i>                              
                            </div>
                            <div class="comment-actions">
                                <i class="fas fa-thumbs-up like-comment ${post.IsLiked ? 'liked' : ''}" title="Like" data-post-id="${post.PostID}" data-liked="${post.IsLiked ? 'true' : 'false'}"></i>
                                <span class="like-count" data-post-id="${post.PostID}" data-likes="${post.LikesCount}">${post.LikesCount}</span>
                            </div>
                        </div>
                    </div>`;
                $('.all-content').append(postHTML);
            });
        },
        error: function(xhr, status, error) {
           
        }
    });

    
    //when user clicks
    $(document).on('click', '.topic-filter', function(e) {
        e.preventDefault(); 

        //select topic
        var selectedTopic = $(this).text();
       

        //highlight the selected topic
        $('.topic-filter').removeClass('active');
        $(this).addClass('active');

        //update currentTopic based on the selected topic
        currentTopic = selectedTopic === 'Show all topics' ? 'all' : selectedTopic.toLowerCase().replace(/\s+/g, '-');

        //call filterPosts to apply both the topic and search term filters
        filterPosts();
    });

    //function to filter posts by topic and search term
    function filterPosts() {
       
        var $allPosts = $('.all-content .post'); //target all posts

        $allPosts.each(function() {
            var $post = $(this);
            var title = $post.find('.post-title').text().toLowerCase();
            var postClass = $post.attr('class').split(/\s+/).find(function(cl) {
                return cl !== 'post';
            });

            //determine if the post matches the current search term
            var matchesSearch = !currentSearchTerm || title.startsWith(currentSearchTerm);

            //determine if the post matches the current topic
            var matchesTopic = currentTopic === 'all' || postClass === currentTopic;

            if (matchesSearch && matchesTopic) {
                $post.show();
            } else {
                $post.hide();
            }
        });
    };


//event listener for liking comment
$(document).on('click', '.like-comment', function() {
  const $likeButton = $(this);
  const postID = $likeButton.data('post-id');

  var details = sessionStorage.getItem("user");
  var userID = JSON.parse(details).id;

  //ajax call to update the number of likes (increment or decrement)
  $.ajax({
      url: "update-like.php",
      type: "POST",
      dataType: "json",
      data: { postID: postID, //pass post id
              userID: userID  //pass user id
      },
      success: function(response) {
          if (response.success) {
              //find the like count for specific post and modify it appropriately
              const likeCountSpan = $('.like-count[data-likes][data-post-id="' + postID + '"]');
              likeCountSpan.text(response.newLikeCount); //update the number of likes on the page

              //update the data-likes attribute to the new count
              likeCountSpan.attr('data-likes', response.newLikeCount);

              //toggle the liked class and data-liked attribute based on the new state
              if ($likeButton.data('liked') === 'true' || $likeButton.data('liked') === true) {
                  $likeButton.removeClass('liked').data('liked', false).attr('data-liked', 'false');
              } else {
                  $likeButton.addClass('liked').data('liked', true).attr('data-liked', 'true');
              }
          } else {
              
          }
      },
      error: function(xhr, status, error) {
         
      }
  });
});


//filtering topics
$('.section.topics').on('click', 'a', function(e) {
    e.preventDefault();

    var topicText = $(this).text();
    $('.section.topics a').removeClass('active');
    $(this).addClass('active');

    //special handling for "Show all topics"
    currentTopic = (topicText === 'Show all topics') ? 'all' : topicText.toLowerCase().replace(/\s+/g, '-');

    filterPosts(); //reapply filters
});


  //event listener for the search input
  $('#search-term').on('input', function() {
	//update the current search term
	currentSearchTerm = $(this).val().toLowerCase().trim();

	//apply filters
	filterPosts();
  });

	



  //event listener for topic links in the sidebar
  $('.section.topics a').click(function(e) {
    e.preventDefault();
    currentTopic = $(this).attr('id');
    filterPosts(); // apply both filters
  });

  //event listener for the search input
  $('#search-term').on('input', function() {
    currentSearchTerm = $(this).val().toLowerCase().trim();
    filterPosts(); //apply both filters
  });

  $(document).ready(function() {
      //fetch and insert dynamic topics from PHP script
      $.get("dynamic-topics.php", function(data) {
          //prepend "Show all topics" with an id for easy access
          $("#dynamic-topics").html('<li><a href="#" id="show-all-topics" class="topic-filter active">Show all topics</a></li>' + data);
      });

  });

  
  //sort by most liked comments
	function sortByTop() {
	  const posts = document.querySelectorAll('.all-content > .post');
	  const sortedPosts = Array.from(posts).sort((a, b) => {
		//retrieve the like count from the data-likes attribute
		const likesA = parseInt(a.querySelector('.like-count').getAttribute('data-likes')) || 0;
		const likesB = parseInt(b.querySelector('.like-count').getAttribute('data-likes')) || 0;
		return likesB - likesA; //sort in descending order of likes
	  });

	  const container = document.querySelector('.all-content');
	  if (container) {
		//remove all current post elements from the DOM to avoid duplicates
		posts.forEach(post => post.remove());

		//append sorted posts back to the container
		sortedPosts.forEach(post => container.appendChild(post));
	  } else {
		
	  }
	}


  //sort by newest comments
  function sortByNewest() {
    const posts = document.querySelectorAll('.all-content > .post');
    
    const sortedPosts = Array.from(posts).sort((a, b) => {
      //use the data-date-created attribute for sorting
      let dateA = new Date(a.getAttribute('data-date-created'));
      let dateB = new Date(b.getAttribute('data-date-created'));
  
      return dateB - dateA; //sort by descending date order (newest first)
    });
  
    const container = document.querySelector('.all-content');
    if (container) {
      //remove all post elements from the DOM
      posts.forEach(post => post.remove());
  
      //append sorted posts back to the container
      sortedPosts.forEach(post => {
        container.appendChild(post);
      });
    } else {
   
    }
  }

		
    //call the function once the page content has loaded
    document.addEventListener('DOMContentLoaded', () => {
      sortByNewest(); //sort by newest on initial page load
      newestPostsBtn.classList.add('active'); //set the newest button to active on initial load
    });

    //adjust event listeners to check for null and toggle active class
    const topPostsBtn = document.getElementById('topPostsBtn');
    const newestPostsBtn = document.getElementById('newestPostsBtn');

    //function to toggle active class on buttons
    function toggleButtonActive(clickedBtn) {
      //remove 'active' class from both buttons
      topPostsBtn.classList.remove('active');
      newestPostsBtn.classList.remove('active');
      //add 'active' class to the button that was clicked
      clickedBtn.classList.add('active');
    }

    //if users are sorting by newest
    if (topPostsBtn) {
      topPostsBtn.addEventListener('click', () => {
      sortByTop();
      toggleButtonActive(topPostsBtn);
      });
    } else {
   
    }

    //if user is sorting by newest:
    if (newestPostsBtn) {
      newestPostsBtn.addEventListener('click', () => {
      sortByNewest();
      toggleButtonActive(newestPostsBtn);
      });
    } else {
    
    }

});
