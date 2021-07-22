<?php

// This script allows the ADMIN to edit the TOYS contained in the site DB
// It displays ALL toys and provides links to edit, delete and add new ones

// execute the header script:
require_once "header.php";
// read in the details of our MySQL server:
require_once "credentials.php";

// default field to sort toys by
$sortToys = "toyName";
$sort = "ASC";

// switch the table of toys off and on
$showToys = true;

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

        // see if the user has clicked a field to sort the toys
        if (isset($_GET['sortToys']) && isset($_GET['sort'])) {
            $sortToys = $_GET['sortToys'];
            $sort = $_GET['sort'];

            if ($sort=="ASC") {
                $sort = "DESC";
            }
            else {
                $sort = "ASC";
            }

        }

        else if (isset($_GET['sortToys'])) {
            $sortToys = $_GET['sortToys'];
        }

        else if (isset($_GET['op']) && (isset($_GET['id']))) {

            // is tis a delete operation? If so, do it!
            if ($_GET['op']=="delete") {
                delToy($dbhost, $dbuser, $dbpass, $dbname, $dbport, $_GET['id']);
                $showToys = true;
            }

        }

        // sort and display all of the TOYS when set to true
        if ($showToys) {
            // call the function that sorts and displays the toys in the table
            sortToys($dbhost, $dbuser, $dbpass, $dbname, $dbport, $sortToys, $sort);
        }

    }

    else {
        echo "Sorry, you must be an administrator to access this resource";
    }




    ////////////////////////
    // SORT TOYS FUNCTION //
    ////////////////////////
 function sortToys($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field, $sort) {

    // connect to the host:
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }

    // connect to our database:
    mysqli_select_db($connection, $dbname);
    // run query to get the contents of the TOYS table
    $query = "SELECT * FROM toys ORDER BY $field $sort";
    // this query can return data ($result is an identifier):
    $result = mysqli_query($connection, $query);
    // how many rows came back?:
    $n = mysqli_num_rows($result);

     //format a table and layout
     echo "<div class=\"generalInfo\"><fieldset><legend><h2>Manage Toys</h2></legend><table>";
     echo "<tr><th>Picture</th><th><a href='admin_toys.php?sortToys=id&sort={$sort}'>id</a></th><th><a href='admin_toys.php?sortToys=creator&sort={$sort}'>Creator</a></th>
            <th><a href='admin_toys.php?sortToys=toyName&sort={$sort}'>Name</a></th><th><a href='admin_toys.php?sortToys=price&sort={$sort}'>Price</a></th>
            <th><a href='admin_toys.php?sortToys=stock&sort={$sort}'>Stock</a></th><th>Action</th></tr>";

    if ($n>0) {

        for ($i = 0; $i < $n; $i++) {
            $row = mysqli_fetch_assoc($result);
            echo <<<_END
                <tr><td><img height="40" src="{$row['picture']}"></td><td>{$row['id']}</td>
                <td>{$row['creator']}</td><td>{$row['toyName']}</td><td>£{$row['price']}</td>
                <td>{$row['stock']}</td><td><a href="edit_toy.php?id={$row['id']}">Edit</a> || <a href="admin_toys.php?op=delete&id={$row['id']}">Delete</a></td></tr>
_END;
        }

        // complete formatting
        echo "<tr><td></td><td><td></td><td></td></td><td><td></td><td><a href='new_toy.php'>Add New Toy</a></td></tr>";
        echo "<tr><td></td><td><td></td><td></td></td><td><td></td><td><a href='download.php?download=toys'>Download CSV</a></td></tr>";
        echo "</table></fieldset></div>";

    }
    else {
        echo "No information found in toys table";
    }
     // we're finished with the database, close the connection:
     mysqli_close($connection);
}


    /////////////////////////
    // DELETE TOY FUNCTION //
    /////////////////////////

function delToy ($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field) {
    // connect to the host:
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }

    // connect to our database:
    mysqli_select_db($connection, $dbname);

    // run query to delete the current toy (item) from the TOYS table
    $query = "DELETE FROM contents WHERE toyID='$field'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "deleted from contents table<br>";
    }

    // run query to delete the current toy (item) from the TOYS table
    $query = "DELETE FROM toys WHERE id='$field'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "deleted from toys table<br>";
    }
    // we're finished with the database, close the connection:
    mysqli_close($connection);
}


// finish of the HTML for this page:
require_once "footer.php";

?>