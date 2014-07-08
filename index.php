<?php
require('./config/config.php');
$TITLE=$team_name.' Softball';
require('./config/header.php');
?>

<h1><?php echo $TITLE ?></h1>

<hr>

<h3><a href="<?php echo $stat_dir ?>/playerbios.php">Player Info</a></h3>
<h3><a href="<?php echo $stat_dir ?>/gamelisting.php">Game Info</a></h3>
<h3><a href="<?php echo $stat_dir ?>/plays.php">Play-By-Play</a></h3>
<h3><a href="<?php echo $stat_dir ?>/playerstats.php">Player Statistics</a></h3>

<?php
require('./config/footer.php');
?>
