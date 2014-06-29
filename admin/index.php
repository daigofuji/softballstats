<?php
require('../config/config.php');
$TITLE="$team_name Softball Admin Pages";

if (!isset($HTTP_SERVER_VARS['PHP_AUTH_PW']) || !isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) || $HTTP_SERVER_VARS['PHP_AUTH_PW'] <> $admin_pass || strtoupper($HTTP_SERVER_VARS['PHP_AUTH_USER']) <> strtoupper($admin_user)) {
 authorize();
}


require('../config/header.php');
?>

<h1><?=$TITLE?></h1>
<hr />
<h3><A HREF="<?=$stat_dir?>/admin/season.php">Add & Remove, and Update Seasons</a></h3>
<h3><A HREF="<?=$stat_dir?>/admin/game.php">Add & Remove, and Update Games</a></h3>
<h3><A HREF="<?=$stat_dir?>/admin/player.php">Add, Remove, and Update Players</a></h3>
<h3><A HREF="<?=$stat_dir?>/admin/plays.php">Add & Remove Plays</a></h3>
<hr />
To start a new season:
<ol>
  <li>Add a Season</li>
  <li>Add Games</li>
  <li>Add Players</li>
  <li>Add Plays</li>
  <li>Update Opposing Team Scores (Using <A HREF="game.php">Game Admin</a>)</li>
</ol>

<?php
require('../config/footer.php');
?>



