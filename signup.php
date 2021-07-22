<?php

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

// Salts to be added to the start and end of the password when entered
$preSalt = "QpfE9q";
$postSalt = "WdcDXi";

// setup variables to help with functionality and validation of data
$showLogin = false;
$username = $password = "";
// error messages to display about each field to be used for combination of all server-side errors
$username_errors = $password_errors = $firstName_errors = $lastName_errors = $email_errors = $dob_errors = $telephone_errors = $errors = "";
$message = "";


    if (isset($_SESSION['loggedIn']))
    {
        // user is already logged in, just display a message:
        echo <<<_END
                <div class="loginDialog"><fieldset><legend><h2>Already Logged In</h2></legend>
                <table align="center" border="0" cellpadding="2"><tr><td>
                <br>You are already logged in, please <a href="signout.php">log out</a> first.<br><br><br>
                </td></tr></table></fieldset></div>
_END;
        echo "<br>";
    }

    elseif (isset($_POST['username'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $picture = "img/blank.png";


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
        $username = sanitise($username, $connection);
        $password = sanitise($password, $connection);
        $firstName = sanitise($firstName, $connection);
        $lastName = sanitise($lastName, $connection);
        $dob = sanitise($dob, $connection);
        $email = sanitise($email, $connection);
        $telephone = sanitise($telephone, $connection);
        // Next, validate the user input (functions in helper.php)
        $username_errors = validateString($username, 1, 32);
        $password_errors = validateString($password, 6, 40);
        $firstName_errors = validateString($firstName, 1, 32);
        $lastName_errors = validateString($lastName, 1, 64);
        $telephone_errors = validateString($telephone, 0, 25);
        $email_errors = validateEmail($email);
        $dob_errors = validateDOB($dob);

        // concatenate the errors from both validation calls
        $errors = $username_errors . $password_errors . $firstName_errors . $lastName_errors . $telephone_errors . $email_errors . $dob_errors;

        if ($errors == "") {
            $lastLogin = date("Y-m-d");
            // SALT and HASH the password
            $password = sha1($preSalt . $password . $postSalt);


            if (($_FILES["picture"]["name"])!="") {
                ////////////////////////////
                ////// FILE UPLOAD /////////
                ///  based upon W3Schools ////
                ///  https://www.w3schools.com/php/php_file_upload.asp ///
                ////////////////////////////

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

            $sql = "INSERT INTO users (username, password, firstName, lastName, email, telephone, dob, picture, lastLogin)
            VALUES ('$username', '$password', '$firstName', '$lastName', '$email', 
                    '$telephone', '$dob', '$picture', '$lastLogin')";

            // no data returned, we just test for true(success)/false(failure):
            if (mysqli_query($connection, $sql)) {
                echo <<<_END
                <div class="generalInfo"><fieldset><legend><h2>Sign-up Successful</h2></legend>
                <br>Your account has been created. <br>Please <a href="login.php">sign-in</a> to use the site.<br><br><br>
               </fieldset></div>
_END;

            }
            else {
                die("Error inserting row: " . mysqli_error($connection));
            }
            // we're finished with the database, close the connection:
            mysqli_close($connection);
        }

        else {
            /// deal with server side errors";
            /// show form again, highlighting problems
            $showLogin = true;
        }
    }

    // user has arrived at the page for the first time
    // show the registration form to them
    else {
        $showLogin = true;
    }

if ($showLogin) {

    echo <<<_END

        <form action="signup.php" method="post" enctype="multipart/form-data">
        
             <div class="generalInfo"><fieldset><legend><h2>Register for the Toy Workshop</h2></legend><table>
             <tr><th align="right">Username</th><td><input size="30" type="text" minlength="1" maxlength="32" name="username" required><b>$username_errors</b></td></tr>
             <tr><th>Password</th><td><input size="30" type="password" minlength="6" maxlength="40" name="password" required><b>$password_errors</b></td></tr>
             <tr><th>First Name</th><td><input size="30" type="text" minlength="1" maxlength="32" name="firstName" required><b>$firstName_errors</b></td></tr>
             <tr><th>Last Name</th><td><input size="30" type="text" minlength="1" maxlength="64" name="lastName" required><b>$lastName_errors</b></td></tr>
             <tr><th>DOB</><td><input name="dob" type="date" required></td><b>$dob_errors</b></tr>
             <tr><th>Email</th><td><input size="30" type="email" minlength="3" maxlength="64" name="email" required><b>$email_errors</b></td></tr>
             <tr><th>Telephone</th><td><input size="30" type="text" minlength="1" maxlength="25" name="telephone"><b>$telephone_errors</b></td></tr>
             <tr><th>Picture</th><td><input type="file" name="picture" id="pictureUpload"></td></tr>
             </table>
                <input type="submit" value="Sign me up">
             </fieldset>
        
        </form>

_END;

}


// finish of the HTML for this page:
require_once "footer.php";

?>