var toDoListArray;
  var user_id = 3;
  var max_item_id = 0;

  if (sessionStorage.getItem("user")){
    user_id = sessionStorage.getItem("user")["id"];
  }


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

})

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



// Add a "checked" indicator when clicking on a list item
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


