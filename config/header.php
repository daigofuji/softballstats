<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $TITLE; ?></title>
	<link rel="shortcut icon" href="<?php echo $stat_dir?>/img/softball.ico" type="image/x-icon" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.2.3/css/foundation.min.css">
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/725b2a2115b/integration/foundation/dataTables.foundation.css">
  <link rel="stylesheet" href="<?php echo $stat_dir?>/css/softballstats.css" type="text/css" />

</head>

<body>

<nav class="top-bar" data-topbar>
  <ul class="title-area">
    <li class="name">
      <h1><a href="<?php echo $stat_dir; ?>/"><?php echo $team_name?></a></h1>
    </li>
     
    <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
  </ul>

  <section class="top-bar-section">
    <!-- Right Nav Section -->
	<?php if ($show_admin_links) { ?>
    <ul class="right">
      <li class="has-dropdown">
        <a href="<?php echo $stat_dir?>/admin/">Admin</a>
        <ul class="dropdown">
			<li><a href="<?php echo $stat_dir?>/admin/season.php">Season Admin</a></li>
			<li><a href="<?php echo $stat_dir?>/admin/game.php">Game Admin</a></li>
			<li><a href="<?php echo $stat_dir?>/admin/player.php">Player Admin</a></li>
			<li><a href="<?php echo $stat_dir?>/admin/plays.php">Play Admin</a></li>
        </ul>
      </li>
    </ul>
	<?php } ?>
    <!-- Left Nav Section -->
    <ul class="left">
		<li><a href="<?php echo $stat_dir?>/playerbios.php">Player Info</a></li>
		<li><a href="<?php echo $stat_dir?>/gamelisting.php">Game Info</a></li>
		<li><a href="<?php echo $stat_dir?>/plays.php">Play-by-Play</a></li>
		<li><a href="<?php echo $stat_dir?>/playerstats.php">Player Statistics</a></li>
    </ul>
  </section>
</nav>

<div class="row">
	<div class="small-12 column">
