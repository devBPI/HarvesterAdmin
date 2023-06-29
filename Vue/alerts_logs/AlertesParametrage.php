<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/selectStyle.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" />
	<link rel="stylesheet" href="../../css/alerts_logs/alertes_homepage.css" />

	<title><?= $section ?></title>
</head>

<?php
include("../Vue/common/Header.php");
?>

<body>
<div class="content">
	<div class="triple-column-container">
		<div class="column-text-left">
			<p class="alertes_homepage_title">État des tâches de surveillance</p>
			<div class="alertes_homepage_div_ext">
				<div class="alertes_homepage_div_int">
					<table class="table-config table_home_alertes_enabled">
						<?php foreach($alert_jobs as $alert_job) { ?>

						<tr>
							<td><?= $alert_job["name"] ?></td>
							<td class="<?= $alert_job["is_enabled"] == "t"?"activated":"deactivated" ?>"><?= $alert_job["is_enabled"]=="t"?"Activée":"Désactivée" ?></td>
						</tr>
						<?php } ?>

					</table>
				</div>
				<div class="alertes_homepage_div_button">
					<a class="submit-button" href="../Controlleur/AlertesActivation.php">Activer / Désactiver une tâche</a>
				</div>
			</div>
		</div>

		<div class="column-text-left">
			<p class="alertes_homepage_title">Seuils et pourcentages des alertes</p>
			<div class="alertes_homepage_div_ext">
				<div class="alertes_homepage_div_int">
					<table class="table-config table_home_threshold">
					<?php foreach($alert_parameters as $alert_parameter) { ?>

						<tr>
							<td><?= $alert_parameter["name"] ?></td>
							<td style="width:50px;padding-right:5px"><?= $alert_parameter["value"] ?></td>
						</tr>
						<?php } ?>

					</table>
					</div>
					<div class="alertes_homepage_div_button">
						<a class="submit-button" href="../Controlleur/AlertesReglage.php">Éditer les seuils et pourcentages</a>
					</div>
			</div>
		</div>

		<div class="column-text-left">
			<p class="alertes_homepage_title">Liste de diffusion</p>
			<div class="alertes_homepage_div_ext">
				<div class="alertes_homepage_div_int">
					<table class="table-config table_home_recipients">
						<tbody>
						<?php foreach($mailing_list as $recipient) { ?>

							<tr>
								<td><?= $recipient["mail"] ?></td>
								<td class="<?= $recipient["is_enabled"] == "t"?"activated":"deactivated" ?>"><?= $recipient["is_enabled"]=="t"?"Activée":"Désactivée" ?></td>
							</tr>
						<?php } ?>

						</tbody>
					</table>
				</div>
				<div class="alertes_homepage_div_button">
					<a class="submit-button" href="../Controlleur/AlertesMailingList">Gérer la liste de diffusion</a>
				</div>
			</div>
		</div>

	</div>
</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">
</script>

</html>