<div lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />
	<title>Historique des Moissons</title>
</head>
<td id="haut" style="height: 100%;">
<?php
include ('../Vue/common/Header.php');
$url = "HistoriqueMoisson.php?&order=";
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
			<th scope="col" style="cursor:pointer" onclick='location.href="<?= $url ?>name <?= $sens ?>"'>Nom abrégé<?= $arrow ?></th>
			<th scope="col" style="cursor:default">Progression</th>
<?php $arrow = "";
	$sens = "";
	if ($order == "status") {
		$arrow = "▼";
		$sens = "DESC";
	} else if ($order == "status DESC") {
		$arrow = "▲";
	} ?>
			<th scope="col" style="cursor:pointer" onclick='location.href="<?= $url ?>status <?= $sens ?>"'>Statut<?= $arrow ?></th>
			<th scope="col" style="cursor:default">Documents <br>insérés</th>
			<th scope="col" style="cursor:default">Volume de notices <br>attendu</th>
<?php

	$arrow = "";
	$sens = "";
	if ($order == "creation_date") {
		$arrow = "▼";
		$sens = "DESC";
	} else if ($order == "creation_date DESC") {
		$arrow = "▲";
	} ?>
			<th scope="col" style="cursor:pointer" onclick='location.href="<?= $url ?>creation_date <?= $sens  ?>"'>Création Demande<?= $arrow ?></th>
<?php	$arrow = "";
	$sens = "";
	if ($order == "start_time") {
		$arrow = "▼";
		$sens = "DESC";
	} else if ($order == "start_time DESC") {
		$arrow = "▲";
	} ?>
			<th scope="col" style="cursor:pointer" onclick='location.href="<?= $url ?>start_time <?= $sens ?>"'>Début Moisson<?= $arrow ?></th>
<?php $arrow = "";
	$sens = "";
	if ($order == "end_time") {
		$arrow = "▼";
		$sens = "DESC";
	} else if ($order == "end_time DESC") {
		$arrow = "▲";
	}
	?>
			<th scope="col" style="cursor:pointer" onclick='location.href="<?= $url ?>end_time <?= $sens ?>"'>Fin Moisson<?= $arrow ?></th>
			<th scope="col" style="cursor:default">Durée effective moisson</th>
			<th scope="col" style="cursor:default">Message</th>
		</thead>

<?php
			foreach ($tasks as $task) {
				// On s'occupe de créer et formater les dates comme on le souhaite
				$creationDateSyst = date('d-m-Y H:i:s', strtotime($task['creation_date'])) . " ";
				$modificationDateSyst = date('d-m-Y H:i:s', strtotime($task['modification_date'])) . " ";
				$originalStartTime = $task['start_time'];
				$originalEndTime = $task['end_time'];
				// Formatage des autres champs
				$task['notices_number'] = $task['notices_number']>0?$task['notices_number']:"-";
				$task['expected_notices_number'] = $task['expected_notices_number']>0?$task['expected_notices_number']:"-";
				$totalEffectiveDurationSec = $task['total_effective_duration_sec'];
				$progression = isset($task["progress"])?"<div class='progress' name='PRGR-".$task['id']."'/>":"";
				$creationDateSyst = empty($creationDateSyst)?"-":$creationDateSyst;
				$harvestStartDateSyst = empty($originalStartTime)?"-":date('d-m-Y H:i:s', strtotime($originalStartTime)) . " ";
				$harvestEndDateSyst = empty($originalEndTime)?"-":date('d-m-Y H:i:s', strtotime($originalEndTime)) . " ";;
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
				<td data-label="Nom"><?= str_replace("_", "_<wbr>",$task['name']); ?></td>
				<td data-label="Progression"><?= $progression ?></td>
				<td data-label="Statut">
<?php 			if(preg_match('/(ERROR)/',$task['status'])) { ?>
					<div>
<?php				if($task['has_no_more_recent_indexed'] == 'true' OR $task['has_no_more_recent_indexed'] == 't' ){ // CTLG-400 (pour se preserver de la double-relance du INDEX_ERROR)
						// Libelle Statut (en gras car pas de plus recent indexe) ?>
						<div style="color:red;font-weight:bold"><?= $task['status'] ?></div>
<?php				} else {
						// Libelle Statut (pas en gras) ?>
						<div style="color:red;font-weight:bold"><?= $task['status'] ?></div>
<?php				    } // Bouton Relance ?>
						<form method="post" action="MoissonSurDemande.php" class="hmForm" onsubmit="return confirm('Relancer la moisson maintenant ?')">
							<input name="suppr" type="hidden" value="<?= $task['id'] ?>">
							<select aria-label="Id de la configuration à relancer" style="display: none" id="configuration-select-whithout-file" name="configuration-select-whithout-file">
								<option value="<?= $task['configuration_id'] ?>" selected></option>
							</select>
							<input class="error-light-color" type="image" alt="Relancer la moisson" name="launch-without-file-button" src="../ressources/reload.png" style="width:15px;height:15px">
						</form>
						</div>
					</div>
<?php 			} else { ?>
					<div class="statusprogress" name="STAT-<?= $task['id'] ?>"></div>
<?php 			} ?>
				</td>
				<td data-label="Documents insérés"><?= $task['notices_number']; ?></td>
				<td data-label="Nombre attendu"><?= $task['expected_notices_number']; ?></td>
				<td data-label="Création demande"><?= $creationDateSyst; ?></td>
				<td data-label="Début moisson"><?= $harvestStartDateSyst; ?></td>
				<td data-label="Fin moisson"><?= $harvestEndDateSyst; ?></td>
				<td data-label="Durée effective"><?= $totalEffectiveDuration ?></td>
				<td data-label="Message"><?php 			if("Erreur" !== '' && strncmp($task['message'], "Erreur", strlen("Erreur")) === 0){
					$message=str_replace([CHR(10), CHR(13), "- "],["<br>", "<br>", "<br>"], $task['message']);
					//$message = htmlspecialchars($message, ENT_QUOTES);
echo "\n					<div class='form-popup' id='validateForm" . $task['id'] ."'>
							<div class='form-container' id='formProperty'>
								<h3>Message d'erreur</h3>
									<div class='form-popup-corps'>
										<p id='msgAlert" . $task['id'] . "'>" . $message ."</p>
										<button onclick='closeForm(". $task['id'] .")' class='buttonlink'>OK</button>
									</div>
							</div>
						</div>
						<div onclick='openFormWithId(".$task['id'].")' style=\"color:red;font-weight:bold;cursor:pointer\">ERROR  <img src=\"../ressources/message.png\" width='20px' height='20px'/>
						</div>";
				} else {
					echo $task['message'];
				}
			?></td>
			</tr>
<?php } ?>
		</table>
	</div>
	<br>
	<div style="margin : 0 auto; padding:3% 0; width: max-content;">
	<?php
		if($page>3){
			echo "<a href='HistoriqueMoisson.php?page=1' class='buttonpage'>&laquo;</a>\t";
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
				echo "<a href='HistoriqueMoisson.php?page=" . $i . "' class='buttonpage' style='background-color:#4b6a7c'>" . $i . "</a>\t";
			} else {
				echo "<a href='HistoriqueMoisson.php?page=" . $i . "' class='buttonpage'>" . $i . "</a>\t";
			}
		}
		if($page<$total_pages-2){
			echo "<a href='HistoriqueMoisson.php?page=" . $total_pages . "' class='buttonpage'>&raquo;</a>";
		}
	?>
	</div>

	<div id="page-mask"></div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/moissons/progress.js"></script>
	<script src="../js/histo-task-status.js"></script>
	<script src="../js/pop_up.js"></script>
</body>
</html>
