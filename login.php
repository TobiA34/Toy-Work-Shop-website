<?php

// This is the login script
// Allowing existing users to login to the site
// When verified - it sets session variables, cookies, etc.

// execute the header script:
require_once "header.php";
require_once "credentials.php";

// Salts to be added to the start and end of the password when entered
$preSalt = "QpfE9q";
$postSalt = "WdcDXi";

// setup variables to help with functionality and validation of data
$showLogin = true;
$username = $password = "";
// error messages to display about each field to be used for combination of all server-side errors
$username_errors = $password_errors = $errors = "";
$message = "";


    if (isset($_SESSION['loggedIn']))
    {
        $showLogin = false;
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
        // user has just tried to log in, check form data against database:
        // take copies of the credentials the user submitted:
        $username = $_POST['username'];
        // apply the SALT and HASH to the paswword entered
        $password = sha1($preSalt . $_POST['password'] . $postSalt);

        // connect directly to our database (notice 4th argument):
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);

        // if the connection fails, we need to know, so allow this exit:
        if (!$connection) {
            die("Connection failed: " . $mysqli_connect_error);
        }

        //////// SERVER-SIDE VALIDATION /////////
        // First, sanitise the user input (functions in helper.php)
        $username = sanitise($username, $connection);
        $password = sanitise($password, $connection);
        // Next, validate the user input (functions in helper.php)
        $username_errors = validateString($username, 1, 16);
        $password_errors = validateString($password, 6, 40);
        // concatenate the errors from both validation calls
        $errors = $username_errors . $password_errors;

        if ($errors == "") {
            // check for a row in our USERS table with a matching username and password:
            $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            // this query can return data ($result is an identifier):
            $result = mysqli_query($connection, $query);
            // how many rows came back? (can only be 1 or 0 because usernames are the primary key in our table):
            $n = mysqli_num_rows($result);
            // fetch the data about this user
            $row = mysqli_fetch_assoc($result);

            // if there was a match then set the session variables and display a success message:
            if ($n == 1) {
                // set a session variable to record that this user has successfully logged in:
                $_SESSION['loggedIn'] = true;
                // and copy their username into the session data for use by our other scripts:
                $_SESSION['username'] = $username;
                // add the user's picture link as a session variable
                $_SESSION['picture'] = $row['picture'];
                // add the first name as a session var
                $_SESSION['firstName'] = $row['firstName'];
                // hide the form
                $showLogin = false;

                // update the last login date
                $lastLogin = date("Y-m-d");
                $sql = "UPDATE users SET lastLogin='$lastLogin' WHERE username='$username' AND password='$password'";
                // no data, just true/false: we're tring to update a field, not request a value back
                $result = mysqli_query($connection, $sql);

                // update login telemetry
                // check to see if there is already data for today in the DB
                $sql = "SELECT * FROM loginTracking WHERE date='$lastLogin'";
                $result = mysqli_query($connection, $sql);
                $n = mysqli_num_rows($result);

                // if a row comes back then today already exists so do an UPDATE
                if ($n==1) {
                    $sql = "UPDATE loginTracking SET number=number+1 WHERE date='$lastLogin'";
                    // no data, just true/false: we're tring to update a field, not request a value back
                    $result = mysqli_query($connection, $sql);
                }

                // otherwise, create the date for today and set first login value
                else {
                    $sql = "INSERT INTO loginTracking (date, number) VALUES ('$lastLogin','1')";
                    // no data, just true/false: we're tring to update a field, not request a value back
                    $result = mysqli_query($connection, $sql);
                }

                // show a successful signin message:
                echo <<<_END
                <div class="loginDialog"><fieldset><legend><h2>Login Successful</h2></legend>
                <table align="center" border="0" cellpadding="2"><tr><td><img height="100" src="{$_SESSION['picture']}"><br>
                <br>Hi, <b>{$_SESSION['firstName']}</b>!<br> You have successfully logged in. <br>Please <a href='index.php'>click here.</a><br><br>
                </td></tr></table></fieldset></div>
_END;
            }

            else {

                // show an unsuccessful signin message:
                $message = "Sign in failed, please try again";
                // no matching credentials found so redisplay the signin form with a failure message:
                $showLogin = true;

            }

            // we're finished with the database, close the connection:
            mysqli_close($connection);

        } else {
            $message = "Sign-in Failed";
            $showLogin = true;
        }
    }

    else if (isset($_GET['clear'])) {
        // wipe existing cookies of name and picture
        setcookie('firstName', "", time() - 2592000, '/');
        setcookie('picture', "", time() - 2592000, '/');
        $message = "Welcome!<br>";
        $showLogin = true;
    }

    // check to see if a cookie exists with user's name - they have been to the site recently
    elseif (isset($_COOKIE['firstName']) && isset($_COOKIE['picture']) && isset($_COOKIE['username'])) {
        $message = "<img height='80' src='{$_COOKIE['picture']}'><br>Welcome back, {$_COOKIE['firstName']}<br>please sign-in...<br>Not you? <a href='login.php?clear=y'>Click Here</a>";
        $username = $_COOKIE['username'];
        $showLogin = true;
    }


    else
    {
        // user has arrived at the page for the first time, just ask them to log in:
        $showLogin = true;
    }

    // when set to TRUE this shows the login form in the browser
    if ($showLogin) {
            echo <<<_END
            <div class="loginDialog">
                <form action="login.php" method="post">
                    <fieldset><legend><h2>Account Login:</h2></legend>
                    <b>$message</b>
                    <table align="center" border="0" cellpadding="2">
                        <tr>
                            <td>Username: </td>
                            <td><input type="text" maxlength="16" name="username" value="$username" required></td><b><i>$username_errors</b></i>
                        </tr>
                        <tr>
                            <td>Password: </td>
                            <td><input type="password" minlength="6" maxlength="40" value="$password" name="password" required></td><b><i>$password_errors</b></i>
                        </tr>
                    </table>
                    <input type="submit" value="Login">
                    </fieldset>
                </form>
                <br><i><font size="2">No account? No problem. <a href="signup.php">Register</a> for The Toy Workshop.</font></i>
            </div>
_END;
    }

// finish of the HTML for this page:
require_once "footer.php";

?>