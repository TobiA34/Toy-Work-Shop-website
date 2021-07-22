<?php

// This script is used if we need to save some data from the site
// It receives the table name that we want to work with
// Runs the SELECT * query on that table and generates a CSV using it

require_once "credentials.php";

// Check that the download parameter has been sent - this is the DB table we will download
if (isset($_GET['download'])) {

    $downloadTable = $_GET['download'];

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
    $out = fopen('php://output', 'w');
    $query = "SELECT * FROM $downloadTable";
    $results = mysqli_query($connection, $query);

    $first = true;

    // tell the browser what file type is being returned so it can deal accordingly
    // also set the filename, which includes the TABLE name in the filename
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename='.$downloadTable.'-export.csv');
    header('Cache-Control: max-age=0');

    $out = fopen('php://output', 'w');
    while($row = mysqli_fetch_assoc($results)){
        if($first){
            $titles = array();
            foreach($row as $key=>$val){
                $titles[] = $key;
            }
            fputcsv($out, $titles);
            $first = false;
        }
        fputcsv($out, $row);
    }
    fclose($out);

}

?>