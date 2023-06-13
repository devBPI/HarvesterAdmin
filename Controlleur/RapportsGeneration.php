<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

$type = $_POST["report_type"] ?? "";

if(!empty($_POST) && $_POST["submit_value"] == "generate") {
	//var_dump($_POST);
	$configuration = Gateway::getReport($_POST["report_id"]);
	$configuration["criterias"] = Gateway::getCriterias($_POST["report_id"], "query");
	$configuration["data_to_display"] = Gateway::getDataToDisplay($_POST["report_id"]);
	foreach ($configuration["data_to_display"] as $key => $dtd) {
		$configuration["data_to_display"][$key] = array_unique(
			array_merge($dtd,Gateway::getDataMappingByDisplay_Value($dtd["display_value"])),
			SORT_REGULAR
		);
	}
	//var_dump($configuration);

	$select = "SELECT configuration.harvest_task.id AS task_id";
	$from_where = " FROM configuration.harvest_task, configuration.harvest_configuration
    WHERE configuration.harvest_task.configuration_id = configuration.harvest_configuration.id AND ";
	$end_query = "";
	$join_external_link = false;
	$display_name_external_link = "";
	$join_notice = false;
	$display_name_notice = "";

	for ($i = 0; $i < count($configuration["data_to_display"]); $i++) {
		if (preg_match('/(public.)/',$configuration["data_to_display"][$i]["data_table"])) {
			if ($configuration["data_to_display"][$i]["data_table"] == "public.notice") {
				$join_notice = true;
				$display_name_notice = $configuration["data_to_display"][$i]["display_name"];
			}
			else {
				$join_external_link = true;
				$display_name_external_link = $configuration["data_to_display"][$i]["display_name"];
			}
		} else {
			if (preg_match('/(\([^)]*\))/', $configuration["data_to_display"][$i]["table_field"])) {
				$select = $select . ", " . $configuration["data_to_display"][$i]["table_field"] . " AS \"" . $configuration["data_to_display"][$i]["display_name"] . "\"";
			} else {
				$select = $select . ", " . $configuration["data_to_display"][$i]["data_table"] . "." . $configuration["data_to_display"][$i]["table_field"] . " AS \"" . $configuration["data_to_display"][$i]["display_name"] . "\"";
			}
		}
	}

	//print_r($select);

	$increment_non_vide = 0; // increment seulement si != cas 2 (pour construction de la requete)
	for ($i = 0; $i < count($configuration["criterias"]); $i++) {
		// Cas 1 : fonction (par exemple : abs)
		if (preg_match('/(\([^)]*\))/', $configuration["criterias"][$i]["table_field"])) {
			// Cas où abs(expected_notices_number-notices_number) est en %
			if (preg_match('/(%)/', $configuration["criterias"][$i]["value_to_compare"])) {
				$v = (rtrim($configuration["criterias"][$i]["value_to_compare"], "%")) / 100;
				$value_to_compare = "(" . $v . "*expected_notices_number)";
			} else
				$value_to_compare = $configuration["criterias"][$i]["value_to_compare"];

			if ($increment_non_vide == 0) {
				$from_where = $from_where . $configuration["criterias"][$i]["table_field"]
					. $configuration["criterias"][$i]["query_code"] . $value_to_compare;
			} else {
				$from_where = $from_where . " AND " . $configuration["criterias"][$i]["table_field"]
					. $configuration["criterias"][$i]["query_code"] . $value_to_compare;
			}
			$increment_non_vide++;
		} // Cas 2 : nombre de moissons = dernière uniquement
		else if ($configuration["criterias"][$i]["table_field"] == null) {
			$end_query = $end_query . " ORDER BY harvest_task.id DESC LIMIT 1";
		} // Autres cas
		else {
			if ($increment_non_vide == 0) $from_where = $from_where . $configuration["criterias"][$i]["data_table"] . "." . $configuration["criterias"][$i]["table_field"]
				. $configuration["criterias"][$i]["query_code"] . "'" . $configuration["criterias"][$i]["value_to_compare"] . "'";
			else $from_where = $from_where . " AND " . $configuration["criterias"][$i]["data_table"] . "." . $configuration["criterias"][$i]["table_field"]
				. $configuration["criterias"][$i]["query_code"] . "'" . $configuration["criterias"][$i]["value_to_compare"] . "'";
			$increment_non_vide++;
		}
	}

	//print_r($from_where);

	$report["result"] = Gateway::select($select.$from_where.$end_query);
	if ($join_notice) {
		foreach ($report["result"] as $key => $line) {
			$report["result"][$key][$display_name_notice] = Gateway::getNumberNotices($line["task_id"]);
			unset($report["result"][$key]["task_id"]);
		}
	} else {
		foreach ($report["result"] as $key => $line) {
			unset($report["result"][$key]["task_id"]);
		}
	}
	//var_dump($report);

	/*$tab_header = [];
	foreach ($data_to_display as $dtd) {
		$tab_header[] = $dtd["display_name"];
	}*/
	//var_dump($report_result);
	/*header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="'. $configuration["name"] .'.csv";');
	$out = fopen("php://output", 'w');
	fputcsv($out, $tab_header, ";");
	foreach ($report_result as $fields) {
		fputcsv($out, $fields, ";");
	}*/
	// Envoie un csv, mais capture tous les outputs (même les echo et var_dump, ce qui me pose problème)

	$section = $configuration["name"];

	include("../Vue/rapports/Rapport.php");
}

?>