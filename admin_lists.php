<?php

// This script lets the ADMIN user see all toy LISTS and links to edit/delete them
// The admin can also have a link to create a NEW list

// execute the header script:
require_once "header.php";
// read in the details of our MySQL server:
require_once "credentials.php";

// default field to sort lists by
$sortLists = "username";
$sort = "ASC";

// switch the table of users off and on
$showLists = true;

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

// check that the current user is the ADMIN
if (isset($_SESSION['loggedIn']) && ($_SESSION['username']=='admin')) {

        // see if the user has clicked a field to sort the user
        if (isset($_GET['sortLists']) && isset($_GET['sort'])) {
            $sortLists = $_GET['sortLists'];
            $sort = $_GET['sort'];

            if ($sort=="ASC") {
                $sort = "DESC";
            }
            else {
                $sort = "ASC";
            }
        }


        else if (isset($_GET['sortLists'])) {
            $sortLists = $_GET['sortLists'];
        }

        else if (isset($_GET['op']) && (isset($_GET['id']))) {

            // is this a delete operation? If so, do it!
            if ($_GET['op']=="delete") {
                delLists($dbhost, $dbuser, $dbpass, $dbname, $dbport, $_GET['id']);
                $showLists = true;
            }

        }

        // when true, will SORT and DISPLAY the lists in the browser
        if ($showLists) {
            // call the function that sorts and displays the users in the table
            sortLists($dbhost, $dbuser, $dbpass, $dbname, $dbport, $sortLists, $sort);
        }

    }

    else {
        echo "Sorry, you must be an administrator to access this resource";
    }


    /////////////////////////
    // SORT LISTS FUNCTION //
    /////////////////////////
 function sortLists($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field, $sort) {

    // connect to the host:
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }

    // connect to our database:
    mysqli_select_db($connection, $dbname);
    // run query to get the contents of the LISTS table
    $query = "SELECT * FROM lists ORDER BY $field $sort";
    // this query can return data ($result is an identifier):
    $result = mysqli_query($connection, $query);
    // how many rows came back?:
    $n = mysqli_num_rows($result);

     //format a table and layout
     echo "<div class=\"generalInfo\"><fieldset><legend><h2>Manage Lists</h2></legend><table>";
     echo "<tr><th><a href='admin_lists.php?sortLists=id&sort={$sort}'>ID</a></th><th><a href='admin_lists.php?sortLists=username&sort={$sort}'>Owner</a></th>
            <th><a href='admin_lists.php?sortLists=name&sort={$sort}'>Name</a></th><th>Public</th>
            <th><a href='admin_lists.php?sortLists=updated&sort={$sort}'>Updated</a></th><th>Action</th></tr>";

    if ($n>0) {

        for ($i = 0; $i < $n; $i++) {
            $row = mysqli_fetch_assoc($result);

            if ($row['visible']) {
                $shared = "Yes";
            } else $shared = "No";

            echo <<<_END
                <tr><td>{$row['id']}</td><td>{$row['username']}</td><td>{$row['name']}</td>
                <td>$shared</td><td>{$row['updated']}</td>
                <td><a href="edit_list.php?op=edit&listID={$row['id']}">Edit</a> || <a href="admin_lists.php?op=delete&id={$row['id']}">Delete</a></td></tr>
_END;

        }

        echo "<tr><td><td></td></td><td></td></td><td><td></td><td><a href='new_list.php'>Add New List</a></td></tr>";
        echo "<tr><td><td></td></td><td></td></td><td><td></td><td><a href='download.php?download=lists'>Download CSV</a></td></tr>";
        // complete formatting
        echo "</table></fieldset></div>";

    }

    else {
        echo "No information found in users table";
    }
     // we're finished with the database, close the connection:
     mysqli_close($connection);
}


        ///////////////////////////
        // DELETE LISTS FUNCTION //
        ///////////////////////////

function delLists ($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field) {
    // connect to the host:
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }

    // connect to our database:
    mysqli_select_db($connection, $dbname);

    // get rid of any contents linking before removing the LIST
    $query = "DELETE FROM contents WHERE listID='$field'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "deleted - list contents<br>";
    }
    else {
        echo "List contents not deleted - error";
    }

    // run query to remove the LIST
    $query = "DELETE FROM lists WHERE id='$field'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "deleted";
    }
    else {
        echo "Not deleted - error";
    }
    // we're finished with the database, close the connection:
    mysqli_close($connection);
}


// finish of the HTML for this page:
require_once "footer.php";

?>