<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?=$TITLE?></title>
	<link rel="shortcut icon" href="<?=$stat_dir?>/gifs/softball.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?=$stat_dir?>/softballstats.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style type="text/css">
		body {background: <?=$html_bgcolor?> url(<?=$html_background_image?>); color:<?=$html_text_color?>;
		}
		a:link {color:<?=$html_link_color?>;}
		a:visited {color:<?=$html_vlink_color?>;}
		a:hover, h1 {color:<?=$html_alink_color?>;}
		td.topstats {color:<?=$html_high_score_color?>;}
		td.even, tr.even td {background-color:<?=$html_row_highlight_color?>;}
		div#nav a:link, div#nav a:visited {color:<?=$html_link_color?>;
			border-bottom-color:<?=$html_link_color?>; border-left-color:<?=$html_link_color?>; }
		div#nav a:hover {color:<?=$html_alink_color?>;
			border-bottom-color:<?=$html_alink_color?>; border-left-color:<?=$html_alink_color?>; }
  	</style>
</head>

<body>

<img src="<?=$stat_dir?>/gifs/banner2.gif" class="logo" alt="<?=$team_name?>"
width="400" height="130" border="0">

<br />

<div id="nav">
<a href="<?=$stat_dir?>/">Home</a>
<a href="<?=$stat_dir?>/playerbios.php">Player Info</a>
<a href="<?=$stat_dir?>/gamelisting.php">Game Info</a>
<a href="<?=$stat_dir?>/plays.php">Play-by-Play</a>
<a href="<?=$stat_dir?>/playerstats.php">Player Statistics</a>

<?php if ($show_admin_links) { ?>
 <hr />
 <a href="<?=$stat_dir?>/admin/">Admin</a>
 <a href="<?=$stat_dir?>/admin/season.php">Season Admin</a>
 <a href="<?=$stat_dir?>/admin/game.php">Game Admin</a>
 <a href="<?=$stat_dir?>/admin/player.php">Player Admin</a>
 <a href="<?=$stat_dir?>/admin/plays.php">Play Admin</a>
<?php } ?>



</div>

<div id="content">
