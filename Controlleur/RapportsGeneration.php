<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

date_default_timezone_set('Europe/Paris');

$report_id = $_POST["report_id"] ?? "";
if ($report_id == "")
	$report_id = $_GET["report_id"] ?? "";
$type = $_POST["report_type"] ?? "";
if ($type == "")
	$type = $_GET["report_type"] ?? "";

$query_empty_or_error = false;


function reportToCsv($filename, $headers, $data) {
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="'. $filename .'.csv";');
	ob_clean();
	$out = fopen("php://output", 'w');
	fputcsv($out, $headers, ";");
	foreach ($data as $fields) {
		fputcsv($out, $fields, ";");
	}
	fclose($out);
}

function buildRegularWhere($criteria, $where, $increment_non_vide) {
	// Autres cas de la construction du where (commun à Processus et Métadonnées)
	if ($increment_non_vide == 0) {
		return $where . $criteria["data_table"] . "." . $criteria["table_field"]
			. $criteria["query_code"] . "'" . $criteria["value_to_compare"] . "'";
	}
	else {
		return $where . " AND " . $criteria["data_table"] . "." . $criteria["table_field"]
			. $criteria["query_code"] . "'" . $criteria["value_to_compare"] . "'";
	}
}

// -- Si tri du tableau (clic sur en-tête du tableau)
if (isset($_POST["ordre"]) && isset($_POST["champ"]) && isset($_POST["report_list"])) {
	$tab_header = [];
	$indice = $_POST["champ"];
	$new_array = $_POST["report_list"];
	usort($new_array, function($a, $b) use ($indice) {
		if (strtolower($a[$indice]) < strtolower($b[$indice])) return 1;
		return -1;
	});
	if ($_POST["ordre"] == "asc") {
		$new_array = array_reverse($new_array);
	}
	foreach ($new_array as $ligne) {
		echo "<tr>";
		foreach ($ligne as $key => $value) {
			echo "<td>".str_replace("_", "_<wbr>", $value)."</td>";
		}
		echo "</tr>";
	}
}
// -- Sinon, génération de la requête et du rapport associé
else {
if((!empty($_POST) && $_POST["submit_value"] == "generate") || !empty($_GET) && $_GET["submit_value"] == "generate") {
	//var_dump($_POST);
	$configuration = Gateway::getReport($report_id);
	$configuration["criterias"] = Gateway::getCriterias($report_id, "query");
	$configuration["data_to_display"] = Gateway::getDataToDisplay($report_id);
	foreach ($configuration["data_to_display"] as $key => $dtd) { // Récupération de toutes les infos des data_to_display
		$configuration["data_to_display"][$key] = array_unique(
			array_merge($dtd,Gateway::getDataMappingByDisplay_Value($dtd["display_value"])),
			SORT_REGULAR
		);
	}
	//var_dump($configuration);
	// --------------------------------- DONNEES
	if ($type == "donnees") {
		$select = "SELECT ";
		$from = " FROM public.notice, configuration.harvest_configuration ";
		$where = " WHERE configuration.harvest_configuration.id=public.notice.configuration_id ";
		$end_query = "";
		$must_be_group_by = false;

		$is_set_from_sub_query = false; // determine si on doit ajouter la sous-requete unnest(date_publishing) a la clause from

		// ------------- DISTINCT OU NON
		foreach ($configuration["criterias"] as $key=>$criteria) {
			if ($criteria["display_value"]=="results_distinct") {
				$select = "SELECT DISTINCT ";
				unset($configuration["criterias"][$key]);
			}
		}

		// ------------- FROM ET WHERE
		$increment_non_vide = 1; // increment seulement si != cas 2 (pour construction de la requete)
		foreach ($configuration["criterias"] as $criteria) {
			// Cas 1 : fonction (par exemple : abs)
			// Cas 2 : nombre de moissons = dernière uniquement
			// Cas 3 : critère sur notice.date_publishing
			if ($criteria["table_field"] == "unnest(public.notice.date_publishing)") {
				// Seulement si le "from" n'a pas déjà été ajouté -> on le rajoute + on fait la jointure
				if(!$is_set_from_sub_query) {
					$is_set_from_sub_query = true;
					$from = $from . ", (SELECT id, unnest(date_publishing) AS annee FROM public.notice) rq";
					if ($increment_non_vide == 0) $where = $where . "rq.id=public.notice.id";
					else $where = $where . " AND rq.id=public.notice.id";
					$increment_non_vide++;
				}
				if ($increment_non_vide == 0) {
					$where = $where . "rq.annee" . $criteria["query_code"] . $criteria["value_to_compare"];
				} else {
					$where = $where . " AND rq.annee" . $criteria["query_code"] . $criteria["value_to_compare"];
				}
				$increment_non_vide++;
			} // Autres cas
			else {
				$where = buildRegularWhere($criteria, $where, $increment_non_vide);
				$increment_non_vide++;
			}
		}

		// ------------- SELECT
		$ind = 0;
		foreach ($configuration["data_to_display"] as $key => $dtd) {
			if ($is_set_from_sub_query && $dtd["table_field"] == "unnest(public.notice.date_publishing)") {
				$configuration["data_to_display"][$key]["table_field"] = "annee";
				$configuration["data_to_display"][$key]["data_table"] = "rq";
			}
			if (preg_match('/(\([^)]*\))/', $configuration["data_to_display"][$key]["table_field"])) {
				if (preg_match('/(count)/', $configuration["data_to_display"][$key]["table_field"]))
					$must_be_group_by = true;
				if ($key == 0)
					$select = $select . $configuration["data_to_display"][$key]["table_field"] . " AS \"" . $configuration["data_to_display"][$key]["display_name"] . "\"";
				else
					$select = $select . ", " . $configuration["data_to_display"][$key]["table_field"] . " AS \"" . $configuration["data_to_display"][$key]["display_name"] . "\"";
			} else {
				if ($key == 0) {
					$select = $select . $configuration["data_to_display"][$key]["data_table"] . "." . $configuration["data_to_display"][$key]["table_field"] .
						" AS \"" . $configuration["data_to_display"][$key]["display_name"] . "\"";
				} else {
					$select = $select . ", " . $configuration["data_to_display"][$key]["data_table"] . "." . $configuration["data_to_display"][$key]["table_field"] .
						" AS \"" . $configuration["data_to_display"][$key]["display_name"] . "\"";
				}
			}
		}

		// ------------- GROUP BY
		if ($must_be_group_by) {
			$end_query = " GROUP BY (";
			$ind = 0;
			foreach ($configuration["data_to_display"] as $key => $dtd) {
				if (!preg_match('/(count\([^)]*\))/', $dtd["table_field"])) {
					if (preg_match('/(\([^)]*\))/', $dtd["table_field"])) {
						if ($ind == 0) $end_query = $end_query . $dtd["table_field"];
						else $end_query = $end_query . "," . $dtd["table_field"];
					} else {
						if ($ind == 0) $end_query = $end_query . $dtd["data_table"] . "." . $dtd["table_field"];
						else $end_query = $end_query . "," . $dtd["data_table"] . "." . $dtd["table_field"];
					}
					$ind++;
				}
			}
			$end_query = $end_query . ")";
		}

		$requete_generee = $select.$from.$where.$end_query;
		//print_r($requete_generee);
		$report["result"] = Gateway::select($requete_generee);

		if (!$report["result"]) {
			$query_empty_or_error = true;
		}
	}
	// --------------------------------- PROCESSUS
	if ($type == "processus") {
		$select = "SELECT configuration.harvest_task.id AS task_id";
		$from = " FROM configuration.harvest_task, configuration.harvest_configuration ";
		$where = " WHERE configuration.harvest_task.configuration_id = configuration.harvest_configuration.id AND ";
		$end_query = "";
		$join_external_link = false;
		$display_name_external_link = "";
		$join_notice = false;
		$display_name_notice = "";
		$join_grabber = false; // passe à vrai si la table grabber a déjà été ajoutée au from et au where
		$increment_non_vide = 0; // increment seulement si != cas 2 (pour construction de la requete)

		foreach ($configuration["data_to_display"] as $key => $dtd) {
			if (preg_match('/(public.)/', $dtd["data_table"])) {
				if ($dtd["data_table"] == "public.notice") {
					$join_notice = true;
					$display_name_notice = $dtd["display_name"];
				} else if ($dtd["data_table"] == "public.external_link") {
					$join_external_link = true;
					$display_name_external_link = $dtd["display_name"];
				}
			} else {
				if (preg_match('/(\([^)]*\))/', $dtd["table_field"])) {
					$select = $select . ", " . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
				} else if ($dtd["data_table"] == "configuration.grabber") {
					if (!$join_grabber) {
						$join_grabber = true;
						$from = $from.", configuration.grabber, configuration.harvest_grab_configuration";
						if ($increment_non_vide == 0)
							$where = $where." harvest_grab_configuration.grabber_id=grabber.id";
						else
							$where = $where." AND harvest_grab_configuration.grabber_id=grabber.id";
						$increment_non_vide++;
					}
					$select = $select . ", " . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
				} else {
					$select = $select . ", " . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
				}
			}
		}

		//print_r($select);

		foreach ($configuration["criterias"] as $criteria) {// Cas 1 : fonction (par exemple : abs)
			if (preg_match('/(\([^)]*\))/', $criteria["table_field"])) {
				// Cas où abs(expected_notices_number-notices_number) est en %
				if (preg_match('/(%)/', $criteria["value_to_compare"])) {
					$v = (rtrim($criteria["value_to_compare"], "%")) / 100;
					$value_to_compare = "(" . $v . "*expected_notices_number)";
				} else
					$value_to_compare = $criteria["value_to_compare"];

				if ($increment_non_vide == 0) {
					$where = $where . $criteria["table_field"] . $criteria["query_code"] . $value_to_compare;
				} else {
					$where = $where . " AND " . $criteria["table_field"] . $criteria["query_code"] . $value_to_compare;
				}
				$increment_non_vide++;
			} // Cas 2 : nombre de moissons = dernière uniquement
			else if ($criteria["display_value"] == "harvest_last_task") {
				$end_query = $end_query . " ORDER BY harvest_task.id DESC LIMIT 1";
			} // Cas 3 : besoin d'ajouter une jointure à la table grabber
			else if ($criteria["display_value"] == "harvest_grabber_type") {
				if (!$join_grabber) {
					$join_grabber = true;
					$from = $from.", configuration.grabber, configuration.harvest_grab_configuration";
					if ($increment_non_vide == 0)
						$where = $where." harvest_grab_configuration.grabber_id=grabber.id";
					else
						$where = $where." AND harvest_grab_configuration.grabber_id=grabber.id";
					$where = $where. " AND harvest_configuration.grab_configuration_id=harvest_grab_configuration.id";
					$increment_non_vide++;
				}
				$where = buildRegularWhere($criteria, $where, $increment_non_vide);
				$increment_non_vide++;
			} // Autres cas
			else {
				$where = buildRegularWhere($criteria, $where, $increment_non_vide);
				$increment_non_vide++;
			}
		}

		//print_r($select.$from_where.$end_query);
		$requete_generee = $select.$from.$where.$end_query;
		$report["result"] = Gateway::select($requete_generee);
		if ($report["result"]) {
			if ($join_notice) {
				foreach ($report["result"] as $key => $line) {
					$nb = Gateway::getNumberNotices($line["task_id"]);
					$report["result"][$key][$display_name_notice] = $nb > 0 ? $nb : "";
				}
			}
			if ($join_external_link) {
				foreach ($report["result"] as $key => $line) {
					$nb = Gateway::getNumberExternalLink($line["task_id"]);
					$report["result"][$key][$display_name_external_link] = $nb > 0 ? $nb : "";
				}
			}
			foreach ($report["result"] as $key => $line) {
				unset($report["result"][$key]["task_id"]);
			}
		} else {
			$query_empty_or_error = true;
		}
	}

	$tab_header = [];
	if ($report["result"]) {
		foreach ($report["result"][0] as $key => $value) {
			$tab_header[] = $key;
		}
	}
	//var_dump($report_result);

	$section = $configuration["name"];
	if(isset($_POST["generate_csv"]) || isset($_GET["generate_csv"])) {
		// reportToCsv($configuration["name"], $tab_header, $report["result"]);
		ob_clean(); // Permet d'enlever les deux lignes vides au début du fichier
		echo implode(";",$tab_header);
		echo "\n";
		if ($report["result"]) {
			foreach ($report["result"] as $line) {
				echo implode(";", $line);
				echo "\n";
			}
		}
	}
	else {
		include("../Vue/rapports/Rapport.php");
	}
}
}
?>