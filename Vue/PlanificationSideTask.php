<html lang="fr">
<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}

require '../Composant/ComboBox.php';
?>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/selectStyle.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<title>Planification d'une Tâche annexe</title>
</head>
<body id="haut">
<?php
$section = "Planifier une Tâche annexe";
include('../Vue/common/Header.php');
require_once("../PDO/Gateway.php");
Gateway::connection();
$codes = Gateway::getConfigCodes();
$i;

$array = array("PURGE", "CAROUSEL_FULLFILLMENT", "OPTIMIZE_DATABASE", "OPTIMIZE_INDEXES", "OPTIMIZE_FULL");  /* voir SideTaskName (java) pour les valeurs */
$array_formatee = [];
foreach ($array as $item) {
	$array_formatee[] = ["id" => $item, "name" => $item];
}

if (isset($_POST["now"]) || isset($_POST["quot"]) || isset($_POST["hebdo"]) || isset($_POST["month"])) {
	include('../Controlleur/CmPlanificationSideTask.php');
}

?>

<div class="content">
	<div class="triple-column-container" style="height:50px">
		<div>
			<a href="../Controlleur/PlanningTachesAnnexes.php" class="buttonlink">&laquo; Retour</a>
		</div>
	</div>
	<FORM method="post" action="PlanificationSideTask.php" onsubmit="return confirm('Voulez vous vraiment ajouter cette planification ?');">
		<div class="cartouche-solo" style="width:auto;height:auto;padding:5%;">
			<div class="row">
				<div class="col-25">
					<label for="taskname">Nom de la tâche</label>
				</div>
				<div class="col-50">
					<select id="taskname" name="taskname">
						<option value="0">Choisissez une tâche annexe</option>
						<?= ComboBox::makeComboBox($array_formatee); ?>
					</select>
				</div>
				<div class="col-25">
					<input type="submit" name="now" value="Démarrer maintenant">
				</div>
			</div>
			<div class="row" id="parametre">
				<div class="col-25">
					<label for="taskparameter">Paramètre</label>
				</div>
				<div class="col-75">
					<select id="taskparameter" name="taskparameter">
						<?php
							foreach ($codes as $select_code){
								echo '<option value="'.$select_code.'">'.$select_code.'</option>';
							}
						?>
					</select>
				</div>
			</div>
		</div>

		<div class="triple-column-container">
			<div class="column">
				<h3><label for="heureQuot">Quotidienne</label></h3>
				<select id="heureQuot" name="heureQuot">
					<?= Combobox::makeComboBoxHeure() ?>
				</select>
				<input type="submit" name="quot" value="Valider">
			</div>
			<div class="column">
				<h3><label for="heureHebdo">Hebdomadaire</label></h3>
				<select id="heureHebdo" name="heureHebdo">
					<?= Combobox::makeComboBoxHeure() ?>
				</select>
				<select aria-label="Jour de tâche hebdomadaire" id="jourHebdo" name="jourHebdo">
					<?= Combobox::makeComboBoxJour() ?>
				</select>
				<input type="submit" name="hebdo" value="Valider">
			</div>
			<div class="column">
				<h3><label for="heureMonth">Mensuelle</label></h3>
				<!-- <input type="text" id="datepicker" size="30" readonly> -->
				<select id="heureMonth" name="heureMonth">
					<?= Combobox::makeComboBoxHeure() ?>
				</select>
				<select aria-label="Jour de tâche mensuelle" id="jourMonth" name="jourMonth">
					<?= Combobox::makeComboBoxJour() ?>
				</select>
				<select aria-label="Semaine de tâche mensuelle" id="semaine" name="semaine">
					<?= Combobox::makeComboBoxSemaine() ?>
				</select>
				<input type="submit" name="month" value="Valider">
			</div>
		</div>
	</FORM>
</div>
<!-- <form method="post" action="PlanificationSideTask.php" onsubmit="return confirm('Voulez vous vraiment ajouter cette planification ?');">
		<div style="width: 100%; height: 7%">
			<div class="custom-select left" style="width: 30%">
				<select id="taskname" name="taskname" onChange="window.location=getTextArea.php" class="select-hide">
					<option value="0">Choisissez une tâche</option>
					<php
					
					$array = array("PURGE", "CAROUSEL_FULLFILLMENT", "OPTIMIZE_DATABASE", "OPTIMIZE_INDEXES", "OPTIMIZE_FULL");  /* voir SideTaskName (java) pour les valeurs */
					
					/* // OLD CODE FOR HARVEST TASK (example) 
					$i = 0;
					foreach ($data as $combo_key => $var) {
					    $i ++;
					    echo '<option value="' . $combo_key . '"' . (($id_param == $combo_key) ? ' selected' : '') . '>' . $var['name'] . '</option>';
					}
					*/
					
					$i = 0;
					foreach ($array as $combo_key => $var) {
					    $i ++;
					    echo '<option value="' . $var . '"' . (($id_param == $combo_key) ? ' selected' : '') . '>' . $var . '</option>';
					}
					
					?> 
				</select>
			</div>
			<div class="left" style="font: bold 16px/30px Georgia, serif;">
				 <p>Paramètre (facultatif selon tâche) : <input type="text" name="taskparameter" /></p>
			</div>
			<div class="left">
				<input class="button primairy-color round" type="submit" name="now"
					value="Lancer Maintenant" />
			</div>
			<div
				style="margin-left: 40%; margin-right: 0%; margin-top: 7%; left: 0; right: 0; position: absolute;">
			<php
if (isset($_POST["now"])) {
    include ('../Controlleur/CmPlanificationSideTask.php');
}
?>
		</div>
		</div>
		<div style="width: 100%; height: 25%; margin-top: 5%">
			<div style="width: 28%; margin-left: 4%" class="left">
				<h3>Quotidienne</h3>
				<br>
				<div class="custom-select" style="width: 55%; left: 0;">
					<select id="heureQuot" name="heureQuot" class="select-hide">
				   <php
    include '../Vue/combobox/ComboBoxHeure.php';
    ?>
				</select>
				</div>

				<input class="button primairy-color round" style="margin-top: 5%"
					type="submit" name="quot" value="Ajouter" />
				<div style="margin-left: 0%; bottom: 20%; position: absolute;">
				<php
    if (isset($_POST["quot"])) {
        include ('../Controlleur/CmPlanificationSideTask.php');
    }
    ?>
			</div>

			</div>

			<div style="width: 28%; margin-left: 4%" class="left">
				<h3>Hebdomadaire</h3>
				<br>
				<div class="custom-select" style="width: 55%; left: 0;">
					<select id="heureHebdo" name="heureHebdo" class="select-hide">
					<php
    include '../Vue/combobox/ComboBoxHeure.php';
    ?>
				</select>
				</div>
				<br>
				<br>
				<div class="custom-select" style="width: 55%; left: 0;">
					<select id="jourHebdo" name="jourHebdo" class="select-hide">
					<php
    include '../Vue/combobox/ComboBoxJour.php';
    ?>
				</select>
				</div>

				<input class="button primairy-color round" style="margin-top: 5%"
					type="submit" name="hebdo" value="Ajouter" />
				<div
					style="margin-left: 0%; margin-right: auto; bottom: 20%; position: absolute;">
				<php
    if (isset($_POST["hebdo"])) {
        include ('../Controlleur/CmPlanificationSideTask.php');
    }
    ?>
			</div>
			</div>

			<div style="width: 28%; margin-left: 4%" class="left">
				<h3>Mensuelle</h3>
				<br>
				<div class="custom-select" style="width: 55%; left: 0;">
					<select id="heureMonth" name="heureMonth" class="select-hide">
					<php
    include '../Vue/combobox/ComboBoxHeure.php';
    ?>
				</select>
				</div>
				<br>
				<br>
				<div class="custom-select" style="width: 55%; left: 0;">
					<select id="jourMonth" name="jourMonth" class="select-hide">
					<php
    include '../Vue/combobox/ComboBoxJour.php';
    ?>
				</select>
				</div>
				<br>
				<br>
				<div class="custom-select" style="width: 55%; left: 0;">
				<php
    include '../Vue/combobox/ComboBoxSemaine.php';
    ?>
			</div>
				<input class="button primairy-color round" style="margin-top: 5%"
					type="submit" name="month" value="Ajouter" />
				<div
					style="margin-left: 0%; margin-right: auto; bottom: 20%; position: absolute;">
				<php
    if (isset($_POST["month"])) {
        include ('../Controlleur/CmPlanificationSideTask.php');
    }
    ?>
			</div>
			</div>
		</div>
		<script src="../js/select-item.js"></script>

	</form> -->

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
	<script>
		$( function() {
			$( "#datepicker" ).datepicker({ showAnim: "slide" });
			$( "#datepicker" ).datepicker( "option", "showWeek", true );
		} );

		$('select[name="taskname"]').on('change',function(){
			if(this.value=="PURGE"){
				document.getElementById("parametre").style.display = 'block';
			} else {
				document.getElementById("parametre").style.display = 'none';
                document.getElementById("parametre").value = null;
			}
		});
 	</script>
	<script src="../js/toTop.js"></script>
</body>
<!-- Fin du body -->

</html>
