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
$TITLE=$team_name.' Player Statistic Report - '.$season_name;
require('./config/header.php');
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
<?php
if (isset($_GET['ID'])) {
  if (!is_numeric($_GET['ID'])) {
    print('<h2>Invalid ID number!</h2>');
    require('./config/footer.php');
    exit;
  }
}
?>

<?php
if (!isset($_GET['ID'])) {
  $recPlayer=mysql_query('SELECT * FROM player WHERE SeasonID = '.$season_id.' ORDER BY LastName, FirstName') ;
} else {
  $recPlayer=mysql_query('SELECT * FROM player WHERE ID = '.addslashes($_GET['ID']).' AND SeasonID = '.$season_id
             .' ORDER BY LastName, FirstName') ;
}
$nTotalGames = 0;
$nTotalAtBats = 0;
$nTotalRuns = 0;
$nTotalHits = 0;
$nTotalDoubles = 0;
$nTotalTriples = 0;
$nTotalHomeRuns = 0;
$nTotalRBIs = 0;
$nTotalWalks = 0;
$nTotalStrikeOuts = 0;
$nTotalErrors = 0;
$nTotalNPs = 0;
$nTotalSlug = 0;
$nTotalAve = 0;
?>
<table border="0" width="100%" class="stats">
  <tr valign="top">
    <th width="18%">Name</th>
    <th width="6%">G</th>
    <th width="6%">AB</th>
    <th width="6%">R</th>
    <th width="6%">H</th>
    <th width="6%">2B</th>
    <th width="6%">3B</th>
    <th width="6%">HR</th>
    <th width="6%">RBI </th>
    <th width="6%">BB</th>
    <th width="6%">K</th>
    <th width="6%">Er</th>
<?php if ($show_np) { ?>
    <th width="6%">NPs*</th>
<?php } ?>
    <th width="10%">Slg%</th>
    <th width="10%">Ave.</th>
  </tr>
<?php
$i = 0;
// Reset max stats
$nRunsMax = $nHitsMax = $nRBIsMax = $nWalksMax = $nStrikeOutsMax = $nHomeRunsMax = $nErrorsMax = $nAverageMax = $nDoublesMax = 0;
$nTriplesMax = $nNPsMax = $nSlugMax = $nRunsMax = $nHitsMax = $nRBIsMax = $nWalksMax = $nStrikeOutsMax = $nHomeRunsMax = $nErrorsMax = 0;
$nAverageMax = $nDoublesMax = $nTriplesMax = $nNPsMax = $nSlugMax = 0;
// Reset max stats ID
$sRunsMaxID = $sHitsMaxID = $sRBIsMaxID = $sWalksMaxID = $sStrikeOutsMaxID = $sHomeRunsMaxID = $sErrorsMaxID = $sAverageMaxID = NULL;
$sDoublesMaxID = $sTriplesMaxID = $sNPsMaxID = $sSlugMaxID = $sRunsMaxID = $sHitsMaxID = $sRBIsMaxID = $sWalksMaxID = $sStrikeOutsMaxID = NULL;
$sHomeRunsMaxID = $sErrorsMaxID = $sAverageMaxID = $sDoublesMaxID = $sTriplesMaxID = $sNPsMaxID = $sSlugMaxID = NULL;
// Calculate Stats for each Player
while ($rowPlayer=mysql_fetch_assoc($recPlayer)) {
  $i++;
  $recPlays=mysql_query('SELECT * FROM plays WHERE PlayerID = '.$rowPlayer['ID'].' AND SeasonID = '.$season_id
            .' ORDER BY GameID, Inning, PlayerID') ;
  // Reset player stats
  $nAtBats = $nRuns = $nHits = $nRBIs = $nWalks = $nStrikeOuts = $nHomeRuns = $nErrors = $nAverage = $nDoubles = $nTriples = $nNPs = 0;
  $nSlug = $nGames = 0;
  // Reset game number
  $nGameNumber = NULL;
  // Increment stats for each play made
  while ($rowPlays=mysql_fetch_assoc($recPlays)) {
    // Increment number of games if not the same GameID(depends on proper sorting, see recPlays query statement)
    if ($nGameNumber <> $rowPlays['GameID']) {
      $nGameNumber = $rowPlays['GameID'];
      $nGames++;
    }
    switch ($rowPlays['TypeID']) {
      case 2:
        $nDoubles++;
        break;
      case 3:
        $nTriples++;
        break;
      case 4:
        $nHomeRuns++;
        break;
      case 5:
        $nRBIs++;
        break;
      case 7:
        $nStrikeOuts++;
        break;
      case 11:
        $nErrors++;
        break;
      case 12:
        $nErrors++;
        break;
      case 13:
        $nErrors++;
        break;
      case 14:
        $nErrors++;
        break;
      case 15:
        $nErrors++;
        break;
      case 16:
        $nErrors++;
        break;
      case 17:
        $nErrors++;
        break;
      case 18:
        $nErrors++;
        break;
      case 19:
        $nErrors++;
        break;
      case 20:
        $nErrors++;
        break;
      case 21:
        $nWalks++;
        break;
      case 24:
        $nRuns++;
        break;
      case 25:
        $nNPs++;
        break;
    }
    $recType=mysql_query('SELECT * FROM type WHERE ID = '.$rowPlays['TypeID']);
    $rowType=mysql_fetch_assoc($recType);
    // Tally At Bats and Hits
    switch ($rowType['HitOrOutID']) {
      case 1:
        $nAtBats++;
        $nHits++;
        break;
      case 2:
        $nAtBats++;
        break;
    }
  }
  // Calculate Average and Slugging Percentage
  if ($nHits == 0 || $nAtBats == 0) {
    $sAverage = '.000';
    $sSlug = '0.000';
  } else {
    $nAverage = $nHits / $nAtBats;
    $nSlug = ($nHits + $nDoubles + ($nTriples * 2) + ($nHomeRuns * 3)) / $nAtBats;
    // Cleanup Slugging and Average strings
    $sAverage = substr($nAverage,1,4);
    $sSlug = substr($nSlug,0,5);
    if ($nHits == $nAtBats) {
      $sAverage = '1.000';
    }
    if (strlen($sAverage) == 2) {
      $sAverage = $sAverage.'00';
    } elseif (strlen($sAverage) == 3) {
      $sAverage = $sAverage.'0';
    }
    if (strlen($sSlug) == 1) {
      $sSlug = $sSlug.'.000';
    } elseif (strlen($sSlug) == 3) {
      $sSlug = $sSlug.'00';
    } elseif (strlen($sSlug) == 4) {
      $sSlug = $sSlug.'0';
    }
  }
  // Check to see if this player has the Max score in any category
  if (!isset($_GET['ID'])) {
    if ($nRuns > $nRunsMax) {
      $nRunsMax = $nRuns;
      $sRunsMaxID = $rowPlayer['ID'];
    } elseif ($nRuns == $nRunsMax) {
      $sRunsMaxID = NULL;
    }
    if ($nHits > $nHitsMax) {
      $nHitsMax = $nHits;
      $sHitsMaxID = $rowPlayer['ID'];
    } elseif ($nHits == $nHitsMax) {
      $sHitsMaxID = NULL;
    }
    if ($nDoubles > $nDoublesMax) {
      $nDoublesMax = $nDoubles;
      $sDoublesMaxID = $rowPlayer['ID'];
    } elseif ($nDoubles == $nDoublesMax) {
      $sDoublesMaxID = NULL;
    }
    if ($nTriples > $nTriplesMax) {
      $nTriplesMax = $nTriples;
      $sTriplesMaxID = $rowPlayer['ID'];
    } elseif ($nTriples == $nTriplesMax) {
      $sTriplesMaxID = NULL;
    }
    if ($nHomeRuns > $nHomeRunsMax) {
      $nHomeRunsMax = $nHomeRuns;
      $sHomeRunsMaxID = $rowPlayer['ID'];
    } elseif ($nHomeRuns == $nHomeRunsMax) {
      $sHomeRunsMaxID = NULL;
    }
    if ($nRBIs > $nRBIsMax) {
      $nRBIsMax = $nRBIs;
      $sRBIsMaxID = $rowPlayer['ID'];
    } elseif ($nRBIs == $nRBIsMax) {
      $sRBIsMaxID = NULL;
    }
    if ($nWalks > $nWalksMax) {
      $nWalksMax = $nWalks;
      $sWalksMaxID = $rowPlayer['ID'];
    } elseif ($nWalks == $nWalksMax) {
      $sWalksMaxID = NULL;
    }
    if ($nStrikeOuts > $nStrikeOutsMax) {
      $nStrikeOutsMax = $nStrikeOuts;
      $sStrikeOutsMaxID = $rowPlayer['ID'];
    } elseif ($nStrikeOuts == $nStrikeOutsMax) {
      $sStrikeOutMaxID = NULL;
    }
    if ($nErrors > $nErrorsMax) {
      $nErrorsMax = $nErrors;
      $sErrorsMaxID = $rowPlayer['ID'];
    } elseif ($nErrors == $nErrorsMax) {
      $sErrorsMaxID = NULL;
    }
    if ($nNPs > $nNPsMax) {
      $nNPsMax = $nNPs;
      $sNPsMaxID = $rowPlayer['ID'];
    } elseif ($nNPs == $nNPsMax) {
      $sNPsMaxID = NULL;
    }
    if ($nSlug > $nSlugMax) {
      $nSlugMax = $nSlug;
      $sSlugMaxID = $rowPlayer['ID'];
    } elseif ($nSlug == $nSlugMax) {
      $sSlugMaxID = NULL;
    }
    if ($nAverage > $nAverageMax) {
      $nAverageMax = $nAverage;
      $sAverageMaxID = $rowPlayer['ID'];
    } elseif ($nAverage == $nAverageMax) {
      $sAverageMaxID = NULL;
    }
  }

  $StatTemp[$rowPlayer['ID']]['PlayerID'] = $rowPlayer['ID'];
  $StatTemp[$rowPlayer['ID']]['PlayerName'] = $rowPlayer['LastName'].', '.$rowPlayer['FirstName'];
  $StatTemp[$rowPlayer['ID']]['Games'] = $nGames;
  $StatTemp[$rowPlayer['ID']]['AtBats'] = $nAtBats;
  $StatTemp[$rowPlayer['ID']]['Runs'] = $nRuns;
  $StatTemp[$rowPlayer['ID']]['Hits'] = $nHits;
  $StatTemp[$rowPlayer['ID']]['Doubles'] = $nDoubles;
  $StatTemp[$rowPlayer['ID']]['Triples'] = $nTriples;
  $StatTemp[$rowPlayer['ID']]['HomeRuns'] = $nHomeRuns;
  $StatTemp[$rowPlayer['ID']]['RBIs'] = $nRBIs;
  $StatTemp[$rowPlayer['ID']]['Walks'] = $nWalks;
  $StatTemp[$rowPlayer['ID']]['StrikeOuts'] = $nStrikeOuts;
  $StatTemp[$rowPlayer['ID']]['Errors'] = $nErrors;
  $StatTemp[$rowPlayer['ID']]['NPs'] = $nNPs;
  $StatTemp[$rowPlayer['ID']]['Slug'] = $sSlug;
  $StatTemp[$rowPlayer['ID']]['Average'] = $sAverage;
  if (!isset($_GET['ID'])) {
    if ($nGames > $nTotalGames) {
      $nTotalGames = $nGames;
    }
    $nTotalAtBats = $nTotalAtBats + $nAtBats;
    $nTotalRuns = $nTotalRuns + $nRuns;
    $nTotalHits = $nTotalHits + $nHits;
    $nTotalDoubles = $nTotalDoubles + $nDoubles;
    $nTotalTriples = $nTotalTriples + $nTriples;
    $nTotalHomeRuns = $nTotalHomeRuns + $nHomeRuns;
    $nTotalRBIs = $nTotalRBIs + $nRBIs;
    $nTotalWalks = $nTotalWalks + $nWalks;
    $nTotalStrikeOuts = $nTotalStrikeOuts + $nStrikeOuts;
    $nTotalErrors = $nTotalErrors + $nErrors;
    $nTotalNPs = $nTotalNPs + $nNPs;
    $nTotalSlug = $nTotalSlug + $nSlug;
    $nTotalAve = $nTotalAve + $nAverage;
  }
}

if (isset($StatTemp)) {
  $i = 0;
  while ($tmp = array_shift($StatTemp)) {
  if ($i&1) {
    $tdbg = ' class="odd"';
  } else {
    $tdbg = ' class="even"';
  }
    $i++;
?>
  <tr<?= $tdbg ?>>
    <td valign="top"> <a href="playerbios.php?ID=<?=$tmp['PlayerID']?>"><?=
    stripslashes($tmp['PlayerName'])?></a> </td>
    <td valign="top"> <?=$tmp['Games']?> </td>
    <td valign="top"> <?=$tmp['AtBats']?> </td>
    <?php if ($tmp['PlayerID'] == $sRunsMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Runs']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Runs']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sHitsMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Hits']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Hits']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sDoublesMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Doubles']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Doubles']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sTriplesMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Triples']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Triples']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sHomeRunsMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['HomeRuns']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['HomeRuns']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sRBIsMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['RBIs']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['RBIs']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sWalksMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Walks']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Walks']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sStrikeOutsMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['StrikeOuts']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['StrikeOuts']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sErrorsMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Errors']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Errors']?> </td>
    <?php } ?>
    <?php if ($show_np) { ?>
    <?php   if ($tmp['PlayerID'] == $sNPsMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['NPs']?> </td>
    <?php   } else { ?>
      <td valign="top"> <?=$tmp['NPs']?> </td>
    <?php   } ?>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sSlugMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Slug']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Slug']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sAverageMaxID) { ?>
      <td valign="top" class="topstats"><?=$tmp['Average']?> </td>
    <?php } else { ?>
      <td valign="top"> <?=$tmp['Average']?> </td>
    <?php } ?>
  </tr>
<?php
  }
}
if (!isset($_GET['ID'])) {
?>
 <tr valign="top">
   <td valign="top"><hr /><strong>Totals:</strong></td>
   <td valign="top"><hr /><strong><?=$nTotalGames?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalAtBats?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalRuns?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalHits?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalDoubles?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalTriples?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalHomeRuns?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalRBIs?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalWalks?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalStrikeOuts?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalErrors?></strong></td>
   <td valign="top"><hr /><strong><?=$nTotalNPs?></strong></td>
   <td valign="top"><hr /><strong>
   <?php if ($i <> 0) { echo substr(($nTotalSlug / $i),0,5); } else { echo '0'; } ?></strong></td>
   <td valign="top"><hr /><strong>
   <?php if ($i <> 0) { echo substr(($nTotalAve / $i),1,4); } else { echo '0'; } ?></strong></td>
 </tr>
<?php
}
?>
</table>
<p>
<?php if ($show_np) { ?>
*NP = Nice Play - Any defensive play that would make a highlight reel!
<?php
}
?>
</p>
<?php
require('./config/footer.php');
?>
