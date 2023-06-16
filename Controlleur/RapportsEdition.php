<?php

require_once ("../PDO/Gateway.php");

$msg_error = null;

$id = $_GET["id"] ?? "";

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

		$ind = 0;
		$donnees["infos"]["id"] = $id;
		$donnees["infos"]["name"] = $_POST["name_rapport"];
		$donnees["infos"]["type"] = "METADATA";
		unset($_POST["name_rapport"]);
		$donnees["criteria_id_list"] = [];
		$donnees["criterias_to_update"] = [];
		$donnees["criterias_to_insert"] = [];
		$donnees["data_id_list"] = [];
		$donnees["data_to_update"] = [];
		$donnees["data_to_insert"] = [];
		$is_to = "update";

		foreach ($_POST as $key => $value) {
			if (preg_match('/(id_cond_)/', $key) && $value != "") {
				$is_to = "update";
				$donnees["criteria_id_list"][] = $value;
				$donnees["criterias_to_" . $is_to][$ind]["id"] = $value;
			} else if (preg_match('/(id_cond_)/', $key) && $value == "") {
				$is_to = "insert";
				$donnees["criterias_to_" . $is_to][$ind]["report_id"] = $id;
			} else if (preg_match('/(champ_cond_)/', $key)) {
				$donnees["criterias_to_" . $is_to][$ind]["display_value"] = $value;
			} else if (preg_match('/(operateur_cond_)/', $key)) {
				$donnees["criterias_to_" . $is_to][$ind]["code"] = $value;
			} else if (preg_match('/(valeur_cond_)/', $key)) {
				$donnees["criterias_to_" . $is_to][$ind++]["value_to_compare"] = $value;
			} else if (preg_match('/(id_champ_aff_)/', $key) && $value != "") {
				$is_to = "update";
				$donnees["data_id_list"][] = $value;
				$donnees["data_to_" . $is_to][$ind]["id"] = $value;
			} else if (preg_match('/(id_champ_aff_)/', $key) && $value == "") {
				$is_to = "insert";
				$donnees["data_to_" . $is_to][$ind]["report_id"] = $id;
			} else if (preg_match('/(display_champ_aff_)/', $key)) {
				$donnees["data_to_" . $is_to][$ind]["display_value"] = $value;
			} else if (preg_match('/(name_champ_aff_)/', $key)) {
				$donnees["data_to_" . $is_to][$ind++]["display_name"] = $value;
			}
		}

		// On enleve le "T" des criteres a ajouter
		foreach ($donnees["criterias_to_insert"] as $key => $criteria) {
			if (preg_match("/(date)/", $criteria["display_value"])
				|| preg_match("/(time)/", $criteria["display_value"])) {
				if (preg_match("/(([0-9]{4}-[0-9]{2}-[0-9]{2})T((0?[0-9]|1[0-9]|2[0-3]):[0-9]+))/", $criteria["value_to_compare"]))
					$donnees["criterias_to_insert"][$key]["value_to_compare"] = str_replace("T", " ", $criteria["value_to_compare"]);
			}
		}

		// On enleve le "T" des criteres a modifier
		foreach ($donnees["criterias_to_update"] as $key => $criteria) {
			if (preg_match("/(date)/", $criteria["display_value"])
				|| preg_match("/(time)/", $criteria["display_value"])) {
				if (preg_match("/(([0-9]{4}-[0-9]{2}-[0-9]{2})T((0?[0-9]|1[0-9]|2[0-3]):[0-9]+))/", $criteria["value_to_compare"]))
					$donnees["criterias_to_update"][$key]["value_to_compare"] = str_replace("T", " ", $criteria["value_to_compare"]);
			}
		}

		if (isset($_GET["id"])) {
			$insert_ok = Gateway::updateReport($donnees); // Retourne -1 si erreur d'insertion, 0 sinon
			if ($insert_ok == -1) {
				$msg_error = "Erreur : le titre du rapport (" . $donnees["infos"]["name"] . ") est déjà utilisé";
			} else {
				// Si pas d'erreur, redirection
				header("Location: ../Controlleur/Rapports".$maj_type."Edition.php?id=".$_GET["id"]."&viewonly");
			}
		} else {
			$new_id = Gateway::insertReport($donnees); // Retourne -1 si erreur d'insertion, l'id de la configuration insérée sinon
			if ($new_id == -1) {
				$msg_error = "Erreur : le titre du rapport (" . $donnees["infos"]["name"] . ") est déjà utilisé";
				$configuration["id"] = $donnees["infos"]["id"];
				$configuration["name"] = $donnees["infos"]["name"];
				$configuration["type"] = $donnees["infos"]["type"];
				$configuration["criterias"] = array_merge($donnees["criterias_to_insert"], $donnees["criterias_to_update"]);
				$configuration["data_to_display"] = array_merge($donnees["data_to_insert"], $donnees["data_to_update"]);
			} else {
				// Si pas d'erreur, redirection
				header("Location: ../Controlleur/Rapports".$maj_type."Edition.php?id=".$new_id."&viewonly");
			}
		}
	}
	// ------------------------------------------------------------- Sinon / Apres etre entres dans le "Si"
	if (isset($_GET["id"])) {
		$section = $section . "modification de la configuration de rapport";
	} else {
		$section = $section . "nouvelle configuration de rapport";
	}
	if (isset($_GET["id"])) {
		$configuration = Gateway::getReport($_GET["id"]);
		$configuration["criterias"] = Gateway::getCriterias($_GET["id"]);
		$configuration["data_to_display"] = Gateway::getDataToDisplay($_GET["id"]);
	}

	$operators_old = Gateway::getOperators();
	$operators = [];
	foreach ($operators_old as $operator) {		// Formatage des operateurs
		$operators[] = [
			"id" => $operator["code"],
			"name" => $operator["label"]
		];
	}
	$operators_short = [["id" => "equals", "name" => "&equals;"], ["id" => "not_equals", "name" => "&ne;"]];

	$data_to_show["general_infos"] = Gateway::getDataToShowByGroup($data_type, true, "general_infos");
	$data_to_show["follow_up"] = Gateway::getDataToShowByGroup($data_type, true, "follow_up");
	$data_to_show["number_of_results_infos"] = Gateway::getDataToShowByGroup($data_type, true, "number_of_results_infos");
	$data_to_show_for_display = Gateway::getDataToShow($data_type, false);

	include "../Vue/rapports/RapportConfigurationEdition.php";
}
// ---------------------------------------------------------------------------------------------- AFFICHAGE
else {
	if (isset($_GET["id"])) {
		$configuration = Gateway::getReport($_GET["id"]);
		$configuration["criterias"] = Gateway::getCriterias($_GET["id"], "poster");
		$configuration["data_to_display"] = Gateway::getDataToDisplay($_GET["id"]);
		$section = $section . "détails de la configuration";
	}
	include "../Vue/rapports/RapportConfigurationAffichage.php";
}
?>