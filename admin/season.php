<?php
require('../config/config.php');
$TITLE=$team_name.' Season Admin Page';
if ((isset($_POST['cboSeasonU']) && $_POST['cboSeasonU'] <> '') && !isset($_POST['Update']) && !isset($_POST['Add'])&& !isset($_POST['Delete'])) {
  $BODY_CODE='onLoad="document.season.cboSeasonU.focus();"';
} else {
  $BODY_CODE='onLoad="document.season.txtSeasonName.focus();"';
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

// If Add button clicked, add new season
if (isset($_POST['Add']) && $demo_mode == '0') {
  $_POST['cboSeasonU'] = '';
  if (!isset($_POST['txtSeasonName']) || $_POST['txtSeasonName'] == '') {
    print ('<H2>Failed to add season to DB<br>There is a blank field</H2>');
    require('../config/footer.php');
    exit;
  } else {
    if (isset($_POST['chkDefaultSeason'])) {
      $def = 1;
      $result = $pdo->query('UPDATE season SET DefaultSeason = 0');
      if (!$result) {
        print ('<H2>Failed to set season as default<br>'.mysql_error().'</H2>');
        require('../config/footer.php');
        exit;
      }
    } else {
      $def = 0;
    }
    $result = $pdo->query('INSERT INTO season (ID,Description,DefaultSeason)
              VALUES (NULL, "'.addslashes($_POST['txtSeasonName']).'", '.$def.')');
    if (!$result) {
      print ('<H2>Failed to add season to DB<br>'.mysql_error().'</H2>');
      require('../config/footer.php');
      exit;
    }
  }
}

// If Update button clicked, edit season
if (isset($_POST['Update']) && $demo_mode == '0') {
  if (!isset($_POST['txtSeasonNameU']) || !isset($_POST['cboSeasonU']) || $_POST['txtSeasonNameU'] == '' || $_POST['cboSeasonU'] == '') {
    print ('<h2>Failed to update season in DB<br>There is a blank field</h2>');
    require('../config/footer.php');
    exit;
  } else {
    if (isset($_POST['chkDefaultSeasonU']) && $_POST['chkDefaultSeasonU'] <> '') {
      $def = 1;
      $result = $pdo->query('UPDATE season SET DefaultSeason = 0');
      if (!$result) {
        print ('<h2>Failed to set season as default<br>'.mysql_error().'</h2>');
        require('../config/footer.php');
        exit;
      }
    } else {
      $def = 'NULL';
    }
    $result = $pdo->query('UPDATE season SET Description = "'.addslashes($_POST['txtSeasonNameU'])
              .'",DefaultSeason = '.$def.' WHERE ID = '.addslashes($_POST['cboSeasonU']));
    if (!$result) {
      print ('<h2>Failed to update season in DB<br>'.mysql_error().'</h2>');
      require('../config/footer.php');
      exit;
    }
  }
  $_POST['cboSeasonU'] = '';
}

// If Delete button clicked, delete play
if (isset($_POST['Delete']) && $demo_mode == '0') {
  $_POST['cboSeasonU'] = '';
  if (!isset($_POST['cboSeason']) || $_POST['cboSeason'] == '') {
    print ('<h2>Failed to delete season from DB<br>There is a blank field</h2>');
    require('../config/footer.php');
    exit;
  }
  $result = $pdo->query('SELECT * FROM game WHERE SeasonID = '.addslashes($_POST['cboSeason']));
  $row = $result->fetch(PDO::FETCH_ASSOC);
  if ($row) {
      print ('<h2>Failed to delete season from DB<br>There are still games for this season in the game table!</h2>');
      require('../config/footer.php');
      exit;
  }
  $result = $pdo->query('SELECT * FROM player WHERE SeasonID = '.addslashes($_POST['cboSeason']));
  $row = $result->fetch(PDO::FETCH_ASSOC);
  if ($row) {
      print ('<h2>Failed to delete season from DB<br>There are still players for this season in the player table!</h2>');
      require('../config/footer.php');
      exit;
  }
  $result = $pdo->query('SELECT Description FROM season WHERE ID = '.addslashes($_POST['cboSeason']).' AND DefaultSeason = 1');
  $row = $result->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    print ('<h2>You can\'t delete "'.$row['Description'].'", it is the current season!</h2>');
    require('../config/footer.php');
    exit;
  }
  $result = $pdo->query('DELETE FROM season WHERE ID = '.addslashes($_POST['cboSeason']));
  if ($result == False) {
      print ('<H2>Failed to delete season from DB<br>'.mysql_error().'</H2>');
      require('../config/footer.php');
      exit;
  }
}

$recSeason = $pdo->query('SELECT * FROM season ORDER BY ID') ;

?>
[Hint] - "Current Season" is the season that will be displayed on the stats pages.
<form name="season" method="POST">
  <table border="0" class="small-12">
    <tr valign="CENTER">
      <td><strong>Add Season:</strong></td>
      <td>
        Season Name:<br>
        <input type="TEXT" name="txtSeasonName" value="" maxlength="50" size="20"></td>
      <td>
        Current Season?:<br>
        <input type="CHECKBOX" checked name="chkDefaultSeason"></td>
      <td>
        <button type="SUBMIT" name="Add" value="">Add >></button></td>
    </tr>
    <tr>
      <td><strong>Remove Season:</strong></td>
      <td>
        <select name="cboSeason" size="1">
          <option value=""></option>
<?php
if ($recSeason) {
  while ($rowSeason = $recSeason->fetch(PDO::FETCH_ASSOC)) {
    if ($rowSeason['DefaultSeason'] == '1') {
      $curr = '[Current]';
    } else {
      $curr = '';
    }
    print '          <option value="'.$rowSeason['ID'].'">'.stripslashes($rowSeason['Description']).' '.$curr.'</option>';
  }
}
?>
        </select></td>
      <td>
      </td>
      <td>
        <button type="SUBMIT" name="Delete" value="">Delete >></button></td>
    </tr>
    <tr>
      <td><strong>Update Season:</strong></td>
      <td colspan="3">
        <select onchange="document.season.submit()" name="cboSeasonU" size="1">
          <option value=""></option>
<?php
$desc = '';
$check = '';
$recSeason = $pdo->query('SELECT * FROM season ORDER BY ID') ;
if ($recSeason) {
  while ($rowSeason = $recSeason->fetch(PDO::FETCH_ASSOC)) {
    $sel = '';
    $curr = '';
     if ($rowSeason['DefaultSeason'] == '1') {
       $curr = ' [Current]';
    }
    if (isset($_POST['cboSeasonU']) && $_POST['cboSeasonU'] <> '') {
      if ($rowSeason['ID'] == $_POST['cboSeasonU']) {
        print $sel;
        $sel = 'selected ';
        $desc = stripslashes($rowSeason['Description']);
        if ($rowSeason['DefaultSeason'] == '1') {
          $check = 'checked ';
        }
      }
    }
    print '          <option '.$sel.'value="'.$rowSeason['ID'].'">'.stripslashes($rowSeason['Description']).''.$curr."</option>\n";
  }
}
?>
        </select></td>
    </tr>
<?php
if (isset($_POST['cboSeasonU']) && $_POST['cboSeasonU'] <> '') {
?>
    <tr>
      <td>
      </td>
      <td>
        Season Name:<br>
        <input type="TEXT" name="txtSeasonNameU" value="<?php echo $desc; ?>" maxlength="50" size="20"></td>
      <td>
        Current Season?:<br>
        <imput type="CHECKBOX" <?php echo $check; ?>NAME="chkDefaultSeasonU"></td>
      <td>
        <button type="SUBMIT" name="Update">Update >></button></td>
    </tr>
<?php
}
?>
  </table>
</form>
<hr>
<a href="./">Main Admin Page</a> | Season Admin Page | <a href="game.php">Game Admin Page</a> |
<a href="player.php">Player Admin Page</a> | <a href="plays.php">Plays Admin Page</a>
<?php
require('../config/footer.php');
?>
