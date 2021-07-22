<?php

// what year is it?
$year = date("Y");

// output the HTML footer, end the body, and end the HTML
echo <<<_END
       <p align="center"><font size="2"><b>&copy The Toy Workshop {$year}</b><br>(Toy information and their images come from from Amazon.co.uk)</font></p>
            </div>    
        </body>
    </html>

_END;
?>