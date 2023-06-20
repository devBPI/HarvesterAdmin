<?php

//ini_set("display_errors", 0);
//error_reporting(0);

include("../Composant/ErrorReportingConfig.php");

date_default_timezone_set('Europe/Paris');

$id = $_GET['param'] ?? "";

$alerts = Gateway::getAlertsForCartridge($id);

$i = 1;

if (!$alerts) { ?>
	Aucune alerte dans l'historique.
<?php
} else { ?>
	<table class="table-backoffice">
		<tr>
			<th style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:5px">Date</th>
			<th style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:5px">Niveau</th>
			<th style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:5px">Catégorie</th>
			<th style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:5px">Message</th>
		</tr>
		<?php foreach ($alerts as $alert) { ?>
		<tr>
			<td><?= date("d-m-Y", strtotime($alert["creation_time"])) ?></td>
			<td style="color:<?= $alert['level']=='URGENT'?"red":"#fb7d00"?>;font-weight:bold"><?= $alert["level"] ?></td>
			<td><?= $alert["category"] ?></td>
			<td><img id="alertOpener<?= $i ?>" alt="Message de l'alerte" src="../../ressources/message.png" style="cursor:pointer" width="20px" height="20px">
				<div style="display:none" id="alertPopUp<?= $i++ ?>" title="Détails de l'alerte">
					<p><?= $alert["message"] ?></p>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>

<?php
}
?>

