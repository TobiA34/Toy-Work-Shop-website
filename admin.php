<?php

// This is the ADMIN TOOLS page
// Only the Admin user is allowed to see its contents
// From here, the Admin can change many aspects of the site
// Including users, lists, toys and see dashboard information

// execute the header script:
require_once "header.php";

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
    
    padding: 4px;
}
</style>
_END;


if (isset($_SESSION['loggedIn']) && ($_SESSION['username']=='admin')) {

       // USER logged in is the ADMIN - show the options
        echo <<<_END
        
        <div class="generalInfo"><fieldset><legend><h2>Admin Tools</h2></legend><table>
        <tr><th align="left" width="90">Manage Site</th></tr>
        <tr><td align="center" width="90"><a href="admin_users.php"><img width="55" src="img/usersAdmin.png"></a></td><td align="left"><img height="15" src="img/edit.png"> <a href="admin_users.php">USERS</a></td></tr>
        <tr><td align="center" width="90"><a href="admin_users.php"><img width="55" src="img/listsAdmin.png"></a></td><td align="left"><img height="15" src="img/edit.png"> <a href="admin_lists.php">LISTS</a></td></tr>
        <tr><td align="center" width="90"><a href="admin_users.php"><img width="55" src="img/toysAdmin.png"></a></td><td align="left"><img height="15" src="img/edit.png"> <a href="admin_toys.php">TOYS</a></td></tr>
         <tr><td align="center" width="90"><a href="admin_dash.php"><img width="55" src="img/dashboard.png"></a></td><td align="left"><img height="15" src="img/edit.png"> <a href="admin_dash.php">DASHBOARD</a></td></tr>
        </table></fieldset></div>
_END;
        }

        else {
        echo "Sorry, you must be an administrator to access this resource";
    }

// finish of the HTML for this page:
require_once "footer.php";

?>