<?php

// This script removes a TOY from a LIST - by removing the pairing
// bring in the credentials required to access the MySQL database
// notice the ".." prefix as we are ascending back up the directory structure
// since this script is in the "htdocs/api/" folder and credentials.php is in the parent folder
include_once "../credentials.php";
$allRows = "";

// check to make sure the caller has accessed the script with a POST request
// and that they have included a value fo the parameter 'pairID'

if (!isset($_POST['pairID'])) {
    // set the kind of data we're sending back and an error response code:
    header("Content-Type: application/json", NULL, 400);
    // and send:
    echo json_encode($allRows);
    // and exit this script: meaning the rest of the PHP in the script won't be executed
    exit;
}

else {

    // get the pairID value from the requesting client
    $pairID = $_POST['pairID'];

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

    // delete the pairing between a LIST and TOY from the CONTENTS table
    $query = "DELETE FROM contents WHERE pairID='$pairID'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        echo "deleted - list contents<br>";
    }
    else {
        echo "List contents not deleted - error";
    }


// we're finished with the database, close the connection:
    mysqli_close($connection);

// set the kind of data we're sending back and a success code:
    header("Content-Type: application/json", NULL, 200);
    $allRows = $pairID;
    echo json_encode($allRows);
  }

?>