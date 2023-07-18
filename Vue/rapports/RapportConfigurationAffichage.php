<html lang="fr">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/composants.css">
	<link rel="stylesheet" href="../css/accueilStyle.css">
	<link rel="stylesheet" href="../css/formStyle.css">
	<link rel="stylesheet" href="../css/reporting.css">
	<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css">
	<title>Paramétrage des rapports</title>

</head>

<body>
<?php
require_once("../Composant/ComboBox.php");
include_once("../Vue/common/Header.php");
if ($type == "processus") $page = "Processus";
else $page = "Donnees";
?>

<div class="content">
	<div class="button_top_div">
		<?php if ($type == "processus") { ?>
			<a href="../../Controlleur/Rapports.php?id=processus" class="buttonlink" style="float:none; height:16px">« Retour aux rapports sur les processus</a>
		<?php } else { ?>
			<a href="../../Controlleur/Rapports.php?id=donnees" class="buttonlink" style="float:none">« Retour aux rapports sur les métadonnées</a>
		<?php } ?>
		<?php if (isset($_GET["id"]) && $_GET["id"] != "" && isset($configuration) && $configuration!=null) { ?>
			<form action="../Controlleur/Rapports.php?id=<?= $type ?>" method="post" style="margin-bottom:0"
				  onsubmit="return confirm('Souhaitez-vous supprimer cette configuration de rapport ? Cette action est irréversible.');">
				<input type="hidden" id="input_delete_id" name="report_id" value="<?= $_GET["id"] ?>">
				<input type="submit" class="buttonlink buttonlinkdanger" name="submit_type" value="Supprimer le rapport"/>
			</form>
		<?php } ?>
	</div>
	<?php if (isset($configuration) && $configuration!=null) { ?>
	<!-- Section titre du rapport -->
	<div class="border_div param_content_div" style="padding:5px;">
		<table class="report_config_table" style="width: 100%">
			<tbody>
			<tr>
				<td style="width:200px">
					<label>Titre du rapport</label>
				</td>
				<td style="border-left:2px solid dimgrey">
					<p class="report_name_posting"><?= $configuration["name"] ?></p>
				</td>
				<td>
					<a href="../Controlleur/Rapports<?= $page ?>Edition.php?id=<?= $configuration["id"] ?>">
						<img src="../ressources/edit.png" alt="Modifier la configuration" style="width:30px;height:30px">
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
				<?php require_once("../Composant/RapportTreeComposant.php");
					RapportTreeComposant::tree_display($configuration["criterias_tree"], 0, ["data_type" => "METADATA", "tree_type" => "report", "for_what" => "viewonly"]);
				?>
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
					<div class="data_to_display_left"><?= $data["display_name"] ?></div>
				</div>
				<?php } ?>
			</div>
		</fieldset>
	</div>
	<div class="button_end_div">
		<form action="../../Controlleur/RapportsGeneration.php" method="post" style="margin-bottom: 0">
			<input type="hidden" id="input_generate_id" name="report_id" value="<?= $_GET["id"] ?>">
			<input type="hidden" id="input_generate_type" name="report_type" value="<?= $type ?>">
			<button id="input_generate" style="width:200px;border-bottom-left-radius:0;border-bottom-right-radius:0" type="submit" name="submit_value" value="generate">Visualiser le rapport</button>
		</form>
	</div>
	<div class="button_end_div">
		<button class="submit-button" style="width:200px;border-top:1px solid grey;border-top-left-radius:0;border-top-right-radius:0" onclick="generer_csv()">Générer un fichier CSV</button>
	</div>
	<?php } else { ?>
		<div class="avertissement">
			La configuration demandée n'a pas été trouvée. Veuillez vérifier l'URL.
		</div>
	<?php } ?>
</div>

<?php include "../Vue/common/Footer.php" ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">
    function generer_csv() {
        <?php if(isset($configuration) && $configuration!=null) { ?>
        window.open("../Composant/GenererCSV.php?id=<?= $_GET["id"] ?>&name=<?= str_replace(" ", "_", $configuration["name"]) ?>&report_type=<?= $type ?>",
			"mynewwindow", "pop-up, menubar=no, titlebar=no, height=200, width=275");
        <?php } ?>
    }
</script>
</body>