<?php
// This is the landing page for the Toy Workshop site
// Remember that most Web Servers default to a file with the name index.* when a directory URL is given

// execute the header script:
require_once "header.php";

echo <<<_END
    <body>

        <div class="main">

            <h3>Welcome to the Toy Workshop!</h3>
            <p>
                 Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>

            <p>
                <img src="img/chess.jpg" alt="Chess Pieces" style="padding-right: 5px; float:left;width:250px;">
                Vitae nunc sed velit dignissim sodales ut eu. Volutpat commodo sed egestas egestas. Interdum varius sit amet mattis vulputate. Aenean sed adipiscing diam donec adipiscing tristique. Dignissim enim sit amet venenatis. Mauris sit amet massa vitae. Proin nibh nisl condimentum id venenatis a. Id aliquet lectus proin nibh nisl. Dictum varius duis at consectetur lorem donec massa sapien. Vulputate eu scelerisque felis imperdiet proin fermentum leo. 
            </p>

            <p>
                Non curabitur gravida arcu ac tortor dignissim convallis aenean. Turpis egestas pretium aenean pharetra magna ac placerat. Sed odio morbi quis commodo odio aenean sed. Interdum varius sit amet mattis vulputate. Nunc pulvinar sapien et ligula ullamcorper. Cras adipiscing enim eu turpis egestas pretium. Turpis egestas sed tempus urna et. Donec adipiscing tristique risus nec feugiat. Integer vitae justo eget magna fermentum iaculis. 
            </p>

            <p>
                 Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
            </p>
            
_END;

// finish of the HTML for this page:
require_once "footer.php";

?>