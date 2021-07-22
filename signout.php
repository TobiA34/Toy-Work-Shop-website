<?php

// The main job of this script is to end the session
// execute the header script:
require_once "header.php";

if (!isset($_SESSION['loggedIn']))
{
    // user isn't logged in, display a message saying they must be:
    echo "You must be logged in to view this page.<br>";
}
else
{
    // user just clicked to logout, so destroy the session data:
    //first, grab their name and photo url to put in a cookie, so we can recognise them if they decide to login again
    $nameTemp = $_SESSION['firstName'];
    $pictureTemp = $_SESSION['picture'];
    $username = $_SESSION['username'];

    // next clear the session superglobal array:
    $_SESSION = array();
    // then the cookie that holds the session ID:
    setcookie(session_name(), "", time() - 2592000, '/');
    // then the session data on the server:
    session_destroy();
    // keep their name and photo in cookies for the next day to 'remember' them
    setcookie('firstName', "$nameTemp", time() + 86400, '/');
    setcookie('picture', "$pictureTemp", time() + 86400, '/');
    setcookie('username', "$username", time() + 86400, '/');

    // display message confirming signout has worked
    echo <<<_END
<div class="loginDialog"><fieldset><legend><h2>Logged Out Successfully</h2></legend>
                <table align="center" border="0" cellpadding="2"><tr><td>
                <br>You have successfully logged out, please <a href="login.php">click here</a><br><br>
                </td></tr></table></fieldset></div>
_END;

}

// finish of the HTML for this page:
require_once "footer.php";

?>