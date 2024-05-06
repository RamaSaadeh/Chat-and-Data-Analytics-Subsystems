var details = sessionStorage.getItem("user");
var role = JSON.parse(details).role;

//ensure that user is logged in to grant access to this page else redirect them to login.html
function checkLogin() {
    try {
        var details = sessionStorage.getItem("user");
        var role = JSON.parse(details).role;
        switch (role) {
            case "a":
                window.location.replace("permission-denied.html");
                break;
            case "g":
                break;
            case "m":
                window.location.replace("permission-denied.html");
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

// requesting staff info  on page load

  var tasksData;
  var toDoListArray;
  
  var max_item_id = 0;

  var details = sessionStorage.getItem("user");
  var user_id = JSON.parse(details).id;


  function populateToDoList(){
    // request to-do list data for user asynchronously
    $.ajax({
      url: 'return-to-do.php',
      dataType: "json",
      type: 'POST',
      data: { user_id: user_id },
      success: function(data) {
          // Handle the response from the server
          
          toDoListArray = data;
          console.log('todo list returned successfully');
          document.getElementById("toDoUL").innerHTML = "";
          toDoListArray.forEach(function(itemData){
                // if item_id > max_item_id then replace max_item_id
                if(parseInt(itemData['item_id']) > max_item_id){
                  max_item_id = parseInt(itemData['item_id']);
                }

                //
                var li = document.createElement("li");
                var p = document.createElement("p");
                p.textContent = itemData['description'];
                li.appendChild(p);
                if (itemData['checked'] == '1'){
                  li.classList.add('checked');
                }
                li.setAttribute("id", itemData['item_id']);
                var span = document.createElement("SPAN");
                var txt = document.createTextNode("\u{1F5D1}");
                span.className = "close";
                span.appendChild(txt);
                li.appendChild(span);
                document.getElementById("toDoUL").appendChild(li);
          })
          addOnDeleteFunc();
          console.log(max_item_id);
 
      },
      error: function(xhr, status, error) {
          // Handle errors
          console.error('Error accessing to do items:', error);
      }
    });
    

  }




$(document).ready(function(){
    populateToDoList();
    addOnDeleteFunc();
    getUserTasks();
}); 


    
     // Create a "close" button and append it to each list item
     var myNodelist = document.getElementById("toDoList").getElementsByTagName("LI");
     var i;
     for (i = 0; i < myNodelist.length; i++) {
       var span = document.createElement("SPAN");
       var txt = document.createTextNode("\u{1F5D1}");
       span.className = "close";
       span.appendChild(txt);
       myNodelist[i].appendChild(span);
     }
     
function addOnDeleteFunc(){     
     // Click on a close button to hide the current list item
     var close = document.getElementsByClassName("close");
     var i;
     for (i = 0; i < close.length; i++) {
       close[i].onclick = function() {
        // remove to do list item
         var li = this.parentElement;
         if (confirm("Are you sure you want to delete this item?: \n\n\n" +  "\"" + li.querySelector("p").textContent + "\"")){
            // delete the to-do list item
            
            // update the database asynchronously
            // Prepare the data to be sent to the server
            if (li.id){
              // item has an id attribute set - delete from database
                  var requestData = {
                    itemId: li.id,
                    userId: user_id
                  };
                  li.style.display = "none";
                  // Make the AJAX request
                  
                  $.ajax({
                    url: 'delete-to-do-item.php',
                    type: 'POST',
                    data: requestData,
                    success: function(response) {
                        // Handle the response from the server
                        console.log('Item deleted successfully');
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        console.error('Error deleting item:', error);
                    }
                  });

            } else {
                    // item has no id set, was not stored in database
                    li.style.display = "none";
            }
         }
         
       }
     }
    }



// Add a "checked" symbol when clicking on a list item
// action when to do list item clicked
var list = document.getElementById("toDoList").querySelector('ul');
list.addEventListener('click', function(ev) {
    if (ev.target.tagName === 'LI' || ev.target.tagName === 'P') {
        const listItem = (ev.target.tagName === 'LI') ? ev.target : ev.target.closest('li');
        var description = listItem.querySelector('p').textContent;
          var checked = 0;
        if (listItem) {
          
          if (listItem.classList.contains("checked")){
            // if the item was checked then update db and adjust position
            checked = 1; 
          } 
          $.ajax({
            url: 'toggle-to-do-status.php',
            type: 'POST',
            data: {'item_id': listItem.id, 'user_id': user_id, 'description': description, 'current_status': checked},
            success: function(response) {
                // Handle the response from the server
                console.log('Item edited in db successfully');
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error('Error toggling item:', error);
            }
          });
          
          moveToDoItem(listItem.id, description, checked);
        }
    }
  }, false);

function moveToDoItem(id, description, checked){
  oldItem = document.getElementById(id);
  document.getElementById("toDoUL").removeChild(oldItem);
  li = document.createElement("li");
  li.setAttribute("id", id);
  p = document.createElement("p");
  p.appendChild(document.createTextNode(description));
  li.appendChild(p);
  var span = document.createElement("SPAN");
  var txt = document.createTextNode("\u{1F5D1}");
  span.className = "close";
  span.appendChild(txt);
  li.appendChild(span);
  if (checked == '0'){
    // unchecked -> checked
    li.classList.add("checked");
    // append item to end of the list
    document.getElementById("toDoUL").appendChild(li);
  } else {
    // add item to the start of the list
    document.getElementById("toDoUL").insertAdjacentElement('afterbegin', li);
  }
  addOnDeleteFunc();

}


 function keyPressed(event) {
  // Check if the pressed key is Enter (key code 13)
  if (event.keyCode === 13) {
      // Call the newElement() function
      newElement();
  }
}

// Add event listener for key press on document
document.addEventListener('keypress', keyPressed);
     
// Create a new list item when clicking on the "Add" button
function newElement() {
    var li = document.createElement("li");
    var p = document.createElement("p");
    var inputValue = document.getElementById("toDoInput").value;
    var t = document.createTextNode(inputValue);
    p.appendChild(t);
    li.appendChild(p);
    var itemId = max_item_id + 1;
    max_item_id +=1;
    li.setAttribute("id", itemId);
    if (inputValue === '') {
      // they didn't type anything
    } else {
    // add new item to the start of the list 
      document.getElementById("toDoUL").insertAdjacentElement('afterbegin', li);
      // update the todolist table using jQuery AJAX
      $.ajax({
        url: 'add-to-do-item.php',
        type: 'POST',
        data: {'item_id': itemId, 'user_id': user_id, 'description': inputValue},
        success: function(response) {
            // Handle the response from the server
            console.log('Item added to db successfully');
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error('Error creating item:', error);
        }
      });


    }
    document.getElementById("toDoInput").value = "";

    var span = document.createElement("SPAN");
    var txt = document.createTextNode("\u{1F5D1}");
    span.className = "close";
    span.appendChild(txt);
    li.appendChild(span);

    addOnDeleteFunc();
}


// code for user tasks

function getUserTasks(){

    $.ajax({
        url: 'return-tasks.php',
        type: 'POST',
        dataType: "json",
        data: {'user_id': user_id},
        success: function(data) {
            // Handle the response from the server
            console.log('tasks retrieved successfully');
            tasksData = data;
            console.log(tasksData);
            populateTasksTable();
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error('Error retrieving tasks:', error);
        }
      });

}


function populateTasksTable(){
    //replace sample content with user 
    if (tasksData.length != 0) {
        document.getElementById("placeholder").style.display = "none";
        document.getElementById("taskTable").innerHTML = "";
        var header = document.createElement("tr");
        var heading1 = document.createElement("th");
        heading1.classList.add("details");
        heading1.textContent = "Details";
        var heading2 = document.createElement("th");
        heading2.classList.add("status");
        heading2.textContent = "Status";
        header.appendChild(heading1);
        header.appendChild(heading2);
        document.getElementById("taskTable").appendChild(header);
    }
    tasksData.forEach(function(task){
        // accessing variables 
        var task_id = task['task_id'];
        var project_id = task['project_id']
        //task_id.textContent = "Task ID #" + task['task_id'];
        var task_name = document.createElement("h4");
        task_name.textContent = task['task_name'];
        var hrs_left = document.createElement("h5");
        hrs_left.textContent = "Hours remaining: " +task['hrs_remaining'];
        var status = task['status'];
        var deadline = document.createElement("h5");
        deadline.textContent = "Deadline: " +task['deadline'];
        var notes = document.createElement("h5");
        var em = document.createElement("em");
        notes.appendChild(em);
        notes.textContent = "Notes: " + task['notes'];
        var proj_name = document.createElement("h5")
        proj_name.textContent = "Project Name: " + task['proj_name'];

        // populating table cells

        var row = document.createElement("tr");
        row.setAttribute("id", (String(task['task_id']) + String(task['project_id'])) );
        leftCell = document.createElement("td");
        leftCell.classList.add("details");
        rightCell = document.createElement("td");
        rightCell.classList.add("status");

        // populate leftCell
        leftCell.appendChild(task_name);
        //leftCell.appendChild(task_id);
        leftCell.appendChild(proj_name);
        leftCell.appendChild(deadline);
        leftCell.appendChild(hrs_left);
        leftCell.appendChild(notes);
        row.appendChild(leftCell);

        // populate rightCell

        
        rightCell.innerHTML = `
            <label class="container">Not Started
            <input type="radio" id="notStarted${task['task_id']}${task['project_id']}" name="status${task['task_id']}${task['project_id']}">
            <span class="radio"></span>
            </label>
            <label class="container">On Track
                <input type="radio" id="onTrack${task['task_id']}${task['project_id']}" name="status${task['task_id']}${task['project_id']}">
                <span class="radio"></span>
            </label>
            <label class="container">Completed
                <input type="radio" id="completed${task['task_id']}${task['project_id']}" name="status${task['task_id']}${task['project_id']}">
                <span class="radio"></span>
            </label>
            <label class="container">Overdue
                <input type="radio" id="overdue${task['task_id']}${task['project_id']}" name="status${task['task_id']}${task['project_id']}">
                <span class="radio"></span>
            </label>
            <br>
            <input type="submit" class="submitButton" value="Save" onclick="saved(${task['task_id']},${task['project_id']})">
            <h5 id="submitted${task['task_id']}${task['project_id']}" style="color: #2fe617; display: none">Status updated</h5> 
            <br>`;  
        row.appendChild(rightCell);
        document.getElementById("taskTable").appendChild(row);
        switch (status) {
            case "Not Started":
                document.getElementById("notStarted" + task_id + project_id).checked = true;
                break;
            case "On Track":
                document.getElementById("onTrack" + task_id + project_id).checked = true;
                break;
            case "Completed":
                document.getElementById("completed" + task_id + project_id).checked = true;
                break;
            case "Overdue":
                document.getElementById("overdue" + task_id + project_id).checked = true;
                break;
        };
    });

    
}











// end of code for user tasks






// adding prompts for problem and training buttons
function reportTaskProblem(taskId, projectId){
    //

    var problem = "problem with " + taskId;
    document.getElementById("reported" + (String(taskId) + String(projectId))).style.display = "block"; // show message
    if (document.getElementById("problem" + (String(taskId) + String(projectId))).value.trim().length == 0) {
        document.getElementById("reported" + (String(taskId) + String(projectId))).style.color = "red";
        document.getElementById("reported" + (String(taskId) + String(projectId))).innerHTML = "Please type a problem";
    }
    else {
        document.getElementById("reported" + (String(taskId) + String(projectId))).style.color = "#2fe617";
        document.getElementById("reported" + (String(taskId) + String(projectId))).innerHTML = "Problem reported";
        document.getElementById("problem" + (String(taskId) + String(projectId))).value = "";
    }
}
function suggestTraining(taskId, projectId){
    document.getElementById("requested" + (String(taskId) + String(projectId))).style.display = "block"; // show message
    if (document.getElementById("training"+(String(taskId) + String(projectId))).value.trim().length == 0) {
        document.getElementById("requested" + (String(taskId) + String(projectId))).style.color = "red";
        document.getElementById("requested" + (String(taskId) + String(projectId))).innerHTML = "Please type training";
    }
    else {
        document.getElementById("requested" + (String(taskId) + String(projectId))).style.color = "#2fe617";
        document.getElementById("requested" + (String(taskId) + String(projectId))).innerHTML = "Training requested";
        document.getElementById("training" + (String(taskId) + String(projectId))).value = "";
    }
}



function saved(taskId, projectId) {
    var uniqueId = String(taskId) + String(projectId);
    var statuses = {"On Track": document.getElementById("onTrack" + uniqueId).checked,
        "Completed": document.getElementById("completed" + uniqueId).checked,
        "Overdue": document.getElementById("overdue" + uniqueId).checked,
        "Not Started": document.getElementById("notStarted" + uniqueId).checked};
    console.log(statuses);
    var currentStatus;
    for (var key in statuses) {
        if (statuses.hasOwnProperty(key)) {
            if(statuses[key]){
                currentStatus = key;
            }
        }
    }
    // display message
    document.getElementById("submitted" + uniqueId).style.display = "block";
    document.getElementById("submitted" + uniqueId).style.color = "#2fe617";
    document.getElementById("submitted" + uniqueId).innerHTML = "Task Status Updated";

    // edit data in database
    $.ajax({
        url: 'update-task-status.php',
        dataType: "json",
        type: 'POST',
        data: { 'status': currentStatus, 'taskId': taskId, 'projectId': projectId},
        success: function(data) {
            //alert("success");
        }
    })
}


//Beginning of js for navbar


//Nav Java
var inviteForm = document.getElementById("inviteuseropaquebg");

function openForm() {
inviteForm.style.display = "block";
}

function closeForm() {
inviteForm.style.display = "none";
document.getElementById("email").value = "";
document.getElementById("emailError").style.display = "none";
}

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


//Decides which Dash to Link to
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


//End of js for navbar
