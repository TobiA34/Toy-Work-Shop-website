<?php

// This script allows the ADMIN user to add a new TOY to the DB table
// It provides the form to allow this to be done

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

// setup variables to help with functionality and validation of data
$showToy = false;
// error messages to display about each field to be used for combination of all server-side errors
$errors = $name_errors = $price_errors = $ages_errors = $description_errors = $stock_errors = "";

// check that the current user is the ADMIN
if (isset($_SESSION['loggedIn']) && ($_SESSION['username']=="admin"))
    {
        // do this if submitting
        if (isset($_POST['newToy'])) {

            $name = $_POST['name'];
            $price = $_POST['price'];
            $age = $_POST['age'];
            $description = $_POST['description'];
            $stock = $_POST['stock'];
            $picture = "";

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
            $name = sanitise($name, $connection);
            $price = sanitise($price, $connection);
            $age = sanitise($age, $connection);
            $description = sanitise($description, $connection);
            $stock = sanitise($stock, $connection);

            // Next, validate the user input (functions in helper.php)
            $name_errors = validateString($name, 1, 64);
            $price_errors = validateDecimal($price);
            $age_errors = validateString($age, 0, 16);
            $description_errors = validateString($description, 0, 800);
            $stock_errors = validateInt($stock, 0, 4294967295);

            // concatenate the errors from both validation calls
            $errors = $name_errors . $price_errors . $age_errors . $description_errors . $stock_errors;

            if ($errors == "") {

                if (($_FILES["picture"]["name"])!="") {
                    ////////////////////////////////////////////////////////////
                    ///////////////////// FILE UPLOAD //////////////////////////
                    ////////////////  based upon W3Schools ////////////////////
                    ///  https://www.w3schools.com/php/php_file_upload.asp ///
                    //////////////////////////////////////////////////////////

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

                $sql = "INSERT INTO toys (toyName, price, ages, creator, stock, picture, description)
                      VALUES ('$name', '$price', '$age', '{$_SESSION['username']}', '$stock', '$picture', '$description')";

                // no data returned, we just test for true(success)/false(failure):
                if (mysqli_query($connection, $sql)) {

                    echo "New Toy Added";
                    $showToy = true;

                }
                else {
                    die("Error inserting row: " . mysqli_error($connection));
                }
                // we're finished with the database, close the connection:
                mysqli_close($connection);
            }

            else {
                /// deal with server side errors
                /// show form again, highlighting problems
                $showToy = true;
            }
        }

            // user has arrived at the page for the first time
            else {
                $showToy = true;
            }

        }

    else {
        echo "Sorry, you must be an administrator to access this resource";
    }


// when TRUE show the FORM
if ($showToy) {
    echo <<<_END

        <form action="new_toy.php" method="post" id="newToy" enctype="multipart/form-data">
        
             <div class="generalInfo"><fieldset><legend><h2>Add a New Toy</h2></legend><table>

             <tr><th align="right">Name</th><td><input size="40" type="text" name="name" minlength="1" maxlength="64" required><b>{$name_errors}</b></td></tr>
             <tr><th align="right">Price (£)</th><td><input step="0.01" type="number" name="price" min="0.00"><b>{$price_errors}</b></td></tr>
             <tr><th align="right">Ages</th><td><input size="40" maxlength="16" type="text" name="age"><b>{$ages_errors}</b></td></tr>
             <tr><th align="right">Stock</th><td><input type="number" name="stock" min="0"><b>{$stock_errors}</b></td></tr>
             <tr><th align="right">Creator</th><td><input size="32" type="text" minlength="1" maxlength="32" value="{$_SESSION['username']}" name="username" readonly required></td></tr>
             <tr><th align="right">Description</th><td><textarea rows="4" cols="70" name="description" maxlength="800" form="newToy"></textarea><b>{$description_errors}</b></td></tr>
             <tr><th>Picture</th><td><input type="file" name="picture" id="pictureUpload"></td></tr>
             </table>
                <input type="submit" name="newToy" value="Add New Toy">
             </fieldset>
        
        </form>
_END;
}

// finish of the HTML for this page:
require_once "footer.php";

?>