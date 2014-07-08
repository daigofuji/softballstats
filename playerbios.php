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
$TITLE=$team_name.' Player Bios Report - '.$season_name;
require('./config/header.php');
?>
<h1><?php echo $TITLE; ?></h1>
<hr>

<?php
if (isset($_GET['ID'])) {
  if (!is_numeric($_GET['ID'])) {
    print('<h2>Invalid ID number!</h2>');
    require('./config/footer.php');
    exit;
  }
}
?>

<ul class="hint no-bullet">
  <li><strong>[Hint]</strong> Click the player number to see that player's stats.</li>
  <li><strong>[Hint]</strong> Click the player name to send that player an email.</li>
</ul>

<hr>

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
 VALUE="<?php echo $rowSeason['ID'] ?>"><?php echo stripslashes($rowSeason['Description']) ?></option>
<?php
  }
}
?>
</select>
</form>
<p>
<table border="0" class="small-12">
  <tr>
    <th>Number</th>
    <th>Player Name</th>
    <th>Bio</th>
  </tr>
<?php
if (!isset($_GET['ID'])) {
  $recPlayer=$pdo->query('SELECT * FROM player WHERE SeasonID = '.$season_id.' ORDER BY LastName, FirstName');
} else {
  $recPlayer=$pdo->query('SELECT * FROM player WHERE ID = '.addslashes($_GET['ID']).' AND SeasonID = '.$season_id.' ORDER BY LastName, FirstName');
}

$i = 0;
while($rowPlayer = $recPlayer->fetch(PDO::FETCH_ASSOC)) {
  if ($rowPlayer['Bio'] == NULL) {
    $bio = 'No Bio';
  } else {
    $bio = nl2br(stripslashes($rowPlayer['Bio']));
  }
  if ($i&1) {
    $tdbg = ' class="odd"';
  } else {
    $tdbg = ' class="even"';
  }
  $i++;
?>
   <td <?php echo $tdbg ?> valign="top"><strong><a href="playerstats.php?ID=<?php echo $rowPlayer['ID']?>"><?php echo $rowPlayer['ID'] ?></a></strong></td>
<?php if ($rowPlayer['EMail']  == '') { ?>
    <td<?php echo $tdbg ?> valign="top"><?php echo stripslashes($rowPlayer['LastName'])?>,
      <?php echo stripslashes($rowPlayer['FirstName']) ?></td>
<?php } else { ?>
    <td<?php echo $tdbg ?> valign="top"><a href="mailto:<?php echo stripslashes($rowPlayer['EMail']) ?>">
      <?php echo stripslashes($rowPlayer['LastName'])?>, <?php echo stripslashes($rowPlayer['FirstName']) ?></a></td>
<?php } ?>
    <td<?php echo $tdbg ?> valign="top"><?php echo $bio ?></td>
  </tr>
<?php
}
?>
 </table>
<?php
require('./config/footer.php');
?>
