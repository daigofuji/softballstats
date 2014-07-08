<?php
require('../config/config.php');
$TITLE=$team_name.' Softball Player Admin Page';
if (!isset($_POST['cboSeason']) || $_POST['cboSeason'] == '') {
  $BODY_CODE='onLoad="document.player.cboSeason.focus();"';
} else {
  if ((isset($_POST['cboPlayerNum']) && $_POST['cboPlayerNum'] <> '') && !isset($_POST['Update']) && !isset($_POST['Add'])&& !isset($_POST['Delete'])) { 
    $BODY_CODE='onLoad="document.player.cboPlayerNum.focus();"';
  } else {
    $BODY_CODE='onLoad="document.player.txtPlayerNum.focus();"';
  }
}
if (!isset($HTTP_SERVER_VARS['PHP_AUTH_PW']) || !isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) || $HTTP_SERVER_VARS['PHP_AUTH_PW'] <> $admin_pass || strtoupper($HTTP_SERVER_VARS['PHP_AUTH_USER']) <> strtoupper($admin_user)) {
 authorize();
}
require('../config/header.php');
?>

<h1><?php echo $TITLE;?></h1>
<hr>
<?php

// Connect to DB
opendb();
$recSeason = $pdo->query('SELECT * FROM season ORDER BY ID') ;

// If Add button clicked, add new player
if (isset($_POST['Add']) && $demo_mode == '0') {
  $_POST['cboPlayerNum'] = '';
  if (!isset($_POST['txtPlayerNum']) || !isset($_POST['txtPlayerFirstName']) || !isset($_POST['txtPlayerLastName']) || !isset($_POST['cboSeason']) || $_POST['txtPlayerNum'] == '' || $_POST['txtPlayerFirstName'] == '' || $_POST['txtPlayerLastName'] == '' || $_POST['cboSeason'] == '') {
    print ('<H2>Failed to add play to DB<br>There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  if (isset($_POST['txtPlayerEMail']) && $_POST['txtPlayerEMail'] <> '') {
    $email = addslashes($_POST['txtPlayerEMail']);
  } else {
    $email = NULL;
  }
  if (isset($_POST['txtPlayerBio']) && $_POST['txtPlayerBio'] <> '') {
    $bio = addslashes($_POST['txtPlayerBio']);
  } else {
    $bio = NULL;
  }
  $result = $pdo->query('INSERT INTO player (ID,FirstName,LastName,EMail,Bio,SeasonID)
            VALUES ('.addslashes($_POST['txtPlayerNum']).',"'.addslashes($_POST['txtPlayerFirstName']).'","'.
            addslashes($_POST['txtPlayerLastName']).'","'.$email.'","'.$bio.'",'.addslashes($_POST['cboSeason']).')');
  if (!$result) {
    print ('<H2>Failed to add play to DB<br>'.mysql_error().'</H2>');
    require('../config/footer.php');
    exit;
  }
}

// If Update button clicked, edit player
if (isset($_POST['Update']) && $demo_mode == '0') {
  if (!isset($_POST['txtPlayerFirstNameU']) || !isset($_POST['txtPlayerLastNameU']) || !isset($_POST['cboSeason']) || $_POST['txtPlayerFirstNameU'] == '' || $_POST['txtPlayerLastNameU'] == '' || $_POST['cboSeason'] == '') {
    print ('<H2>Failed to update play to DB<br>There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  if (isset($_POST['txtPlayerEMailU']) && $_POST['txtPlayerEMailU'] <> '') {
    $email = addslashes($_POST['txtPlayerEMailU']);
  } else {
    $email = NULL;
  }
  if (isset($_POST['txtPlayerBioU']) && $_POST['txtPlayerBioU'] <> '') {
    $bio = addslashes($_POST['txtPlayerBioU']);
  } else {
    $bio = NULL;
  }
  $result = $pdo->query('UPDATE player SET FirstName = "'.addslashes($_POST['txtPlayerFirstNameU'])
            .'",LastName = "'.addslashes($_POST['txtPlayerLastNameU']).'", EMail = "'.$email.'", Bio = "'.$bio.
            '" WHERE ID ='.addslashes($_POST['cboPlayerNum']).' AND SeasonID = '.addslashes($_POST['cboSeason']));
  if (!$result) {
    print ('<H2>Failed to update play to DB<br>'.mysql_error().'</H2>');
    require('../config/footer.php');
    exit;
  }
  $_POST['cboPlayerNum'] = '';
}

// If Delete button clicked, delete player
if (isset($_POST['Delete']) && $demo_mode == '0') {
  $_POST['cboPlayerNum'] = '';
  if (!isset($_POST['cboPlayer']) || $_POST['cboPlayer'] == '') {
    print ('<H2>Failed to delete play from DB<br>There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  $result = $pdo->query('SELECT * FROM plays WHERE PlayerID = '.addslashes($_POST['cboPlayer']).' AND SeasonID = '.addslashes($_POST['cboSeason']));
  $row = $result->fetch(PDO::FETCH_ASSOC);
  if ($row) {
      print ('<H2>Failed to delete player from DB<br>There are still plays for this player in the play table!</H2>');
      require('../config/footer.php');
      exit;
  }
  $result = $pdo->query('DELETE FROM player WHERE ID = '.addslashes($_POST['cboPlayer']));
  if ($result == False) {
      print ('<H2>Failed to delete play from DB<br>'.mysql_error().'</H2>');
      require('../config/footer.php');
      exit;
  }
  $_POST['cboPlayerNum'] = '';
}

?>
* = Required Field
<form name="player" method="POST">
  <table border="0" class="small-12">
    <tr>
      <td>
        <p><strong>*Season:</strong></p></td>
      <td colspan="3">
      <select name="cboSeason" size="1" onchange="document.player.submit()">
          <option value=""></option>
<?php
if ($recSeason) {
  while ($rowSeason = $recSeason->fetch(PDO::FETCH_ASSOC)) {
    if (isset($_POST['cboSeason']) && $_POST['cboSeason'] == $rowSeason['ID']) {
      print '          <option selected';
    } else {
      print '          <option';
    }
?>
 VALUE="<?php echo $rowSeason['ID']?>"><?php echo stripslashes($rowSeason['Description'])?></option>
<?php
  }
}
?>
        </select></td>
    </tr>
<?php
if (!isset($_POST['cboSeason']) || $_POST['cboSeason'] == '') {
  echo "  </table>";
  require('../config/footer.php');
  exit;
}
?>

    <tr valign="CENTER">
      <td><strong>Add Player:</strong></td>
      <td>
        *Player #:<br>
        <input type="TEXT" name="txtPlayerNum" maxlength="3" size="3"></td>
      <td>
        *Player First Name:<br>
        <input type="TEXT" name="txtPlayerFirstName" maxlength="50" size="20"></td>
      <td>
        *Player Last Name:<br>
        <input type="TEXT" name="txtPlayerLastName" maxlength="50" size="20">
    </tr>
    <tr valign="TOP">
      <td></td>
      <td>
        Player EMail:<br>
        <input type="TEXT" name="txtPlayerEMail" maxlength="100" size="20"></td>
      <td>
        Player Bio:<br>
        <textarea name="txtPlayerBio" maxlength="255" cols="25" rows="4" wrap="VIRTUAL"></textarea></td>
      <td>
        <br><button type="SUBMIT" name="Add" value="">Add >></button></td>
    </tr>

    <tr>
      <td><font face="Arial"><strong>Remove Player:</strong></td>
      <td colspan="3">
        <select name="cboPlayer" size="1">
          <option value=""></option>
<?php
if (isset($_POST['cboSeason']) && $_POST['cboSeason'] <> '') {
  $recPlayer = $pdo->query('SELECT * FROM player WHERE SeasonID = '.addslashes($_POST['cboSeason']).' ORDER BY LastName, FirstName') ;
}
if ($recPlayer) {
  while ($rowPlayer = $recPlayer->fetch(PDO::FETCH_ASSOC)) {
?>
          <option value="<?php echo $rowPlayer['ID']?>"><?php echo stripslashes($rowPlayer['LastName']).', '.stripslashes($rowPlayer['FirstName'])
          .' ('.$rowPlayer['ID'].')'?></option>
<?php
  }
}
?>
        </select>
        <button type="SUBMIT" name="Delete" value="">Delete >></button></td>
    </tr>

    <tr valign="CENTER">
      <td><strong>Update Player:</strong></td>
      <td>
        *Player #:<br>
        <select name="cboPlayerNum" onchange="document.player.submit()">
          <option value=""></option>
<?php
mysql_data_seek($recPlayer, 0);
if ($recPlayer->fetchColumn() <> 0) {
  while ($rowPlayer = $recPlayer->fetch(PDO::FETCH_ASSOC)) {
    if (isset($_POST['cboPlayerNum']) && $_POST['cboPlayerNum'] == $rowPlayer['ID']) {
      $sel = ' SELECTED';
    } else {
      $sel = '';
    }
?>
          <OPTION VALUE="<?php echo $rowPlayer['ID']?>"<?php echo $sel?>>(<?php echo $rowPlayer['ID']?>) <?php echo $rowPlayer['LastName']?>, <?php echo
          $rowPlayer['FirstName']?></option>
<?php
  }
}
if (isset($_POST['cboPlayerNum']) && $_POST['cboPlayerNum'] <> '') {
  $recPlayer = $pdo->query('SELECT * FROM player WHERE ID = '.addslashes($_POST['cboPlayerNum']).' AND SeasonID = '
               .addslashes($_POST['cboSeason'])) ;
  $rowPlayer = $recPlayer->fetch(PDO::FETCH_ASSOC);
}
?>
        </select></td>
      <td>
        *Player First Name:<br>
        <input type="TEXT" name="txtPlayerFirstNameU" maxlength="50" size="20" value="<?php echo stripslashes($rowPlayer['FirstName'])?>"></td>
      <td>
        *Player Last Name:<br>
        <input type="TEXT" name="txtPlayerLastNameU" maxlength="50" size="20" value="<?php echo stripslashes($rowPlayer['LastName'])?>">
    </tr>
    <tr valign="TOP">
      <td></td>
      <td>
        Player EMail:<br>
        <INPUT TYPE="TEXT" NAME="txtPlayerEMailU" MAXLENGTH="100" SIZE="20" VALUE=<?php echo stripslashes($rowPlayer['EMail'])?>></td>
      <td>
        Player Bio:<br>
        <textarea name="txtPlayerBioU" maxlength="255" cols="25" rows="4" wrap="VIRTUAL"><?php echo
        stripslashes($rowPlayer['Bio'])?></textarea></td>
      <td>
        <br><button type="SUBMIT" name="Update" value="">Update >></button></td>
  </table>
</form>
<hr>
<a href="./">Main Admin Page</a> | <a href="season.php">Season Admin Page</a> | <a href="game.php">Game Admin Page</a> |
Player Admin Page | <a href="plays.php">Plays Admin Page</a>
<?php
require('../config/footer.php');
?>
