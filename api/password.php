<?php
// This script allows a valid user to change their password
// bring in the credentials required to access the MySQL database
// notice the ".." prefix as we are ascending back up the directory structure
// since this script is in the "htdocs/api/" folder and credentials.php is in the parent folder
include_once "../credentials.php";

// declare some empty variables to store the data we'll send back to the requesting caller
$thisRow = array();
$response = "";

//create a variable used to signify if the username / current password is recognised in the database
$validUser = false;

// check to make sure the caller has accessed the script with a POST request
// and that they have included the correct parameters in the request
if (!isset($_POST['username']) || !isset($_POST['currentPW']) || !isset($_POST['newPW']))
{
    // set the kind of data we're sending back and an error response code:
    header("Content-Type: application/json", NULL, 400);
    // and send:
    echo json_encode($allRows);
    // and exit this script: meaning the rest of the PHP in the script won't be executed
    exit;
}

// if everything is OK with the caller's request then take the parameters they have included
else
{
    $username = $_POST['username'];
    $currentPW = $_POST['currentPW'];
    $newPW = $_POST['newPW'];
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

// SQL to update the password
$query = "UPDATE users SET password='$newPW' WHERE username='$username' AND password='$currentPW'";
// no data, just true/false: we're tring to update a field, not request a value back
$result = mysqli_query($connection, $query);

// no data returned, we just test for true(success)/false(failure):
if ($result)
{
    // did we actually change a row?
    if (mysqli_affected_rows($connection) == 1)
    {
        // set the kind of data we're sending back and a success response code:
        header("Content-Type: application/json", NULL, 201);
        $response = $username;

    }
    else
    {
        // set the kind of data we're sending back and an error response code:
        header("Content-Type: application/json", NULL, 400);
    }
}
else
{
    // something went wrong set the kind of data we're sending back and an error response code:
    header("Content-Type: application/json", NULL, 400);
}


// we're finished with the database, close the connection:
mysqli_close($connection);

// and send:
echo json_encode($response);

?>