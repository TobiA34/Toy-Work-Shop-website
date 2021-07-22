<?php

// This script allows a user to edit the contents of their toy LIST
// They can ADD and REMOVE the toys that are in the TOYS DB table

// execute the header script:
require_once "header.php";
require_once "credentials.php";

// Set some of the jQuery / AJAX parameters
// how many milliseconds to wait between updates:
// in this case, set to half-a-second
$milliseconds = 1000;

$username = $_SESSION['username'];

if (isset($_GET['listID'])) {
    $_SESSION['listID'] = $_GET['listID'];
}


echo <<<_END
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="jquery.min.js"></script>
_END;

// some styling for the tables
echo <<<_END
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}
td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 4px;
}
</style>

    <body>
        <div class="main">
        <h3 id="heading">List Editing</h3>     
        <table id="editing">
        <tr><th>Toy</th><th>Description</th><th>Price</th><th>Action</th></tr>
        
_END;



// what to do if the user chooses to add a NEW toy to the current LIST
if (isset($_GET['addNewToy'])) {

    $toyID = $_GET['addNewToy'];

    ///////////////////////////////////////
    // JAVASCRIPT TO ADD A TOY TO A LIST //
    ///////////////////////////////////////

    echo <<<_END
    <script>
    // make a javascript object (JSON) to hold our request data:
        var requestAdd = {};
             requestAdd['listID'] = '{$_SESSION['listID']}';
             requestAdd['toyID'] = '$toyID';
             
        // initiate the POST request to the API script
        $.post('api/add_item.php', requestAdd)
         // if the request is successful (done) we don't need to do anything else
         // since the API script updates the database table
        // and this JS later refreshes the table contents
         .done(function(data) {
           // debug message to help during development:
          console.log('request successful ' + requestAdd.listID + ' updated');
           })
         // if the request fails, note the error status in the console window
        .fail(function(jqXHR) {
           // debug message to help during development:
                  console.log('request returned failure, HTTP status code ' + jqXHR.status);
           })
              // regardless of the done/fail outcome, we will always do the following
           .always(function() {
          // debug message to help during development:
              console.log('request completed');
            });
    </script>
_END;

}


// get the list of available toys to populate drop-down list
// connect directly to our database
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
// connection failed, return an error message
if (!$connection)
{
    die("Connection failed: " . $mysqli_connect_error);
}
$query = "SELECT id, toyName FROM toys ORDER BY toyName";
// this query can return data ($result is an identifier):
$result = mysqli_query($connection, $query);
// how many rows came back?:
$n = mysqli_num_rows($result);

// if we got some results then add them all into a big array:
if ($n > 0)
{
    echo "<form action=\"edit_list.php?listID='{$_SESSION['listID']}'\" method=\"get\">";
    echo "<tr><td></td><td></td><td></td><td><select id='addNewToy' name='addNewToy'>";
    // loop over all rows, adding them into our array:
    for ($i=0; $i<$n; $i++)
    {
        // fetch one row as an associative array (elements named after columns):
        $thisRow = mysqli_fetch_assoc($result);
        echo "<option value='{$thisRow['id']}'>{$thisRow['toyName']}</option>";

    }
    echo "</select><br><input type=\"submit\" value=\"Add Toy\"></td></tr></form></td></tr></table>";

}
else {
    echo "empty toys list<br>";
}
// we're finished with the database, close the connection:
mysqli_close($connection);



/////////////////////////////////////////////////////////////
// JAVASCRIPT TO REFRESH THE CURRENT LIST AND ITS CONTENTS //
/////////////////////////////////////////////////////////////

echo <<<_END
<script>
// wait for the script to load in the browser
$(document).ready(function()
{
    // start checking for updates:
    updateList();
});

function updateList(){
    
    var requestData = {};
    requestData['username'] = '$username';
    requestData['id'] = '{$_SESSION['listID']}';  
    
    
	// make the request to the API for the list of contents in a given list	
    $.post('api/get_list.php', requestData)
		.done(function(data) {
			// debug message to help during development:
			console.log('request successful');
			
			// remove the old table rows:
			$('.result').remove();	
			// loop through what we got and add it to the table (data is already a JavaScript object thanks to getJSON()):
			$.each(data, function(index, value) {
				$('#editing').append("<tr class='result'><td>" + value.toyName + "</td><td>" + value.description + "</td><td>" + value.price + "</td><td><button data-title='" + value.pairID + "'>Delete</button></td></tr>");
			});
			
		})
		.fail(function(jqXHR) {
			// debug message to help during development:
			console.log('request returned failure, HTTP status code ' + jqXHR.status);
		})
		.always(function() {
			// debug message to help during development:
			console.log('request completed');
			
			//////////////////////////////////////////////
			// JS to allow the DELETE button link to be clicked and respond
			/////////////////////////////////////////////
            $("#editing button").click(function(event) {
            // this stops the user being taken to a different screen, for instance
             event.preventDefault(); 
                                    
             // make a javascript object (JSON) to hold our request data:
             // in this case, we want to send the 'pairID' parameter that matches a TOY to a LIST
             // this will be sent with the request to delete a toy from the list, if clicked
             var request = {};
             request["pairID"] = $(this).data('title');
             console.log('button clicked'+request[0]);
                            
             // initiate the POST request to the API script
              $.post('api/del_item.php', request)
              // if the request is successful (done) we don't need to do anything else
              // since the API script updates the database table
              // and this JS later refreshes the table contents
              .done(function(data) {
              // debug message to help during development:
                                console.log('request successful ' + data.pairID + ' updated');                
              })
               // if the request fails, note the error status in the console window
               .fail(function(jqXHR) {
                // debug message to help during development:
                 console.log('request returned failure, HTTP status code ' + jqXHR.status);
                })
                 // regardless of the done/fail outcome, we will always do the following
              .always(function() {
               // debug message to help during development:
               console.log('request completed');
                });
                        
             }); 	
                       
			
			// call this function again after a brief pause:
			setTimeout(updateList, $milliseconds);
		});    
    
}
</script>


_END;


// finish of the HTML for this page:
require_once "footer.php";

?>