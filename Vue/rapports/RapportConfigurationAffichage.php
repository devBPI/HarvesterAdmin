<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />
	<link rel="stylesheet" href="../css/reporting.css" />
	<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css" />
	<title>Paramétrage des rapports</title>

</head>

<body>
<?php
require "../Composant/ComboBox.php";
include('../Vue/common/Header.php');
if ($type == "processus") $page = "Processus";
else $page = "Donnees";
?>

<div class="content">
	<div style="display:flex;justify-content: space-between;">
		<?php if ($type == "processus") { ?>
			<a href="../../Controlleur/Rapports.php?id=processus" class="buttonlink" style="float:none; height:16px">«
				Retour aux rapports sur les processus</a>
		<?php } else { ?>
			<a href="../../Controlleur/Rapports.php?id=donnees" class="buttonlink" style="float:none">« Retour aux
				rapports sur les métadonnées</a>
		<?php } ?>
		<?php if (isset($_GET["id"]) && $_GET["id"] != "") { ?>
			<form action="../Controlleur/Rapports.php?id=<?= $type ?>" method="post" style="margin-bottom:0"
				  onsubmit="return confirm('Souhaitez-vous supprimer cette configuration de rapport ? Cette action est irréversible.');">
				<input type="hidden" id="input_delete_id" name="report_id" value="<?= $_GET["id"] ?>">
				<input type="submit" class="buttonlink buttonlinkdanger" name="submit_type" value="Supprimer le rapport"/>
			</form>
		<?php } ?>
	</div>
	<!-- Section titre du rapport -->
	<div class="border_div param_content_div" style="padding:5px;">
		<table class="report_config_table" style="width: 100%">
			<tbody>
			<tr>
				<td width="200px">
					<label>Titre du rapport</label>
				</td>
				<td style="border-left:2px solid dimgrey">
					<p class="report_name_posting"><?= $configuration["name"] ?></p>
				</td>
				<td>
					<a href="../Controlleur/Rapports<?= $page ?>Edition.php?id=<?= $configuration["id"] ?>">
						<img src="../ressources/edit.png" alt="Modifier la configuration" width="30px" height="30px"/>
					</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<div class="border_div param_content_div">
		<fieldset class="param_fieldset">
			<legend>Critères</legend>
			<div id="criteres_rapport">
				<?php foreach ($configuration["criterias"] as $criteria) { ?>
				<div class="critere_rapport critere_rapport_posting">
					<div class="div_decorative_left"></div>
					<div class="criteria_left">
						<?= $criteria["default_name"] ?>
					</div>
					<div class="criteria_middle">
						<?= $criteria["label"] ?>
					</div>
					<div class="criteria_right">
						<?= $criteria["value_to_compare"] ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</fieldset>
	</div>

	<!-- Section données du rapport -->
	<div class="border_div param_content_div">
		<fieldset class="param_fieldset">
			<legend>Données affichées</legend>
			<div id="donnees_affichees">
				<?php foreach ($configuration["data_to_display"] as $data) { ?>
				<div class="donnee_affichee donnee_affichee_posting">
					<div class="div_decorative_left"></div>
					<div class="data_to_display_left">
						<?= $data["display_name"] ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</fieldset>
	</div>
	<form action="../../Controlleur/RapportsGeneration.php" method="post">
		<input type="hidden" id="input_generate_id" name="report_id" value="<?= $_GET["id"] ?>">
		<input type="hidden" id="inpute_generate_type" name="report_type" value="<?= $type ?>">
		<button id="input_generate" type="submit" name="submit_value" value="generate">Générer le rapport
		</button>
	</form>
</div>

<?php include "../Vue/common/Footer.php" ?>

</body>