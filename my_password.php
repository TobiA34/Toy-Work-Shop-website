<?php

// This script allows a validated user to change their site password
// Doing so makes a call to the my_password.php API script
// This facilitated via some jQuery / AJAX

// execute the header script:
require_once "header.php";
require_once "credentials.php";

// Salts to be added to the start and end of the password when entered
$preSalt = "QpfE9q";
$postSalt = "WdcDXi";

// create the basic HTML and form on the page

echo <<<_END
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
 <form action="my_password.php" method="post">
 <div class="generalInfo"><fieldset><legend><h2>Change Password: {$_SESSION['username']}</h2></legend>
     <table cellpadding="2">
     <tr><td alig="right">Enter current password: </td> <td><input type="password" minlength="6" maxlength="40" name="currentPW" required></td></tr>
     <tr><td align="right">Enter new password: </td> <td><input type="password" minlength="6" maxlength="40" name="newPW" required></td></tr>
     <tr><td align="right">Confirm new password: </td> <td><input type="password" minlength="6" maxlength="40" name="newPW2" required></td></tr>
     <tr><td><input type="submit" value="Change Password"></td></tr>
    </table>
</form>
<br><div id='results'></div>
_END;

// the user has just submitted the form to try to call the password.php API
// they should have included a username, current password and a new password
if (isset($_POST['currentPW']) && isset($_POST['newPW']) && isset($_POST['newPW2'])) {

    $username = $_SESSION['username'];
    $currentPW = $_POST['currentPW'];
    $newPW = $_POST['newPW'];
    $newPW2 = $_POST['newPW2'];

    // check the two NEW passwords are the same
    if ($newPW==$newPW2) {

    // connect directly to our database (notice 4th argument):
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // if the connection fails, we need to know, so allow this exit:
    if (!$connection) {
        die("Connection failed: " . $mysqli_connect_error);
    }
    /////////////////////////////////////////
    //////// SERVER-SIDE VALIDATION /////////
    /////////////////////////////////////////
    $errors = "";
    // First, sanitise the user input (functions in helper.php)
    $username = sanitise($username, $connection);
    $currentPW = sanitise($currentPW, $connection);
    $newPW = sanitise($newPW, $connection);
    // Next, validate the user input (functions in helper.php)
    $username_errors = validateString($username, 1, 32);
    $currentPW_errors = validateString($currentPW, 6, 40);
    $newPW_errors = validateString($newPW, 1, 16);
    // concatenate the errors from both validation calls
    $errors = $username_errors . $currentPW_errors . $newPW_errors;

        if ($errors == "") {

            // SALT the passwords and HASH them
            $currentPW = sha1($preSalt . $currentPW .$postSalt);
            $newPW = sha1($preSalt . $newPW .$postSalt);

            // execute the client request to the API using JQuery
            echo <<<_END
                <script>
                // wait for the script to load in the browser
                $(document).ready(function()
                {
                    var requestData = {};
                    requestData['username'] = '$username';
                    requestData['currentPW'] = '$currentPW';
                    requestData['newPW'] = '$newPW';             
                    
                    // run the getJSON query, sending the username from the HTML form
                    $.post('api/password.php', requestData)
                        .done(function(data) {
                            // debug message to help during development:
                            console.log('password change request successful');
                                                  // show the result from the API in the field named 'results' in the page HTML
                             $('#results').append("<b>Password updated: </b>" + requestData.username + "<br>");     
                         })
                            
                        .fail(function(jqXHR) {
                            // debug message to help during development:
                            console.log('request returned failure, HTTP status code ' + jqXHR.status);
                            // display some error text on the page
                            $('#results').append("<b>Update failed</b> <br>");
                        })
                        
                        .always(function() {
                            // debug message to help during development:
                            console.log('request completed');
                        });
                 });
                </script>
_END;

        }

        else {
            echo "<br>Server-side validation failed<br>";
        }
}

    // if the two NEW passwords don't match then show an error message
    else {
            echo "<b>Passwords do not match</b>";
    }

}

echo "</fieldset></div>";

// execute the footer script:
require_once "footer.php";

?>