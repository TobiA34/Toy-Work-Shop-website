<?php

// Things to notice:
// This script holds the sanitisation function that we pass all our user data to
// This script holds the validation functions that double-check our user data is valid

// function to sanitise (clean) user data:
function sanitise($str, $connection)
{
    if (get_magic_quotes_gpc())
    {
        // just in case server is running an old version of PHP with "magic quotes" running:
        $str = stripslashes($str);
    }
    // escape any dangerous characters, e.g. quotes:
    $str = mysqli_real_escape_string($connection, $str);
    // ensure any html code is safe by converting reserved characters to entities:
    $str = htmlentities($str);
    // return the cleaned string:
    return $str;
}

// if the data is valid return an empty string, if the data is invalid return a help message
function validateString($field, $minlength, $maxlength)
{
    if (strlen($field)<$minlength)
    {
        // wasn't a valid length, return a help message:
        return "Minimum length: " . $minlength;
    }
    elseif (strlen($field)>$maxlength)
    {
        // wasn't a valid length, return a help message:
        return "Maximum length: " . $maxlength;
    }
    // data was valid, return an empty string:
    return "";
}

// if the data is valid return an empty string, if the data is invalid return a help message
function validateInt($field, $min, $max)
{
    // see PHP manual for more info on the options: http://php.net/manual/en/function.filter-var.php
    $options = array("options" => array("min_range"=>$min,"max_range"=>$max));

    if (!filter_var($field, FILTER_VALIDATE_INT, $options))
    {
        // wasn't a valid integer, return a help message:
        return "Not a valid number (must be whole and in the range: " . $min . " to " . $max . ")";
    }
    // data was valid, return an empty string:
    return "";
}

// if the data is valid return an empty string, if the data is invalid return a help message
function validateDOB($field)
{
    // parse the supplied date into array to get day, month, year out for checking
    $date = date_parse($field);

    // use the PHP checkdate() function to confirm the date is valid
    if (checkdate($date['month'], $date['day'], $date['year'])) {
        //echo "Date is OK";
        return "";
    }

    else {
        return "Date is not valid";
    }

}


// if the data is valid return an empty string, if the data is invalid return a help message
function validateEmail($field)
{
    // Remove all illegal characters from email
    $field = filter_var($field, FILTER_SANITIZE_EMAIL);

    // Check to see if the email address conforms to the expected format
    if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
        // data was valid, return an empty string:
        return "";
    }

    else {
        return "Email address is not valid ";
    }

}


// if the data is valid return an empty string, if the data is invalid return a help message
function validateDecimal($field)
{

    if (!filter_var($field, FILTER_VALIDATE_FLOAT) && $field<0)
    {
        // wasn't a valid integer, return a help message:
        return "Not a valid number - must be decimal";
    }
    // data was valid, return an empty string:
    return "";
}

?>