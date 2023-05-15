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
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<title>Tâche Annexe sur Demande</title>
</head>
<body name="haut" id="haut">

<?php

 $section = "Tâche Annexe sur Demande";
 include ('../Vue/Header.php');
 require_once ("../PDO/Gateway.php");
 Gateway::connection();
?>

<div class="content">
	<FORM  method="post" action="TacheAnnexeSurDemande.php" onsubmit="return confirm('Voulez vous vraiment lancer maintenant la tache annexe ?');">
		<div class="cartouche-solo" style="width:auto;height:auto;padding:5%;">
			<div class="row">
				<div class="col-25">
					<label for="taskname">Nom de la tâche</label>
				</div>
				<div class="col-50">
					<select id="taskname" name="taskname">
						<option value="0">Choisissez une tâche annexe</option>
						<?php
							$array = array("PURGE", "CAROUSEL_FULLFILLMENT", "BIBLIO_DATA_FULLFILLMENT", "OPTIMIZE_DATABASE", "OPTIMIZE_INDEXES", "OPTIMIZE_FULL");  /* voir SideTaskName (java) pour les valeurs */
							
							$i = 0;
							foreach ($array as $combo_key => $var) {
								$i ++;
								if($i == 1){
									// Par defaut la premiere option renseignee est selectionnee
									echo '<option value="' . $var . '"' . ' selected>' . $var . '</option>';
								}else{
									echo '<option value="' . $var . '"' . '>' . $var . '</option>';
								}
								
							}
						?>
					</select>
				</div>
				<div class="col-25">
					<input type="submit" name="launch" value="Démarrer maintenant">
				</div>
			</div>
			<div class="row" id="parametre">
				<div class="col-25">
					<label for="taskparameter">Paramètre</label>
				</div>
				<div class="col-50">
					<select id="taskparameter" name="taskparameter">
						<?php

								$purgeOptionList = $codes;

								foreach ($purgeOptionList as $option_item){
									echo '<option value="'.$option_item.'">'.$option_item.'</option>';
								}
							
						?>
					</select>
				</div>
			</div>
		</div>
	</FORM>
</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script>
		$('select[name="taskname"]').on('change',function(){
			if(this.value=="PURGE"){
				// Remplissage des items specifiques a la tache

				var taskparameterElt = document.getElementById("taskparameter");

				// Remove all options
				var i = 0;
				for(i = (taskparameterElt.options.length - 1); i >= 0; i--) {
					taskparameterElt.remove(i);
				}

				var optionList = [];


				<?php
						$purgeOptionList = $codes;
						
						foreach ($purgeOptionList as $option_item){
							echo 'optionList.push("'.$option_item.'");';
						}		
							
				?>
				
				i = 0;
				while (i < optionList.length) {
					var value = optionList[i];
					const opt = document.createElement("option");
					opt.value = "" + value;
					opt.text = "" + value;

					taskparameterElt.add(opt, null);

    				i++;
				}
			

				document.getElementById("taskparameter").style.display = 'block';
				document.getElementById("parametre").style.display = 'block';
			} else if (this.value=="BIBLIO_DATA_FULLFILLMENT"){
				// Remplissage des items specifiques a la tache

				var taskparameterElt = document.getElementById("taskparameter");

				// Remove all options
				var i = 0;
				for(i = (taskparameterElt.options.length - 1); i >= 0; i--) {
					taskparameterElt.remove(i);
				}

				var optionList = ['100','200','500','1000','2000','5000','10000','20000','50000'];
				
				i = 0;
				while (i < optionList.length) {
					var value = optionList[i];
					const opt = document.createElement("option");
					opt.value = "" + value;
					opt.text = "" + value;

					taskparameterElt.add(opt, null);

    				i++;
				}
			

				document.getElementById("taskparameter").style.display = 'block';
				document.getElementById("parametre").style.display = 'block';
			} else{
				document.getElementById("taskparameter").style.display = 'none';
				document.getElementById("parametre").style.display = 'none';
			}
		});
	</script>
</body>
<!-- Fin du body -->

</html>
