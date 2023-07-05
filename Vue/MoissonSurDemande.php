<html lang="fr">
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
	<title>Moisson sur demande</title>
</head>

<body>
<?php
$section = "Moisson sur Demande";
include ('../Vue/common/Header.php');
require_once ("../PDO/Gateway.php");
Gateway::connection();
$datawithoutfile = Gateway::getConfigurationsWithoutFileToUpload();
$datawithfiles = Gateway::getConfigurationsWithFilesToUpload();
$datawithfile = Gateway::getConfigurationsWithFileToUpload();

// Cette boucle permet de récupérer les fichiers à upload en inversant les arrays :
// Au lieu d'avoir dans ce sens : Attribut -> valeurs de l'attribut pour chaque fichier
// On a : Chaque fichier -> valeur de l'attribut
// if($_FILES['input_files']['name'][0]!=''){
//     foreach($_FILES['input_files'] as $key => $attribute){
//         foreach($_FILES['input_files'][$key] as $j => $value){
//             $files_array[$j][$key] = $value;
//         }
//     }
// 	if(isset($_SESSION['files_upload_session'])){
// 		foreach($files_array as $f_array){
// 			array_push($_SESSION['files_upload_session'],$f_array);
// 		}
// 	} else {
// 		$_SESSION['files_upload_session'] = $files_array;
// 	}
// } else {
// 	if(isset($_SESSION['files_upload_session'])){
// 		$files_array = $_SESSION['files_upload_session'];
// 	}
// }
// if($_POST["delete"]!=""){
//     array_splice($files_array,$_POST["delete"],1);
//     $_SESSION['files_upload_session']=$files_array;
// }
?>


<div class="content">
	
	<form method="post">
		<div class="cartouche-solo" style="width:auto;height:auto;padding:5%;">
			<h3 style="margin:0 auto;">Lancement sans fichier</h3>
			<div class="row">
				<div class="col-50">
				<select id="configuration-select-whithout-file" name="configuration-select-whithout-file" onChange="disableMsgAborted()" required>
    					<option value="" disabled selected>Choisissez une configuration</option>
    					<?php
    					$i = 0;
    					foreach ($datawithoutfile as $combo_key => $var) {
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
				<div class="col-30">
					<div style="text-align:center;margin-left:15px;">
                    	<input type="checkbox" id="type-moisson-without-file" name="type-moisson-without-file">
                    	<label for="type-moisson-without-file">Import et Indexation uniquement</label>
                    </div>
				</div>
				<div class="col-20">
					<input type="submit" name="launch-without-file-button" onclick="return formSubmit()" value="Démarrer maintenant">
				</div>
				<br/>
			</div>
		</div>
	</form>
	<div class="avertissement" id="msgAborted"></div>

	<form id="formFiles" method="post" enctype="multipart/form-data">
		<input id="file-to-upload" name="file-to-upload[]" type="hidden" value=""/>
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000">  <!-- On limite le fichier à 10Mo -->
		<!-- ATTENTION : la taille max du fichier doit etre bien compatible avec la valeur de upload_max_filesize (et post_max_size) dans php.ini -->
		<div class="cartouche-solo" style="width:auto;height:auto;padding:2% 5%;">
			<h3>Lancement avec fichier(s)</h3>
			<div class="triple-column-container">
				<div class="column" style="height:80px">
					<label for="input_files_0" class="custom-file-upload" style="float:left">
					<input class='file-input' type="file" accept=".txt,.csv" id="input_files_0" name="input_files[]" style="display:none" onchange="newInput(this)" onclick="appendInput(this)" multiple />
					Ajouter des fichiers</label>
				</div>
				<div class="column" style="height:80px">
					<select id="configuration-select-whith-files" name="configuration-select-whith-files" onchange='changeCSV(this)' required>
						<option value="" disabled selected>Choisissez une configuration</option>
						<?php
						$i = 0;
						echo "<optgroup label=\"Multiple CSV\">";
						foreach ($datawithfiles as $combo_key => $var) {
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
						echo "</optgroup><optgroup label=\"Simple CSV\">";
						foreach ($datawithfile as $combo_key => $var) {
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
						echo "</optgroup>";
						
						?> 
					</select>
				</div>
				<div class="column" style="height:80px">
					<input type="submit" name="launch-with-file-button" value="Démarrer maintenant">
				</div>
			</div>
		</div>
		<input id="formIgnoreValues" name="formIgnoreValues" type="hidden" value=""/>
		<input id="formTypeCSV" name="formTypeCSV" type="hidden" value=""/>
	</form>
	<div id="divCSV" class="avertissement"></div>

	<table class="table-planning" id="moissonTable" style="margin-bottom:50px">
        <thead>
            <tr><th scope="col" style ="width:65%">Nom du fichier</th><th scope="col" style="width:30%">Taille</th><th scope="col" style="width:5%"></th></tr>
        </thead>
		<tbody id="displaytbody">
		</tbody>
    </table>
</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script>

		function disableMsgAborted() {
            document.getElementById("msgAborted").innerText = "";
		}

		// Valide la soumisson du formulaire si type-moisson-without-file est coché
		function formSubmit() {
            if (document.getElementById('type-moisson-without-file').checked) {
                if (confirm("Vous souhaitez effectuer une moisson sans grab. Confirmer ?")) {
                    return true;
                } else {
                    document.getElementById("msgAborted").innerHTML = "La moisson ne sera pas réalisée.";
                    return false;
                }
            } else return true;
		}

		function deleteRow(i){
			document.getElementById('displaytbody').rows[i].style.display = "none";
			document.getElementById('formIgnoreValues').value += i;
		}

		var nbRow = 0;
		function newInput(obj) {
			var domArray = obj.files;
			console.log(domArray);
			for (var i = 0; i < domArray.length; i++) {
				var tbodyRef = document.getElementById('displaytbody');
				var row = tbodyRef.insertRow(-1);
				var cell1 = row.insertCell(0);
				var cell2 = row.insertCell(1);
				var cell3 = row.insertCell(2);
				cell1.innerHTML = domArray[i].name;
				cell2.innerHTML = domArray[i].size;
				cell3.innerHTML = "<div class=\"button-hover\" onclick=\"deleteRow(" + nbRow + ")\" id=\"" + nbRow + "\" style=\"cursor:pointer\"><img src=\"../ressources/cross.png\" width='20px' height='20px'/></div>"
				$(obj).class = 'file-input-used';
				nbRow++;
			}
		}

		var nbButton = 1;
		function appendInput(obj) {	
			$(obj).hide();
			$(obj).parent().hide();
			$(obj).parent().parent().append("<label for='input_files_" + nbButton + "' class='custom-file-upload' style='float:left'><input class='file-input' type='file' accept='.txt,.csv' id='input_files_" + nbButton + "' name='input_files[]' style='display:none' onchange='newInput(this)' onclick='appendInput(this)' multiple />Ajouter des fichiers</label>");
			nbButton++;
		}

		function changeCSV(obj) {
			console.log(obj.options[obj.selectedIndex].parentElement.label);
			var groupofselected = obj.options[obj.selectedIndex].parentElement.label;
			var msg = document.getElementById('divCSV');
			if(groupofselected == 'Simple CSV'){
				msg.innerHTML = "/!\\ Seul le premier fichier sera pris en compte. /!\\";
				document.getElementById('formTypeCSV').value = 'simple';
			} else {
				msg.innerHTML = "";
				document.getElementById('formTypeCSV').value = 'multiple';
			}
		}
	</script>
</body>
<!-- Fin du body -->

</html>
