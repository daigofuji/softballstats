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
<h1><?=$TITLE;?></h1>
<hr />
<?php
if (isset($_GET['ID'])) {
  if (!is_numeric($_GET['ID'])) {
    print('<h2>Invalid ID number!</h2>');
    require('./config/footer.php');
    exit;
  }
}
?>
<ul class="hint">
<li><strong>[Hint]</strong> Click the player number to see that player's stats.</li>
<li><strong>[Hint]</strong> Click the player name to send that player an email.</li>
</ul>
<hr />
<form name="bios" method="post">
Switch Season:
<select name="cboSeason" size="1" onchange="document.bios.submit()">
<?php
$recSeason = mysql_query('SELECT * FROM season ORDER BY ID') ;
if ($recSeason) {
  while ($rowSeason = mysql_fetch_assoc($recSeason)) {
    if ((isset($_POST['cboSeason']) && $_POST['cboSeason'] == $rowSeason['ID']) || $rowSeason['DefaultSeason']) {
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
</select>
</form>
<p>
<table width="100%" border="0">
  <tr>
    <th width="5%">Number</th>
    <th width="25%">Player Name</th>
    <th width="70%">Bio</th>
  </tr>
<?php
if (!isset($_GET['ID'])) {
  $recPlayer=mysql_query('SELECT * FROM player WHERE SeasonID = '.$season_id.' ORDER BY LastName, FirstName') ;
} else {
  $recPlayer=mysql_query('SELECT * FROM player WHERE ID = '.addslashes($_GET['ID']).' AND SeasonID = '
                         .$season_id.' ORDER BY LastName, FirstName') ;
}

$i = 0;
while($rowPlayer=mysql_fetch_assoc($recPlayer)) {
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
   <td <?=$tdbg?> valign="top"><strong><a href="playerstats.php?ID=<?=$rowPlayer['ID']?>"><?= $rowPlayer['ID'] ?></a></strong></td>
<?php if ($rowPlayer['EMail']  == '') { ?>
    <td<?=$tdbg?> valign="top"><?=stripslashes($rowPlayer['LastName'])?>,
      <?=stripslashes($rowPlayer['FirstName']) ?></td>
<?php } else { ?>
    <td<?=$tdbg?> valign="top"><a href="mailto:<?= stripslashes($rowPlayer['EMail']) ?>">
      <?=stripslashes($rowPlayer['LastName'])?>, <?=stripslashes($rowPlayer['FirstName']) ?></a></td>
<?php } ?>
    <td<?=$tdbg?> valign="top"><?=$bio?></td>
  </tr>
<?php
}
?>
 </table>
<?php
require('./config/footer.php');
?>
