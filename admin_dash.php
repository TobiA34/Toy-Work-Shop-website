<?php

// This is the dashboard for the ADMIN user only
// It shows some graphs about useful info they might need

// execute the header script:
require_once "header.php";
require_once "credentials.php";

// check that the ADMIN user is logged in
if (isset($_SESSION['loggedIn']) && ($_SESSION['username']=='admin')) {


        // connect to the host:
        $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport);
        // exit the script with a useful message if there was an error:
        if (!$connection)
        {
            die("Connection failed: " . $mysqli_connect_error);
        }

        // Fetch the Telemetry Data from the DB
        // connect to our database:
        mysqli_select_db($connection, $dbname);
        // run query to get the contents of the table
        $query = "SELECT * FROM loginTracking";
        // this query can return data ($result is an identifier):
        $result = mysqli_query($connection, $query);
        // how many rows came back?:
        $n = mysqli_num_rows($result);

        // create a HEREDOC to hold the Google Charts script
        echo <<<_END
        <!--Load the AJAX API-->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script type="text/javascript">
                
        // Load the Visualization API and the corechart package - notice the 'controls' portion added
        google.charts.load('current', {'packages':['corechart', 'controls']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);
                
        // Callback that creates and populates a data table, instantiates the pie chart, passes in the data and
        // draws it.
            function drawChart() {
              
                var jsonData = $.ajax({
                        url: "api/toys.php?toys=yes",
                        dataType: "json",
                        async: false
                        }).responseText;          
                                              
                // Create our data table out of JSON data loaded from server.
                 var data = new google.visualization.DataTable(jsonData);
                 
                 // create a dataview
                 var stockData = new google.visualization.DataView(data);
                stockData.setColumns(['Name', 'Stock']);
                 
                 
                 // Create a dashboard.
                  var dashboard = new google.visualization.Dashboard(
                  document.getElementById('stock_div_dash'));
                  
                  // Create a range slider, passing some options
                  var slider = new google.visualization.ControlWrapper({
                              'controlType': 'NumberRangeFilter',
                              'containerId': 'stock_filter',
                              'options': {
                              'filterColumnLabel': 'Stock',
                              'ui':{
                                    'orientation': 'vertical'
                                    }
                              }
                            });
                  
                 // Create our data table out of JSON data loaded from server.
                 var stockData = new google.visualization.DataView(data);
                    stockData.setColumns(['Name', 'Stock']);
                              
            
                   
                        var chart = new google.visualization.ChartWrapper({
                               'chartType': 'ColumnChart',
                               'containerId': 'stock_column',
                               'options': {
                                   'title':'Toy Workshop Stock Levels',
                                   'width': 800,
                                   'height': 360,
                                   'legend': 'none'
                                }
                        }); 
                 
                   dashboard.bind(slider, chart);
                   dashboard.draw(stockData);
                   
                   
                   
                   
///////////////////////////////////
// Deal with the Telemetry Graph //
///////////////////////////////////
                  
                   var data2 = google.visualization.arrayToDataTable([
                    ['Date', 'Logins'],
_END;

        // loop over all rows, to fill the DataTable
        for ($i = 0; $i < $n; $i++) {
            // fetch one row as an associative array (elements named after columns):
            $row = mysqli_fetch_assoc($result);
            // set the size of the bar to plot based upon number of votes versus total votes
            echo "['{$row['date']}', {$row['number']}],";
        }

        // we're finished with the database, close the connection:
        mysqli_close($connection);

echo <<<_END
                    ]);

        var options2 = {
          title: 'Daily Logins',
          hAxis: {title: 'Date'},
          vAxis: {title: 'Logins', minValue: 0},
          width: 850,
          height: 360,
          legend: 'none'
        };

        var chart = new google.visualization.AreaChart(document.getElementById('telemetry_area'));
        chart.draw(data2, options2);

                   

        }
                    </script>
                           
_END;

        // setup a display structure to show the dashboards
        echo <<<_END
        <table cellspacing='0' cellpadding='0'>
        
        <!-- DIV TO HOLD TELEMETRY GRAPH -->
        <tr><td align="center" colspan="3"><div id="telemetry_area"></div></td></tr> 
        
        <!-- DIV TO HOLD STOCK GRAPH -->
        <tr><td align="center"><div id="stock_div_dash">
            <td><div id="stock_filter"></div></td>
            <td><div id="stock_column"></div></td>
        </div></td></tr>
                  
        </table>
_END;

    }

    // if the current user isn't the ADMIN - display a message to this effect
    else {
        echo "Sorry, you must be an administrator to access this resource";
    }


// finish of the HTML for this page:
require_once "footer.php";

?>