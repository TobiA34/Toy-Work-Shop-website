<?php

// This script allows a LIST of TOYS to be viewed

// execute the header script:
require_once "header.php";
require_once "credentials.php";


if (isset($_GET['sortLists']) && isset($_GET['id']) && isset($_GET['sort'])) {
    $field = $_GET['sortLists'];
    $id = $_GET['id'];
    $sort = $_GET['sort'];

    if ($sort=="ASC") {
        $sort = "DESC";
    }
    else {
        $sort = "ASC";
    }

}

elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    $field = "name";
    $sort = "ASC";
    }

    else {
        $id = "";
        $field = "";
        $sort = "ASC";
    }

showList($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field, $id, $sort);

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
_END;



/////////////////////////
// SORT LISTS FUNCTION //
/////////////////////////
function showList($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field, $id, $sort) {

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
    $query = "SELECT name FROM lists WHERE visible=true AND id='$id'";
    // this query can return data ($result is an identifier):
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    echo "<h3>Viewing List: ${row['name']}</h3>";
    // run query to get the data from the CONTENTS table
    // this means the PAIRS of TOYS and LISTS will be checked
    //$query = "SELECT id, name, username, updated FROM lists WHERE visible=true ORDER BY $field";
    $query = "SELECT * from contents INNER JOIN toys on contents.toyID=toys.id INNER JOIN lists on contents.listID=lists.id WHERE listID='$id' ORDER BY $field $sort";

    // this query can return data ($result is an identifier):
    $result = mysqli_query($connection, $query);
    // how many rows came back?:
    $n = mysqli_num_rows($result);

    //format a table and layout
    echo "<table>";
    echo "<tr><th><a href='view_list.php?sortLists=toyName&id={$id}&sort={$sort}'>Toy</a></th>
            <th><a href='view_list.php?sortLists=description&id={$id}&sort={$sort}'>Description</a></th>
            <th><a href='view_list.php?sortLists=price&id={$id}&sort={$sort}'>Price</a></th></tr>";

    if ($n>0) {

        $listTotal=0;
        for ($i = 0; $i < $n; $i++) {
            $row = mysqli_fetch_assoc($result);
            $listTotal= $listTotal + $row['price'];
            echo <<<_END
                <tr><td>{$row['toyName']}</a></td><td>{$row['description']}</td><td>{$row['price']}</td>
_END;

        }
        // complete formatting
        echo "<tr><td></td><td><div align='right'><b>Total Cost (£): </b></div></td><td><b>$listTotal</b></td></tr>";
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