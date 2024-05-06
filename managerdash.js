function checkLogin() {
  try {
      var details = sessionStorage.getItem("user");
      var role = JSON.parse(details).role;
      switch (role) {
          case "a":
              window.location.replace("permission-denied.html");
              break;
          case "g":
              window.location.replace("permission-denied.html");
              break;
          case "m":
              break;
          case "l":
              window.location.replace("permission-denied.html");
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
    a.href = "accessproject.php";
    break;
  default:
    a.href = "#";
}
});



//function that takes in info about a task as parameters and populates the cells in the table with these
function addtaskTotable(task_id,task_name,hrs_remaining,status,deadline,assigned_to,notes){

var table = document.getElementById("taskstable");
var row = table.insertRow(-1);

var taskidcell = row.insertCell(0);
var tasknamecell = row.insertCell(1);
var hrscell = row.insertCell(2);
var statuscell = row.insertCell(3);
var deadlinecell = row.insertCell(4);
var staffassignedcell = row.insertCell(5);
var taskactionscell = row.insertCell(6);

taskidcell.innerHTML = task_id;
tasknamecell.innerHTML = task_name;
hrscell.innerHTML = hrs_remaining;
statuscell.innerHTML = status;
deadlinecell.innerHTML = deadline;  
staffassignedcell.innerHTML = assigned_to; 

taskactionscell.innerHTML = '<button class = "notesbtn" onclick="opentasknotesForm(this)">Notes</button><button class="editbtn" onclick="openedittaskForm(this)"><span id="editsymbol" class="material-symbols-outlined">edit</span></button>';
}

//function that takes in info about a user as parameters and populates the cells in the table with these
function addStaffTotable(user_id,name,role,email){

var table = document.getElementById("StaffTable");
var row = table.insertRow(-1);

var useridcell = row.insertCell(0);
var namecell = row.insertCell(1);
var rolecell = row.insertCell(2);
var emailcell = row.insertCell(3);

useridcell.innerHTML = user_id;
namecell.innerHTML = name;
rolecell.innerHTML = role;
emailcell.innerHTML = email;
}


//gets the project ID that we have loaded from the URL
const urlParams = new URLSearchParams(window.location.search);
const selectedProjectID = urlParams.get('selected_project_ID');
var projectname;

$.ajax({
type: "POST",
url: "load_project_todash.php",
data: {
  ID: selectedProjectID
},
success: function (response) {
  if (response === "invalid") {
    alert("Something went wrong");
  } else {	

    projectname = JSON.parse(response)[0];
    var allTasks = JSON.parse(response)[1]; //[0] takes the first half of the encoded json array
    var allStaff = JSON.parse(response)[2]; //[1] takes the first half of the encoded json array

    document.getElementById("section_header").innerHTML = '<div class="section-header">' + projectname + '<button class="addtaskbtn" onclick="openaddtaskForm()">Add Task</button></div>';
    document.getElementById("projID_text").innerHTML = '<div id="projID_text">' + 'Project ID: ' + selectedProjectID + '</div>';
    document.getElementById("projname_text").innerHTML = '<div id="projname_text">' + 'Project Name: ' + projectname + '</div>';


    for (var taskrow in allTasks) { //for every task returned, add each to the table
      addtaskTotable(allTasks[taskrow][0],allTasks[taskrow][1],allTasks[taskrow][2],allTasks[taskrow][3],allTasks[taskrow][4],allTasks[taskrow][5],allTasks[taskrow][6]);
    }


    for (var staffrow in allStaff) { //for every staff returned, add each to the table
      addStaffTotable(allStaff[staffrow][0],allStaff[staffrow][1],allStaff[staffrow][2],allStaff[staffrow][3]);
    }
  }
}
});




function openaddstaffForm() {

$.ajax({
  type: "POST",
  url: "loadstaff_not_in_proj.php",
  data: {
    ID: selectedProjectID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {	
      
      var allStaff = JSON.parse(response); 

      var selectDropdown = document.getElementById("select_addstaff");
      //we want to overwrite the select/reset it each time so we dont just add to what was already there
      selectDropdown.innerHTML = '<option value="" disabled selected>Select staff member to add to project</option>';


      for (var staffrow in allStaff) { //for every staff returned, add each to the table

        var option = document.createElement("option");

          // Set the value of the option to user_id
          option.value = allStaff[staffrow][0];
          
          // Concatenate name and email and set it as the text of the option
          option.text = "#" + allStaff[staffrow][0] + "        |         " + allStaff[staffrow][1] + "         |         " + allStaff[staffrow][2];
          
          selectDropdown.appendChild(option);
      }
    }
  }
});

// now staff not in the team are loaded into the <select> we are going to open the form
document.getElementById("addstaffopaquebg").style.display = "block";

}

function addstaff_toteam(){

event.preventDefault();

var selectedUserID = document.getElementById("select_addstaff").value;

$.ajax({
  type: "POST",
  url: "add_team_member.php",
  data: {
    projectID: selectedProjectID,
    userID: selectedUserID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } 

    window.location.href = "managerdash.html?selected_project_ID="+selectedProjectID;
  }
});

document.getElementById("addstaffopaquebg").style.display = "none";
}

function closeaddstaffForm(){
document.getElementById("addstaffopaquebg").style.display = "none";
}

function openchangeroleForm() {

$.ajax({
  type: "POST",
  url: "loadstaff_in_proj.php",
  data: {
    ID: selectedProjectID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {	

      var allStaff = JSON.parse(response);

      var selectDropdown = document.getElementById("select_changerole");
      //we want to overwrite the select/reset it each time so we dont just add to what was already there
      selectDropdown.innerHTML = '<option value="" disabled selected>Select staff member to add to project</option>';


      for (var staffrow in allStaff) { //for every staff returned, add each to the table

        var option = document.createElement("option");

          // Set the value of the option to user_id
          option.value = allStaff[staffrow][0];
          
          // Concatenate name and email and set it as the text of the option
          option.text = "#" + allStaff[staffrow][0] + "        |         " + allStaff[staffrow][1] + "         |         " + allStaff[staffrow][2];
          
          selectDropdown.appendChild(option);
      }
    }
  }
});

document.getElementById("changeroleopaquebg").style.display = "block";
}

function maketeamleader(){

event.preventDefault();

var selectedUserID = document.getElementById("select_changerole").value;

$.ajax({
  type: "POST",
  url: "make_teamleader.php",
  data: {
    projectID: selectedProjectID,
    userID: selectedUserID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } 

    window.location.href = "managerdash.html?selected_project_ID="+selectedProjectID;
  }
});

document.getElementById("changeroleopaquebg").style.display = "none";
}

function closechangeroleForm() {
document.getElementById("changeroleopaquebg").style.display = "none";
}

function openremovestaffForm(){
$.ajax({
  type: "POST",
  url: "loadstaff_in_proj.php",
  data: {
    ID: selectedProjectID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {	

      var allStaff = JSON.parse(response);

      var selectDropdown = document.getElementById("select_removestaff");
      //we want to overwrite the select/reset it each time so we dont just add to what was already there
      selectDropdown.innerHTML = '<option value="" disabled selected>Select staff member to add to project</option>';


      for (var staffrow in allStaff) { //for every staff returned, add each to the table

        var option = document.createElement("option");

          // Set the value of the option to user_id
          option.value = allStaff[staffrow][0];
          
          // Concatenate name and email and set it as the text of the option
          option.text = "#" + allStaff[staffrow][0] + "        |         " + allStaff[staffrow][1] + "         |         " + allStaff[staffrow][2];
          
          selectDropdown.appendChild(option);
      }
    }
  }
});

// now staff not in the team are loaded into the <select> we are going to open the form
document.getElementById("removestaffopaquebg").style.display = "block";
}

function remove_fromteam(){

event.preventDefault();

var selectedUserID = document.getElementById("select_removestaff").value;

$.ajax({
  type: "POST",
  url: "remove_teammember.php",
  data: {
    projectID: selectedProjectID,
    userID: selectedUserID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } 

    window.location.href = "managerdash.html?selected_project_ID="+selectedProjectID;
  }
});

document.getElementById("removestaffopaquebg").style.display = "none";
}

function closeremovestaffForm() {
document.getElementById("removestaffopaquebg").style.display = "none";
}

function openaddtaskForm() {

$.ajax({
  type: "POST",
  url: "find_new_taskID.php",
  data: {
    ID: selectedProjectID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {	

      var newtaskID = JSON.parse(response);
      var newtaskID_textbox = document.getElementById("new_taskID");

      
      newtaskID_textbox.value = newtaskID;


      $.ajax({
        type: "POST",
        url: "loadstaff_in_proj.php",
        data: {
          ID: selectedProjectID
        },
        success: function (response) {
          if (response === "invalid") {
            alert("Something went wrong");
          } else {	
    
            var allStaff = JSON.parse(response);
    
            var selectDropdown = document.getElementById("new_choosestaff_select");
            //we want to overwrite the select/reset it each time so we dont just add to what was already there
            selectDropdown.innerHTML = '';
    
    
            for (var staffrow in allStaff) { //for every staff returned, add each to the table
    
              var option = document.createElement("option");
    
                // Set the value of the option to user_id
                option.value = allStaff[staffrow][0];
                
                // Concatenate name and email and set it as the text of the option
                option.text = "#" + allStaff[staffrow][0] + "        |         " + allStaff[staffrow][1] + "         |         " + allStaff[staffrow][2];
                
                selectDropdown.appendChild(option);
            }
    
          //placed inside the success so that only displays form after loaded
          document.getElementById("addtaskopaquebg").style.display = "block";
          }
        }
      });
    }
  }
});
}

function isValidDateString(str) {
// Split the string by hyphens
var parts = str.split('-');

if (parts.length !== 3) {
  return false;
}

// Check if each part is a valid integer
for (var i = 0; i < 3; i++) {
  var num = parseInt(parts[i], 10);

  // Check if the parsed number is a valid integer
  if (isNaN(num)) {
    return false;
  }
}

var year = parseInt(parts[0], 10);
var month = parseInt(parts[1], 10);
var day = parseInt(parts[2], 10);

if (year < 1000 || year > 9999 || month < 1 || month > 12 || day < 1 || day > 31) {
  return false;
}

return true;
}

function addnewtask() {

event.preventDefault();

var newtaskID = document.getElementById("new_taskID").value;
var newtaskName = document.getElementById("new_taskname").value;
var newtaskStatus = document.getElementById("new_statusselect").value; 
var newhrs = document.getElementById("new_hrs").value; 
var newtaskDeadline = document.getElementById("new_deadline").value; 

//to create an array of all staff assigned to this task
var selectdropdown = document.getElementById("new_choosestaff_select");
var selectedOptions = selectdropdown.selectedOptions;
var newtaskStaff = [];
for (var i = 0; i < selectedOptions.length; i++) {
  newtaskStaff.push(selectedOptions[i].value);
}



if(isValidDateString(newtaskDeadline)){
$.ajax({
  type: "POST",
  url: "add_new_task.php",
  data: {
    projectID: selectedProjectID,
    taskID: newtaskID,
    name: newtaskName,
    status: newtaskStatus,
    hrs: newhrs,
    deadline: newtaskDeadline,
    staff: newtaskStaff
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } 

    window.location.href = "managerdash.html?selected_project_ID="+selectedProjectID;
  }
});

document.getElementById("addtaskopaquebg").style.display = "none";
} else{
alert("Invalid date entered");
}
}

function closeaddtaskForm() {
document.getElementById("addtaskopaquebg").style.display = "none";
}

var current_tasks_id; // Declare a global variable to store the current task ID

function opentasknotesForm(button) {
var row = button.parentNode.parentNode;

// Get the data from the cells of the row
var this_tasks_id = row.cells[0].innerText;

$.ajax({
  type: "POST",
  url: "load_tasknotes.php",
  data: {
    ID: selectedProjectID,
    taskid: this_tasks_id
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {  
      var task_notes = JSON.parse(response);

      // Store the task ID in the global variable
      current_tasks_id = this_tasks_id;

      document.getElementById("tasknotescontent").value = task_notes;

      // Show the task notes form
      document.getElementById("tasknotesopaquebg").style.display = "block";
    }
  },
});
}

function updatetasknotes() {
event.preventDefault();
var tasknotes = document.getElementById("tasknotescontent").value;

var confirmation = confirm("Are you sure you want to update the task notes?");

if(confirmation){
  $.ajax({
      type: "POST",
      url: "change_tasknotes.php",
      data: {
        projectID: selectedProjectID,
        taskid: current_tasks_id, // Use the current_tasks_id variable
        notes: tasknotes
      },
      success: function (response) {
        if (response === "invalid") {
          alert("Something went wrong");
        } else {
        
            // Hide the task notes form
            document.getElementById("tasknotesopaquebg").style.display = "none";
            // Redirect to the manager dashboard
            window.location.href = "managerdash.html?selected_project_ID=" + selectedProjectID;
        }
      },
    });
}
}

function closetasknotesForm() {
document.getElementById("tasknotesopaquebg").style.display = "none";
}

function openedittaskForm(button){
var row = button.parentNode.parentNode;

// Get the data from the cells of the row
var row_id = row.cells[0].innerText;
var row_taskname = row.cells[1].innerText;
var row_hrs = row.cells[2].innerText;
var row_status =  row.cells[3].innerText;
var row_deadline = row.cells[4].innerText;


document.getElementById("edit_taskID").value = row_id;
document.getElementById("edit_taskname").value = row_taskname;
document.getElementById("edit_hrs").value = row_hrs;
document.getElementById("edit_statusselect").value = row_status;
document.getElementById("edit_deadline").value = row_deadline;

$.ajax({
  type: "POST",
  url: "loadstaff_in_proj.php",
  data: {
    ID: selectedProjectID
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {	

      var allStaff = JSON.parse(response);

      var selectDropdown = document.getElementById("edit_choosestaff");
      //we want to overwrite the select/reset it each time so we dont just add to what was already there
      selectDropdown.innerHTML = '';


      for (var staffrow in allStaff) { //for every staff returned, add each to the table

        var option = document.createElement("option");

          // Set the value of the option to user_id
          option.value = allStaff[staffrow][0];
          
          // Concatenate name and email and set it as the text of the option
          option.text = "#" + allStaff[staffrow][0] + "        |         " + allStaff[staffrow][1] + "         |         " + allStaff[staffrow][2];
          
          selectDropdown.appendChild(option);
      }

    //placed inside the success so that only displays form after loaded
    document.getElementById("edittaskopaquebg").style.display = "block";
    }
  }
});
}

function save_changesto_task(){
event.preventDefault();

var task_id = document.getElementById("edit_taskID").value;
var task_name = document.getElementById("edit_taskname").value;
var task_hrs = document.getElementById("edit_hrs").value;
var task_status = document.getElementById("edit_statusselect").value;
var task_deadline = document.getElementById("edit_deadline").value;

//to create an array of all staff assigned to this task
var selectdropdown = document.getElementById("edit_choosestaff");
var selectedOptions = selectdropdown.selectedOptions;
var newtaskStaff = [];
for (var i = 0; i < selectedOptions.length; i++) {
  newtaskStaff.push(selectedOptions[i].value);
}


var confirmation = confirm("Are you sure you want to change task details?");

if(confirmation){
  $.ajax({
      type: "POST",
      url: "change_task_details.php",
      data: {
        projectID: selectedProjectID,
        taskID: task_id,
        name: task_name,
        status: task_status,
        hrs: task_hrs,
        deadline: task_deadline,
        staff: newtaskStaff
      },
      success: function (response) {
        if (response === "invalid") {
          alert("Something went wrong");
        } else {
            
            // Redirect to the manager dashboard
            window.location.href = "managerdash.html?selected_project_ID=" + selectedProjectID;

        }
      },
    });
}
document.getElementById("edittaskopaquebg").style.display = "none";
}

function delete_task(){
var task_id = document.getElementById("edit_taskID").value;

var confirmation = confirm("Are you sure you want to delete this task?");

if(confirmation){
  $.ajax({
      type: "POST",
      url: "delete_task.php",
      data: {
        projectID: selectedProjectID,
        taskID: task_id,
      },
      success: function (response) {
        if (response === "invalid") {
          alert("Something went wrong");
        } else {
        
            // Hide the task notes form
            document.getElementById("edittaskopaquebg").style.display = "none";
            // Redirect to the manager dashboard
            window.location.href = "managerdash.html?selected_project_ID=" + selectedProjectID;
        }
      },
    });
}
}

function closeedittaskForm() {
document.getElementById("edittaskopaquebg").style.display = "none";
}



function deleteProject(){
  event.preventDefault();

  var confirmation = confirm("Are you sure you want to delete this Project?");

  if(confirmation){
    $.ajax({
        type: "POST",
        url: "delete_project.php",
        data: {
          projectID: selectedProjectID
        },
        success: function (response) {
          if (response === "invalid") {
            alert("Something went wrong");
          } else {
            window.location.href = "accessproject.php";
          }
        },
      });
  }
}


function openeditprojectForm(){
  document.getElementById("edit_projID").value = selectedProjectID;
  document.getElementById("edit_projname").value = projectname;
  document.getElementById("editprojopaquebg").style.display = "block";
}

function edit_projectname(){

  event.preventDefault();

  var new_projname = document.getElementById("edit_projname").value;
  var confirmation = confirm("Are you sure you want to rename this project?");
  
  if(confirmation){
    $.ajax({
        type: "POST",
        url: "change_proj_name.php",
        data: {
          projectID: selectedProjectID,
          projname: new_projname,
        },
        success: function (response) {
          if (response === "invalid") {
            alert("Something went wrong");
          } else {
              // Hide the task notes form
              document.getElementById("editprojopaquebg").style.display = "none";
              // Redirect to the manager dashboard
              window.location.href = "managerdash.html?selected_project_ID=" + selectedProjectID;
          }
        },
      });
  }
}

function closeeditprojectForm(){
  document.getElementById("editprojopaquebg").style.display = "none";
}







function toggleTaskSortbyDropdown() {
document.getElementById("tasksortbydropdown").classList.toggle("show");
}





function sorttasks_byAlphabetical() {
var table = document.getElementById("taskstable");
var rows = table.rows;
var switching = true;

while (switching) {
    switching = false;
    for (var i = 1; i < (rows.length - 1); i++) {
        var shouldSwitch = false;
        var currentCell = rows[i].getElementsByTagName("td")[1]; 
        var nextCell = rows[i + 1].getElementsByTagName("td")[1]; 
        if (currentCell.innerHTML.toLowerCase() > nextCell.innerHTML.toLowerCase()) {
            shouldSwitch = true;
            break;
        }
    }
    if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
    }
}
}

function customSort(a, b) {
const order = { "Overdue": 0, "On Track": 1, "Not Started": 2, "Completed": 3 };
return order[a.cells[3].innerText] - order[b.cells[3].innerText];
}

function sorttasks_byCompletion() {
const table = document.getElementById("taskstable");
const tbody = table.querySelector("tbody");
const rows = Array.from(tbody.querySelectorAll("tr"));

rows.sort(customSort);

tbody.innerHTML = ""; // Clear the existing table body

rows.forEach(row => {
  tbody.appendChild(row); // Append sorted rows to the table body
});
}

function sorttasks_byDeadline(){
  var table = document.getElementById("taskstable");
  var rows = Array.from(table.rows).slice(1); 

  rows.sort(function(a, b) {
      var dateA = new Date(a.cells[4].innerText);
      var dateB = new Date(b.cells[4].innerText);

      return dateA - dateB;
  });

  // Reorder the rows in the table
  rows.forEach(function(row) {
      table.appendChild(row);
  });
}

function find_statusquantities(callback) {
$.ajax({
  type: "POST",
  url: "get_tasks_forpie.php",
  data: {
    ID: selectedProjectID,
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {
      var statusquantities = JSON.parse(response);
      callback(statusquantities);
    }
  }
});
}

// we want to refresh/load this pie chart whenever the window laods- however, before it loads we have to get task summaries
$(document).ready(function() {
find_statusquantities(function(statusquantities) {
  var statustypes = ["Completed", "OnTrack", "Overdue", "Not Started"];
  var barColors = ["#52ab0c", "#efbf1a", "#ab450c", "#646c6c"];

  new Chart("piechart", {
    type: "doughnut",
    data: {
      labels: statustypes,
      datasets: [{
        backgroundColor: barColors,
        data: statusquantities
      }]
    },
    options: {
      legend: { display: false },
    }
  });
});
});


//finds the workload of each member of staff- then returns it and uses this array as the data for the workload chart
function find_workload(callback) {
$.ajax({
  type: "POST",
  url: "get_workload.php",
  data: {
    ID: selectedProjectID,
  },
  success: function (response) {
    if (response === "invalid") {
      alert("Something went wrong");
    } else {
      var data = JSON.parse(response);
      callback(data[0], data[1]);
    }
  }
});
}

find_workload(function(workload, staffnames) {
new Chart("workload-chart", {
  type: "horizontalBar",
  data: {
    labels: staffnames,
    datasets: [{
      backgroundColor: "#efbf1a",
      data: workload
    }]
  },
  options: {
    legend: { display: false },
    scales: {
      xAxes: [{
        scaleLabel: {
          display: true,
          labelString: 'Hrs of Work Remaining',
          fontColor: "white",
          fontSize: 18
        },
        gridLines: { display: false },
        ticks: { display: false, min: 0, max: 80 }
      }],
      yAxes: [{
        gridLines: { display: false },
        ticks: { fontColor: "white", fontSize: 18 }
      }]
    }
  }
});
});









