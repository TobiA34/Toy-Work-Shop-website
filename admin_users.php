<?php

// This script allows the ADMIN to administer other site users
// It begins by providing a list of all site users
// From there, the ADMIN can edit, delete and add new ones

// execute the header script:
require_once "header.php";
// read in the details of our MySQL server:
require_once "credentials.php";

// setup variables to help with functionality and validation of data
$username = $password = "";
// error messages to display about each field to be used for combination of all server-side errors
$username_errors = $password_errors = $firstName_errors = $lastName_errors = $email_errors = $dob_errors = $telephone_errors = $errors = "";
$message = "";

// default field to sort users by
$sortUsers = "username";
$sort = "ASC";

// switch the table of users off and on
$showUsers = true;

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

// check that the current user is the ADMIN user
if (isset($_SESSION['loggedIn']) && ($_SESSION['username']=='admin')) {

        // see if the user has clicked a field to sort the user
        if (isset($_GET['sortUsers']) && isset($_GET['sort'])) {
            $sortUsers = $_GET['sortUsers'];

            $sort = $_GET['sort'];

            if ($sort=="ASC") {
                $sort = "DESC";
            }
            else {
                $sort = "ASC";
            }
        }

        else if (isset($_GET['sortUsers'])) {
            $sortUsers = $_GET['sortUsers'];
        }

        else if (isset($_GET['op']) && (isset($_GET['id']))) {

            // is this a delete operation? If so, do it!
            if ($_GET['op']=="delete") {
                delUsers($dbhost, $dbuser, $dbpass, $dbname, $dbport, $_GET['id']);
                $showUsers = true;
            }

            //if the admin wants to edit and existing, or add a new, user - provide that option
            else if ($_GET['op']=="new" || $_GET['op']=="edit"){
                $showUsers = false;

                if ($_GET['op']=="new") {
                    $action="Add New";
                    $usernameAction = "";
                    $dbAction = "newUser";
                    $editRow['username'] = $editRow['password'] = $editRow['firstName'] = $editRow['lastName'] = $editRow['dob'] = $editRow['email'] = $editRow['telephone'] = $editRow['picture'] = "";
                }

                else {
                    $action="Edit";
                    $dbAction = "changeUser";
                    $usernameAction = "readonly";
                    $editRow = editUsers($dbhost, $dbuser, $dbpass, $dbname, $dbport, $_GET['id']);
                }


                echo <<<_END

                <form action="admin_users.php" method="post" enctype="multipart/form-data">
                     <div class="generalInfo"><fieldset><legend><h2>$action User</h2></legend><table>
                     <tr><th align="right">Username</th><td><input size="30" type="text" minlength="1" maxlength="32" name="username" value="{$editRow['username']}" $usernameAction required><b>$username_errors</b></td></tr>
                     <tr><th>Password</th><td><input size="30" type="password" minlength="6" maxlength="40" name="password" required><b>$password_errors</b></td></tr>
                     <tr><th>First Name</th><td><input size="30" type="text" minlength="1" maxlength="32" name="firstName" value="{$editRow['firstName']}" required><b>$firstName_errors</b></td></tr>
                     <tr><th>Last Name</th><td><input size="30" type="text" minlength="1" maxlength="64" name="lastName" value="{$editRow['lastName']}" required><b>$lastName_errors</b></td></tr>
                     <tr><th>DOB</><td><input name="dob" type="date" value="{$editRow['dob']}" required></td><b>$dob_errors</b></tr>
                     <tr><th>Email</th><td><input size="30" type="email" minlength="3" maxlength="64" name="email" value="{$editRow['email']}" required><b>$email_errors</b></td></tr>
                     <tr><th>Telephone</th><td><input size="30" type="text" minlength="1" maxlength="25" name="telephone" value="{$editRow['telephone']}" ><b>$telephone_errors</b></td></tr>
                     <tr><th>Picture</th><td><img height="100" src="{$editRow['picture']}"><br><input type="file" name="picture" value="{$editRow['picture']}" id="pictureUpload"></td></tr>
                     </table>
                        <input type="submit" name="$dbAction" value="$action User">
                     </fieldset>
                </form>
_END;

                }

            }

        elseif (isset($_POST['changeUser']) || (isset($_POST['newUser']))) {
            // admin has attempted to add a new user to the database

            // Salts to be added to the start and end of the password when entered
            $preSalt = "QpfE9q";
            $postSalt = "WdcDXi";

            $username = $_POST['username'];
            $password = $_POST['password'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $dob = $_POST['dob'];
            $email = $_POST['email'];
            $telephone = $_POST['telephone'];

            if (isset($_POST['newUser'])) {
                $picture = "img/blank.png";
            }

            // need to retain old image and username in case of changes
            else {
                // connect to the host:
                $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
                // get the old picture path
                $query = "SELECT picture FROM users WHERE username='$username'";
                $result = mysqli_query($connection, $query);
                $row = mysqli_fetch_assoc($result);
                // we're finished with the database, close the connection:
                mysqli_close($connection);
                $picture = $row['picture'];

            }


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
                    ///////////////////////////////////////////////////////////
                    ///////////////////// FILE UPLOAD /////////////////////////
                    ////////////////  based upon W3Schools ////////////////////
                    ///  https://www.w3schools.com/php/php_file_upload.asp ////
                    ///////////////////////////////////////////////////////////

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

                // NEW USER being added by admin
                if (isset($_POST['newUser'])) {

                    $sql = "INSERT INTO users (username, password, firstName, lastName, email, telephone, dob, picture, lastLogin)
            VALUES ('$username', '$password', '$firstName', '$lastName', '$email', 
                    '$telephone', '$dob', '$picture', '$lastLogin')";

                    // no data returned, we just test for true(success)/false(failure):
                    if (mysqli_query($connection, $sql)) {
                        echo "New user $username has been added";
                        // everything is OK, show the user table in admin view
                        $showUsers = true;
                    }

                    else {
                        die("Error inserting row: " . mysqli_error($connection));
                    }
                }

                // EXISTING USER being updated by admin
                else {

                    $sql = "UPDATE users SET username='$username', password='$password', firstName='$firstName', lastName='$lastName', dob='$dob', email='$email', telephone='$telephone', picture='$picture' 
            WHERE username='$username'";

                    // no data returned, we just test for true(success)/false(failure):
                    if (mysqli_query($connection, $sql)) {

                        echo "User $username has been edited";
                        // everything is OK, show the user table in admin view
                        $showUsers = true;
                    }
                    else {
                        die("Error updating row: " . mysqli_error($connection));
                    }

                }

            }
            // we're finished with the database, close the connection:
            mysqli_close($connection);
        }


         // if set to TRUE then sort the USERS list and display in the browser
        if ($showUsers) {
            // call the function that sorts and displays the users in the table
            sortUsers($dbhost, $dbuser, $dbpass, $dbname, $dbport, $sortUsers, $sort);
        }

    }

    else {
        echo "Sorry, you must be an administrator to access this resource";
    }



        /////////////////////////
        // SORT USERS FUNCTION //
        /////////////////////////
 function sortUsers($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field, $sort) {

    // connect to the host:
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }

    // connect to our database:
    mysqli_select_db($connection, $dbname);
    // run query to get the contents of the USERS table
    $query = "SELECT * FROM users ORDER BY $field $sort";
    // this query can return data ($result is an identifier):
    $result = mysqli_query($connection, $query);
    // how many rows came back?:
    $n = mysqli_num_rows($result);

     //format a table and layout
     echo "<div class=\"generalInfo\"><fieldset><legend><h2>Manage Users</h2></legend><table>";
     echo "<tr><th><a href='admin_users.php?sortUsers=username&sort={$sort}'>Username</a></th><th><a href='admin_users.php?sortUsers=firstName&sort={$sort}'>First Name</a></th>
            <th><a href='admin_users.php?sortUsers=lastName&sort={$sort}'>Last Name</a></th>
            <th><a href='admin_users.php?sortUsers=lastLogin&sort={$sort}'>Last Login</a></th><th>Action</th></tr>";

    if ($n>0) {

        for ($i = 0; $i < $n; $i++) {
            $row = mysqli_fetch_assoc($result);
            echo <<<_END
                <tr><td>{$row['username']}</td><td>{$row['firstName']}</td><td>{$row['lastName']}</td>
                <td>{$row['lastLogin']}</td>
                <td><a href="admin_users.php?op=edit&id={$row['username']}">Edit</a> || <a href="admin_users.php?op=delete&id={$row['username']}">Delete</a></td></tr>
_END;

        }

        // complete formatting
        echo "<tr><td></td><td><td></td><td></td></td><td><a href='admin_users.php?op=new&id=new'>Add New User</a></td></tr>";
        echo "<tr></td><td></td></td><td><td></td><td></td><td><a href='download.php?download=users'>Download CSV</a></td></tr>";
        echo "</table></fieldset></div>";

    }

    else {
        echo "No information found in users table";
    }
     // we're finished with the database, close the connection:
     mysqli_close($connection);
}


    //////////////////////////
    // DELETE USER FUNCTION //
    //////////////////////////

function delUsers ($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field) {
    // connect to the host:
    $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }

    // stop admin account from being removed
    if ($field=="admin") {
        echo "Admin account cannot be removed!<br>";
    }

    else {
        // disable foreign key checks to allow users with lists to be removed
        $sql = "SET FOREIGN_KEY_CHECKS=0";
        mysqli_query($connection, $sql);

        // connect to our database:
        mysqli_select_db($connection, $dbname);
        $query = "DELETE FROM lists WHERE username='$field'";
        $result = mysqli_query($connection, $query);
        if ($result) {
            echo "deleted - user lists<br>";
        } else {
            echo "Not deleted from lists - error<br>";
        }
        // run query
        $query = "DELETE FROM users WHERE username='$field'";
        $result = mysqli_query($connection, $query);
        if ($result) {
            echo "deleted - user account";
        } else {
            echo "Not deleted from users- error<br>";
        }

        // Re-enable foreign key checks
        $sql = "SET FOREIGN_KEY_CHECKS=1";
        mysqli_query($connection, $sql);

        // we're finished with the database, close the connection:
        mysqli_close($connection);
    }

}


    /////////////////////
    // EDIT USER FUNCTION //
    /////////////////////

function editUsers ($dbhost, $dbuser, $dbpass, $dbname, $dbport, $field) {
    // connect to the host:
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    // exit the script with a useful message if there was an error:
    if (!$connection)
    {
        die("Connection failed: " . $mysqli_connect_error);
    }

    // connect to our database:
    mysqli_select_db($connection, $dbname);
    // run query to get the contents of the USERS table
    $query = "SELECT * FROM users WHERE username='$field'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    // we're finished with the database, close the connection:
    mysqli_close($connection);
    // send back the associative array to be edited
    return $row;
}


// finish of the HTML for this page:
require_once "footer.php";

?>