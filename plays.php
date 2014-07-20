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
    print('<strong>Invalid ID number!</strong>');
    require('./config/footer.php'); 
    exit; 
  } 
}  
?>

<h1><?php echo $TITLE;?></h1>
<hr>

<form name="bios" method="post">
Switch Season:
<select name="cboSeason" size="1" onchange="document.bios.submit()">
<?php
$recSeason = $pdo->query('SELECT * FROM season ORDER BY ID') ;
if ($recSeason) {
  while ($rowSeason = $recSeason->fetch(PDO::FETCH_ASSOC)) {
    if ((isset($_POST['cboSeason']) && $_POST['cboSeason'] == $rowSeason['ID']) || $rowSeason['DefaultSeason']) {
      print '          <option selected="selected"';
    } else {
      print '          <option';
    }
?>
 VALUE="<?php echo $rowSeason['ID']?>"><?=stripslashes($rowSeason['Description'])?></option>
<?php
  }
}
?>
</select>
</form>
<p>
<form name="plays" method="post">
<?php
$recGame=$pdo->query('SELECT * FROM game WHERE SeasonID = '.$season_id.' ORDER BY GameDate');
?>
Select Game: <select name="cboGame" size="1" onchange="document.plays.submit()">
  <option value="0"></option>
<?php
if ($recGame) {
  while ($rowGame = $recGame->fetch(PDO::FETCH_ASSOC)) {
    if (isset($game_id) && $game_id == $rowGame['ID']) {
      $sel = ' selected="selected"';
    } else {
      $sel = '';
    }
?>
    <option<?php echo $sel?> VALUE="<?php echo $rowGame['ID']?>"><?=stripslashes($rowGame['OpposingTeam'])?> [<?=date('m/d/y h:i a',
    strtotime($rowGame['GameDate']))?>]</option>
<?php
  }
}
?>
</select>
</form>
<?php
if (!isset($game_id) || $game_id == '') {
  require('./config/footer.php');
  exit;
}
$recPlays=$pdo->query('SELECT * FROM plays WHERE GameID = '.$game_id.' AND SeasonID = '.$season_id.' ORDER BY Inning, DateAdded' );
// if ($recPlays->fetchColumn() == 0) {
//   echo "<h2>There are no plays for this game yet</h2>";
//   require('./config/footer.php');
//   exit;
// }
?>
<table border="0" class="small-12">
  <tr valign="top">
    <th>Inning</th>
    <th>Player</th>
    <th>Play</th>
  </tr>
<?php
$i = 1;
$last_inning = '';
$last_player = '';

while($rowPlays=$recPlays->fetch(PDO::FETCH_ASSOC)) {
  
  $recPlayer = $pdo->query('SELECT * FROM player WHERE ID = '.$rowPlays['PlayerID'].' AND SeasonID = '.$season_id);
  $rowPlayer = $recPlayer->fetch(PDO::FETCH_ASSOC);
  $recPlayType = $pdo->query('SELECT * FROM type WHERE ID = '.$rowPlays['TypeID']);
  $rowPlayType = $recPlayType->fetch(PDO::FETCH_ASSOC);
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
    <td<?php echo $inning_tdbg; ?>><?php echo $inning; ?></td>
    <td<?php echo $tdbg; ?>><?php echo $player; ?></td>
    <td<?php echo $tdbg; ?>><?php echo $rowPlayType['Description']; ?></td>
  </tr>
<?php
}
?>
</table>

<?php 
// Game Box Would like to get 
// AB   R     H    2B  3B  HR  BB   K    RBI 
// for each player
$boxPlays=$pdo->query('SELECT PlayerID, TypeID FROM plays WHERE GameID = '.$game_id.' AND SeasonID = '.$season_id.' ORDER BY PlayerID' );
$thisPlayer = 0;
$StatTemp = [];

while($bPlays=$boxPlays->fetch(PDO::FETCH_ASSOC)) {

  // is this the same player as the one before?
  if($bPlays['PlayerID']>$thisPlayer)  {
    // not the same player, increase the player id untill it is the same, and construct the stat object
    while($bPlays['PlayerID']>$thisPlayer) {
      $thisPlayer++;
    }
    //print("New player id is ".$thisPlayer);
    //Look up this player
    $recPlayer = $pdo->query('SELECT * FROM player WHERE ID = '.$thisPlayer.' AND SeasonID = '.$season_id);
    $rowPlayer = $recPlayer->fetch(PDO::FETCH_ASSOC);
    $playerName = $rowPlayer['LastName'].', '.$rowPlayer['FirstName'].' ('.$rowPlayer['ID'].')';
    $StatTemp[$bPlays['PlayerID']] = [
      "NAME" => $playerName,
      "AB" => 0,
      "R" => 0,
      "H" => 0,
      "2B" => 0,
      "3B" => 0,
      "HR" => 0,
      "BB" => 0,
      "K" => 0,
      "RBI" => 0
    ]; }


    //echo('NOW What is this player?: '.$thisPlayer.'<br>');

    //NOW RECORD STATS
    // echo('Player is '.$bPlays['PlayerID'].' compared to '.$thisPlayer.' and play type was '.$bPlays['TypeID'].'<br>');
  switch ($bPlays['TypeID']) {
    case 1: //single
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      $StatTemp[$bPlays['PlayerID']]['H']++;
      break;      
    case 2: //double
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      $StatTemp[$bPlays['PlayerID']]['H']++;
      $StatTemp[$bPlays['PlayerID']]['2B']++;
      break;
    case 3: //triple
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      $StatTemp[$bPlays['PlayerID']]['H']++;
      $StatTemp[$bPlays['PlayerID']]['3B']++;
      break;
    case 4: // home run
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      $StatTemp[$bPlays['PlayerID']]['H']++;
      $StatTemp[$bPlays['PlayerID']]['HR']++;
      break;
    case 5: //RBI
      $StatTemp[$bPlays['PlayerID']]['RBI']++;
      //echo('RBI<br>');
      break;
    case 6: // foul out
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      break;
    case 7: // strikeout
      $StatTemp[$bPlays['PlayerID']]['K']++;
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      break;
    case 8: // ground out
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      break;
    case 9: // double play
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      break;
    case 10 : // FC
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      break;
    case 22 : // reached on error
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      break;
    case 21:
      $StatTemp[$bPlays['PlayerID']]['BB']++;
      break;
    case 24:
      $StatTemp[$bPlays['PlayerID']]['R']++;
      break;
    case 26: // fly out
      $StatTemp[$bPlays['PlayerID']]['AB']++;
      break;
    case 27: //stolen base
      break;
    case 29: //HBP
      $StatTemp[$bPlays['PlayerID']]['BB']++;
      break;
  }
  
}
  
//print_r($StatTemp);
?>
<h4>Game Box Score</h4>

<table>
  <tr><th>PLAYER</th><th>AB</th><th>R</th><th>H</th><th>2B</th><th>3B</th><th>HR</th><th>BB</th><th>K</th><th>RBI</th></tr>
<?php 
// print the table from constructed object
foreach ($StatTemp as $player) {

  if(!$player["AB"]==0 || !$player["BB"]==0) {

    ?><tr><td><?php echo $player["NAME"]; ?></td>
    <td><?php echo $player["AB"]; ?></td>
    <td><?php echo $player["R"]; ?></td>
    <td><?php echo $player["H"]; ?></td>
    <td><?php echo $player["2B"]; ?></td>
    <td><?php echo $player["3B"]; ?></td>
    <td><?php echo $player["HR"]; ?></td>
    <td><?php echo $player["BB"]; ?></td>
    <td><?php echo $player["K"]; ?></td>
    <td><?php echo $player["RBI"]; ?></td></tr><?php

  }

}


?>

</table>  

<?php
require('./config/footer.php');
?>
