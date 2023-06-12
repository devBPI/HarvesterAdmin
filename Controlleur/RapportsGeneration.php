<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

if(!empty($_POST) && $_POST["submit_value"] == "generate") {
	//var_dump($_POST);
	$configuration = Gateway::getReport($_POST["report_id"]);
	$configuration["criterias"] = Gateway::getCriterias($_POST["report_id"], "query");
	$data_to_display = Gateway::getDataToDisplay($_POST["report_id"]);
	$configuration["data_to_display"] = [];
	foreach ($data_to_display as $data_to_disp) {
		$configuration["data_to_display"][] = Gateway::getDataMappingByDisplay_Value($data_to_disp["display_value"]);
	}
	//var_dump($configuration);
	$end_query = "";
	$query = "SELECT ";
	$join_public_external_link = false;
	$join_public_notice = false;
	for ($i = 0; $i < count($configuration["data_to_display"]); $i++) {
		if (!preg_match('/(public.)/',$configuration["data_to_display"][$i]["data_table"])) {
			if (preg_match('/(\([^)]*\))/', $configuration["data_to_display"][$i]["table_field"])) {
				if ($i == 0) $query = $query.$configuration["data_to_display"][$i]["table_field"];
				else $query = $query.", ".$configuration["data_to_display"][$i]["table_field"];
			} else {
				if ($i == 0) $query = $query.$configuration["data_to_display"][$i]["data_table"].".".$configuration["data_to_display"][$i]["table_field"];
				else $query = $query.", ".$configuration["data_to_display"][$i]["data_table"].".".$configuration["data_to_display"][$i]["table_field"];
			}
		} else {
			if ($configuration["data_to_display"][$i]["data_table"] == "public.notice")
				$join_public_notice = true;
			else
				$join_public_external_link = true;
			if ($i == 0) $query = $query.$configuration["data_to_display"][$i]["table_field"];
				else $query = $query.", ".$configuration["data_to_display"][$i]["table_field"];
		}
	}

	if ($join_public_notice || $join_public_external_link) {
		$end_query = $end_query." GROUP BY ";
		for ($i = 0; $i < count($configuration["data_to_display"]); $i++) {
			if (!preg_match('/(public.)/', $configuration["data_to_display"][$i]["data_table"])) {
				if (preg_match('/(\([^)]*\))/', $configuration["data_to_display"][$i]["table_field"])) {
					if ($i == 0) $end_query = $end_query . $configuration["data_to_display"][$i]["table_field"];
					else $end_query = $end_query . ", " . $configuration["data_to_display"][$i]["table_field"];
				} else {
					if ($i == 0) $end_query = $end_query . $configuration["data_to_display"][$i]["data_table"] . "." . $configuration["data_to_display"][$i]["table_field"];
					else $end_query = $end_query . ", " . $configuration["data_to_display"][$i]["data_table"] . "." . $configuration["data_to_display"][$i]["table_field"];
				}
			}
		}
	}

	if (!$join_public_notice && !$join_public_external_link) {
		$query = $query . " FROM configuration.harvest_task, configuration.harvest_configuration
    WHERE configuration.harvest_task.configuration_id = configuration.harvest_configuration.id AND ";
	} else if ($join_public_notice && !$join_public_external_link) {
		$query = $query . " FROM configuration.harvest_task, configuration.harvest_configuration, public.notice
    WHERE configuration.harvest_task.configuration_id=configuration.harvest_configuration.id
    AND configuration.harvest_configuration.id=public.notice.configuration_id
    AND public.notice.harvesting_date BETWEEN configuration.harvest_task.start_time AND configuration.harvest_task.end_time AND ";
	} else if(!$join_public_notice && $join_public_external_link) {
		$query = $query . " FROM configuration.harvest_task, configuration.harvest_configuration, public.external_link
    WHERE configuration.harvest_task.configuration_id=configuration.harvest_configuration.id
    AND configuration.harvest_configuration.id=public.external_link.configuration_id
    AND public.external_link.harvesting_date BETWEEN configuration.harvest_task.start_time AND configuration.harvest_task.end_time AND ";
	} else {
		$query = $query . " FROM configuration.harvest_task, configuration.harvest_configuration, public.external_link, public.notice 
    WHERE configuration.harvest_task.configuration_id=configuration.harvest_configuration.id
    AND configuration.harvest_configuration.id=public.external_link.configuration_id
    AND public.external_link.harvesting_date BETWEEN configuration.harvest_task.start_time AND configuration.harvest_task.end_time
    AND public.notice.harvesting_date BETWEEN configuration.harvest_task.start_time AND configuration.harvest_task.end_time AND ";
	}
	$increment_non_vide = 0; // increment seulement si != cas 2 (pour construction de la requete)
	for ($i = 0; $i < count($configuration["criterias"]); $i++) {
		// Cas 1 : fonction (par exemple : abs)
		if (preg_match('/(\([^)]*\))/', $configuration["criterias"][$i]["table_field"])) {
			// Cas où abs(expected_notices_number-notices_number) est en %
			if (preg_match('/(%)/', $configuration["criterias"][$i]["value_to_compare"])) {
				$v = (rtrim($configuration["criterias"][$i]["value_to_compare"],"%"))/100;
				$value_to_compare = "(".$v."*expected_notices_number)";
			} else
				$value_to_compare = $configuration["criterias"][$i]["value_to_compare"];

			if ($increment_non_vide == 0) {
				$query = $query.$configuration["criterias"][$i]["table_field"]
					.$configuration["criterias"][$i]["query_code"].$value_to_compare;
			}
			else {
				$query = $query." AND ".$configuration["criterias"][$i]["table_field"]
					.$configuration["criterias"][$i]["query_code"].$value_to_compare;
			}
			$increment_non_vide++;
		}
		// Cas 2 : nombre de moissons = dernière uniquement
		else if ($configuration["criterias"][$i]["table_field"] == null) {
			$end_query = $end_query." ORDER BY harvest_task.id DESC LIMIT 1";
		}
		// Autres cas
		else {
			if ($increment_non_vide == 0) $query = $query.$configuration["criterias"][$i]["data_table"].".".$configuration["criterias"][$i]["table_field"]
				.$configuration["criterias"][$i]["query_code"]."'".$configuration["criterias"][$i]["value_to_compare"]."'";
			else $query = $query." AND ".$configuration["criterias"][$i]["data_table"].".".$configuration["criterias"][$i]["table_field"]
				.$configuration["criterias"][$i]["query_code"]."'".$configuration["criterias"][$i]["value_to_compare"]."'";
			$increment_non_vide++;
		}
	}
	//print_r($query . $end_query);
	$report["result"] = Gateway::select($query . $end_query);
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