<?php

// This script allows the caller to obtain ALL of the PAIRS in the CONTENTS table
// Each returned element is a PAIRING of a LIST with a TOY
// bring in the credentials required to access the MySQL database
// notice the ".." prefix as we are ascending back up the directory structure
// since this script is in the "htdocs/api/" folder and credentials.php is in the parent folder
include_once "../credentials.php";

// declare some empty variables to store the data we'll send back to the requesting caller
$thisRow = array();
$allRows = array();

// check to make sure the caller has accessed the script with a GET request
// and that they have included a value for the parameter 'publicLists' to activate
if (!isset($_GET['publicLists']))
{
    // set the kind of data we're sending back and an error response code:
    header("Content-Type: application/json", NULL, 400);
    // and send:
    echo json_encode($allRows);
    // and exit this script: meaning the rest of the PHP in the script won't be executed
    exit;
}


// connect directly to our database
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);

// connection failed, return an internal server error:
if (!$connection)
{
    // set the kind of data we're sending back and a failure code:
    header("Content-Type: application/json", NULL, 500);
    // and send:
    echo json_encode($allRows);
    // and exit this script:
    exit;
}

// create the query
$query = "SELECT * from contents INNER JOIN toys on contents.toyID=toys.id INNER JOIN lists on contents.listID=lists.id";

// this query can return data ($result is an identifier):
$result = mysqli_query($connection, $query);

// how many rows came back?:
$n = mysqli_num_rows($result);

// if we got some results then add them all into a big array:
if ($n > 0)
{
    // loop over all rows, adding them into our array:
    for ($i=0; $i<$n; $i++)
    {
        // fetch one row as an associative array (elements named after columns):
        $thisRow = mysqli_fetch_assoc($result);
        // add current row to all the rows to be sent back
        $allRows[] = $thisRow;
    }
}


// we're finished with the database, close the connection:
mysqli_close($connection);

// set the kind of data we're sending back and a success code:
header("Content-Type: application/json", NULL, 200);

// and send - packed up as JSON:
echo json_encode($allRows);

?>