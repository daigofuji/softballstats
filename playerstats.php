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
  $recPlayer=$pdo->query('SELECT * FROM player WHERE SeasonID = '.$season_id.' ORDER BY LastName, FirstName') ;
} else {
  $recPlayer=$pdo->query('SELECT * FROM player WHERE ID = '.addslashes($_GET['ID']).' AND SeasonID = '.$season_id
             .' ORDER BY LastName, FirstName') ;
}
$nTotalGames = 0;
$nTotalPAs = 0;
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
$nTotalSBs = 0;
$nTotalNPs = 0;
$nTotalSlug = 0;
$nTotalOBP = 0;
$nTotalAve = 0;
?>
<table border="0" class="stats small-12">
  <tr valign="top">
    <th>Name</th>
    <th>G</th>
    <th>PA</th>
    <th>AB</th>
    <th>R</th>
    <th>H</th>
    <th>2B</th>
    <th>3B</th>
    <th>HR</th>
    <th>RBI </th>
    <th>BB/HBP</th>
    <th>K</th>
    <th>Er</th>
    <th>SB</th>
<?php if ($show_np) { ?>
    <th>NPs*</th>
<?php } ?>
    <th>Slg%</th>
    <th>OBP</th>
    <th>Ave.</th>
  </tr>
<?php
$i = 0;
// Reset max stats
$nRunsMax = $nHitsMax = $nRBIsMax = $nWalksMax = $nStrikeOutsMax = $nHomeRunsMax = $nErrorsMax = $nSBsMax = $nAverageMax = $nDoublesMax = 0;
$nTriplesMax = $nNPsMax = $nSlugMax = $nRunsMax = $nHitsMax = $nRBIsMax = $nWalksMax = $nStrikeOutsMax = $nHomeRunsMax = $nErrorsMax = 0;
$nAverageMax = $nDoublesMax = $nTriplesMax = $nNPsMax = $nSlugMax = $nOBPMax = 0;
// Reset max stats ID
$sRunsMaxID = $sHitsMaxID = $sRBIsMaxID = $sWalksMaxID = $sStrikeOutsMaxID = $sHomeRunsMaxID = $sErrorsMaxID = $sSBsMaxID = $sAverageMaxID = NULL;
$sDoublesMaxID = $sTriplesMaxID = $sNPsMaxID = $sSlugMaxID = $sRunsMaxID = $sHitsMaxID = $sRBIsMaxID = $sWalksMaxID = $sStrikeOutsMaxID = NULL;
$sHomeRunsMaxID = $sErrorsMaxID = $sSBsMaxID = $sAverageMaxID = $sDoublesMaxID = $sTriplesMaxID = $sNPsMaxID = $sSlugMaxID = $sOBPMaxID = NULL;
// Calculate Stats for each Player
while ($rowPlayer=$recPlayer->fetch(PDO::FETCH_ASSOC)) {
  $i++;
  $recPlays=$pdo->query('SELECT * FROM plays WHERE PlayerID = '.$rowPlayer['ID'].' AND SeasonID = '.$season_id
            .' ORDER BY GameID, Inning, PlayerID') ;
  // Reset player stats
  $nAtBats = $nPAs = $nRuns = $nHits = $nRBIs = $nWalks = $nStrikeOuts = $nHomeRuns = $nErrors = $nSBs = $nAverage = $nDoubles = $nTriples = $nNPs = 0;
  $nSlug = $nOBP = $nGames = 0;
  // Reset game number
  $nGameNumber = NULL;
  // Increment stats for each play made
  while ($rowPlays=$recPlays->fetch(PDO::FETCH_ASSOC)) {
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
      case 27:
        $nSBs++;
        break;
      case 29:
        $nWalks++;
        break;
    }
    $recType = $pdo->query('SELECT * FROM type WHERE ID = '.$rowPlays['TypeID']);
    $rowType = $recType->fetch(PDO::FETCH_ASSOC);
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
  // Calculate PA
  $nPAs = $nAtBats + $nWalks;
  // Calculate Average, OBP and Slugging Percentage
  if ($nHits == 0 || $nAtBats == 0) {
    $sAverage = '.000';
    $sSlug = '0.000';
    $sOBP = '.000';
  } else {
    $nAverage = $nHits / $nAtBats;
    $nSlug = ($nHits + $nDoubles + ($nTriples * 2) + ($nHomeRuns * 3)) / $nAtBats;
    $nOBP = ($nHits + $nWalks) / ($nAtBats +$nWalks);
    // Cleanup Slugging, OBP and Average strings
    $sAverage = substr($nAverage,1,4);
    $sOBP = substr($nOBP,1,4);
    $sSlug = substr($nSlug,0,5);
    if ($nHits == $nAtBats) {
      $sAverage = '1.000';
      $sOBP = '1.000';
    }
    if (strlen($sAverage) == 2) {
      $sAverage = $sAverage.'00';
    } elseif (strlen($sAverage) == 3) {
      $sAverage = $sAverage.'0';
    }
    if (strlen($sOBP) == 2) {
      $sOBP = $sOBP.'00';
    } elseif (strlen($sOBP) == 3) {
      $sOBP = $sOBP.'0';
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
    if ($nSBs > $nSBsMax) {
      $nSBsMax = $nSBs;
      $sSBsMaxID = $rowPlayer['ID'];
    } elseif ($nSBs == $nSBsMax) {
      $sSBsMaxID = NULL;
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
    if ($nOBP > $nOBPMax) {
      $nOBPMax = $nOBP;
      $sOBPMaxID = $rowPlayer['ID'];
    } elseif ($nOBP == $nOBPMax) {
      $sOBPMaxID = NULL;
    }
    if ($nAverage > $nAverageMax) {
      $nAverageMax = $nAverage;
      $sAverageMaxID = $rowPlayer['ID'];
    } elseif ($nAverage == $nAverageMax) {
      $sAverageMaxID = NULL;
    }
  }

  $StatTemp[$rowPlayer['ID']]['PlayerID'] = $rowPlayer['ID'];
  $StatTemp[$rowPlayer['ID']]['PlayerName'] = $rowPlayer['LastName'].', '.$rowPlayer['FirstName'].' ('.$rowPlayer['ID'].')';
  $StatTemp[$rowPlayer['ID']]['Games'] = $nGames;
  $StatTemp[$rowPlayer['ID']]['PAs'] = $nPAs;
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
  $StatTemp[$rowPlayer['ID']]['SBs'] = $nSBs;
  $StatTemp[$rowPlayer['ID']]['NPs'] = $nNPs;
  $StatTemp[$rowPlayer['ID']]['Slug'] = $sSlug;
  $StatTemp[$rowPlayer['ID']]['OBP'] = $sOBP;
  $StatTemp[$rowPlayer['ID']]['Average'] = $sAverage;
  if (!isset($_GET['ID'])) {
    if ($nGames > $nTotalGames) {
      $nTotalGames = $nGames;
    }
    $nTotalAtBats = $nTotalAtBats + $nAtBats;
    $nTotalPAs = $nTotalPAs + $nPAs;
    $nTotalRuns = $nTotalRuns + $nRuns;
    $nTotalHits = $nTotalHits + $nHits;
    $nTotalDoubles = $nTotalDoubles + $nDoubles;
    $nTotalTriples = $nTotalTriples + $nTriples;
    $nTotalHomeRuns = $nTotalHomeRuns + $nHomeRuns;
    $nTotalRBIs = $nTotalRBIs + $nRBIs;
    $nTotalWalks = $nTotalWalks + $nWalks;
    $nTotalStrikeOuts = $nTotalStrikeOuts + $nStrikeOuts;
    $nTotalErrors = $nTotalErrors + $nErrors;
    $nTotalSBs = $nTotalSBs + $nSBs;
    $nTotalNPs = $nTotalNPs + $nNPs;
    $nTotalSlug = $nTotalSlug + $nSlug;
    $nTotalOBP = $nTotalOBP + $nOBP;
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
  <tr<?php echo $tdbg ?>>
    <td valign="top"> <a href="playerbios.php?ID=<?php echo $tmp['PlayerID']?>"><?php echo
    stripslashes($tmp['PlayerName'])?></a> </td>
    <td valign="top"> <?php echo $tmp['Games']?> </td>
    <td valign="top"> <?php echo $tmp['PAs']?> </td>
    <td valign="top"> <?php echo $tmp['AtBats']?> </td>
    <?php if ($tmp['PlayerID'] == $sRunsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Runs']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Runs']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sHitsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Hits']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Hits']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sDoublesMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Doubles']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Doubles']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sTriplesMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Triples']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Triples']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sHomeRunsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['HomeRuns']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['HomeRuns']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sRBIsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['RBIs']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['RBIs']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sWalksMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Walks']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Walks']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sStrikeOutsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['StrikeOuts']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['StrikeOuts']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sErrorsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Errors']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Errors']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sSBsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['SBs']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['SBs']?> </td>
    <?php } ?>
    <?php if ($show_np) { ?>
    <?php   if ($tmp['PlayerID'] == $sNPsMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['NPs']?> </td>
    <?php   } else { ?>
      <td valign="top"> <?php echo $tmp['NPs']?> </td>
    <?php   } ?>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sSlugMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Slug']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Slug']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sOBPMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['OBP']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['OBP']?> </td>
    <?php } ?>
    <?php if ($tmp['PlayerID'] == $sAverageMaxID) { ?>
      <td valign="top" class="topstats"><?php echo $tmp['Average']?> </td>
    <?php } else { ?>
      <td valign="top"> <?php echo $tmp['Average']?> </td>
    <?php } ?>
  </tr>
<?php
  }
}
if (!isset($_GET['ID'])) {
?>
 <tr valign="top">
   <td valign="top"><hr><strong>Totals:</strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalGames?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalPAs?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalAtBats?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalRuns?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalHits?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalDoubles?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalTriples?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalHomeRuns?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalRBIs?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalWalks?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalStrikeOuts?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalErrors?></strong></td>
   <td valign="top"><hr><strong><?php echo $nTotalSBs?></strong></td>
  <?php if ($show_np) { ?>
   <td valign="top"><hr><strong><?php echo $nTotalNPs?></strong></td>
  <?php } ?>
   <td valign="top"><hr><strong>
   <?php if ($i <> 0) { echo substr(($nTotalSlug / $i),0,5); } else { echo '0'; } ?></strong></td>
   <td valign="top"><hr><strong>
   <?php if ($i <> 0) { echo substr(($nTotalOBP / $i),1,4); } else { echo '0'; } ?></strong></td>
   <td valign="top"><hr><strong>
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
