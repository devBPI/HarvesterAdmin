<!-- Il y a différentes div et boutons de menus car je n'ai encore décidé lesquels j'utiliserai -->
<html>
<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
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
include ('../Vue/Header.php');
require_once ("../Gateway.php");
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
	include ('../Controlleur/CmPlanificationMoisson.php');
}

?>

<div class="content">

	<div class="triple-column-container" style="height:50px">
		<div class="column">
			<a href="../Controlleur/PlanningMoisson.php" class="buttonlink">&laquo; Retour</a>
		</div>
	</div>
	<FORM  method="post" action="PlanificationMoisson.php" onsubmit="return confirm('Voulez vous vraiment ajouter cette planification ?');">
		<div class="cartouche-solo" style="width:auto;height:auto;padding:5%;">
			<div class="row">
				<div class="col-25">
					<label for="template">Nom de la configuration</label>
				</div>
				<div class="col-50">
					<select id="template" name="template">
						<option value="0">Choisissez une configuration</option>
						<?php
							$i = 0;
							foreach ($data as $combo_key => $var) {
								$i ++;
								if(isset($var['id']))
								{
									echo '<option value="' . $var['id'] . '"' . (($id_param == $var['id']) ? ' selected' : '') . '>' . $var['name'] . '</option>';
								}
								else
								{
									echo '<option value="' . $combo_key . '"' . (($id_param == $combo_key) ? ' selected' : '') . '>' . $var['name'] . '</option>';
								}
							}
						?>
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
					<!-- <option value="null">Heure</option> -->
					<?php include '../Vue/ComboBoxHeure.php'; ?>
				</select>
				<input type="submit" name="quot" value="Valider">
			</div>
			<div class="column">
				<h3>Hebdomadaire</h3>
				<select id="heureHebdo" name="heureHebdo">
					<?php include '../Vue/ComboBoxHeure.php'; ?>
				</select>
				<select id="jourHebdo" name="jourHebdo">
					<?php include '../Vue/ComboBoxJour.php'; ?>
				</select>
				<input type="submit" name="hebdo" value="Valider">
			</div>
			<div class="column">
				<h3>Mensuelle</h3>
				<!-- <input type="text" id="datepicker" size="30" readonly> -->
				<select id="heureMonth" name="heureMonth">
					<?php include '../Vue/ComboBoxHeure.php'; ?>
				</select>
				<select id="jourMonth" name="jourMonth">
					<?php include '../Vue/ComboBoxJour.php'; ?>
				</select>
				<select id="semaine" name="semaine">
					<?php include '../Vue/ComboBoxOccurence.php'; ?>
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
