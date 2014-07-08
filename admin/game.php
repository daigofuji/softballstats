<?php
require('../config/config.php');
$TITLE=$team_name.' Softball Game Admin Page';
if (!isset($_POST['cboSeason']) || $_POST['cboSeason'] == '') {
  $BODY_CODE='onLoad="document.game.cboSeason.focus();"';
} else {
  if ((isset($_POST['cboGameU']) && $_POST['cboGameU'] <> '') && !isset($_POST['Update']) && !isset($_POST['Add'])&& !isset($_POST['Delete'])) { 
    $BODY_CODE='onLoad="document.game.cboGameU.focus();"';
  } else {
    $BODY_CODE='onLoad="document.game.txtOpposingTeam.focus();"';
  }
}
if (!isset($HTTP_SERVER_VARS['PHP_AUTH_PW']) || !isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) || $HTTP_SERVER_VARS['PHP_AUTH_PW'] <> $admin_pass || strtoupper($HTTP_SERVER_VARS['PHP_AUTH_USER']) <> strtoupper($admin_user)) {
  authorize();
}
require('../config/header.php');
?>

<h1><?php echo $TITLE; ?></h1>
<hr>
<ul class="hint no-bullet">
  <li><strong>[Hint]</strong> After the plays for a game are entered, set the "Opposing Team Score" below under Update Game.</li>
  <li><strong>[Hint]</strong> If the opposing team forefits, enter a negative score for "Opposing Team Score".</li>
  <li><strong>[Hint]</strong> If the your team forefits, enter a positive score for "Opposing Team Score", and don't enter any plays fror the game.</li>
</ul>

<hr>
<p>* Required Field</p>

<?php

// Connect to DB
opendb();
$recSeason = $pdo->query('SELECT * FROM season ORDER BY ID') ;

// If Add button clicked, add new game
if (isset($_POST['Add']) && $demo_mode == '0') {
  $_POST['cboGameU'] = '';
  if (!isset($_POST['txtOpposingTeam']) || !isset($_POST['txtField']) || !isset($_POST['txtFieldNumber']) || !isset($_POST['txtDate']) || !isset($_POST['txtTime']) || $_POST['txtOpposingTeam'] == '' || $_POST['txtField'] == '' || $_POST['txtFieldNumber'] == '' || $_POST['txtDate'] == '' || $_POST['txtTime'] == '') {
    print ('<H2>Failed to add game to DB<br>There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  if (isset($_POST['txtNotes']) && $_POST['txtNotes'] <> '') {
    $notes = addslashes($_POST['txtNotes']);
  } else {
    $notes = NULL;
  }
  $result = $pdo->query('INSERT INTO game (ID,OpposingTeam,Field,FieldNumber,GameDate,Notes,SeasonID)
            VALUES (NULL, "'.addslashes($_POST['txtOpposingTeam']).'","'.addslashes($_POST['txtField']).'",'.addslashes($_POST['txtFieldNumber']).',
            "'.date('Y-m-d H:i:s',strtotime(addslashes($_POST['txtDate']).' '.addslashes($_POST['txtTime']))).'","'.$notes.'",
	    '.addslashes($_POST['cboSeason']).')');
  if (!$result) {
    print ('<H2>Failed to add game to DB<br>'.mysql_error().'</H2>');
    require('../config/footer.php');
    exit;
  }
}

// If Delete button clicked, delete game
if (isset($_POST['Delete']) && $demo_mode == '0') {
  $_POST['cboGameU'] = '';
  if (!isset($_POST['cboGame']) || $_POST['cboGame'] == '') {
    print ('<H2>Failed to delete game from DB<br>There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  $result = $pdo->query('SELECT * FROM plays WHERE GameID = '.addslashes($_POST['cboGame']));
  $row = $result->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    print ('<H2>Failed to delete game from DB<br>There are still plays for this game in the play table!</H2>');
    require('../config/footer.php');
    exit;
  }
  $result = $pdo->query('DELETE FROM game WHERE ID = '.addslashes($_POST['cboGame']));
  if ($result == False) {
    print ('<H2>Failed to delete game from DB<br>'.mysql_error().'</H2>');
    require('../config/footer.php');
    exit;
  }
}

// If Update button clicked, add new game
if (isset($_POST['Update']) && $demo_mode == '0') {
  if (!isset($_POST['txtOpposingTeamU']) || !isset($_POST['txtFieldU']) || !isset($_POST['txtFieldNumberU']) || !isset($_POST['txtDateU']) || !isset($_POST['txtTimeU']) || $_POST['txtOpposingTeamU'] == '' || $_POST['txtFieldU'] == '' || $_POST['txtFieldNumberU'] == '' || $_POST['txtDateU'] == '' || $_POST['txtTimeU'] == '') {
    print ('<H2>Failed to update game in DB<br>There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  if (!isset($_POST['txtMUDateU']) || !isset($_POST['txtMUTimeU']) || !isset($_POST['txtMUFieldU']) || !isset($_POST['txtMUFieldNumberU']) || $_POST['txtMUDateU'] == '' || $_POST['txtMUTimeU'] == '' || $_POST['txtMUFieldU'] == '' || $_POST['txtMUFieldNumberU'] == '') {
    $MakeUpField = 'NULL';
    $MakeUpFieldNumber = 'NULL';
    $MakeUpDate = 'NULL';
  } else {
    $MakeUpField = '"'.addslashes($_POST['txtMUFieldU']).'"';
    $MakeUpFieldNumber = addslashes($_POST['txtMUFieldNumberU']);
    $MakeUpDate = '"'.date('Y-m-d H:i:s',strtotime(addslashes($_POST['txtMUDateU']).' '.addslashes($_POST['txtMUTimeU']))).'"';
  }
  if (isset($_POST['txtNotesU']) && $_POST['txtNotesU'] <> '') {
    $notes = addslashes($_POST['txtNotesU']);
  } else {
    $notes = NULL;
  }
  if (!isset($_POST['txtOpposingTeamScoreU']) || $_POST['txtOpposingTeamScoreU'] == '') {
    $OTS = 'NULL';
  } else {
    $OTS = '"'.addslashes($_POST['txtOpposingTeamScoreU']).'"';
  }
  $result = $pdo->query('UPDATE game SET OpposingTeam = "'.addslashes($_POST['txtOpposingTeamU']).'", Field = "'
            .addslashes($_POST['txtFieldU']).'", FieldNumber = '.addslashes($_POST['txtFieldNumberU']).', GameDate = "'
            .date('Y-m-d H:i:s',strtotime(addslashes($_POST['txtDateU']).' '.addslashes($_POST['txtTimeU']))).'", MakeUpField = '
            .$MakeUpField.', MakeUpFieldNumber = '.$MakeUpFieldNumber.', MakeUpDate = '.$MakeUpDate.', OpposingTeamScore = '.$OTS
            .', Notes = "'.$notes.'" WHERE ID = '.$_POST['cboGameU']);
  if (!$result) {
    print ('<H2>Failed to update game in DB<br>'.mysql_error().'</H2>');
    require('../config/footer.php');
    exit;
  }
  $_POST['cboGameU'] = '';
}

if (isset($_POST['cboSeason']) && !$_POST['cboSeason'] == '') {
  $recGame = $pdo->query('SELECT * FROM game WHERE SeasonID = '.addslashes($_POST['cboSeason']).' ORDER BY GameDate') ;
}

?>
<form name="game" method="POST">
  <table border="0" class="small-12">
    <tr>
      <td>
        <p> <strong>*Season:</strong> </p></td>
      <td colspan="3"> 
      <select name="cboSeason" size="1" onchange="document.game.submit()">
          <option value=""></option>
<?php
if ($recSeason) {
  while ($rowSeason = $recSeason->fetch(PDO::FETCH_ASSOC) ) {
    if (isset($_POST['cboSeason']) && $_POST['cboSeason'] == $rowSeason['ID']) {
      print '          <OPTION SELECTED';
    } else {
      print '          <OPTION';
    }
?>
 VALUE="<?php echo $rowSeason['ID']?>"><?php echo stripslashes($rowSeason['Description'])?></option>
<?php
  }
}
?>
        </select> </td>
    </tr>
<?php
if (!isset($_POST['cboSeason']) || $_POST['cboSeason'] == '') {
  echo "  </TABLE>";
  require('../config/footer.php');
  exit;
}
?>

    <tr valign="CENTER">
      <td> <strong>Add Game:</strong> </td>
      <td> 
        *Opposing Team:<br>
        <input type="TEXT" name="txtOpposingTeam" value="<?php
        if (isset($_POST['txtOpposingTeam']) && $_POST['txtOpposingTeam'] <> '') { echo stripslashes($_POST['txtOpposingTeam']); }
        ?>" maxlength="50" size="20"> </td>
      <td> 
        *Field:<br>
        <input type="TEXT" name="txtField" value="<?php
        if (isset($_POST['txtField']) && $_POST['txtField'] <> '') { echo stripslashes($_POST['txtField']); }
        ?>" maxlength="50" size="20"> </td>
      <td> 
        *Field #:<br>
        <input type="TEXT" name="txtFieldNumber" value="<?php
        if (isset($_POST['txtFieldNumber']) && $_POST['txtFieldNumber'] <> '') { echo stripslashes($_POST['txtFieldNumber']); }
        ?>" maxlength="3" size="3"> </td>
    </tr>
    <tr>
      <td>   </td>
      <td> 
        *Date (mm/dd/yyyy):<br>
        <input type="TEXT" name="txtDate" value="<?php
        if (isset($_POST['txtDate']) && $_POST['txtDate'] <> '') { echo stripslashes($_POST['txtDate']); }
        ?>" maxlength="50" size="20"> </td>
      <td> 
        *Time (hh:mm 24 hour time):<br>
        <input type="TEXT" name="txtTime" value="<?php
        if (isset($_POST['txtTime']) && $_POST['txtTime'] <> '') { echo stripslashes($_POST['txtTime']); }
         ?>" maxlength="50" size="20"> </td>
      <td>   </td>
    </tr>
    <tr>
      <td>   </td>
      <td colspan="2"> 
        Game Notes:<br>
        <textarea name="txtNotes" cols="30" rows="4" wrap="VIRTUAL"><?php if (isset($_POST['txtNotes']) && $_POST['txtNotes'] <> '') { echo stripslashes($_POST['txtNotes']); } ?></textarea>
         </td>
      <td> 
        <button type="SUBMIT" name="Add" class="expand">Add >></button> 
      </td>
    </tr>

    <tr>
      <td> <strong>Remove Game:</strong> </td>
      <td colspan="3"> 
        <select name="cboGame" size="1">
          <option value=""></option>
<?php
if ($recGame) {
  while ($rowGame = $recGame->fetch(PDO::FETCH_ASSOC)) {
?>
          <option value="<?php echo $rowGame['ID']?>"><?php echo stripslashes($rowGame['OpposingTeam'])?> [<?php echo date('m/d/y h:i a',
          strtotime($rowGame['GameDate']))?>]</option>
<?php
  }
}
?>
        </select>
        <button type="submit" name="Delete" value="">Delete >></button> </td>
    </tr>

    <tr>
      <td> <strong>Update Game:</strong> </td>
      <td colspan="3"> 
        <select name="cboGameU" size="1" onchange="document.game.submit()">
          <option value=""></option>
<?php
if ($recGame->fetchColumn() <> 0) {
  mysql_data_seek($recGame, 0);
  if ($recGame) {
    while ($rowGame = $recGame->fetch(PDO::FETCH_ASSOC)) {
      if (isset($_POST['cboGameU']) && $_POST['cboGameU'] == $rowGame['ID']) {
        $selected = 'SELECTED ';
      } else {
        $selected = '';
      }
?>
          <OPTION <?php echo $selected?>VALUE="<?php echo $rowGame['ID']?>"><?php echo stripslashes($rowGame['OpposingTeam'])?> [<?php echo date('m/d/y h:i a',
          strtotime($rowGame['GameDate']))?>]</option>
<?php
    }
  }
}
?>
        </select>
    </tr>
<?php
if (isset($_POST['cboGameU']) && $_POST['cboGameU'] <> '') {
  $recGame = $pdo->query('SELECT * FROM game WHERE ID = '.addslashes($_POST['cboGameU'])) ;
  $rowGame = $recGame->fetch(PDO::FETCH_ASSOC);
?>
    <tr valign="CENTER">
      <td>   </td>
      <td> 
        *Opposing Team:<br>
        <input type="TEXT" name="txtOpposingTeamU" value="<?php echo stripslashes($rowGame['OpposingTeam'])?>" maxlength="50" size="20"> </td>
      <td> 
        *Field:<br>
        <input type="TEXT" name="txtFieldU" value="<?php echo stripslashes($rowGame['Field']) ?>" maxlength="50" size="20"> </td>
      <td> 
        *Field #:<br>
        <input type="TEXT" name="txtFieldNumberU" value="<?php echo $rowGame['FieldNumber']?>" maxlength="3" size="3"> </td>
    </tr>
    <tr>
      <td>   </td>
      <td> 
        *Date (mm/dd/yyyy):<br>
<?php
  if ($rowGame['GameDate'] <> '') {
    $datearray = explode(' ',$rowGame['GameDate']);
    $date = explode('-',$datearray[0]);
    $time = explode(':',$datearray[1]);
    $datestr = $date[1].'/'.$date[2].'/'.$date[0];
    $timestr = $time[0].':'.$time[1];
  } else {
    $datestr = $timestr = '';
  }
?>
        <input type="TEXT" name="txtDateU" value="<?php echo $datestr?>" maxlength="50" size="20"> </td>
      <td> 
        *Time (hh:mm 24 hour time):<br>
        <input type="TEXT" name="txtTimeU" value="<?php echo $timestr?>" maxlength="50" size="20"> </td>
      <td> 
        Opposing Team Score:<br>
        <input type="TEXT" name="txtOpposingTeamScoreU" value="<?php echo $rowGame['OpposingTeamScore']?>" maxlength="3" size="3"> </td>
    </tr>
    <tr valign="CENTER">
      <td> 
         </td>
      <td> 
        Make-up Field:<br>
        <input type="TEXT" name="txtMUFieldU" value="<?php echo stripslashes($rowGame['MakeUpField']) ?>" maxlength="50" size="20"> </td>
      <td> 
        Make-up Field #:<br>
        <input type="TEXT" name="txtMUFieldNumberU" value="<?php echo $rowGame['MakeUpFieldNumber']?>" maxlength="3" size="3"> </td>
    </tr>
    <tr>
      <td>   </td>
      <td> 
        Make-up Date (mm/dd/yyyy):<br>
<?php
  if ($rowGame['MakeUpDate'] <> '') {
    $datearray = explode(' ',$rowGame['MakeUpDate']);
    $date = explode('-',$datearray[0]);
    $time = explode(':',$datearray[1]);
    $datestr = $date[1].'/'.$date[2].'/'.$date[0];
    $timestr = $time[0].':'.$time[1];
  } else {
    $datestr = $timestr = '';
  }
?>
        <input type="TEXT" name="txtMUDateU" value="<?php echo $datestr?>" maxlength="50" size="20"> </td>
      <td> 
        *Time (hh:mm 24 hour time):<br>
        <input type="TEXT" name="txtMUTimeU" value="<?php echo $timestr?>" maxlength="50" size="20"> </td>
      <td>   </td>
    </tr>
    <tr>
      <td>   </td>
      <td colspan="2"> 
        Game Notes:<br>
        <textarea name="txtNotesU" cols="30" rows="4" wrap="VIRTUAL"><?php echo $rowGame['Notes']?></textarea> </td>
      <td> 
        <button type="SUBMIT" name="Update" class="expand">Update >></button> </td>
    </tr>
<?php
}
?>
  </table>
</form>
<hr>
<a href="./">Main Admin Page</a> | <a href="season.php">Season Admin Page</a> | Game Admin Page |
<a href="player.php">Player Admin Page</a> | <a href="plays.php">Plays Admin Page</a>
<?php
require('../config/footer.php');
?>
