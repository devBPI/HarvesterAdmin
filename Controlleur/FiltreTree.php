<?php

function makeTree(&$donnees_post) {
	$nb_children = 0;
	$arbre = [];
	if ($donnees_post) {
		if (preg_match("/(operator_group_)/", key($donnees_post))) {
			$operator = array_shift($donnees_post);
			if (preg_match("/(nb_children_operator_group)/", key($donnees_post))) {
				$nb_children = array_shift($donnees_post);
			}
			if ($nb_children > 0) {
				$keys = array_keys($donnees_post);
				for ($i = 0; $i < 2; $i++) {
					if (preg_match("/(entity)/", $keys[$i])) {
						$arbre[$i]["code"] = $donnees_post[$keys[$i]];
						$arbre[$i]["operator"] = "OPERATION";
						unset($donnees_post[$keys[$i]]);
					} else {
						$arbre[$i] = makeTree($donnees_post);
					}
				}
			}
			$arbre["operator"] = $operator;
		} else if(preg_match("/(entity_)/", key($donnees_post))) {
			$arbre["code"] = array_shift($donnees_post);
			$arbre["operator"] = "OPERATION";
		}
	}
	return $arbre;
}

function recursiveCount($criterias_tree, $cpt_groups=1): array
{
	$cpt_criterias = 0;
	if ($cpt_groups < 1) $cpt_groups = 0;
	foreach ($criterias_tree as $key => $donnee) {
		if (is_array($donnee)) {
			if (isset($donnee["operator"]) && $donnee["operator"] != null) {
				$cpt_groups++;
				$cpt_groups += recursiveCount($donnee, 0)[1];
				$cpt_criterias += recursiveCount($donnee, 0)[0];
			} else {
				$cpt_criterias++;
			}
		}
	}
	return array($cpt_criterias, $cpt_groups);
}

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
require_once ("../PDO/Gateway.php");

$success=false;

$ruleEntity = Gateway::getRuleEntity($_GET["id"])["entity"];
$predicats = Gateway::getPredicatsByEntity($ruleEntity);

$predicats_formates = [];

foreach ($predicats as $predicat) {
	$predicats_formates[] = ["id" => $predicat["code"], "name" => $predicat["code"]];
}

/* Si affichage de l'arbre de la règle */
if(isset($_GET["id"]) && !isset($_POST["form_submit"]))
{
	$id=$_GET["id"];
	$val=Gateway::getRuleNameRootEntity($id); // $val['name'] : nom de la règle; $val['id'] : id de la racine de l'arbre
	$idR=$val["id"];
}
/* Si modification de la règle */
else if(isset($_GET["id"]) && isset($_POST["form_submit"]))
{
	$id=$_GET["id"];
	$val=Gateway::getRuleNameRootEntity($id); // $val['name'] : nom de la règle; $val['id'] : id de la racine de l'arbre
	$idR=$val["id"]; // Racine de l'arbre
	unset($_POST["form_submit"]);
	$keys = array_keys($_POST);
	for ($i = 0; $i < count($keys); $i++) {
		if (preg_match("/(nb_children_operator_)/", $keys[$i]) && !preg_match("/(nb_children_operator_group)/", $keys[$i])) {
			unset ($_POST[$keys[$i]]);
		}
	}
	// var_dump($_POST);
	$donnees = makeTree($_POST); // Création de l'arbre
	// var_dump($donnees);
	$idRoot=Gateway::insertTree($donnees,$idR);
	if($idRoot!=null) {
		Gateway::setRoot($id,$idRoot);
		$idR=$idRoot;
	}
	$success=true;
}
$name=$val["name"];
$entity=$val["entity"];
$data = null;
if($idR != null) $data=Gateway::getRuleTree($idR); // On retrouve l'arbre grâce à sa racine

if ($data) {
	list($nb_criterias, $nb_groups) = recursiveCount($data);
}
$profondeur = 0;
$id = $_GET["id"] ?? $_GET["modify"];
$configurations = Gateway::getConfigurationByFilterRule($id);

$section = "Définition d'une règle";

include ('../Vue/filtre/FiltreTree.php');
?>

