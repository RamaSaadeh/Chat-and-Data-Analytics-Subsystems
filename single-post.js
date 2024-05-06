//Nav Java
var inviteForm = document.getElementById("inviteuseropaquebg");

//close to open invite form
function openForm() {
	inviteForm.style.display = "block";
}

//close invite form
function closeForm() {
	inviteForm.style.display = "none";
	document.getElementById("email").value = "";
	document.getElementById("emailError").style.display = "none";
}

//send invite popup
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

//check user has logged in 
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

//decides which Dash to Link to
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


//single-post.js
document.addEventListener("DOMContentLoaded", function () {
	hideCharCountDisplay();
   
    const urlParams = new URLSearchParams(window.location.search);
    let messageTimeout; //variable to store timeout
    
    //character limit for the comments
    const charLimit = 500;
   
    const commentTextArea = document.getElementById('comment');
    const charCountDisplay = document.getElementById('charNum');

    //get post 'title' and 'topic'
    const title = urlParams.get('title');
    const topic = urlParams.get('topic');
	
    //form element for submitting comments
	const commentForm = document.getElementById('comment-form');
	
    //display the post title and topic in the page
    document.getElementById('postTitle').textContent = title || "Unknown Title";
    document.getElementById('postTopic').textContent = topic || "Unknown Topic";
	
    //counter for assigning IDs to comments
	let commentIdCounter = 0;
	
	let hasUnsavedChanges = false;

    const comments = [];
	
    const posts = {
      
    };

	
	$(document).ready(function() {

		var details = sessionStorage.getItem("user");
		var userID = JSON.parse(details).id;

		//extract postID from the URL parameters
		const urlParams = new URLSearchParams(window.location.search);
		const postID = urlParams.get('id'); 


		//AJAX request to fetch the single post based on postID
		$.ajax({
			url: "fetch-single-post.php",
			type: "GET",
			dataType: "json",
			data: { id: postID,
					userID: userID
			}, 
			success: function(response) {
				if (response.success) {
					
					const post = response.data;

					//populate HTML elements with post details
					$('#postTopic').text(post.Topic);
					$('#postTitle').text(post.Title);
					$('#postContent').html(post.Content);
					$('#authorName').text(post.AuthorName);
					const displayDate = post.DateLastModified ? post.DateLastModified : post.DateCreated;
					$('#postDate').text(displayDate);
					$('#likeCount').text(post.LikesCount);

					//show or hide edit and delete buttons based on `IsUserOwner` value
					if (post.IsUserOwner || post.IsAdmin) {

						$('.edit-post, .delete-post').show();
					} else {
						$('.edit-post, .delete-post').hide();
					}
					//update like button based on `IsLiked`
					if (post.IsLiked) {
						$('.like-post').addClass('liked').attr('data-liked', 'true').attr('title', 'Unlike');
					} else {
						$('.like-post').removeClass('liked').attr('data-liked', 'false').attr('title', 'Like');
					}

					if (!post.DateLastModified) {
						$('.edited-mark').remove();
					}
					
				} else {
					//handle case where post is not found or another error occurred
					
				}
				
			},
			error: function(xhr, status, error) {
			
			}
		});
		//handles liking posts
		$('.like-post').click(function() {
			var details = sessionStorage.getItem("user");
			var userID = JSON.parse(details).id;
			const isLiked = $(this).hasClass('liked');

			$.ajax({
				url: 'update-like.php',
				type: 'POST',
				dataType: 'json',
				data: {
					userID: userID,
					postID: postID,
					isLiked: isLiked
				},
				success: function(response) {
					if (response.success) {
						//update the like count display for the post
						$('#likeCount').text(response.newLikeCount);
						
						//toggle the like button class and title attribute for the post
						if (isLiked) {
							$('.like-post').removeClass('liked').attr('data-liked', 'false').attr('title', 'Like');
						} else {
							$('.like-post').addClass('liked').attr('data-liked', 'true').attr('title', 'Unlike');
						}
					} else {
						
					}
				},
				error: function(xhr, status, error) {
				
					
				}
			});
		});
	});

	
	
	
	$(document).ready(function() {
		//get user id
		var details = sessionStorage.getItem("user");
		var userID = JSON.parse(details).id;

		//extract postID from the URL parameters
		const urlParams = new URLSearchParams(window.location.search);
	
		const postID = urlParams.get('id');
		//function to fetch comments for a post
		function fetchComments(postID) {
			$.ajax({
				url: "fetch-comments.php",
				type: "GET",
				dataType: "json",
				data: { id: postID, 
						userID: userID
				},
				 success: function(responseComments) {
					//clear previous comments
					$('#previousComments').empty();
	
					const comments = responseComments.reverse();
	
					//loop through comments and append to the sidebar
					comments.forEach(function(comment) {

                    	const editDeleteIcons = (comment.IsUserOwner || comment.IsAdmin) ? `
                        	<i class="fas fa-edit edit-comment" title="Edit"></i>
                        	<i class="fas fa-trash-alt delete-comment" title="Delete"></i>` : '';

   						const likeButtonClass = comment.HasLiked ? 'like-comment liked' : 'like-comment';
    					const likeButtonTitle = comment.HasLiked ? 'Unlike' : 'Like';


						$('#previousComments').append(`
    						<div class="media comment" data-comment-id="${comment.CommentID}">
       							<div class="media-body comment-content">${comment.CommentContent}</div>
       							<div class="comment-metadata">
            						<div class="comment-user-date">
                						<i class="far fa-user">${comment.AuthorName}</i>
                						&nbsp;
                						<i class="far fa-calendar">${comment.IsEdited ? comment.LastModified + ' (edited)' : comment.LastModified}</i>
            						</div>
            						<div class="comment-actions">
                						${editDeleteIcons}
                						<i class="fas fa-thumbs-up ${likeButtonClass}" title="${likeButtonTitle}"></i>
                						<span class="like-count">${comment.Likes}</span>
            						</div>
       							</div>
    						</div>
						`);
					});
				},
				error: function(xhr, status, error) {
				
				}
			});
		}
	
	
		//fetch comments when the page loads
		fetchComments(postID);
	});
	

	
	

	
	
	commentForm.addEventListener('submit', function(event) {
		event.preventDefault(); //stop the form from submitting the usual way
	
		//retrieve comment text and validate it
		const commentText = commentTextArea.value;
	
		//extract postID from the URL
		const urlParams = new URLSearchParams(window.location.search);
		const postID = urlParams.get('id');
	
		if (!postID) {
			displayMessage('Error: Post ID is missing.', false);
			return;
		}
	
		if (commentText.trim() === '') {
			//show error message if comment is empty
			displayMessage('Error: Comment cannot be empty.', false);
		} else {
			//add the comment with postID and show success message
			addComment(commentText, postID);
			displayMessage('Comment posted successfully!', true);
			commentTextArea.value = ''; 
			updateCharCount(); 
		}
	});
	

    //function to update displayed character count
    function updateCharCount() {
        //calculate remaining characters and update the display
        const remaining = charLimit - commentTextArea.value.length;
        charCountDisplay.textContent = `Characters remaining:  ${remaining}`;

        //show error styling if user exceeds the character limit
        if (remaining < 0) {
            charCountDisplay.classList.add('error-text');
        } else {
            charCountDisplay.classList.remove('error-text');
        }
    }

    //function to display messages to the user
    function displayMessage(message, isSuccess) {
        //cancel any existing timeout to clear messages
        if (messageTimeout) {
            clearTimeout(messageTimeout);
        }

        //get elements to display error/success messages
        const errorWrapper = document.getElementById('error-message');
        const successWrapper = document.getElementById('success-message');

      
        errorWrapper.style.display = 'none';
        successWrapper.style.display = 'none';

   
        if (isSuccess) {
    
            document.querySelector('#success-message i').nextSibling.textContent = message;
            successWrapper.style.display = 'flex';
        } else {
   
            document.querySelector('#error-message i').nextSibling.textContent = message;
            errorWrapper.style.display = 'flex';
        }

        //hide messages after 4 seconds
        messageTimeout = setTimeout(() => {
            errorWrapper.style.display = 'none';
            successWrapper.style.display = 'none';
        }, 4000);
    }


    commentTextArea.addEventListener('input', updateCharCount);

    //initial call to set the character count on page load
    updateCharCount();



	//function to show the character count display
	function showCharCountDisplay() {
		const charCountElement = document.getElementById('contentCharsLeft');
		charCountElement.style.display = 'inline-block';
	}

	//function to hide the character count display
	function hideCharCountDisplay() {
		const charCountElement = document.getElementById('contentCharsLeft');
		charCountElement.style.display = 'none';
	}
	
	//function to toggle content editable state and enforce character limit
	function editPost(editIcon) {
		//toggle edit/save icon
		const isEditing = editIcon.classList.contains('fa-edit');
		editIcon.classList.toggle('fa-save', isEditing);
		editIcon.classList.toggle('fa-edit', !isEditing);
	
		const elements = [
			{selector: '#postTitle', limit: 80},
			{selector: '#postTopic', limit: 80},
			{selector: '#postContent', limit: 1500}
		];
		
		elements.forEach(({selector, limit}) => {
			const element = document.querySelector(selector);
			const charsLeftElement = document.querySelector('#contentCharsLeft');
			const isEditable = element.isContentEditable;
			element.contentEditable = !isEditable;
			
			if (isEditing) {
				hasUnsavedChanges = true;
				element.setAttribute('data-original-content', element.textContent);
				
				//add event listener for character limit
				const enforceLimit = function() {
					let charsUsed = this.textContent.length;
					let charsLeft = limit - charsUsed;
					
					//update the displayed remaining characters with context for content
					if (selector === '#postContent') {
						charsLeftElement.textContent = `${charsLeft} characters left for Content`;
					}
	
					if (charsUsed > limit) {
						//prevent additional characters
						this.textContent = this.textContent.substring(0, limit);
						charsUsed = limit; 
						charsLeft = limit - charsUsed;
						charsLeftElement.textContent = `${charsLeft} characters left for Content`;
						
						const range = document.createRange();
						const sel = window.getSelection();
						range.selectNodeContents(this);
						range.collapse(false);
						sel.removeAllRanges();
						sel.addRange(range);
					}
				};
	
				if (!element.enforceLimitListener) {
					element.addEventListener('input', enforceLimit);
					element.enforceLimitListener = enforceLimit;
				}
	
				//initialize the character count display specifically for content when editing starts
				if (selector === '#postContent') {
					enforceLimit.call(element);
					showCharCountDisplay(); //show the character count display when editing starts
				}
	
				if (selector === '#postContent') element.focus();
			} else {
				hasUnsavedChanges = false;
				//remove the listener if it exists to prevent duplication
				if (element.enforceLimitListener) {
					element.removeEventListener('input', element.enforceLimitListener);
					delete element.enforceLimitListener;
				}
				hideCharCountDisplay(); //hide the character count display when editing ends
			}
		});
	
	
		if (!isEditing) openSaveConfirmationModal();
	}

	
		//close the modal
		function closeModal() {
			//get both modals by ID
			const confirmationModal = document.getElementById('confirmationModal');
			const saveModal = document.getElementById('savePostModal');


			if (confirmationModal.style.display !== "none") {
				confirmationModal.style.display = "none";
			}

			if (saveModal.style.display !== "none") {
				saveModal.style.display = "none";
			}
		}

		  //function to save edits to single post
		  function saveEdits() {
			  const titleElement = document.querySelector('#postTitle');
			  const topicElement = document.querySelector('#postTopic');
			  const contentElement = document.querySelector('#postContent');
		  
			  //character limits
			  let title = titleElement.textContent.substring(0, 80).trim();
			  let topic = topicElement.textContent.substring(0, 80).trim();
			  let content = contentElement.textContent.substring(0, 1500).trim();
		  
			  //update elements with trimmed content
			  titleElement.textContent = title;
			  topicElement.textContent = topic;
			  contentElement.textContent = content;
		  
			  //check for changes and update the database via AJAX
			  const elements = [titleElement, topicElement, contentElement];
			  const hasChanges = elements.some(el => el.textContent.trim() !== el.getAttribute('data-original-content'));
			  if (hasChanges) {
				
				  updatePost({title, topic, content}); //function to make ajax call
				  showEditedStatus(); //add (edited) to end of date
		  
		
			  }
		  
			  //close the modal
			  closeModal(document.getElementById('savePostModal'));
		  }
		  
		  function updatePost(data) {
			  //get postID from the URL parameters
			  const urlParams = new URLSearchParams(window.location.search);
			  const postID = urlParams.get('id');
		  
			  //ajax call to update the post in Posts table
			  $.ajax({
				  url: 'update-post.php',
				  type: 'POST',
				  data: {
					  postID: postID, 
					  title: data.title,
					  topic: data.topic,
					  content: data.content
				  },
				  success: function(response) {
				
					  //success handling
				  },
				  error: function(xhr, status, error) {
					 
					  //error handling
				  }
			  });
		  }
		  
		  function showEditedStatus() {
			//find the date element by its ID
			const dateElement = document.getElementById('postDate');
		
			//check if the text content of the date element already includes "(edited)"
			if (!dateElement.textContent.includes('(edited)')) {
				//if it does not include "(edited)", append it to the existing text content
				dateElement.textContent += ' (edited)';
			}
			//if the "(edited)" text exists do nothing
		}
		
		  

		
	
		//function to open the save confirmation modal
		function openSaveConfirmationModal(contentElement) {
			const modal = document.getElementById('savePostModal');
			const confirmSaveBtn = document.getElementById('confirmSave');
			const cancelSaveBtn = document.getElementById('cancelSave');
			const closeSpan = modal.querySelector('.close');


			modal.style.display = "block";


			confirmSaveBtn.onclick = null;
			cancelSaveBtn.onclick = null;
			closeSpan.onclick = null;
			window.onclick = null;


			confirmSaveBtn.onclick = function() {
				saveEdits(contentElement);
				closeModal(); 
			};

		
			cancelSaveBtn.onclick = function() {
				closeModal(); 
			};


			closeSpan.onclick = function() {
				closeModal(); 
			};

			
			window.onclick = function(event) {
				if (event.target === modal) {
					closeModal(); 
				}
			};
		}



	
	function attachCharCountListeners() {
		document.querySelectorAll('[contenteditable="true"]').forEach(el => {
			el.addEventListener('input', () => {
				//determine the character limit based on the element's ID or data attribute
				let charLimit;
				switch(el.id) {
					case 'postTitle':
					case 'postTopic':
						charLimit = 80;
						break;
					case 'postContent':
						charLimit = 1500;
						break;
					default:
						charLimit = 0; //default case
				}
				updateCharCount(el.id, charLimit);
			});
		});
	}

	
	document.querySelectorAll('.edit-post').forEach(button => {
		button.addEventListener('click', function() {
			
			const titleElement = document.getElementById('postTitle');
			const topicElement = document.getElementById('postTopic');
			const contentElement = document.getElementById('postContent');

			//toggle editing state for each post component
			[titleElement, topicElement, contentElement].forEach(el => {
				if (!el.hasAttribute('data-original-content')) {
					el.setAttribute('data-original-content', el.textContent.trim());
				}
			});

			//pass the edit icon to the function
			editPost(this); 

			//attach character count listeners after making elements editable
			attachCharCountListeners();
		});
	});

	
		

	//function to open the modal and set up the deletion process
	function askDeleteConfirmation(deleteIcon) {
		const postId = deleteIcon.closest('.post').getAttribute('data-post-id'); 
		$('#confirmPostDelete').data('post-id', postId); //attach post id to the delete button
		$('#deleteConfirmationModal').modal('show'); 
	}
	
	
	
	$('#confirmPostDelete').click(function() {

		const postID = urlParams.get('id'); //get post id from the url
		
	
		$.ajax({
			url: 'delete-post.php', 
			type: 'POST',
			dataType: 'json', 
			data: { postId: postID },
			success: function(response) {
				if (response.success) {
					//if the post was successfully deleted
					$(`.post[data-post-id="${postID}"]`).remove(); 
					$('#deleteConfirmationModal').modal('hide'); 
					
				} else {
					//if the server responded with an error
					
				}
			},
			error: function(xhr, status, error) {
				//handle any AJAX errors
				
				
			}
		});
	});
	
	$('.delete-post-icon').click(function() {
		askDeleteConfirmation($(this)); //pass the clicked delete icon/button to the function
	});
	

	document.body.addEventListener('click', function(event) {
		if (event.target.classList.contains('delete-comment')) {
			//get the closest comment element and its ID
			const commentElement = event.target.closest('.comment');
			const commentId = commentElement.getAttribute('data-comment-id');
	
			//show the confirmation modal
			document.getElementById('confirmationModal').style.display = 'block';
	
			//store the comment ID in a global variable
			document.getElementById('confirmDelete').setAttribute('data-comment-id', commentId);
		}
	});
	
	//handle confirmation of deletion
	document.getElementById('confirmDelete').addEventListener('click', function() {
		const commentId = this.getAttribute('data-comment-id'); //retrieve comment id
	
		//call the function to delete the comment
		deleteComment(commentId);
	});
	
	//handle cancellation of deletion
	document.getElementById('cancelDelete').addEventListener('click', function() {
		document.getElementById('confirmationModal').style.display = 'none'; //hide the modal
	});
			
		  //delete icon for posts
		  document.addEventListener('click', function(event) {
			//check if the delete-post icon was clicked
			if (event.target.classList.contains('delete-post')) {			
			  event.preventDefault();
			  //show the delete confirmation modal
			  document.getElementById('deletePostModal').style.display = 'block';
			}
		  });
	
	
		  const confirmPostDeleteBtn = document.getElementById('confirmPostDelete');
		  const cancelPostDeleteBtn = document.getElementById('cancelPostDelete');
		  const deletePostModal = document.getElementById('deletePostModal');
	
		  confirmPostDeleteBtn.addEventListener('click', function() {
	
			
			window.location.href = 'all-posts.html';
		  });
	
		  cancelPostDeleteBtn.addEventListener('click', function() {
			deletePostModal.style.display = 'none';
		  });
	
		  window.addEventListener('click', function(event) {
			if (event.target === deletePostModal) {
			  deletePostModal.style.display = 'none';
			}
		  });
	

	//delete comments from sidebar
	function deleteComment(commentId) {
		
		fetch('delete-comment.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: `commentID=${commentId}`
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				
				const commentElement = document.querySelector(`.comment[data-comment-id="${commentId}"]`);
				commentElement.classList.add('fade-out');
	
			
				commentElement.addEventListener('transitionend', function() {
					commentElement.remove();
				}, { once: true }); 
	
				
				document.getElementById('confirmationModal').style.display = 'none';
			} else {
				
			}
		})
		.catch(error => {
	
		
		});
	}
		
	
	
	//event listener for editing comments
	document.body.addEventListener('click', function(event) {
		if (event.target.classList.contains('edit-comment')) {
			//get the closest comment element and its ID
			const commentElement = event.target.closest('.comment');
			const commentId = commentElement.getAttribute('data-comment-id');
	
			//get the comment content from the comment element
			const commentContent = commentElement.querySelector('.comment-content').textContent;
	
			//ensure the textarea exists in the DOM before attempting to set its value
			const editCommentTextarea = document.getElementById('editedCommentContent'); 
			if (editCommentTextarea) {
				editCommentTextarea.value = commentContent; //initially populate the modal with comment modal
			} else {
				
			}
	
			//store the comment ID in a global variable or directly in the modal's confirm button for later use
			const confirmEditButton = document.getElementById('confirmEdit');
			if (confirmEditButton) {
				confirmEditButton.setAttribute('data-comment-id', commentId);
			} else {
			
			}
	
			//show the edit modal, ensuring the modal exists
			const editCommentModal = document.getElementById('editCommentModal');
			if (editCommentModal) {
				editCommentModal.style.display = 'block';
			} else {
			
			}
		}
	});
	
	//handle confirmation of edit
	document.getElementById('confirmEdit').addEventListener('click', function() {
		const commentId = this.getAttribute('data-comment-id'); // Retrieve the comment ID
		const textarea = document.getElementById('editedCommentContent'); // Updated ID reference
	
		//validate if the textarea is successfully accessed
		if (textarea !== null) {
			const updatedContent = textarea.value; //get the updated comment content
	
			//call the function to update the comment
			updateComment(commentId, updatedContent);
		} else {
	
		}
	});
	
	//handle cancellation of edit
	document.getElementById('cancelEdit').addEventListener('click', function() {
		document.getElementById('editCommentModal').style.display = 'none'; 
	});
	
	
	//function to update the comment
	function updateComment(commentId, updatedContent) {
		fetch('edit-comment.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: `commentID=${encodeURIComponent(commentId)}&newContent=${encodeURIComponent(updatedContent)}`
		})
		.then(response => response.json())

		.then(data => {
			if (data.success) {
				const commentElement = document.querySelector(`.comment[data-comment-id="${commentId}"]`);
				if (commentElement) {
					//update the comment content
					commentElement.querySelector('.comment-content').textContent = updatedContent;
		
					//update the last modified timestamp
					const lastModifiedElement = commentElement.querySelector('.far.fa-calendar');
					if (lastModifiedElement) {
						//check if "(edited)" is already appended to avoid duplication
						const isAlreadyEdited = lastModifiedElement.textContent.includes('(edited)');
						const newLastModifiedText = `${data.lastModified}${isAlreadyEdited ? '' : ' (edited)'}`;
		
						lastModifiedElement.textContent = newLastModifiedText;
					}
		
					//hide the edit modal
					document.getElementById('editCommentModal').style.display = 'none';
				} else {
					
				}
			} else {
				
			}
		})
		.catch(error => {
		
			
		});
	}

	//edit comments
	function addComment(commentText, postID) {

		var details = sessionStorage.getItem("user");
		var userID = JSON.parse(details).id;
		//AJAX call to add the comment to the database
		$.ajax({
			
			url: "add-comment.php",
			type: "POST",
			dataType: "json",
			data: {
				id: postID,
				comment: commentText,
				userID: userID
			},
			success: function(response) {
				//handle success:
				if (response.success) {
					//if successful, fetch comments and update UI
					fetchComments(postID); 
				} else {
					//if it errors, display error
					
				}
			},
			error: function(xhr, status, error) {
				//if it errors display error
			
			}
		});
	
		//function to fetch comments for a post
		function fetchComments(postID) {
			var details = sessionStorage.getItem("user");
			var userID = JSON.parse(details).id;

			$.ajax({
				url: "fetch-comments.php",
				type: "GET",
				dataType: "json",
				data: { id: postID,
						userID: userID
				},
				 success: function(responseComments) {
					//clear previous comments
					$('#previousComments').empty();
	
					const comments = responseComments.reverse();
	
					//loop through comments and append to sidebar 
					comments.forEach(function(comment) {

						const editDeleteIcons = (comment.IsUserOwner || comment.IsAdmin) ? `
						  <i class="fas fa-edit edit-comment" title="Edit"></i>
						  <i class="fas fa-trash-alt delete-comment" title="Delete"></i>` : '';

					   	const likeButtonClass = comment.HasLiked ? 'like-comment liked' : 'like-comment';
						const likeButtonTitle = comment.HasLiked ? 'Unlike' : 'Like';

						$('#previousComments').append(`
						<div class="media comment" data-comment-id="${comment.CommentID}">
							<div class="media-body comment-content">${comment.CommentContent}</div>
							<div class="comment-metadata">
						 		<div class="comment-user-date">
							 		<i class="far fa-user">${comment.AuthorName}</i>
									&nbsp;
									<i class="far fa-calendar">${comment.IsEdited ? comment.LastModified + ' (edited)' : comment.LastModified}</i>
						 		</div>
						 		<div class="comment-actions">
							 		${editDeleteIcons}
							 		<i class="fas fa-thumbs-up ${likeButtonClass}" title="${likeButtonTitle}"></i>
							 		<span class="like-count">${comment.Likes}</span>
						 		</div>
							</div>
				 		</div>
					`);
				});
			},
			error: function(xhr, status, error) {
			
			}
		});
	
	}
}

	function sortByTop() {
		//sort the comments based on the like count
		const commentsContainer = document.querySelector('.previous-comments');
		const comments = Array.from(commentsContainer.children);
		
		comments.sort((a, b) => {
			const likesA = parseInt(a.querySelector('.like-count').textContent, 10);
			const likesB = parseInt(b.querySelector('.like-count').textContent, 10);
			return likesB - likesA; 
		});

		comments.forEach(comment => {
			commentsContainer.appendChild(comment);
		});
	}
		
	//function for getting the date
	function extractDateFromElement(element) {
		const regex = /(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/;
		const match = regex.exec(element.textContent || '');
		return match ? match[1] : null; 
	}

	//sort by newest comments
	function sortByNewest() {
	
		
		const commentsContainer = document.querySelector('.previous-comments');
		const comments = Array.from(commentsContainer.children);

		comments.sort((a, b) => {
			const dateAString = extractDateFromElement(a.querySelector('.comment-user-date'));
			const dateBString = extractDateFromElement(b.querySelector('.comment-user-date'));
			
			
			const dateA = new Date(dateAString);
			const dateB = new Date(dateBString);
			
			return dateB - dateA; 
		});

		comments.forEach(comment => {
			commentsContainer.appendChild(comment);
		});
	}



	document.getElementById('topCommentsBtn').addEventListener('click', function() {
	  sortByTop();
	  setActiveButton(this);
	});

	document.getElementById('newestCommentsBtn').addEventListener('click', function() {
	  sortByNewest();
	  setActiveButton(this);
	});

	function setActiveButton(selectedButton) {
	 
	  var topCommentsBtn = document.getElementById('topCommentsBtn');
	  var newestCommentsBtn = document.getElementById('newestCommentsBtn');
	  
	
	  topCommentsBtn.classList.remove('active');
	  newestCommentsBtn.classList.remove('active');
	  
	
	  selectedButton.classList.add('active');
	}

	$(document).ready(function() {
		$('#previousComments').on('click', '.like-comment', function() {
			var details = sessionStorage.getItem("user");
			var userID = JSON.parse(details).id;

			const $this = $(this);
			//get comment id
			const commentID = $this.closest('.media.comment').data('comment-id');
			
			const isLiked = $this.hasClass('liked'); 
		
	
			$.ajax({
				url: 'update-comment-like.php', 
				type: 'POST',
				data: {
					commentID: commentID, 
					isLiked: isLiked,
					userID: userID
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						//toggle 'liked' class based on current state
						$this.toggleClass('liked');
						
						//update the title attribute based on whether it's now liked or not
						if ($this.hasClass('liked')) {
							$this.attr('title', 'Unlike'); //if the comment is liked, change the title to 'Unlike'
						} else {
							$this.attr('title', 'Like'); //if the comment is unliked, revert the title to 'Like'
						}
						
						//update likes count text
						const $likeCount = $this.siblings('.like-count');
						let likes = parseInt($likeCount.text(), 10);
						likes = isLiked ? likes - 1 : likes + 1; //increment or decrement based on current state
						$likeCount.text(likes);
					} else {
						
					}
				},
				error: function(xhr, status, error) {
				
				}
			});
		});
	});
	
	//when user tries leaving page with unsaved changes to post trigger the following
	window.addEventListener('beforeunload', (event) => {
		if (hasUnsavedChanges) {
			const message = 'You have unsaved changes! Are you sure you want to leave?';
			event.returnValue = message; 
			return message; 
		}
	});


});
