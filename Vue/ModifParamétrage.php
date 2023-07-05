<html lang="fr">
<?php
session_start();
if (! empty($_POST['envoyer'])) {
	$_SESSION['new_name'] = $_POST['mapping_name'];
	header('Location: ../Vue/ValidParamétrage.php?table='.$_GET['table'].'&id='.$_GET['id']);
	exit();
}
if (isset($_GET['param'])) {
	$id_config = $_GET['param'];
}
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
	<link rel="stylesheet" href="../css/formStyle.css" />
	<title>Paramétrage : Modification</title>
</head>


<body id="haut">
<?php
$table = $_GET['table'];
$id = $_GET['id'];
$text = @str_replace("'", "''", $_POST['textArea']);
$name = @str_replace("'", "''", $_POST['name']);
require_once("../PDO/Gateway.php");
Gateway::connection();


if(isset($table,$id))
{

	if($table == "mapping")
	{
		$conf = Gateway::getMappingWithId($id);
		$entities = Gateway::getEntities();
		$i=0;
		foreach($entities as $entity){
			$prop_entities[$i] = Gateway::getNotice($entity);
			$i++;
		}
	}
	else
	{
		$conf = Gateway::getConfiguration($table,$id); // comme avant
	}

	$def = $conf['definition'];
	if(!isset($_SESSION['new_name'])){
		$nom = $conf['name'];
	} else {
		$nom = $_SESSION['new_name'];
	}
}
$title = null;
if ($table == "exclusion") {
	$title = "un Filtre";
} else if ($table == "translation") {
	$title = "une Traduction";
} else if ($table == "mapping") {
	$title = "un Mapping";
}
$section = "Modification d'" . $title;
include ('../Vue/common/Header.php');
//include 'affichageParamétrage.php';

if(empty($_SESSION['session_properties'])){
	$properties_list = explode(PHP_EOL,$def);
	$_SESSION['session_properties']=$properties_list;
} else {
	//$properties_list = $_SESSION['session_properties'];
	$properties_list = explode("\n",$def); // Pour moi 2006
}
if((isset($_POST["list_exist_entity_all"]) && isset($_POST["list_exist_propriety_all"]))&&(isset($_POST["textNom"]))){
	$entity_number = $_POST["list_exist_entity_all"]-1;
	$notice_number = $_POST["list_exist_propriety_all"]-1;
	$addedprop = $prop_entities[$entity_number][$notice_number];
	$addedcontent = $_POST["textNom"];
	if($_POST["insert"]=="auto-start"){
		array_unshift($properties_list,$addedprop." : ".$addedcontent);
	} else if($_POST["insert"]=="auto-end"){
		array_push($properties_list,$addedprop." : ".$addedcontent);
	} else if($_POST["insert"]=="custom"){
		$val = $addedprop." : ".$addedcontent;
		array_splice($properties_list, $_POST["custom_insert"], 0, array($val));
	}
	$_SESSION['session_properties']=$properties_list;
}
if((isset($_POST["property"]) && isset($_POST["content"])) && (isset($_POST["id"])&&($_POST["swapTarget"]==""))){
	$properties_list[$_POST["id"]] = $_POST["property"].":".$_POST["content"];
	$_SESSION['session_properties']=$properties_list;
}
if(isset($_POST["delete"]) && $_POST["delete"]!=""){
	array_splice($properties_list,$_POST["delete"],1);
	$_SESSION['session_properties']=$properties_list;
}
if(isset($_POST["swapTarget"]) && $_POST["swapTarget"]!=""){
	$property_swap = $properties_list[$_POST["id"]];
	$properties_list[$_POST["id"]] = $properties_list[$_POST["swapTarget"]];
	$properties_list[$_POST["swapTarget"]] = $property_swap;
	$_SESSION['session_properties']=$properties_list;
}
foreach($properties_list as $property){
	if (strcmp($property,'')!=1 && $property != null) {
		$exploded_property = explode(":", $property, 2);
		if(isset($exploded_property[0]) && isset($exploded_property[1])) {
			//var_dump($property . " --> [" . $exploded_property[0] . "," . $exploded_property[1] . "]");
			$properties_name[] = $exploded_property[0];
			$properties_name_preview[] = $exploded_property[0];
			$properties_content[] = $exploded_property[1];
			$properties_content_preview[] = $exploded_property[1];
			$properties_list_preview[] = $property;
		}
	}
}

//var_dump($properties_name, $properties_content);
?>

<div class="content">
	<div class="button_top_div_with_margin">
		<a href="../Controlleur/Mapping.php" class="buttonlink">&laquo; Retour aux mappings</a>
	</div>
	<div class="cartouche-solo" style="width:100%;height:auto">
		<form action="" method="post" style="padding:5%">
			<div class="row">
				<div class="col-25">
					<label for="mapping">Nom du mapping</label>
				</div>
				<div class="col-50">
					<input type="text" id="mapping_name" name="mapping_name" placeholder="Nouveau nom du mapping..." value="<?php echo $nom ?>">
				</div>
				<div class="col-25">
					<input type="submit" name="envoyer" id="envoyer" style="background-color:#4bb947" value="Valider les modifications">
				</div>
			</div>
		</form>
	</div>
	<div class="cartouche-solo" style="width:100%;height:auto">
		<form action="" method="post" style="padding:5%" name="formAdd" id="formAdd">
			<div class="row">
				<div class="col-10">
					<label for="mapping">Ajout</label>
				</div>
				<div class="col-25">
					<select id="list_exist_entity_all" name="list_exist_entity_all">
						<option value="0">Choisissez une entité</option>
						<?php
						$i=0;
						foreach ($entities as $entity) {
							$i++;
							echo '<option value="' . $i . '">' . $entity . '</option>';
						}
						?>
					</select>
				</div>
				<div class="col-25">
					<select id="list_exist_propriety_all" name="list_exist_propriety_all" placeholder="Choississez une propriété à ajouter" required>
						<option value="">Choisissez une propriété à ajouter</option>
					</select>
				</div>
				<div class="col-30">
					<input type="text" id="contentprop" name="textNom" placeholder="Contenu de la propriété..." required>
				</div>
			</div>
			<div class="row">
				<div class="col-10">
					<label for="mapping">Position</label>
				</div>
				<div class="col-20">
					<input type="radio" id="auto-start" name="insert" value="auto-start" required>
					<label for="auto-start">au début</label>
				</div>
				<div class="col-20">
					<input type="radio" id="auto-end" name="insert" value="auto-end">
					<label for="auto-end">à la fin</label>
				</div>
				<div class="col-20">
					<input type="radio" id="custom" name="insert" value="custom">
					<label for="auto-end">personnalisé</label>
				</div>
				<div class="col-20">
					<input type="number" id="custom_insert" name="custom_insert" placeholder="numéro de ligne..." min="0" max="<?php echo count($properties_content) ?>" readonly/>
				</div>
				<div class="col-10">
					<input type="submit" name="submit" style="background-color:#4bb947" value="Ajouter">
				</div>
			</div>
		</form>
	</div>
	<table class="table-mapping" id="mapTable">
		<thead>
		<tr><th scope="col" style ="width:5%"></th><th scope="col" style ="width:30%">Propriété</th><th scope="col" style="width:60%">Valeur(s)</th><th scope="col" style="width:5%"></th><th scope="col" style="width:5%"></th></tr>
		</thead>
		<?php
		$i=0;
		foreach($properties_list as $property){
			$exploded_property = explode(":",$property,2);
			if(!empty($exploded_property[1])){
				echo "<tr>
                            <td scope=\"row\" data-label=\"Ligne\">".$i."</td>
                            <td data-label=\"Propriété\">".$exploded_property[0]."</td>
                            <td data-label=\"Valeur(s)\">".$exploded_property[1]."</td>
                            <td>
                                <div class=\"button-hover\" onclick=\"openForm(".$i.")\" id=\"".$i."\" style=\"cursor:pointer\"><img src=\"../ressources/edit.png\" width='20px' height='20px'/></div>
                            </td>
                            <td>
                                <div class=\"button-hover\" id=\"up\" onclick=\"moveRowUp(".$i.")\" style=\"cursor:pointer\">&#9650;</div>
                                <div class=\"button-hover\" id=\"down\" onclick=\"moveRowDown(".$i.")\" style=\"cursor:pointer\">&#9660;</div>
                            </td>
                        </tr>";
				$i++;
			}
		}
		?>
	</table>
</div>
<div id="page-mask"></div>
<div class="form-popup" id="modifyForm">
	<form action="" method="post" class="form-container" id="formProperty">
		<div class='form-container' id='formProperty'>
			<h3>Modification de la propriété</h3>
			<button type="button" class="cross-close" onclick="closeForm()">&times;</button>
			<div class="form-popup-corps">
				<div class="row">
					<div class="col-50">
						<label for="property"><b>Propriété</b></label>
					</div>
					<div class="col-50">
						<label for="content"><b>Contenu de la propriété</b></label>
					</div>
				</div>
				<div class="row">
					<div class="col-50">
						<input type="text" name="property" value="" id="formPropriety" readonly>
					</div>
					<div class="col-50">
						<input type="text" placeholder="contenu..." id="formContent" name="content" required>
					</div>
				</div>
				<div class="button_end_div_with_margin">
					<button type="submit" class="btn delete" onclick="deleteRow()">Supprimer</button>
					<button type="submit" class="btn">Valider</button>
				</div>
				<input id="formID" name="id" type="hidden" value=""/>
				<input id="formDelete" name="delete" type="hidden" value=""/>
				<input id="formSwap" name="swapTarget" type="hidden" value=""/>
			</div>
		</div>
	</form>
</div>

<!-- Ajout des scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script src="../js/mapping.js"></script>
<script>
    function openForm(i) {
		<?php
		$js_name = json_encode($properties_name);
		echo "var jsarray_name = ". $js_name . ";\n";
		$js_content = json_encode($properties_content);
		echo "var jsarray_content = ". $js_content . ";\n";
		?>
        document.getElementById("formPropriety").value = jsarray_name[i];
        document.getElementById("formContent").value= jsarray_content[i];
        document.getElementById("formID").value= i;
        document.getElementById("modifyForm").style.display = "block";
        document.getElementById("page-mask").style.display = "block";
    }

    $('select[name="list_exist_entity_all"]').on('change',function(){
        var selectIndex=$('select option:selected').val();
        if(selectIndex==0){
            $('#list_exist_propriety_all').empty();
            $('#list_exist_propriety_all').append("<option value=\"\">Choisissez une propriété à ajouter</option>");
        } else {
            var options = <?php echo json_encode($prop_entities); ?>;
            $('#list_exist_propriety_all').empty();
            $('#list_exist_propriety_all').append("<option value=\"\">Choisissez une propriété à ajouter</option>");
            for (i = 0; i < options[selectIndex-1].length; i++) {
                $('#list_exist_propriety_all').append("<option value=\"" + (i+1) + "\">" + options[selectIndex-1][i] + "</value>");

            }
        }
    });
</script>
</body>
<!-- Fin du body -->

</html>