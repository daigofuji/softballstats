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
$TITLE=$team_name.' Softball Schedule - '.$season_name;
require('./config/header.php');
?>

<h1><?php echo $TITLE;?></h1>

<hr>

<ul class="hint no-bullet">
  <li><strong>[Hint]</strong> Click the opposing team name to see the play-by-play for the game.</li>
</ul>


<form name="bios" method="post">
Switch Season:
<select name="cboSeason" size="1" onchange="document.bios.submit()">
<?php
$recSeason = $pdo->query('SELECT * FROM season ORDER BY ID');
if ($recSeason) {
  while ($rowSeason = $recSeason->fetch(PDO::FETCH_ASSOC)) {
    if ((isset($_POST['cboSeason']) && $_POST['cboSeason'] == $rowSeason['ID']) || $rowSeason['DefaultSeason']) {
      print '          <option selected';
    } else {
      print '          <option';
    }
?>
 value="<?php echo $rowSeason['ID'] ?>"><?php echo stripslashes($rowSeason['Description'])?></option>
<?php
  }
}
?>
</select>
</form>

<br>

<?php
$recGame=$pdo->query('SELECT * FROM game WHERE SeasonID = '.$season_id.' ORDER BY GameDate' ) ;
?>
<table border="0" class="small-12">
  <tr valign=top>
    <th>Date</th>
    <th>Opposing Team</th>
    <th>Field Name (Number)</th>
    <th>Make-Up Date</th>
    <th>Make-Up Field Name</th>
    <th>Score</th>
  </tr>
<?php
$Win = 0;
$Loss = 0;
$Tie = 0;
$Games = 0;
$i = 0;
$j = 0;
while($rowGame=$recGame->fetch(PDO::FETCH_ASSOC)) {
  if ($i&1) {
    $tdbg = ' class="odd"';
  } else {
    $tdbg = ' class="even"';
  }
  $i++;
  if ($j == $games_in_season) {
    echo '<tr><td colspan="5" align="center"><em>Playoffs!!!!</em></td></tr>' ;
  }
  $j++;
  $recPlays=$pdo->query('SELECT * FROM plays WHERE GameID = '.$rowGame['ID'].' AND SeasonID = '.$season_id);
  $nScore = 0;
  if ($recPlays->fetchColumn() == 0) {
    $no_plays = true;
  } else {
    $no_plays = false;
  }
  while($rowPlays=$recPlays->fetch(PDO::FETCH_ASSOC)) {
    if ($rowPlays['TypeID'] == 24) {  // Type 24 is the 'Scored Run' type
      $nScore++;
    }
  }
  $date = date('D, M j, g:i A', strtotime($rowGame['GameDate']));
  if ($nScore > $rowGame['OpposingTeamScore']) {
    $opp_team = '<strong><em><a href="plays.php?ID='.$rowGame['ID'].'">'.stripslashes($rowGame['OpposingTeam']).'</a></em></strong>';
    $Win++;
    $Games++;
  } else {
    $opp_team = '<a href="plays.php?ID='.$rowGame['ID'].'">'.stripslashes($rowGame['OpposingTeam']).'</a>';
    if ($nScore != 0 || $rowGame['OpposingTeamScore'] != 0) {
      $Games++;
      if ($nScore == $rowGame['OpposingTeamScore']) {
        $Tie++;
      } else {
        $Loss++;
      }
    }
  }
  $field = stripslashes($rowGame['Field']).' ('.$rowGame['FieldNumber'].')';
  if ($rowGame['MakeUpDate'] <> '') {
    $mu_date = date('D, M j, g:i A', strtotime($rowGame['MakeUpDate']));
  } else {
    $mu_date = '';
  }
  if ($rowGame['MakeUpField'] <> '') {
    $mu_field = stripslashes($rowGame['MakeUpField']).' ('.$rowGame['MakeUpFieldNumber'].')';
  } else {
    $mu_field = '';
  }
  if ($nScore == 0 && $rowGame['OpposingTeamScore'] == 0) {
    $ots = '';
  } elseif ($rowGame['OpposingTeamScore'] < 0) {
    $ots = 'They Forefit';
  } elseif ($no_plays) {
    $ots = 'We Forefit';
  } else {
    $ots = $nScore.' to '.$rowGame['OpposingTeamScore'];
  }
?>
  <tr<?php echo $tdbg?> valign="top">
    <td><?php echo $date?></td>
    <td><?php echo $opp_team?></td>
    <td><?php echo $field?></td>
    <td><?php echo $mu_date?></td>
    <td><?php echo $mu_field?></td>
    <td><?php echo $ots?></td>
  </tr>
<?php
  if ($rowGame['Notes'] <> '') {
?>
  <tr<?php echo $tdbg?> valign="top">
    <td valign="top" align="right" colspan="1">Game Notes:</td>
    <td colspan="5"> <?php stripslashes($rowGame['Notes'])?></td>
  </tr>
<?php
  }
}
?>
</table>
<hr>
<h4>
Record - Win:<?php echo $Win;?> Tie:<?php echo $Tie;?> Loss:<?php echo $Loss;?>  Games:<?php echo $Games;?>
</h4>
<?php
require('./config/footer.php');
?>
