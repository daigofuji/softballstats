<?php
// General Softball Stuff
  // Name used in page titles
  $team_name = 'The Demonstrators';

  // Show the Nice Play column on stat page.  '1' = show, '0' = hide
  $show_np = '1';

  // The directory that SoftballStats is available on your web server
  // Be sure to prepend a / character, and don't append a / character
  // i.e http://www.your-domain.com<$stat_dir>
  //leave . for relative
  $stat_dir = '.';

  // Use built in authorization for Admin pages.
  // !! If you set this to '0', be sure to setup authorization for /admin with your web server or set $show_admin_links to '0' !!
  $auth_admin_pages = '1';

  // User name you will use to access the Admin pages
  $admin_user = 'admin';

  // Password you will use to access the Admin pages
  $admin_pass = 'adminpass';
  
  // Show the Admin links in the left navagation bar.  '1' = show, '0' = hide
  $show_admin_links = '1';

  // Number of games in a season 
  $games_in_season = '14';


// Softball Database Stuff
  // Name of the MySQL database you created
  $sql_db_name = 'softballstats';

  // User that the scripts will use to access the DB
  $sql_username = 'root';

  // $sql_username's password
  $sql_pass = 'root';

  // MySQL server
  $sql_server = 'localhost';

  // MySQL server port
  $sql_server_port = '3306';

// Demo Mode (read-only Admin pages)
  $demo_mode = '0';


/********** DO NOT EDIT BELOW THIS LINE! ******************/


// Version Info
$version='1.2.1';

// Functions
function opendb () {
  // Returns true or false depending on whether or not the databse was opened
  global $sql_db_name, $sql_server, $sql_server_port, $sql_username, $sql_pass;
  $link = mysql_connect($sql_server.':'.$sql_server_port, $sql_username, $sql_pass);
  $connect = mysql_select_db ($sql_db_name);
}

function seasoninfo ($id) {
  // Returns an array with season.ID and season.Description of the specified season.ID or the
  // default season if no ID is supplied
  opendb();
  if ($id == NULL) {
    $recSeason = mysql_query('SELECT * FROM season WHERE DefaultSeason = 1');
    $rowSeason = mysql_fetch_assoc($recSeason);
    if ($rowSeason) {
      return array ($rowSeason['ID'], stripslashes($rowSeason['Description']));
    } else {
      print '<H2>There are no Default Seasons<br />Please correct this in the Season Admin page</h2>';
      exit;
    }
  } else {
    $recSeason = mysql_query('SELECT * FROM season WHERE ID = '.$id);
    $rowSeason = mysql_fetch_assoc($recSeason);
    if ($rowSeason) {
      return array ($rowSeason['ID'], stripslashes($rowSeason['Description']));
    } else {
      print '<H2>There are no Seasons with the ID of '.$id;
      exit;
    }
  }
}

function authorize() {
  // Checks user/password for Admin pages
  global $version, $demo_mode, $auth_admin_pages;
  if ($demo_mode == '0' && $auth_admin_pages <> '0') {
    header('WWW-Authenticate: Basic realm="Stat Admin Pages"');
    header('HTTP/1.0 401 Unauthorized');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<html><head>
<title>401 Authorization Required</title>
</head><body>
<h1>Authorization Required</h1>
This server could not verify that you
are authorized to access the document
requested.  Either you supplied the wrong
credentials (e.g., bad password), or your
browser doesn't understand how to supply
the credentials required.<p>
<h2>Please enter the correct administrative password</h2>
<hr />
<em>SoftballStats $version</em>
</body></html>";
    exit;
  }
}
?>


