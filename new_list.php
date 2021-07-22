<?php

// This script allows a user to create a new LIST that contains TOYS
// If the user is the ADMIN, they can create a list on behalf of ANY user

// execute the header script:
require_once "header.php";
require_once "credentials.php";

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
_END;

// setup variables to help with functionality and validation of data
$showList = false;
// error messages to display about each field to be used for combination of all server-side errors
$errors = $name_errors = $username_errors = $visible_errors = "";

// check that the current user is logged-in
if (isset($_SESSION['loggedIn']))
    {
        // do this if submitting
        if (isset($_POST['newList'])) {

            $name = $_POST['name'];
            $username = $_POST['username'];
            $visible = 0;

            if (isset($_POST['visible'])) {
                $visible = 1;
            }
            else {
                $visible = 0;
            }

            // connect to the host:
            $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
            // exit the script with a useful message if there was an error:
            if (!$connection) {
                die("Connection failed: " . $mysqli_connect_error);
            }

            /////////////////////////////////////////
            //////// SERVER-SIDE VALIDATION /////////
            /////////////////////////////////////////
            // First, sanitise the user input (functions in helper.php)
            $name = sanitise($name, $connection);
            $username = sanitise($username, $connection);

            // Next, validate the user input (functions in helper.php)
            $name_errors = validateString($name, 1, 64);
            $username_errors = validateString($username, 1, 32);


            // concatenate the errors from both validation calls
            $errors = $name_errors . $username_errors;

            if ($errors == "") {

                $dateNow = date("Y-m-d");

                $sql = "INSERT INTO lists (name, username, created, updated, visible) VALUES ('$name', '$username', '$dateNow', '$dateNow', '$visible')";

                // no data returned, we just test for true(success)/false(failure):
                if (mysqli_query($connection, $sql)) {
                    echo "New List Added";
                    $showList = true;
                }
                else {
                    die("Error inserting row: " . mysqli_error($connection));
                }
                // we're finished with the database, close the connection:
                mysqli_close($connection);
            }

            else {
                /// deal with server side errors";
                /// show form again, highlighting problems
                $showList = true;
            }
        }

        // user has arrived at the page for the first time
        else {
            $showList = true;
        }


        }

        else {
        echo "Sorry, you must <a href='login.php'>login</a> or be a registered user to access this resource.<br>Create an account <a href='signup.php'>here</a>";
    }



if ($showList) {
    echo <<<_END

        <form action="new_list.php" method="post" id="newList" enctype="multipart/form-data">
             <div class="generalInfo"><fieldset><legend><h2>Add a New List</h2></legend><table>
             <tr><th align="right">Name</th><td><input size="40" type="text" name="name" minlength="1" maxlength="64" required><b>{$name_errors}</b></td></tr>   
_END;


    // check to see if the current user is the ADMIN
    // the ADMIN has the ability to create a new LIST on behalf of ANY site user
    if ($_SESSION['username']=="admin") {
        // CONNECT TO DATABASE TO GET LIST OF USERNAMES
        // connect to the host:
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
        // exit the script with a useful message if there was an error:
        if (!$connection)
        {
            die("Connection failed: " . $mysqli_connect_error);
        }
        // connect to our database:
        mysqli_select_db($connection, $dbname);
        // run query to get the contents of the USERS table
        $query = "SELECT username FROM users";
        // this query can return data ($result is an identifier):
        $result = mysqli_query($connection, $query);
        // how many rows came back?:
        $n = mysqli_num_rows($result);
        echo "<tr><th align=\"right\">Owner</th><td><select name=\"username\" required>";
        if ($n>0) {
            for ($i = 0; $i < $n; $i++) {
                $row = mysqli_fetch_assoc($result);
                echo "<option value = \"{$row['username']}\">{$row['username']}</option>";

            }
            echo "</select>";
        }
    }

    else {
        echo "<tr><th align=\"right\">Owner</th><td><input size=\"40\" type=\"text\" name=\"username\" minlength=\"1\" maxlength=\"32\" value=\"{$_SESSION['username']}\" readonly required>";
    }

        // RESUME THE FORM INFORMATION
                echo <<<_END
                
            <b>{$username_errors}</b></td></tr>
             
             <tr><th align="right">Public?</th><td><input type="checkbox" name="visible" value="1">Yes</td></tr>
             </table>
                <input type="submit" name="newList" value="Add New List">
             </fieldset>
        
        </form>
_END;
}

// finish of the HTML for this page:
require_once "footer.php";

?>