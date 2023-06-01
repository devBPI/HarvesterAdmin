<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

$conf=array();
$id = isset($_GET["id"]) ? $_GET["id"] : $_GET["modify"];

if(isset($_GET['modify']) && !empty($_POST)) {
	unset($_POST['set']);
	$donnees=array();
	$nb=-1;
	$nb_conf=-1;
	foreach($_POST as $key => $value)
	{
		if(preg_match('/(entity)/',$key)) {
			$donnees[++$nb]["entity"] = $value;
		}
		if(preg_match('/(property)/',$key)) {
			if ($nb > -1) {
				if (!isset($donnees[$nb]["case"])) $donnees[$nb]["case"] = false;
				if (!isset($donnees[$nb]["trim"])) $donnees[$nb]["trim"] = false;
			}
			$donnees[$nb]["property"] = $value;
		}
		if(preg_match('/(set)/',$key)) {
			$donnees[$nb]["set"] = $value;
		}
		if(preg_match('/(case)/',$key)) {
			$donnees[$nb]["case"]=true;
		}
		if(preg_match('/(trim)/',$key)) {
			$donnees[$nb]["trim"]=true;
		}
	}
	if (!isset($donnees[$nb]["case"])) { $donnees[$nb]["case"] = false; }
	if (!isset($donnees[$nb]["trim"])) { $donnees[$nb]["trim"] = false; }

	// Suppression des incohérences (critère : entity est null)
	for ($i = 0; $i < count($donnees); $i++) {
		if ($donnees[$i]["entity"] == "")
			unset($donnees[$i]);
	}

	// Remplissage du tableau $conf (si erreur, contient les données qui viennent d'être saisies)
	foreach ($donnees as $donnee) {
		$conf[] = [
			"id" => $donnee["set"],
			"property" => $donnee["property"],
			"entity" => $donnee["entity"],
			"case" => $donnee["case"]? "t" : "f",
			"trim" => $donnee["trim"]? "t" : "f"
		];
	}

	$array_error = Gateway::updateTranslationConfiguration($id,$donnees);
}

// Si on n'a pas effectué de modifications
if (!isset($array_error) || (count($array_error) == 0)) {
	$conf = Gateway::getSetByConf($id);
}

$entities = Gateway::getEntities();
$data = Gateway::getRulesSet();
$name = Gateway::getHarvestConfigurationDifferential($id)[0]['name'];
$section = "Configuration et règles de traduction";
include "../Vue/traduction/TraductionConfiguration.php";
?>


