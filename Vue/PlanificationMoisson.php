<!-- Il y a différentes div et boutons de menus car je n'ai encore décidé lesquels j'utiliserai -->
<html>
<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}

require '../Composant/ComboBox.php';
?>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<link rel="stylesheet" href="../css/selectStyle.css" />
<title>Planification d'une Moisson</title>
</head>
<body name="haut" id="haut">
<?php
$section = "Planifier une Moisson";
include('../Vue/common/Header.php');
require_once("../PDO/Gateway.php");
Gateway::connection();
$data = Gateway::getHarvestConfigurationP();
$i;
for($i=0;$i<count($data);$i++)
{
	if($data[$i]['moissons']!='0')
	{
		$data[$i]['name']=$data[$i]['name']." - déjà planifiée";
	}
}

if (isset($_POST["now"]) || isset($_POST["quot"]) || isset($_POST["hebdo"]) || isset($_POST["month"])) {
	include('../Controlleur/CmPlanificationMoisson.php');
}

?>

<div class="content">

	<div class="triple-column-container" style="height:50px">
		<div>
			<a href="../Controlleur/PlanningMoisson.php" class="buttonlink">&laquo; Retour</a>
		</div>
	</div>
	<FORM method="post" action="PlanificationMoisson.php" onsubmit="return confirm('Voulez vous vraiment ajouter cette planification ?');">
		<div class="cartouche-solo" style="width:auto;height:auto;padding:5%;">
			<div class="row">
				<div class="col-25">
					<label for="template">Nom de la configuration</label>
				</div>
				<div class="col-50">
					<select id="template" name="template">
						<option value="0">Choisissez une configuration</option>
						<?= ComboBox::makeComboBox($data); ?>
					</select>
				</div>
				<div class="col-25">
					<input type="submit" name="now" value="Moisson rapide">
				</div>
			</div>
		</div>

		<div class="triple-column-container">
			<div class="column">
				<h3>Quotidienne</h3>
				<select id="heureQuot" name="heureQuot">
					<?= Combobox::makeComboBoxHeure() ?>
				</select>
				<input type="submit" name="quot" value="Valider">
			</div>
			<div class="column">
				<h3>Hebdomadaire</h3>
				<select id="heureHebdo" name="heureHebdo">
					<?= Combobox::makeComboBoxHeure() ?>
				</select>
				<select id="jourHebdo" name="jourHebdo">
					<?= Combobox::makeComboBoxJour() ?>
				</select>
				<input type="submit" name="hebdo" value="Valider">
			</div>
			<div class="column">
				<h3>Mensuelle</h3>
				<!-- <input type="text" id="datepicker" size="30" readonly> -->
				<select id="heureMonth" name="heureMonth">
					<?= Combobox::makeComboBoxHeure() ?>
				</select>
				<select id="jourMonth" name="jourMonth">
					<?= Combobox::makeComboBoxJour() ?>
				</select>
				<select id="semaine" name="semaine">
					<?= Combobox::makeComboBoxOccurence() ?>
				</select>
				<input type="submit" name="month" value="Valider">
			</div>
		</div>
	</FORM>
</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
	<script>
		$( function() {
			$( "#datepicker" ).datepicker({ showAnim: "slide" });
			$( "#datepicker" ).datepicker( "option", "showWeek", true );
		} );
 	</script>
	<script src="../js/toTop.js"></script>
</body>
<!-- Fin du body -->

</html>
