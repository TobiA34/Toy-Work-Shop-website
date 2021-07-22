<?php

// read in the details of our MySQL server:
require_once "credentials.php";

// Salts to be added to the start and end of the password when entered
$preSalt = "QpfE9q";
$postSalt = "WdcDXi";

// connect to the host:
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, '', $dbport);

// exit the script with a useful message if there was an error:
if (!$connection)
{
    die("Connection failed: " . $mysqli_connect_error);
}

// build a statement to create a new database:
$sql = "CREATE DATABASE IF NOT EXISTS " . $dbname;

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql))
{
    echo "Database created successfully, or already exists<br>";
}
else
{
    die("Error creating database: " . mysqli_error($connection));
}

// connect to our database:
mysqli_select_db($connection, $dbname);

    // unset checking for foreign key checks - resetting the DB
    $sql ="SET FOREIGN_KEY_CHECKS=0";
    // no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql))
    {
        echo "Foreign key check set to 0<br>";
    }
    else
    {
        die("Error creating database: " . mysqli_error($connection));
    }

    // connect to our database:
    mysqli_select_db($connection, $dbname);

    /////////////////////////////////////////
    ////////////// USERS TABLE //////////////
     /////////////////////////////////////////

// if there's an old version of our table, then drop it:
    $sql = "DROP TABLE IF EXISTS users";

// no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql)) {
        echo "Dropped existing table: users<br>";
    } else {
        die("Error checking for existing table: " . mysqli_error($connection));
    }

// make our table:
    $sql = "CREATE TABLE users (username VARCHAR(32), password VARCHAR(40), firstName VARCHAR(32), 
    lastName VARCHAR(64), email VARCHAR(64), telephone VARCHAR(25), dob DATE, picture VARCHAR(64), 
    lastLogin DATE, PRIMARY KEY (username))";

// no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql)) {
        echo "Table created successfully: users<br>";
    } else {
        die("Error creating table: " . mysqli_error($connection));
    }

    // put some data in our table:
    $usernames[] = 'admin'; $passwords[] = 'qwerty123456'; $firstNames[] = 'Admin'; $lastNames[] = 'Toy Workshop';
    $emails[] = 'admin@toyworkshop.some-site.co.uk'; $telephones[] = '+44(0)9876543210'; $dobs[] = '1900-1-01';
    $pictures[] = 'img/admin.jpg'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'barryg'; $passwords[] = 'letmein'; $firstNames[] = 'Barry'; $lastNames[] = 'Giles';
    $emails[] = 'barryg@some-domain-pd.com'; $telephones[] = '+44(0)1234567890'; $dobs[] = '1976-11-01';
    $pictures[] = 'img/barryg.jpg'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'heavygorilla136'; $passwords[] = 'texas1'; $firstNames[] = 'Scarlett'; $lastNames[] = 'Hanson';
    $emails[] = 'scarlett.hanson@example.com'; $telephones[] = '038-23512235'; $dobs[] = '1968-11-26';
    $pictures[] = 'img/heavygorilla136.jpg'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'bigtiger271'; $passwords[] = 'spike1'; $firstNames[] = 'Norman'; $lastNames[] = 'Fleming';
    $emails[] = 'norman.fleming@example.com'; $telephones[] = '+1(525)-088-4393'; $dobs[] = '1948-02-01';
    $pictures[] = 'img/bigtiger271.jpg'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'ticklishduck182'; $passwords[] = 'trinity200'; $firstNames[] = 'Maddison'; $lastNames[] = 'Hughes';
    $emails[] = 'maddison.hughes@example.com'; $telephones[] = '0131 100 1102'; $dobs[] = '1966-02-27';
    $pictures[] = 'img/ticklishduck182.jpg'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'lazybutterfly750'; $passwords[] = '99south'; $firstNames[] = 'Omer'; $lastNames[] = 'Moura';
    $emails[] = 'omer.moura@example.com'; $telephones[] = '(86) 1884-9179'; $dobs[] = '1951-02-08';
    $pictures[] = 'img/lazybutterfly750.jpg'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'happyduck111'; $passwords[] = 'love2018'; $firstNames[] = 'Inès'; $lastNames[] = 'Arnaud';
    $emails[] = 'inès.arnaud@example.com'; $telephones[] = '03-75-58-23-93'; $dobs[] = '1996-09-23';
    $pictures[] = 'img/blank.png'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'strongbad'; $passwords[] = 'web2019'; $firstNames[] = 'Strong'; $lastNames[] = 'Bad';
    $emails[] = 'strongbad@sbemails.net'; $telephones[] = '042-160-68-62'; $dobs[] ='2001-03-22';
    $pictures[]='img/strongbad.png'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'doug'; $passwords[] = 'password'; $firstNames[] = 'Douglas'; $lastNames[] = 'Robert';
    $emails[] = 'douggy@roberts-ltd.co.uk';$telephones[] = '(33) 9179-8969'; $dobs[] ='1998-10-27';
    $pictures[]='img/doug.jpg'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'edith'; $passwords[] = 'cake123'; $firstNames[] = 'Edith'; $lastNames[] = 'Artois';
    $emails[] = 'edith@cafe-rene.fr'; $telephones[] = '047-154-85-63'; $dobs[] ='1970-02-03';
    $pictures[]='img/blank.png'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'col_gruber'; $passwords[] = 'tank29'; $firstNames[] = 'Hubert'; $lastNames[] = 'Gruber';
    $emails[] = 'col_gruber@uniform.de'; $telephones[] = '02-04-65-76-40'; $dobs[] ='1974-01-04';
    $pictures[]='img/blank.png'; $lastLogins[] ='2018-12-01';

    $usernames[] = 'yvetteb'; $passwords[] = 'coffee'; $firstNames[] = 'Yvette'; $lastNames[] = 'Blanche';
    $emails[] = 'yvette@allo.fr'; $telephones[] = '0973-249-5650'; $dobs[] ='1986-08-10';
    $pictures[]='img/yvetteb.jpg'; $lastLogins[] ='2018-12-01';


// loop through the arrays above and add rows to the table:
    for ($i = 0; $i < count($usernames); $i++) {

        // SALT and HASH THE PRE-DEFINED PASSWORDS
        $passwords[$i] = $preSalt . $passwords[$i] . $postSalt;
        $passwords[$i] = sha1($passwords[$i]);

        $sql = "INSERT INTO users (username, password, firstName, lastName, email, telephone, dob, picture, lastLogin)
        VALUES ('$usernames[$i]', '$passwords[$i]', '$firstNames[$i]', '$lastNames[$i]', '$emails[$i]', 
        '$telephones[$i]', '$dobs[$i]', '$pictures[$i]', '$lastLogins[$i]')";

            // no data returned, we just test for true(success)/false(failure):
            if (mysqli_query($connection, $sql)) {
                echo "row inserted<br>";
            }

            else {
                die("Error inserting row: " . mysqli_error($connection));
                }
    }


//////////////////////////////////////////////
//////////////// LISTS TABLE /////////////////
//////////////////////////////////////////////

// if there's an old version of our table, then drop it:
    $sql = "DROP TABLE IF EXISTS lists";

// no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql)) {
        echo "Dropped existing table: lists<br>";
    } else {
        die("Error checking for existing table: " . mysqli_error($connection));
    }

// make our table:
    $sql = "CREATE TABLE lists (id INT NOT NULL AUTO_INCREMENT, name VARCHAR(64), username VARCHAR(32),
    created DATE, updated DATE, visible BOOLEAN, PRIMARY KEY (id), FOREIGN KEY (username) REFERENCES users(username))";

// no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql)) {
        echo "Table created successfully: lists<br>";
    } else {
        die("Error creating table: " . mysqli_error($connection));
    }

// put some data in our table:
    $names = $usernames = $created = $updated = $visibles = array(); // clear this array (as we already used it above)

    $names[] = "Test List for Admin 1"; $usernames[] = "admin"; $created[] = $updated[] = "2018-12-01"; $visibles[] = 0;
    $names[] = "Doug''s 2018 Xmas List"; $usernames[] = "doug"; $created[] = $updated[] = "2018-12-01"; $visibles[] = 1;
    $names[] = "Doug''s Guilty Pleasures"; $usernames[] = "doug"; $created[] = $updated[] = "2018-12-01"; $visibles[] = 0;
    $names[] = "Edith''s Ideas"; $usernames[] = "edith"; $created[] = $updated[] = "2018-12-01"; $visibles[] = 1;
    $names[] = "Inès - Wish List"; $usernames[] = "happyduck111"; $created[] = $updated[] = "2018-12-01"; $visibles[] = 0;
    $names[] = "Birthday"; $usernames[] = "edith"; $created[] = $updated[] = "2017-05-11"; $visibles[] = 1;


// loop through the arrays above and add rows to the table:
    for ($i = 0; $i < count($names); $i++) {
        $sql = "INSERT INTO lists (name, username, created, updated, visible) VALUES ('$names[$i]', '$usernames[$i]', '$created[$i]', '$updated[$i]', '$visibles[$i]')";

        // no data returned, we just test for true(success)/false(failure):
        if (mysqli_query($connection, $sql)) {
            echo "row inserted<br>";
        } else {
            die("Error inserting row: " . mysqli_error($connection));
        }
    }

//////////////////////////////////////////////
//////////////// TOYS TABLE /////////////////
//////////////////////////////////////////////

// if there's an old version of our table, then drop it:
    $sql = "DROP TABLE IF EXISTS toys";

// no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql)) {
        echo "Dropped existing table: toys<br>";
    } else {
        die("Error checking for existing table: " . mysqli_error($connection));
    }

// make our table:
    $sql = "CREATE TABLE toys (id INT NOT NULL AUTO_INCREMENT, toyName VARCHAR(64), price DECIMAL(5,2), ages VARCHAR(16),
            creator VARCHAR(32), stock INT, picture VARCHAR(64), description VARCHAR(900), PRIMARY KEY (id))";

// no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql)) {
        echo "Table created successfully: toys<br>";
    } else {
        die("Error creating table: " . mysqli_error($connection));
    }

// put some data in our table:
    $usernames = array(); // clear this array (as we already used it above)

    $names = $prices = $ages = $creators = $stocks = $pictures = $descriptions = array();

    $names[] = "Pop Up Pirate"; $prices[] = "8.99"; $ages[] = "4+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/pirate.jpg";
    $descriptions[]= "The classic children''s action game! Take turns to slide your swords into the barrel. But be careful as one wrong move will send Pirate Pete flying. Whose sword will be the first to pop him out of his barrel?";

    $names[] = "Guess Who?"; $prices[] = "9.99"; $ages[] = "6+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/guess.jpg";
    $descriptions[]= "It''s the guess who? game – the original guessing game! this guess who? game goes back to the tabletop style boards, styled after the original, rather than handheld boards. Each player chooses a mystery character and then using yes or no questions, they try to figure out the other player’s mystery character.";

    $names[] = "Connect4"; $prices[] = "9.90"; $ages[] = "6+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/connect4.jpg";
    $descriptions[]= "Challenge a friend to disc-dropping fun with the simple game of connect four. Drop your red or yellow discs in the grid and be the first to get four in a row to win. If your opponent is getting too close to four in a row, block them with your own disc. Whoever wins can pull out the slider bar to release all the discs and start the fun all over again. ";

    $names[] = "Operation"; $prices[] = "13.49"; $ages[] = "6+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/operation.jpg";
    $descriptions[]= "It''s the family favourite Operation game with fun Try Me packaging and classic funny ailments. Cavity Sam is feeling a bit under the weather, and kids will love to operate and make him better. Use the tweezers to take out all of Cavity Sam''s 12 funny ailment parts that parents might remember -- such as a wishbone, Charlie horse, and Adam''s apple. Players choose a doctor card and operate to remove that ailment from Sam, and collect the money if they can avoid the buzz. The player with the most money wins.";

    $names[] = "Toilet Trouble"; $prices[] = "16.38"; $ages[] = "4+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/toilet.jpg";
    $descriptions[]= "Share some hilarious and suspense-filled moments as players take turns spinning the toilet paper roll, flushing the toilet handle and hoping they don''t get sprayed with water. The number that turns up on the paper roll spinner dictates how many times each player must flush. Players are safe if they hear the flushing sound, but no water is sprayed. Who knows which flush will be the one that sprays water, eliminating that player? Continue taking turns spinning the roll and flushing until one player has not been sprayed.";

    $names[] = "Hairdorables Doll"; $prices[] = "11.99"; $ages[] = "3+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/hairdorables.jpg";
    $descriptions[]= "Introducing Noah and the Hairdorables! The girl squad with ''Big Hair Don''t Care'' attitudes. Hairdorables took off when Noah, a sweet and funny vlogger with a passion for hair-styling, decided to share her side-braiding tutorial on YouTube.";

    $names[] = "Sylvanian Families Grand Department Store"; $prices[] = "69.99"; $ages[] = "3+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/sylvanian.jpg";
    $descriptions[]= "The grand department store is an iconic department store in town with fancy decorations and eye-catching engravings. The set comes with a two-storey department store building and an entrance tower; there’s also a lookout balcony, ideal for spotting your friends from. You can decorate the various shops with the sets included, like the boutique fashion set, cosmetic beauty set, fashion showcase set, chocolate lounge, and the town girl series - chocolate rabbit. The main building has a manual lift that can carry figures to the upper storey if their legs are tired and the windows are detachable and can be replaced with the windows from other town series items. You can open and close the main building to enjoy different layouts or combine the grand department store with other buildings in the town series. ";

    $names[] = "HATCHIMALS Mystery Egg"; $prices[] = "39.99"; $ages[] = "5+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/hatchimals.jpg";
    $descriptions[]= "For the first time in Hatchimals history, who you’ll hatch is a total mystery! inside one egg are four possibilities: you could hatch a super fluffy bunwee, Pandora, hedgyhen or elefy! with soft fur, ears and tails, these mysterious new friends love cuddles and snuggles. Raise your Hatchimals mystery from baby to toddler to kid and experience all new music and games! ";

    $names[] = "Crate Creatures Surprise- Blizz"; $prices[] = "31.99"; $ages[] = "3+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/crate.jpg";
    $descriptions[]= "Each creature comes in their own crate, equipped with a crowbar to unleash the beast! pull their tongues and your creatures will vibrate, make gross and funny noises. Turn them upside down or tip them over for unique creature sounds. Each creature has a unique feature that you control! unique foods for each creature, feed your creature and he will make chomping noises! put your creature back in their crate and unleash them over and over. ";

    $names[] = "Dobble"; $prices[] = "10.99"; $ages[] = "6+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/dobble.jpg";
    $descriptions[]= "The Smash Hit Party Game. Dobble is a speedy observation game where players race to match the identical symbol between cards. Reliant on a sharp eye and quick reflexes, Dobble creates excitement for children and adults alike while keeping every player involved in the action.";

    $names[] = "Rubiks Cube"; $prices[] = "8.49"; $ages[] = "8+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/rubiks.jpg";
    $descriptions[]= "The Rubik''s Cube is the classic colour-matching puzzle that''s a great mental challenge at home or on the move. Turn and twist the sides of the cube so that each of the six faces only has one colour. There''s 43 quintillion combinations, but with lots of practise you can solve it in under 10 seconds.";

    $names[] = "Hide and Squeak Eggs"; $prices[] = "7.98"; $ages[] = "6 months +"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/hide.jpg";
    $descriptions[]= "Tomy hide n squeak eggs are suitable way to develop your child''s shape sorting and motor skills and they provide hours of playtime fun. Crack them open to reveal the brightly coloured press-and-cheep chicks, it''s also a shape sorting game, matching egg bases to the correctly shaped holes in the box. ";

    $names[] = "Shopping List"; $prices[] = "5.60"; $ages[] = "3+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/shopping.jpg";
    $descriptions[]= "This award winning game encourages memory and literacy skills in a really fun way that helps children learn. Suitable for 2 to 4 players, each player picks a trolley or basket that they must fill. Turn over one of the 32 cards showing familiar, everyday items from the supermarket such as eggs, tomatoes, washing powder and toothpaste - if they are on your list, you can pop them in your trolley or basket! The winner is the first player to collect all the items on their list and fill their trolley.";

    $names[] = "Twister"; $prices[] = "8.99"; $ages[] = "6+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/twister.jpg";
    $descriptions[]= "Add a twist of fun into any party or family night with the game that ties you up in knots. This TWISTER game is the classic game with 2 more moves. Give the spinner a whirl and see what’s next as you try to keep your hands and feet on the mat. Right foot red. Can you do it?";

    $names[] = "Exploding Kittens"; $prices[] = "19.99"; $ages[] = "7 months +"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/exploding.jpg";
    $descriptions[]= "Exploding Kittens is a card game for people who are into kittens and explosions and laser beams and sometimes goats. In this highly-strategic, kitty-powered version of Russian Roulette, players draw cards until someone draws an Exploding Kitten, at which point they explode, they are dead, and they are out of the game -- unless that player has a defuse card, which can defuse the Kitten using things like laser pointers, belly rubs, and catnip sandwiches. All of the other cards in the deck are used to move, mitigate, or avoid the Exploding Kittens. ";

    $names[] = "Harry Potter Coding Kit"; $prices[] = "79.99"; $ages[] = "6+"; $creators[] = "admin"; $stocks[] = rand(0,100); $pictures[] = "img/coding.jpg";
    $descriptions[]= "Build your own wand. Learn to code with 70+ creative challenges and games. Make magic on a screen, with a wave, twist, and twirl. Create, share, and play with the Kano community.";


// loop through the arrays above and add rows to the table:
    for ($i = 0; $i < count($names); $i++) {
        $sql = "INSERT INTO toys (toyName, price, ages, creator, stock, picture, description) 
        VALUES ('$names[$i]', '$prices[$i]', '$ages[$i]', '$creators[$i]', '$stocks[$i]', '$pictures[$i]', '$descriptions[$i]')";

        // no data returned, we just test for true(success)/false(failure):
        if (mysqli_query($connection, $sql)) {
            echo "row inserted<br>";
        } else {
            die("Error inserting row: " . mysqli_error($connection));
        }
    }


//////////////////////////////////////////////
//////////////// CONTENTS TABLE /////////////////
//////////////////////////////////////////////

// if there's an old version of our table, then drop it:
$sql = "DROP TABLE IF EXISTS contents";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
    echo "Dropped existing table: contents<br>";
} else {
    die("Error checking for existing table: " . mysqli_error($connection));
}

// make our table:
$sql = "CREATE TABLE contents (pairID INT NOT NULL AUTO_INCREMENT, listID INT NOT NULL, toyID INT NOT NULL, PRIMARY KEY (pairID), FOREIGN KEY(listID) REFERENCES lists(id), FOREIGN KEY (toyID) REFERENCES toys(id))";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
    echo "Table created successfully: contents<br>";
} else {
    die("Error creating table: " . mysqli_error($connection));
}

// put some data in our table:
$toydID = $listID = array(); // clear this array (as we already used it above)

// populate list 1
$listID[] = 1; $toysID[] = 1;
$listID[] = 1; $toysID[] = 3;
$listID[] = 1; $toysID[] = 4;
$listID[] = 1; $toysID[] = 7;

// populate list 2
$listID[] = 2; $toysID[] = 1;
$listID[] = 2; $toysID[] = 2;
$listID[] = 2; $toysID[] = 3;
$listID[] = 2; $toysID[] = 4;
$listID[] = 2; $toysID[] = 6;
$listID[] = 2; $toysID[] = 7;
$listID[] = 2; $toysID[] = 8;

// populate list 4
$listID[] = 4; $toysID[] = 5;
$listID[] = 4; $toysID[] = 10;

// populate list 5
$listID[] = 5; $toysID[] = 2;
$listID[] = 5; $toysID[] = 4;
$listID[] = 5; $toysID[] = 6;
$listID[] = 5; $toysID[] = 8;
$listID[] = 5; $toysID[] = 9;

// populate list 6
$listID[] = 6; $toysID[] = 1;
$listID[] = 6; $toysID[] = 2;
$listID[] = 6; $toysID[] = 7;
$listID[] = 6; $toysID[] = 8;
$listID[] = 6; $toysID[] = 10;


// loop through the arrays above and add rows to the table:
for ($i = 0; $i < count($listID); $i++) {

        $sql = "INSERT INTO contents (listID, toyID) VALUES ('$listID[$i]', '$toysID[$i]')";
        // no data returned, we just test for true(success)/false(failure):
        if (mysqli_query($connection, $sql)) {
            echo "row inserted<br>";
        } else {
            die("Error inserting row: " . mysqli_error($connection));
        }
}

/////////////////////////////////////
/// LOGIN TELEMETRY TABLE////////////
/////////////////////////////////////

// if there's an old version of our table, then drop it:
$sql = "DROP TABLE IF EXISTS loginTracking";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
    echo "Dropped existing table: loginTracking<br>";
} else {
    die("Error checking for existing table: " . mysqli_error($connection));
}

// make our table:
$sql = "CREATE TABLE loginTracking (date DATE, number BIGINT, PRIMARY KEY (date))";

// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql)) {
    echo "Table created successfully: loginTracking<br>";
} else {
    die("Error creating table: " . mysqli_error($connection));
}

// set some dummy telemetry data for the site
$dates = $numbers = array();

// what is today's date (day)? Generate to day prior...
$target = date("d");

for ($i=1; $i<$target; $i++) {
    //create some dummy dates for November 2019
    $dates[] = "2019-11-{$i}";
    //create some dummy login values for November 2019 dates

    // square exponential generation with a little noise
    $numbers[] = (($i+rand(0,1))*($i+rand(0,1)));

    // random generation
    // $numbers[] = rand(1,150);
}

// loop through the arrays above and add rows to the table:
for ($i = 0; $i < count($dates); $i++) {

    $sql = "INSERT INTO loginTracking (date, number) VALUES ('$dates[$i]', '$numbers[$i]')";
    // no data returned, we just test for true(success)/false(failure):
    if (mysqli_query($connection, $sql)) {
        echo "row inserted<br>";
    } else {
        die("Error inserting row: " . mysqli_error($connection));
    }
}


// reset checking for foreign key checks
$sql ="SET FOREIGN_KEY_CHECKS=1";
// no data returned, we just test for true(success)/false(failure):
if (mysqli_query($connection, $sql))
{
    echo "Foreign key checks set to 1<br>";
}
else
{
    die("Error creating database: " . mysqli_error($connection));
}


// we're finished, close the connection:
mysqli_close($connection);

?>