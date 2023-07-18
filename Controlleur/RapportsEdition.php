<?php

ini_set('xdebug.var_display_max_depth', 20);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);


/* Code commun à RapportsDonneesEdition.php et RapportsProcessusEdition.php */

require_once ("../PDO/Gateway.php");
require_once ("../Composant/RapportComposant.php");

$msg_error = null;

$id = $_GET["id"] ?? "";

$configuration = null;

function makeTree(&$donnees_post) {
	$arbre = [];
	if ($donnees_post) {
		if (preg_match("/(operator_group_)/", key($donnees_post))) {
			$arbre["operator"] = array_shift($donnees_post);
			$nb_children = array_shift($donnees_post);
			if ($nb_children == 0) {
				$ind = 0;
				$keys_to_remove = [];
				foreach ($donnees_post as $key => $value) {
					if (preg_match('/(id_cond_)/', $key)) {
						$keys_to_remove[] = $key;
					} else if (preg_match('/(champ_cond_)/', $key)) {
						$arbre["criterias"][$ind]["display_value"] = $value;
						$keys_to_remove[] = $key;
					} else if (preg_match('/(operateur_cond_)/', $key)) {
						$arbre["criterias"][$ind]["code"] = $value;
						$keys_to_remove[] = $key;
					} else if (preg_match('/(valeur_cond_)/', $key)) {
						$arbre["criterias"][$ind++]["value_to_compare"] = $value;
						$keys_to_remove[] = $key;
					} else {
						break;
					}
				}
				// On enleve le "T" des criteres
				foreach ($arbre["criterias"] as $key => $criteria) {
					if (preg_match("/(date)/", $criteria["display_value"])
						|| preg_match("/(time)/", $criteria["display_value"])) {
						if (preg_match("/(([0-9]{4}-[0-9]{2}-[0-9]{2})T((0?[0-9]|1[0-9]|2[0-3]):[0-9]+))/", $criteria["value_to_compare"]))
							$arbre["criterias"][$key]["value_to_compare"] = str_replace("T", " ", $criteria["value_to_compare"]);
					}
				}
				$donnees_post = array_diff_key($donnees_post, array_flip($keys_to_remove));
			} else {
				for ($j = 0; $j < $nb_children; $j++) {
					$arbre[$j] = makeTree($donnees_post);
				}
			}
		}
	}
	return $arbre;
}

/**
 * @param $data_type string METADATA ou PROCESS
 * @return array de trois tableaux : liste d'opérateurs, de deux opérateurs, de données à afficher
 */
function getOperatorsDataToShow($data_type): array
{
	$operators_old = Gateway::getOperators();
	$operators = [];
	foreach ($operators_old as $operator) {        // Formatage des operateurs
		$operators[] = [
			"id" => $operator["code"],
			"name" => $operator["label"]
		];
	}
	$operators_short = [["id" => "equals", "name" => "&equals;"], ["id" => "not_equals", "name" => "&ne;"]];

	$data_to_show["general_infos"] = Gateway::getDataToShowByGroup($data_type, true, "general_infos");
	$data_to_show["follow_up"] = Gateway::getDataToShowByGroup($data_type, true, "follow_up");
	$data_to_show["number_of_results_infos"] = Gateway::getDataToShowByGroup($data_type, true, "number_of_results_infos");
	return array($operators, $operators_short, $data_to_show);
}

/**
 * @param $criterias_tree array arbre des critères
 * @param $cpt_groups int compteur, vaut 1 par défaut (racine de l'arbre)
 * @return array de deux entiers : cpt du nombre de critères et cpt du nombres de groupes
 */
function recursiveCount($criterias_tree, $cpt_groups=1): array
{
	$cpt_criterias = 0;
	if ($cpt_groups < 1) $cpt_groups = 0;
	foreach ($criterias_tree as $key => $donnee) {
		if (is_array($donnee)) {
			if ($donnee["leaf_id"] == null) {
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

if (!isset($_GET["viewonly"])) {
	// ------------------------------------------------------------- Si enregistrement de la configuration
	if (!empty($_POST) && $_POST["submit_value"] == "save") {
		//var_dump($_POST);
		// Suppression des données envoyées inutiles
		unset($_POST["id_cond_"]);
		unset($_POST["champ_cond_"]);
		unset($_POST["operateur_cond_"]);
		unset($_POST["valeur_cond_"]);
		unset($_POST["id_champ_aff_"]);
		unset($_POST["display_champ_aff_"]);
		unset($_POST["name_champ_aff_"]);
		unset($_POST["submit_value"]);

		// Récupération des ids des données à visualiser
		$dtd_id_list = [];
		if ($id != "") $dtd_list = Gateway::getDataToDisplay($id);
		if (isset($dtd_list)) for ($i = 0; $i < count($dtd_list); $i++) { $dtd_id_list[] = $dtd_list[$i]["id"]; }

		// Définition des variables
		$donnees["infos"]["id"] = $id;
		$donnees["infos"]["name"] = $_POST["name_rapport"];
		$donnees["infos"]["type"] = $data_type;
		unset($_POST["name_rapport"]);
		$donnees["data_id_list"] = [];
		$donnees["data_to_update"] = [];
		$donnees["data_to_insert"] = [];
		$is_to = "update";

		// Formattage des données à afficher
		$ind = 0;
		$new_dtd = true;
		foreach ($_POST as $key => $value) {
			if (preg_match('/(id_champ_aff_)/', $key) && isset($dtd_id_list[$ind]) && $new_dtd) {
				$is_to = "update";
				$new_dtd = false;
				$donnees["data_id_list"][] = $dtd_id_list[$ind];
				$donnees["data_to_" . $is_to][$ind]["id"] = $dtd_id_list[$ind];
				unset($_POST[$key]);
			} else if (preg_match('/(id_champ_aff_)/', $key) && !isset($dtd_id_list[$ind]) && $new_dtd) {
				$is_to = "insert";
				$new_dtd = false;
				$donnees["data_to_" . $is_to][$ind]["report_id"] = $id;
				unset($_POST[$key]);
			} else if (preg_match('/(display_champ_aff_)/', $key)) {
				$donnees["data_to_" . $is_to][$ind]["display_value"] = str_replace('"', '\"', str_replace("'", "\'", $value));
				unset($_POST[$key]);
			} else if (preg_match('/(name_champ_aff_)/', $key)) {
				$donnees["data_to_" . $is_to][$ind++]["display_name"] = str_replace('"', '\"', str_replace("'", "\'", $value));
				$new_dtd = true;
				unset($_POST[$key]);
			}
		}

		// Formattage des critères du rapport
		$donnees["criterias_tree"] = makeTree($_POST);

		if ($id != "") {
			$insert_ok = Gateway::updateReport($donnees); // Retourne -1 si erreur d'insertion, 0 sinon
			if ($insert_ok == -1) {
				$msg_error = "Erreur : le titre du rapport (" . $donnees["infos"]["name"] . ") est déjà utilisé";
			} else {
				// Si pas d'erreur, redirection
				header("Location: ../Controlleur/Rapports".$maj_type."Edition.php?id=".$id."&viewonly");
			}
		} else {
			$new_id = Gateway::insertReport($donnees); // Retourne -1 si erreur d'insertion, l'id de la configuration insérée sinon
			if ($new_id == -1) {
				$msg_error = "Erreur : le titre du rapport (" . $donnees["infos"]["name"] . ") est déjà utilisé";
				$configuration["id"] = $donnees["infos"]["id"];
				$configuration["name"] = $donnees["infos"]["name"];
				$configuration["type"] = $donnees["infos"]["type"];
				// $configuration["criterias"] = array_merge($donnees["criterias_to_insert"], $donnees["criterias_to_update"]);
				$configuration["data_to_display"] = array_merge($donnees["data_to_insert"], $donnees["data_to_update"]);
			} else {
				// Si pas d'erreur, redirection
				header("Location: ../Controlleur/Rapports".$maj_type."Edition.php?id=".$new_id."&viewonly");
			}
		}
	}
	// ------------------------------------------------------------- Sinon / Apres etre entres dans le "Si"
	if ($id != "") {
		$section = $section . "modification de la configuration de rapport";
	} else {
		$section = $section . "nouvelle configuration de rapport";
	}
	if ($id != "" && is_numeric($id)) {
		$configuration = Gateway::getReport($id);
		if ($configuration!=null) {
			$tree_root_id = $configuration["tree_root"]; // Id de la racine de l'arbre
			$configuration["criterias_tree"] = Gateway::getCriteriasTree($tree_root_id);
			$configuration["data_to_display"] = Gateway::getDataToDisplay($id);
			list($configuration["nb_criterias"], $configuration["nb_groups"]) = recursiveCount($configuration["criterias_tree"]);
		}
	}

	list($operators, $operators_short, $data_to_show) = getOperatorsDataToShow($data_type);
	$data_to_show_for_display["general_infos"] = Gateway::getDataToShowByGroup($data_type, true, "general_infos");
	$data_to_show_for_display["follow_up"] = Gateway::getDataToShowByGroup($data_type, true, "follow_up");

	include "../Vue/rapports/RapportConfigurationEdition.php";
}
// ---------------------------------------------------------------------------------------------- AFFICHAGE
else {
	if ($id != "" && is_numeric($id)) {
		$configuration = Gateway::getReport($id);
		if ($configuration!=null) {
			$tree_root_id = $configuration["tree_root"]; // Id de la racine de l'arbre
			$configuration["criterias_tree"] = Gateway::getCriteriasTree($tree_root_id);
			$configuration["data_to_display"] = Gateway::getDataToDisplay($id);
			$section = $section . "détails de la configuration";
		}
	}
	include "../Vue/rapports/RapportConfigurationAffichage.php";
}
?>