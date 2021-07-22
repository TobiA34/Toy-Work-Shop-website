<?php

// This is the header - included on almost every normal page on the site
// it sets-up the HTML contents and the main menu
// as well as session information and calling the helper functions

include_once "helper.php";
// start the session
session_start();

echo <<<_END
  <!DOCTYPE html>
    <html>
    <title>The Toy Workshop</title>
    <head>
    <link rel="stylesheet" type="text/css" href="tw-main.css">
_END;

    // CHECK FOR CURRENT USER BEING LOGGED IN AS ADMIN ACCOUNT
    if (isset($_SESSION['loggedIn']) && ($_SESSION['username']=='admin'))
    {
        echo <<<_END
                <div class="navbar">
                    <a href="index.php">Home</a>
                     <a href="lists.php">Toy Lists</a>
                     <a href="admin.php">Admin</a>
                     <a href="account.php">My Account <img style="vertical-align:text-top" height="20" src="{$_SESSION['picture']}"> </a>
                    <a href="signout.php">Logout ({$_SESSION['username']})</a>
                </div>
_END;
    }

    // CHECK FOR CURRENT USER BEING LOGGED IN AS REGISTERED USERS
    else if (isset($_SESSION['loggedIn']) && ($_SESSION['username']!='admin')) {
        echo <<<_END
                    <div class="navbar">
                        <a href="index.php">Home</a>
                          <a href="lists.php">Toy Lists</a>
                         <a href="account.php">My Account <img style="vertical-align:text-top" height="20" src="{$_SESSION['picture']}"> </a>
                        <a href="signout.php">Logout ({$_SESSION['username']})</a>
                    </div>
_END;
    }

    // NO ACCOUNT - PUBLIC VIEW OF THE SITE MENU
    else {
        echo <<<_END
                <div class="navbar">
                    <a href="index.php">Home</a>
                     <a href="lists.php">Toy Lists</a>
                    <a href="login.php">Login / Register</a>
                </div>
   
_END;
    }

    echo "<div class=\"logo\"><img src=\"img/tw-logo_white.png\" alt=\"The Toy Workshop logo\" height=\"125\"></div>";
    echo "</head>";

?>