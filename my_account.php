<?php

// This script will allow a user to EDIT their current account details
// It displays a form to show details and allows them to be edited/changed

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
_END;

// error messages to display about each field to be used for combination of all server-side errors
$firstName_errors = $lastName_errors = $email_errors = $dob_errors = $telephone_errors = $errors = "";

    if (isset($_SESSION['loggedIn']) && isset($_POST['updating'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $picture = $_SESSION['picture'];

        // connect to the host:
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
        // exit the script with a useful message if there was an error:
        if (!$connection) {
            die("Connection failed: " . $mysqli_connect_error);
        }

        /////////////////////////////////////////
        //////// SERVER-SIDE VALIDATION /////////
        /////////////////////////////////////////
        // First, sanitise the user input (functions in helper.php)
        $firstName = sanitise($firstName, $connection);
        $lastName = sanitise($lastName, $connection);
        $dob = sanitise($dob, $connection);
        $email = sanitise($email, $connection);
        $telephone = sanitise($telephone, $connection);
        // Next, validate the user input (functions in helper.php)
        $firstName_errors = validateString($firstName, 1, 32);
        $lastName_errors = validateString($lastName, 1, 64);
        $telephone_errors = validateString($telephone, 0, 25);
        $email_errors = validateEmail($email);
        $dob_errors = validateDOB($dob);

        // concatenate the errors from both validation calls
        $errors = $firstName_errors . $lastName_errors . $telephone_errors . $email_errors . $dob_errors;

        if ($errors == "") {

            if (($_FILES["picture"]["name"])!="") {

                ////////////////////////////////////////////////////////////
                ////////////////////// FILE UPLOAD /////////////////////////
                //////////////////  based upon W3Schools ///////////////////
                ////  https://www.w3schools.com/php/php_file_upload.asp ////
                ////////////////////////////////////////////////////////////

                $target_dir = "img/";
                $target_file = $target_dir . basename($_FILES["picture"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // check the file is real and not fake
                $check = getimagesize($_FILES["picture"]["tmp_name"]);
                if ($check !== false) {
                    //echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }

                // Check file size - less than 750 KB
                if ($_FILES["picture"]["size"] > 750000) {
                    echo "Sorry, your file is too large.";
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif") {
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
                        //echo "The file " . basename($_FILES["picture"]["name"]) . " has been uploaded.";
                        $picture = $target_file;
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                }

            }

            $sql = "UPDATE users SET firstName='$firstName', lastName='$lastName', dob='$dob', email='$email', telephone='$telephone', picture='$picture' 
            WHERE username='{$_SESSION['username']}'";

            // no data returned, we just test for true(success)/false(failure):
            if (mysqli_query($connection, $sql)) {

                //update the session variables for this account
                $_SESSION['picture'] = $picture;
                // add the first name as a session var
                $_SESSION['firstName'] = $firstName;

                echo <<<_END
                <div class="generalInfo"><fieldset><legend><h2>Update Successful</h2></legend>
                <br>Your account has been updated. <br>Please <a href="account.php">click here</a> to view your details<br><br><br>
               </fieldset></div>
_END;

            }
            else {
                die("Error updating row: " . mysqli_error($connection));
            }
            // we're finished with the database, close the connection:
            mysqli_close($connection);
        }

    }

    elseif (isset($_SESSION['loggedIn']))
    {

        // connect to the host:
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
        // exit the script with a useful message if there was an error:
        if (!$connection) {
            die("Connection failed: " . $mysqli_connect_error);
        }

        //get the current user details from the DB table
        $query = "SELECT firstName, lastName, dob, email, telephone, picture FROM users WHERE username='{$_SESSION['username']}'";
        // this query can return data ($result is an identifier):
        $result = mysqli_query($connection, $query);
        // how many rows came back?:
        $n = mysqli_num_rows($result);
        // only one row should come back
        if ($n==1) {
            $row = mysqli_fetch_assoc($result);
        }
        echo <<<_END
        
        <form action="my_account.php" method="post" enctype="multipart/form-data">
        
             <div class="generalInfo"><fieldset><legend><h2>Update Details: {$_SESSION['username']}</h2></legend><table>
             <tr><th>First Name</th><td><input size="30" type="text" minlength="1" maxlength="32" value="{$row['firstName']}" name="firstName" required><b>$firstName_errors</b></td></tr>
             <tr><th>Last Name</th><td><input size="30" type="text" minlength="1" maxlength="64" value="{$row['lastName']}" name="lastName" required><b>$lastName_errors</b></td></tr>
             <tr><th>DOB</><td><input name="dob" type="date" value="{$row['dob']}" required></td><b>$dob_errors</b></tr>
             <tr><th>Email</th><td><input size="30" type="email" minlength="3" maxlength="64" value="{$row['email']}" name="email" required><b>$email_errors</b></td></tr>
             <tr><th>Telephone</th><td><input size="30" type="text" minlength="1" maxlength="25" value="{$row['telephone']}" name="telephone"><b>$telephone_errors</b></td></tr>
             <tr><th>Picture</th><td><img height="120" alt="{$_SESSION['username']}" src="{$_SESSION['picture']}"><br><input type="file" name="picture"></td></tr>
             </table>
                <input type="submit" name="updating" value="Update Details">
             </fieldset>
        
        </form>
_END;

        // we're finished with the database, close the connection:
        mysqli_close($connection);
    }

    // user has arrived at the page for the first time
    // show the registration form to them
    else {
        echo "Sorry, you must <a href='login.php'>login</a> or be a registered user to access this resource.<br>Create an account <a href='signup.php'>here</a>";
    }

// finish of the HTML for this page:
require_once "footer.php";

?>