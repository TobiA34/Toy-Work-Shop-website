<?php

// This script simply returns ALL of the TOYS contained in the TOYS table
// bring in the credentials required to access the MySQL database
// notice the ".." prefix as we are ascending back up the directory structure
// since this script is in the "htdocs/api/" folder and credentials.php is in the parent folder
include_once "../credentials.php";

// declare some empty variables to store the data we'll send back to the requesting caller
$thisRow = array();
$allRows = array();

// check to make sure the caller has accessed the script with a GET request
// and that they have included a value for the parameter 'toys'
if (!isset($_GET['toys']))
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

// get specific details about the toys using this SQL query
$query = "SELECT toyName, price, picture, description, stock FROM toys ORDER BY toyName";

// this query can return data ($result is an identifier):
$result = mysqli_query($connection, $query);

// how many rows came back?:
$n = mysqli_num_rows($result);

// if we got some results then add them all into a big array:
if ($n > 0)

    $allRows['cols'][] = array('id' => '','label' => 'Name', 'type' => 'string');
    $allRows['cols'][] = array('id' => '','label' => 'Price', 'type' => 'number');
    $allRows['cols'][] = array('id' => '','label' => 'Picture', 'type' => 'string');
    $allRows['cols'][] = array('id' => '','label' => 'Description', 'type' => 'string');
    $allRows['cols'][] = array('id' => '', 'label' => 'Stock', 'type' => 'number');

{
    // loop over all rows, adding them into our array:
    for ($i=0; $i<$n; $i++)
    {
        // fetch one row as an associative array (elements named after columns):
        $thisRow = mysqli_fetch_assoc($result);
        // add current row to all the rows to be sent back
        //$allRows[] = $thisRow;
       // $allRows['rows'][] = array({$thisRow['toyName']} => array( array('v'=>'20-01-13'), array('v'=>22)) );
        $allRows['rows'][] = array('c' => array( array('v'=>$thisRow['toyName']), array('v'=>$thisRow['price']), array('v'=>$thisRow['picture']), array('v'=>$thisRow['description']), array('v'=>$thisRow['stock'])) );
    }
}


// we're finished with the database, close the connection:
mysqli_close($connection);

// set the kind of data we're sending back and a success code:
header("Content-Type: application/json", NULL, 200);

// and send - packed up as JSON:
echo json_encode($allRows);

?>