//Nav Java
var inviteForm = document.getElementById("inviteuseropaquebg");
		
//function to open invite form
function openForm() {
	inviteForm.style.display = "block";
}

//function to close invite form
function closeForm() {
	inviteForm.style.display = "none";
	document.getElementById("email").value = "";
	document.getElementById("emailError").style.display = "none";
}

//function to invite user
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

//check user is logged in to account in users table
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
dashboard.addEventListener("click", function() {
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


$(document).ready(function() {
	
	//declare variables for managing draft limit
    var currentEditingDraft = null;
    var draftCounter = 1;
	var currentAction = null; 
	var currentDraftToPost = null;
	
	
    
$(document).ready(function() {
    $('#post-form').submit(function(e) {
        e.preventDefault();
        
		var details = sessionStorage.getItem("user");
		var userID = JSON.parse(details).id;

        //retrieve values from form fields
        const postTopic = $('#post-topic').val();
        const postTitle = $('#post-title').val();
        const postBody = $('#post-body').val();
        //for testing purposes

        
        const errorMessage = $('#error-message');
        const successMessage = $('#success-message');
        
        //validation ensuring fields have values
        if (!postTopic || !postTitle || !postBody) {
            errorMessage.text('Please fill in all fields').addClass('show');
            setTimeout(() => { errorMessage.removeClass('show'); }, 5000);
            return;
        }
        
        if (postBody.length > 1500) {
            errorMessage.text('Character limit exceeded (1500 characters max)').addClass('show');
            setTimeout(() => { errorMessage.removeClass('show'); }, 5000);
            return;
        }
        
        //AJAX call to send data to create-post.php
        $.ajax({
            type: "POST",
            url: "create-post.php",
            data: {
				userID: userID,
                topic: postTopic,
                title: postTitle,
                body: postBody,
                isDraft: 0
            },
            success: function(response) {
                //reset form fields after successful submission
                $('#post-title').val('');
                $('#post-body').val('');
                $('#post-topic').val('');
                $('#character-count').text('1500');
                
                //display success message
                successMessage.text('Post added successfully!').addClass('show');
                setTimeout(() => { successMessage.removeClass('show'); }, 5000);
            },
            error: function(xhr, status, error) {
                //display error message
                errorMessage.text(`Error adding post: ${error}`).addClass('show');
                setTimeout(() => { errorMessage.removeClass('show'); }, 5000);
            }
        });
    });
});

	
//when saving a draft

$('#draftButton').click(function() {
	var details = sessionStorage.getItem("user");
	var userID = JSON.parse(details).id;

    const postTitle = $('#post-title').val();
    const postTopic = $('#post-topic').val();
    const postBody = $('#post-body').val();
    const errorMessage = $('#error-message');
    const successMessage = $('#success-message');

    errorMessage.removeClass('show');
    successMessage.removeClass('show');

    const currentDate = new Date();
    const dateString = currentDate.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

    if (!postTitle || !postTopic || !postBody) {
        errorMessage.text('Please fill in all fields');
        errorMessage.addClass('show');
        setTimeout(function() {
            errorMessage.removeClass('show');
        }, 5000);
        return;
    }

    if (draftCounter >= 5) {
        errorMessage.text("Max 5 saved drafts allowed.");
        errorMessage.addClass('show');
        setTimeout(function() {
            errorMessage.removeClass('show');
        }, 5000);
        return;
    }

    //AJAX call to check the number of drafts
    $.ajax({
        type: "GET",
        url: "check-drafts.php",
		data: { userID: userID }, //pass user id
        dataType: "json",
        success: function(response) {

            
            if (response && response.draftCount && response.draftCount >= 5) {
                errorMessage.text("Maximum of 5 saved drafts allowed.");
                errorMessage.addClass('show');
                setTimeout(() => { errorMessage.removeClass('show'); }, 5000);
            } else {
                //proceed to save the draft
                saveDraft(postTitle, postTopic, postBody);
            }
        },
        error: function(xhr, status, error) {
            errorMessage.text(`Error checking drafts: ${error}`).addClass('show');
            setTimeout(() => { errorMessage.removeClass('show'); }, 5000);
        }
    });
});

function saveDraft(postTitle, postTopic, postBody) {
    //AJAX call to save the draft
	var details = sessionStorage.getItem("user");
	var userID = JSON.parse(details).id;

    const errorMessage = $('#error-message');
    const successMessage = $('#success-message');
	
    $.ajax({
        type: "POST",
        url: "create-post.php",
        data: {
			userID: userID,
            title: postTitle,
            topic: postTopic,
            body: postBody,
            isDraft: 1 //indicating this is a draft
        },
        success: function(response) {
            //handle success
            successMessage.text('Draft saved successfully!').addClass('show');
            setTimeout(() => { successMessage.removeClass('show'); }, 5000);
            fetchAndDisplayDrafts();
            $('#post-title').val('');
            $('#post-topic').val('');
            $('#post-body').val('');
        },
        error: function(xhr, status, error) {
            //handle error
            errorMessage.text(`Error saving draft: ${error}`).addClass('show');
            setTimeout(() => { errorMessage.removeClass('show'); }, 5000);
        }
    });
}



	

	//event listener for delete draft button
	$(document).on('click', '.delete-draft', function() {
	    var draftToDelete = $(this).closest('.draft');
	    var postID = draftToDelete.data('post-id'); //retrieve the postID from data attribute
	
	    //confirm deletion with the user
	    confirmAction('delete', 'Are you sure you want to delete this draft?', function() {
	        //call deleteDraft function with the postID and the draft element
	        deleteDraft(postID, draftToDelete);
	    });
	});

	
	//event listener for the "Post" button on drafts
	$(document).on('click', '.post-draft', function() {
	    var currentDraftToPost = $(this);
	    confirmAction('post', 'Are you sure you want to post this draft?', function() {
	        postDraft(currentDraftToPost);
	    });
	});


	//event listener for save draft button
	$(document).on('click', '.save-draft', function() {
	    var draftElement = $(this).closest('.draft');
	
	    //function to handle confirmation and save action
	    var confirmSaveAction = function() {
	        const postID = draftElement.data('post-id'); // get the post ID of specific draft
	        const title = draftElement.find('.draft-title').val();
	        const topic = draftElement.find('.draft-topic').val();
	        const body = draftElement.find('.draft-body').val();
		    
		//validation to ensure correct lengths of data fields
	        if (title.length > 80 || topic.length > 80 || body.length > 1500) {
	            displayPopup('Character limit exceeded!');
	            return;
	        }
	
	
	        //send AJAX request to update the draft contents in database
	        $.ajax({
	            type: "POST",
	            url: "update-draft.php",
	            data: {
	                postID: postID,
	                title: title,
	                topic: topic,
	                body: body
	            },
	            dataType: "json",
	            success: function(response) {
	                if (response.success) {

						let formattedDate = "Last Modified: " + new Date(response.dateLastModified).toLocaleString('default', {
							year: 'numeric',
							month: 'long',
							day: 'numeric',
							hour: '2-digit',
							minute: '2-digit'
						}) + " (edited)";


                        $(`.draft[data-post-id="${postID}"]`).find('.draft-date').text(formattedDate);

	                    displayPopup('Draft saved successfully!');
	                } else {
	                    displayPopup('Error saving draft. Please try again later.');
	                }
	            },
	            error: function(xhr, status, error) {
	         
	                displayPopup('Error saving draft. Please try again later.');
	            }
	        });
	    };
	
	    //prompt  user for confirmation before saving
	    confirmAction('save', 'Are you sure you want to save this draft?', confirmSaveAction);
	});






	function displayPopup(message) {
		$('#notification-message').text(message);
		$('#notification-popup').fadeIn();

		//hide the popup after 3 seconds
		setTimeout(function() {
			$('#notification-popup').fadeOut();
		}, 3000);
	}


//confirm action with a modal-style confirmation
	function confirmAction(action, message, onConfirm) {
	  //create the confirmation HTML, styled to look like a modal popup
	  var confirmationHTML = `
		<div class="confirmation-overlay">
		  <div class="confirmation-box">
			<p>${message}</p>
			<div class="confirmation-buttons">
			  <button class="confirm-yes">Yes</button>
			  <button class="confirm-no">No</button>
			</div>
		  </div>
		</div>
	  `;

	  //if there's already a confirmation, remove it
	  $('.confirmation-overlay').remove();

	  //append the confirmation popup to the body
	  $('body').append(confirmationHTML);

	  var confirmationOverlay = $('.confirmation-overlay');

	
	  confirmationOverlay.find('.confirm-yes').on('click', function() {
		onConfirm(); 
		confirmationOverlay.remove(); 
	  });


	  confirmationOverlay.find('.confirm-no').on('click', function() {
		confirmationOverlay.remove();
	  });
	}



	$(document).on('click', '.close', function() {
	  $('#confirmationModal').fadeOut();
	});

	//when the user clicks "Yes", post the draft
	$(document).on('click', '#confirmPost', function() {
	
	  if (currentDraftToPost) {
		postDraft(currentDraftToPost);
		displayPopup('Your post has been successfully submitted!');
		//clear the stored draft button after posting
		currentDraftToPost = null;
	  } else {

	  }
	  $('#confirmationModal').fadeOut();
	});
	
	


	//when user clicks No, close the modal
	$(document).on('click', '#cancelPost', function() {
	  displayPopup('Post cancelled.');

      currentDraftToPost = null;
	  currentAction = null;
	  $('#confirmationModal').fadeOut();
	  
	});
	
	
  //toggle the menu
    $('.menu-toggle').on('click', function() {
      $('.nav').toggleClass('showing');
      $('.nav ul').toggleClass('showing');
    });
  
    //character count
    $('#post-body').on('input', function() {
      const characterCount = 1500 - $(this).val().length;
      $('#character-count').text(characterCount);
    });

//function to fetch drafts from the server and display them in the sidebar
function fetchAndDisplayDrafts() {
    var details = sessionStorage.getItem("user");
    var userID = JSON.parse(details).id;
    
    $.ajax({
        type: "GET",
        url: "fetch-drafts.php", 
        dataType: "json",
        data: { userID: userID },
        success: function(response) {
            //clear existing drafts in the sidebar
            $('.drafts-container').empty();
            
            if (response && response.drafts && response.drafts.length > 0) {
                //there are drafts, loop through each draft and append it to the sidebar
                response.drafts.forEach(function(draft) {
                    appendDraftToSidebar(draft);
                });
            } else {
                //no drafts were found, display a message indicating this
                $('.drafts-container').append('<div class="no-drafts-message">You have no saved drafts currently.</div>');
            }
        },
        error: function(xhr, status, error) {
         
            $('.drafts-container').append('<div class="error-message">Error fetching drafts. Please try again later.</div>');
        }
    });
}

//function to append a single draft to the sidebar
function appendDraftToSidebar(draft) {
    //construct HTML for the draft
    var draftHTML = `
	<div class="media draft" data-post-id="${draft.postID}">
	    <div class="media-body draft-content">
		<label for="post-topic" class="label">Topic</label>
		<input type="text" class="draft-topic" value="${draft.topic}" maxlength="89">
		<label for="post-title" class="label">Title</label>
		<input type="text" class="draft-title" value="${draft.title}" maxlength="80">
		<label for="post-body" class="label">Body</label>
		<textarea class="draft-body" maxlength="1500">${draft.body}</textarea>
	    </div>
	    <div class="draft-footer">
		<div class="draft-actions">
		    <button class="post-draft">Post</button>
		    <button class="save-draft">Save</button>
		    <button class="delete-draft">Delete</button>
		</div>
		<div class="draft-date">
			${draft.lastModified}
		</div>
	    </div>
	</div>
    `;

    //append the draft HTML to the drafts container in the sidebar
    $('.drafts-container').append(draftHTML);
}

	
	//call the fetchAndDisplayDrafts function when the page is ready
    $(document).ready(function() {
        fetchAndDisplayDrafts();
    });
	
	var $buttonElement = $("#someButtonId");
	const $draftContainer = $buttonElement.closest('.media.draft');
  

  


//function to delete a draft
function deleteDraft(postID, $draftContainer) {
    $.ajax({
        type: "POST",
        url: "delete-draft.php",
        data: { postID: postID }, //pass the postID to delete-draft.php
        dataType: "json",
        success: function(response) {
            if (response.success) {
                //draft deleted successfully, remove it from the sidebar
                $draftContainer.remove();
                displayPopup('Draft deleted successfully!');
            } else {
                //error deleting draft
                
                displayPopup('Error deleting draft. Please try again later.');
            }
        },
        error: function(xhr, status, error) {
       
            displayPopup('Error deleting draft. Please try again later.');
        }
    });
}

	//function to post a specific draft
	function postDraft($buttonElement) {
	    const $draftContainer = $buttonElement.closest('.draft');
	    const postID = $draftContainer.data('post-id');
	
	    //AJAX request to post the draft
	    $.ajax({
	        type: "POST",
	        url: "post-draft.php",
	        data: { postID: postID }, //pass the postID to post-draft.php
	        dataType: "json",
	        success: function(response) {
	            if (response.success) {
	                //draft posted successfully, remove it from the UI
	                $draftContainer.remove();
	                displayPopup('Draft posted successfully!');
	            } else {
	                //error posting draft
	             
	                displayPopup('Error posting draft. Please try again later.');
	            }
	        },
	        error: function(xhr, status, error) {
	          
	            displayPopup('Error posting draft. Please try again later.');
	        }
	    });
	}
		


});
  

