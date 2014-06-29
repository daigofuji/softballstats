<?php
require('../config/config.php');
$TITLE=$team_name.' Softball Play Admin Page';
if (!isset($_POST['cboSeason']) || $_POST['cboSeason'] == '') {
  $BODY_CODE="onLoad=\"document.plays.cboSeason.focus();\"";
} else {
  if (!isset($_POST['cboGame']) || $_POST['cboGame'] == '') {
    $BODY_CODE="onLoad=\"document.plays.cboGame.focus();\"";
  } else {
    $BODY_CODE="onLoad=\"document.plays.cboPlayer.focus();\"";
  }
}
if (!isset($HTTP_SERVER_VARS['PHP_AUTH_PW']) || !isset($HTTP_SERVER_VARS['PHP_AUTH_USER']) || $HTTP_SERVER_VARS['PHP_AUTH_PW'] <> $admin_pass || strtoupper($HTTP_SERVER_VARS['PHP_AUTH_USER']) <> strtoupper($admin_user)) {
  authorize();
}
require('../config/header.php');
?>

<h1><?=$TITLE;?></h1>
<hr />
<ul class="hint">
<li><strong>[Hint]</strong> When entering a Home Run, don't forget to add a Scored Run and at least one RBI play.</li>
<li><strong>[Hint]</strong> For each inning, the # of RBIs should = the # of Scored Runs.</li>
</ul>

<hr />
<?php

// Connect to DB
opendb();
if (isset($_POST['cboSeason']) && $_POST['cboSeason'] <> '') {
  $recGame = mysql_query('SELECT * FROM game WHERE SeasonID = '.addslashes($_POST['cboSeason']).' ORDER BY GameDate') ;
  $recPlayer = mysql_query('SELECT * FROM player WHERE SeasonID = '.addslashes($_POST['cboSeason']).' ORDER BY LastName, FirstName') ;
}
$recPlayType = mysql_query('SELECT * FROM type ORDER BY Description') ;
$recSeason = mysql_query('SELECT * FROM season ORDER BY ID') ;

// If Add button clicked, add new play
if (isset($_POST['Add']) && $demo_mode == '0') {
  if (!isset($_POST['cboGame']) || !isset($_POST['txtInning']) || !isset($_POST['cboPlayer']) || !isset($_POST['cboPlayType']) || !isset($_POST['cboSeason']) || $_POST['cboGame'] == '' || $_POST['txtInning'] == '' || $_POST['cboPlayer'] == '' || $_POST['cboPlayType'] == '' || $_POST['cboSeason'] == '') {
    print ('<H2>Failed to add play to DB<br />There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  $result = mysql_query('INSERT INTO plays (ID,GameID,Inning,PlayerID,TypeID,DateAdded,SeasonID)
            VALUES (NULL,'.addslashes($_POST['cboGame']).', '.addslashes($_POST['txtInning']).', '.addslashes($_POST['cboPlayer']).',
            '.addslashes($_POST['cboPlayType']).', "'.date('Y-m-d h:i:s').'", '.addslashes($_POST['cboSeason']).')');
  if (!$result) {
    print ('<H2>Failed to add play to DB<br />'.mysql_error().'</H2>');
    require('../config/footer.php');
    exit;
  }
}

// If Delete button clicked, delete play
if (isset($_POST['Delete']) && $demo_mode == '0') {
  if (!isset($_POST['lstPlays']) || $_POST['lstPlays'] == '') {
    print ('<H2>Failed to delete play from DB<br />There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  }
  $result = mysql_query('DELETE FROM plays WHERE ID = '.addslashes($_POST['lstPlays']));
  if ($result == False) {
      print ('<H2>Failed to delete play from DB<br />'.mysql_error().'</H2>');
      require('../config/footer.php');
      exit;
  }
}

if (isset($_POST['cboSeason']) && $_POST['cboSeason'] <> '' && isset($_POST['cboGame']) && $_POST['cboGame'] <> '') {
  $recPlays = mysql_query('SELECT * FROM plays WHERE SeasonID = '.addslashes($_POST['cboSeason']).' AND GameID = '
              .addslashes($_POST['cboGame']).' ORDER BY DateAdded') ;
}

?>
<form name="plays" method="POST">
  <table border="0">
    <tr>
      <td>
        <p>Season:</p></td>
      <td>
        <select name="cboSeason" size="1" onchange="document.plays.submit()">
          <option value=""></option>
<?php
if ($recSeason) {
  while ($rowSeason = mysql_fetch_assoc($recSeason)) {
    if (isset($_POST['cboSeason']) && $_POST['cboSeason'] == $rowSeason['ID']) {
      print '          <option selected';
    } else {
      print '          <option';
    }
?>
 VALUE="<?=$rowSeason['ID']?>"><?=stripslashes($rowSeason['Description'])?></option>
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
    <tr>
      <td>
        <p>Game:</p></td>
      <td>
        <select name="cboGame" size="1" onchange="document.plays.submit()">
          <option value=""></option>
<?php
$found_season = 0;
if ($recGame) {
  while ($rowGame = mysql_fetch_assoc($recGame)) {
    if (isset($_POST['cboGame']) && $_POST['cboGame'] == $rowGame['ID']) {
      $found_season = 1;
      print "          <option selected";
    } else {
      print "          <option";
    }
?>
 VALUE="<?=$rowGame['ID']?>">(<?=date('m/d/y - g:i A', strtotime($rowGame['GameDate']))?>) <?=stripslashes($rowGame['OpposingTeam'])
 ?></option>
<?php
  }
}
?>
        </select></td>
    </tr>
<?php
if (!isset($_POST['cboGame']) || $_POST['cboGame'] == '' || $found_season == 0) {
  echo "  </table>";
  require('../config/footer.php');
  exit;
}
?>
    <tr>
      <td>Inning:</td>
      <td>
        <input type=text name="txtInning" maxlength="50" size="2" value="<?php if (isset($_POST['txtInning'])) { echo $_POST['txtInning']; } ?>">
      </td>
    </tr>
    <tr>
      <td>Player:</td>
      <td>
        <select name="cboPlayer" size="1">
          <option value=""></option>
<?php
if ($recPlayer) {
  while ($rowPlayer = mysql_fetch_assoc($recPlayer)) {
    if (isset($_POST['cboPlayer']) && $_POST['cboPlayer'] == $rowPlayer['ID']) {
      print "          <option selected";
    } else {
      print "          <option";
    }
?>
 VALUE="<?=$rowPlayer['ID']?>"><?=stripslashes($rowPlayer['LastName'])?>, <?=stripslashes($rowPlayer['FirstName'])
 ?> (<?=$rowPlayer['ID']?>)</option>
<?php
  }
}
?>
        </select></td>
    </tr>
    <tr>
      <td>Play Type:</td>
      <td>
        <select name="cboPlayType" size="1">
          <option value=""></option>
<?php
if ($recPlayType) {
  while ($rowPlayType = mysql_fetch_assoc($recPlayType)) {
    if (isset($_POST['cboPlayType']) && $_POST['cboPlayType'] == $rowPlayType['ID']) {
      print "          <option selected";
    } else {
      print "          <option";
    }
?>
 VALUE="<?=$rowPlayType['ID']?>"><?=$rowPlayType['Description']?></option>
<?php
  }
}
?>
        </select></td>
    </tr>
    <tr valign="CENTER">
      <td>
        <input type="SUBMIT" name="Add" value="Add >>"></td>
      <td>
      Game - Inning - Player - Play<br />
      <select name="lstPlays" size="12">
<?php
if ($recPlays) {
  while ($rowPlays = mysql_fetch_assoc($recPlays)) {
    $recGame = mysql_query('SELECT OpposingTeam,GameDate FROM game WHERE ID = '.$rowPlays['GameID']);
    $rowGame = mysql_fetch_assoc($recGame);
    $Game = '('.date('m/d/y - g:i A', strtotime($rowGame['GameDate'])).') '.stripslashes($rowGame['OpposingTeam']);
    $recPlayer = mysql_query('SELECT FirstName,LastName FROM player WHERE SeasonID = '.addslashes($_POST['cboSeason']).
                 ' AND ID = '.$rowPlays['PlayerID']);
    $rowPlayer = mysql_fetch_assoc($recPlayer);
    $Player = stripslashes($rowPlayer['FirstName']).' '.stripslashes($rowPlayer['LastName']);
    $recPlayType = mysql_query('SELECT Description FROM type WHERE ID = '.$rowPlays['TypeID']);
    $rowPlayType = mysql_fetch_assoc($recPlayType);
    $PlayType = $rowPlayType['Description'];
?>
          <option value="<?=$rowPlays['ID']?>"><?=$Game?> - <?=$rowPlays['Inning']?> - <?=$Player?> - <?=$PlayType?></option>
<?php
  }
}
?>
          <option selected value=""></option>
      </select></td>
      <td>
        <input type="SUBMIT" name="Delete" value="Delete >>"></td>
      <td>
    </tr>
  </table>
</form>
<hr />
<a href="./">Main Admin Page</a> | <a href="season.php">Season Admin Page</a> | <a href="game.php">Game Admin Page</a> |
<a href="player.php">Player Admin Page</a> | Plays Admin Page
<?php
require('../config/footer.php');
?>
