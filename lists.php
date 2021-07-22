<?php

// This script displays all of the PUBLIC toy LISTS that have been set by users
// Users can then click on each list to see its contents
// This script can be seen by ANYONE

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

    <body>

        <div class="main">

            <h3>Shared Toy Lists</h3>

            
_END;

// default field to sort lists by
$sortLists = "name";
$sort = "ASC";


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

    //call the function to populate the table
    sortLists($dbhost, $dbuser, $dbpass, $dbname, $dbport, $sortLists, $sort);


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
    $query = "SELECT id, name, username, updated FROM lists WHERE visible=true ORDER BY $field $sort";
    // this query can return data ($result is an identifier):
    $result = mysqli_query($connection, $query);
    // how many rows came back?:
    $n = mysqli_num_rows($result);

    //format a table and layout
    echo "<table>";
    echo "<tr><th><a href='lists.php?sortLists=name&sort={$sort}'>Name</a></th>
            <th><a href='lists.php?sortLists=username&sort={$sort}'>Owner</a></th>
            <th><a href='lists.php?sortLists=updated&sort={$sort}'>Updated</a></th></tr>";

    if ($n>0) {

        for ($i = 0; $i < $n; $i++) {
            $row = mysqli_fetch_assoc($result);
            echo <<<_END
                <tr><td><a href="view_list.php?id={$row['id']}">{$row['name']}</a></td><td>{$row['username']}</td><td>{$row['updated']}</td>
_END;

        }
        // complete formatting
        echo "</table>";

    }

    else {
        echo "No information found in users table";
    }
    // we're finished with the database, close the connection:
    mysqli_close($connection);
}


// finish of the HTML for this page:
require_once "footer.php";

?>