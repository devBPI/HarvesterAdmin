<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<title>Historique des tâches annexes</title>
</head>
<body id="haut">
	<?php
include ('../Vue/common/Header.php');
$url = "HistoriqueTachesAnnexes.php?&order=";
?>

	<div class="content" style="width:90%">
	<table class="table-config">
		<thead>
<?php $arrow = "";
$sens = "";
if ($order == "name") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "name DESC") {
    $arrow = "▲";
} ?>
			<th scope="col" style="cursor:pointer;width:20%" onclick='location.href="<?= $url ?>name <?= $sens?>"'>Nom de la Tâche<?= $arrow ?></th>
			<th scope="col" style="cursor:default;width:8%">Paramètre(s)</th>
<?php $arrow = "";
$sens = "";
if ($order == "status") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "status DESC") {
    $arrow = "▲";
} ?>
			<th scope="col" style="cursor:pointer;width:8%" onclick='location.href="<?= $url ?>status <?= $sens ?>"'>Statut<?= $arrow ?></th>
<?php $arrow = "";
$sens = "";
if ($order == "creation_date") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "creation_date DESC") {
    $arrow = "▲";
} ?>
			<th scope="col" style="cursor:pointer;width:8%" onclick='location.href="<?= $url ?>creation_date <?= $sens ?>"'>Création Demande<?= $arrow ?></th>
<?php $arrow = "";
$sens = "";
if ($order == "start_time") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "start_time DESC") {
    $arrow = "▲";
} ?>
			<th scope="col" style="cursor:pointer;width:8%" onclick='location.href="<?= $url ?>start_time <?= $sens ?>"'>Début Traitement<?= $arrow ?></th>
<?php $arrow = "";
$sens = "";
if ($order == "end_time") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "end_time DESC") {
    $arrow = "▲";
} ?>
			<th scope="col" style="cursor:pointer;width:8%" onclick='location.href="<?= $url ?>end_time <?= $sens ?>"'>Fin Traitement<?= $arrow ?></th>
			<th scope="col" style="cursor:default;width:7%">Durée effective Traitement</th>
			<th scope="col" style="cursor:default;width:12%">Message</th>
		</thead>
<?php
foreach ($tasks as $task) {
    // On s'occupe de créer et formater les date comme on le souhaite
    $creationDateSyst = date('d-m-Y H:i:s', strtotime($task['creation_date'])) . " ";
    $modificationDateSyst = date('d-m-Y H:i:s', strtotime($task['modification_date'])) . " ";
    $originalStartTime = $task['start_time'];
    $originalEndTime = $task['end_time'];
	// Formatage des autres champs
    $totalEffectiveDurationSec = $task['total_effective_duration_sec'];
	$harvestStartDateSyst = empty($originalStartTime)?"-":date('d-m-Y H:i:s', strtotime($originalStartTime)) . " ";
	$harvestEndDateSyst = empty($originalEndTime)?"-":date('d-m-Y H:i:s', strtotime($originalEndTime)) . " ";;
	$creationDateSyst = empty($creationDateSyst)?"-":$creationDateSyst;
	if (!empty($totalEffectiveDurationSec)) {

		$temp = $totalEffectiveDurationSec % 3600;

		$hours = ( $totalEffectiveDurationSec - $temp ) / 3600 ;

		$temp2 = $temp % 60 ;

		$mins = ( $temp - $temp2 ) / 60;

		$secs = $temp2;

		if ($hours < 10) { $hours = '0'.$hours; }
		if ($mins < 10) { $mins = '0'.$mins; }
		if ($secs < 10) { $secs = '0'.$secs; }

		$totalEffectiveDuration = "".$hours."h".$mins."m".$secs."s";

	} else { $totalEffectiveDuration = "-"; }

	$harvestTaskCreationDate = date_create($creationDateSyst);
    $harvestTaskModificationDate = date_create($modificationDateSyst);
   // $harvestStartTime = date_create($harvestStartDateSyst);
   //  $harvestEndTime = date_create($harvestEndDateSyst);

    ?>
		<tr>
			<td data-label="Nom"><?= $task['name']; ?></td>
			<td data-label="Paramètre(s)"><div style="height:25px; line-height: 25px;"><?= $task['parameter'] ?></div></td>
			<td data-label="Status"><?php
				if(preg_match('/(ERROR)/',$task['status']))
				{
					echo "<div style = 'height:25px; line-height: 25px; color: red;  font-weight : bold;'>".$task['status']."</div>";
				}
				else
				{
					echo "<div style = 'height:25px; line-height: 25px;'>".$task['status']."</div>";
				}
			?></td>
			<td data-label="Création demande"><?= $creationDateSyst ?></td>
        	<td data-label="Début traitement"><?= $harvestStartDateSyst ?></td>
			<td data-label="Fin traitement"><?= $harvestEndDateSyst ?></td>
			<td data-label="Durée effective"><?= $totalEffectiveDuration ?></td>
			<td data-label="Message"><?= $task['message']; ?></td>
		</tr>
<?php } ?>
	</table>
	</div>

	<div style="margin : 0 auto; padding:3% 0; width: max-content;">
	<?php
		if($page>3){
			echo "<a href='HistoriqueTachesAnnexes.php?page=1' class='buttonpage'>&laquo;</a>\t";
		}

		$index_lower = 2; 
		$index_upper = 2;
		if(($page-2)<1){
			$index_upper+=1-($page-2);
			$index_lower-=1-($page-2);
		} else if (($page+2)>$total_pages){
			$index_lower+=($page+2)-$total_pages;
			$index_upper-=($page+2)-$total_pages;
		}
		for ($i = $page-$index_lower; $i <= $page+$index_upper; $i ++)
		{
			if($i==$page){
				echo "<a href='HistoriqueTachesAnnexes.php?page=" . $i . "' class='buttonpage' style='background-color:#4b6a7c'>" . $i . "</a>\t";
			} else {
				echo "<a href='HistoriqueTachesAnnexes.php?page=" . $i . "' class='buttonpage'>" . $i . "</a>\t";
			}
		}
		if($page<$total_pages-2){
			echo "<a href='HistoriqueTachesAnnexes.php?page=" . $total_pages . "' class='buttonpage'>&raquo;</a>";
		}
	?>
	</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script src='../js/moissons/progress.js'></script>
<script src='../js/histo-task-status.js'></script>
</html>
