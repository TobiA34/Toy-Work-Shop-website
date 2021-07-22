<?php

// This script allows the signed-in user to see their ACCOUNT details
// It provides them with links to make changes and updates
// They can also create, edit and delete lists from here

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

    // user is logged in - show account information
    if (isset($_SESSION['loggedIn']))  {


        // is this a delete operation? If so, do it!
        if ((isset($_GET['op'])) && ($_GET['op']=="delete")) {
            delList($dbhost, $dbuser, $dbpass, $dbname, $dbport, $_GET['id']);
        }


        // connect to the host:
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
        // exit the script with a useful message if there was an error:
        if (!$connection)
        {
            die("Connection failed: " . $mysqli_connect_error);
        }

        // connect to our database:
        mysqli_select_db($connection, $dbname);

        // run query to get the user details
        $query = "SELECT firstName, lastName, dob, email, telephone, picture, lastLogin FROM users WHERE username='{$_SESSION['username']}'";
        // this query can return data ($result is an identifier):
        $result = mysqli_query($connection, $query);
        // how many rows came back?:
        $n = mysqli_num_rows($result);

        // run query to get the user toy lists
        $query2 = "SELECT id, name, created, updated, visible FROM lists WHERE username='{$_SESSION['username']}'";
        // this query can return data ($result is an identifier):
        $result2 = mysqli_query($connection, $query2);
        // how many rows came back?:
        $n2 = mysqli_num_rows($result2);

        if ($n==1) {
                $row = mysqli_fetch_assoc($result);
        }

        echo <<<_END
    
        <!-- account information -->
         <div class="generalInfo"><fieldset><legend><h2>Account: {$_SESSION['username']}</h2></legend><table>
         <tr><th>First Name</th><td>{$row['firstName']}</td></tr>
         <tr><th>Last Name</th><td>{$row['lastName']}</td></tr>
         <tr><th>DOB</><td>{$row['dob']}</td></tr>
         <tr><th>Email</th><td>{$row['email']}</td></tr>
         <tr><th>Telephone</th><td>{$row['telephone']}</td></tr>
         <tr><th>Last login</th><td>{$row['lastLogin']}</td></tr>
         <tr><th>Picture</th><td><img height="100" alt="{$_SESSION['username']}" src="{$row['picture']}"></td></tr>
         <tr><th>Update Account</th><td><a href="my_account.php">Click here</a> to change your details<br><a href="my_password.php">Click here</a> to change your password</td></tr>
         </table></fieldset>
         
        <!-- toy lists -->         
        <fieldset><legend><h2>My Lists: {$_SESSION['firstName']}</h2></legend><table>
        <tr><th>Name</th><th>Created</th><th>Updated</th><th>Shared</th><th>Actions</th></tr>
_END;

        if ($n2>0) {
            for ($i = 0; $i < $n2; $i++) {
                $row2 = mysqli_fetch_assoc($result2);
                if ($row2['visible']) {
                    $shared = "Yes";
                } else $shared = "No";

                echo "<tr><td>{$row2['name']}</td><td>{$row2['created']}</td><td>{$row2['updated']}</td>
            <td>$shared</td><td><a href='edit_list.php?listID={$row2['id']}'>Edit<a/> || <a href='account.php?op=delete&id={$row2['id']}'>Delete</a></td></tr>";
            }
        }
        echo "<tr><td><td></td></td><td></td></td><td><td><a href='new_list.php'>Add New List</a></td></tr>";
         echo "</table></fieldset></div>";
        // we're finished with the database, close the connection:
        mysqli_close($connection);
    }

    else {
        echo "Sorry, you must be a registered user to access this resource.<br>Create an account <a href='signup.php'>here</a>";
    }


//////////////////////////
// DELETE LIST FUNCTION //
//////////////////////////

function delList ($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field) {
    // connect to the host:
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }
    // connect to our database:
    mysqli_select_db($connection, $dbname);

    //get rid of any contents linking before removing the list
    $query = "DELETE FROM contents WHERE listID='$field'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "deleted - list contents<br>";
    }
    else {
        echo "List contents not deleted - error";
    }

    $query = "DELETE FROM lists WHERE id='$field' AND username='{$_SESSION['username']}'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "deleted - list<br>";
    }
    else {
        echo "List not deleted - error";
    }

    // we're finished with the database, close the connection:
    mysqli_close($connection);
}


// finish of the HTML for this page:
require_once "footer.php";

?>