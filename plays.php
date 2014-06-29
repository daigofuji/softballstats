<?php
require('./config/config.php');
if (isset($_POST['cboSeason']) && $_POST['cboSeason'] <> '' && is_numeric($_POST['cboSeason'])) {
  $id = $_POST['cboSeason'];
} else {
  $id = NULL;
}
$arr = seasoninfo($id);
$season_id = $arr['0'];
$season_name = $arr['1'];
$TITLE=$team_name.' Play Listing - '.$season_name;
if (!isset($_POST['cboGame'])) {
  if (!isset($_GET['ID']) || $_GET['ID'] == '') {
    $BODY_CODE='onLoad="document.plays.cboGame.focus();"';
  } else {
    $game_id = $_GET['ID'];
  }
} elseif ($_POST['cboGame'] == '0') {
  $BODY_CODE='onLoad="document.plays.cboGame.focus();"';
} else {
  $game_id = $_POST['cboGame'];
}
require('./config/header.php');
if (isset($_GET['ID'])) { 
  if (!is_numeric($_GET['ID'])) { 
    print('<font face="Arial" size="+2"><strong>Invalid ID number!</strong></font>');
    require('./config/footer.php'); 
    exit; 
  } 
}  
?>

<h1><?=$TITLE;?></h1>
<hr />

<form name="bios" method="post">
Switch Season:
<select name="cboSeason" size="1" onchange="document.bios.submit()">
<?php
$recSeason = mysql_query('SELECT * FROM season ORDER BY ID') ;
if ($recSeason) {
  while ($rowSeason = mysql_fetch_assoc($recSeason)) {
    if ((isset($_POST['cboSeason']) && $_POST['cboSeason'] == $rowSeason['ID']) || $rowSeason['DefaultSeason']) {
      print '          <option selected="selected"';
    } else {
      print '          <option';
    }
?>
 VALUE="<?=$rowSeason['ID']?>"><?=stripslashes($rowSeason['Description'])?></option>
<?php
  }
}
?>
</select>
</form>
<p>
<form name="plays" method="post">
<?php
$recGame=mysql_query('SELECT * FROM game WHERE SeasonID = '.$season_id.' ORDER BY GameDate' ) ;
?>
Select Game: <select name="cboGame" size="1" onchange="document.plays.submit()">
  <option value="0"></option>
<?php
if ($recGame) {
  while ($rowGame = mysql_fetch_assoc($recGame)) {
    if (isset($game_id) && $game_id == $rowGame['ID']) {
      $sel = ' selected="selected"';
    } else {
      $sel = '';
    }
?>
    <option<?=$sel?> VALUE="<?=$rowGame['ID']?>"><?=stripslashes($rowGame['OpposingTeam'])?> [<?=date('m/d/y h:i a',
    strtotime($rowGame['GameDate']))?>]</option>
<?php
  }
}
?>
</select>
<?php
if (!isset($game_id) || $game_id == '') {
  require('./config/footer.php');
  exit;
}
$recPlays=mysql_query('SELECT * FROM plays WHERE GameID = '.$game_id.' AND SeasonID = '.$season_id.' ORDER BY Inning, DateAdded' ) ;
if (mysql_num_rows($recPlays) == 0) {
  echo "<h2>There are no plays for this game yet</h2>";
  require('./config/footer.php');
  exit;
}
?>
<table border="0" width="100%">
  <tr valign="top">
    <th width="15%">Inning</th>
    <th width="40%">Player</th>
    <th width="45%">Play</th>
  </tr>
<?php
$i = 1;
$last_inning = '';
$last_player = '';
while($rowPlays=mysql_fetch_assoc($recPlays)) {
  $recPlayer = mysql_query('SELECT * FROM player WHERE ID = '.$rowPlays['PlayerID'].' AND SeasonID = '.$season_id);
  $rowPlayer = mysql_fetch_assoc($recPlayer);
  $recPlayType = mysql_query('SELECT * FROM type WHERE ID = '.$rowPlays['TypeID']);
  $rowPlayType = mysql_fetch_assoc($recPlayType);
  if ($last_inning <> $rowPlays['Inning']) {
    $inning = $rowPlays['Inning'];
    $i = 1;
    $inning_tdbg = ' class="even inning"';
  } else {
    $inning = '';
    $inning_tdbg = ' class="odd inning"';
  }
  $last_inning = $rowPlays['Inning'];
  if ($last_player <> $rowPlayer['ID']) {
    $player = $rowPlayer['LastName'].', '.$rowPlayer['FirstName'];
    $i++;
  } else {
    $player = '';
  }
  $last_player = $rowPlayer['ID'];

  if ($i&1) {
    $tdbg = ' class="odd"';
  } else {
    $tdbg = ' class="even"';
  }

?>
  <tr>
    <td<?=$inning_tdbg?>><?=$inning?></td>
    <td<?=$tdbg?>><?=$player?></td>
    <td<?=$tdbg?>><?=$rowPlayType['Description']?></td>
  </tr>
<?php
}
?>
</table>
</form>
<?php
require('./config/footer.php');
?>
