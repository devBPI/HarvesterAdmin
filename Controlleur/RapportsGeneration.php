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


// Génération d'un CSV --> inutilisé, remplacé par du js
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

function buildRegularWhere($criteria, $where, $operator, $increment_non_vide) {
	// Autres cas de la construction du where (commun à Processus et Métadonnées)
	if ($increment_non_vide == 0) {
		return $where . $criteria["data_table"] . "." . $criteria["table_field"]
			. $criteria["query_code"] . "E'" . str_replace("'", "\'", $criteria["value_to_compare"]) . "'";
	}
	else {
		return $where . " " . $operator . " " . $criteria["data_table"] . "." . $criteria["table_field"]
			. $criteria["query_code"] . "E'" . str_replace("'", "\'", $criteria["value_to_compare"]) . "'";
	}
}

function buildWhereFromString($where, $str, $increment_non_vide) {
	if ($increment_non_vide == 0)  return $where . " " . $str;
	else return $where . " AND " . $str;
}

function recursiveCriteriasFormatting($criterias_tmp) {
	$criterias_tree = [];
	foreach ($criterias_tmp as $node) {
		if (is_array($node)) {
			if ($node["leaf_id"] != null) {
				$criteria = Gateway::getCriteria($node["leaf_id"])[0];
				$criterias_tree[] = [
					"data_table" => $criteria["data_table"],
					"table_field" => $criteria["table_field"],
					"query_code" => $criteria["query_code"],
					"value_to_compare" => $criteria["value_to_compare"],
					"display_value" => $criteria["display_value"]
				];
			} else {
				$criterias_tree [] = recursiveCriteriasFormatting($node);
			}
		}
	}
	$criterias_tree["operator"] = $criterias_tmp["operator"];
	return $criterias_tree;
}

/** Recherche une valeur bien précise à la clef indiquée dans un arbre
 * Sert également de fonction raccourcie pour preg_match
 * @param $criterias_tree array arbre de critères (ou critère simple, si on veut utiliser cette fonction comme un preg_match)
 * @param $search_key string clef de recherche (arbre[clef])
 * @param $search_value string valeur attendue (arbre[clef]=valeur)
 * @param $delete boolean true si l'élément trouvé doit être retiré de l'arbre, false sinon
 * @param $delete_parent boolean true si le parent de cet élément doit être éliminé
 * @return array de la forme : [booléen à vrai si la valeur a été trouvée à l'emplacement recherché ;
 * 								array de critères, sans les éléments recherchés si delete = true;
 * 								booléen à vrai si le parent doit être supprimé (ne sert qu'à la récursion)].
 */
function searchInTree($criterias_tree, $search_key, $search_value, $delete=true, $delete_parent=false) {
	if ($criterias_tree == null) {
		return array(false, null, false);
	}
	$result = false; // Résultat (booléen)
	$delete_self = false; // Doit-on supprimer le sous-arbre courant (utile dans car où ... AND (dernière moisson / resultat distinct))
	$c_tree = []; // Arbre
	if (isset($criterias_tree[$search_key]) && preg_match($search_value, $criterias_tree[$search_key])) { // Si feuille recherchée
		if ($delete) return array(true, null, true);
		else return array(true, $criterias_tree, false);
	} else if (isset($criterias_tree[$search_key])) { // Si feuille mais pas l'élément recherché
		return array(false, $criterias_tree, false);
	} else if (is_array($criterias_tree)) { // Si arbre
		foreach ($criterias_tree as $key => $value) {
			if (is_array($value)) {
				if (!$result) { // Si le résultat est faux, on peut le changer
					list($result, $tmp, $delete_self) = searchInTree($value, $search_key, $search_value, $delete);
				} else { // Si le résultat est vrai, au moins une occurrence, pas la peine de le modifier
					list($nothing, $tmp, $delete_self) = searchInTree($value, $search_key, $search_value, $delete);
				}
				if ($tmp != null) $c_tree[] = $tmp;
			}
		}
		if ($delete_self && count($c_tree) == 0) { // Si delete_self -> deuxième élément retourné est null, on supprime le sous-arbre;
			return array($result, null, false);
		} else {
			// Si pas delete_self -> on retourne les trois valeurs
			$c_tree["operator"] = $criterias_tree["operator"];
			return array($result, $c_tree, false);
		}

	}
	return array(false, $criterias_tree, false); // Element non trouvé
}


/** Cette fonction est opérationnelle pour les requêtes simples ("autres cas")
	+ dernière moisson uniquement (n'utilise pas cette fonction mais searchInTree)
	+ résultats distincts (n'utilise pas cette fonction mais searchInTree)
	+ connecteurs (utilise searchInTree + cette fonction)
	+ base de recherche (utilise searchInTree + cette fonction)
	+ fonctions (abs notamment, unnest) */
function buildQuery($criterias_tree, $increment_non_vide=1, $operator=null, $not_the_last=true) {
	if ($criterias_tree == null) {
		return "";
	}
	$where = "";
	if (isset($criterias_tree["operator"]) && $criterias_tree["operator"] != null ) { // Groupe
		$criterias_tree_operator = $criterias_tree["operator"];
		unset($criterias_tree["operator"]);
		$where = $where . " (";
		$first_in_group = true;
		foreach ($criterias_tree as $key => $value) {
			if (is_array($value)) {
				if ($first_in_group) {
					$where = $where . buildQuery($value, 0, $criterias_tree_operator);
					$first_in_group = false;
				} else {
					$keys = array_keys($criterias_tree);
					if ($key == end($keys))
						$where = $where . buildQuery($value, 1, $criterias_tree_operator, false);
					else {
						$where = $where . buildQuery($value, 1, $criterias_tree_operator);
					}
				}
			}
		}
		if ($not_the_last)
			$where = $where . ") " . $operator;
		else
			$where = $where . ")";
	} else { // Critere
		if (searchInTree($criterias_tree,"table_field", "/(\([^)]*\))/")[0]) {
			// Cas où abs(expected_notices_number-notices_number) est en %
			if (preg_match('/(%)/', $criterias_tree["value_to_compare"])) {
				$v = (rtrim($criterias_tree["value_to_compare"], "%")) / 100;
				$value_to_compare = "(" . $v . "*expected_notices_number)";
			} else {
				$value_to_compare = $criterias_tree["value_to_compare"];
			}

			// Si unnest(public.notice.date_publishing)
			if (searchInTree($criterias_tree, "display_value", "/(notice_publishing_year)/")) {
				if ($increment_non_vide == 0) { $where = "rq.annee" . $criterias_tree["query_code"] . $criterias_tree["value_to_compare"]; }
				else { $where = $where . " ". $operator . " rq.annee" . $criterias_tree["query_code"] . $criterias_tree["value_to_compare"]; }
			}
			else {
				if ($increment_non_vide == 0) {
					$where = $where . $criterias_tree["table_field"] . $criterias_tree["query_code"] . $value_to_compare;
				} else {
					$where = $where . " " . $operator . " " . $criterias_tree["table_field"] . $criterias_tree["query_code"] . $value_to_compare;
				}
			}
		} else {
			$where = $where . buildRegularWhere($criterias_tree, $where, $operator, $increment_non_vide);
		}
	}
	return $where;
}


// -- Si tri du tableau (clic sur en-tête du tableau) --> inutilisé
if (isset($_POST["ordre"]) && isset($_POST["champ"]) && isset($_POST["report_list"])) {
	// echo ini_get("max_input_vars"); --> ce qui pose problème pour les grands tableaux report_list
	// var_dump($_POST["report_list"]);
	$tab_header = [];
	$indice = $_POST["champ"];
	$new_array = $_POST["report_list"];
	// echo ini_get("upload_max_filesize");
	// echo ini_get("post_max_size");
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
		$tree_root_id = $configuration["tree_root"]; // Id de la racine de l'arbre
		$configuration["criterias_tree"] = recursiveCriteriasFormatting(Rapport::getCriteriasTree($tree_root_id)); // On retrouve l'arbre grâce à sa racine
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
			$increment_non_vide = 1;
			$must_be_group_by = false;

			$is_set_from_sub_query = false; // determine si on doit ajouter la sous-requete unnest(date_publishing) a la clause from
			$join_grabber = false; // determine si on doit joindre la table grabber
			$join_search_base = false; // determine si on doit joindre la table search_base

			// ------------- DISTINCT ou non
			// Si critère = résultat distincts
			list($unique, $configuration["criterias_tree"]) = searchInTree($configuration["criterias_tree"], "display_value", "/(results_distinct)/");
			if ($unique) $select = "SELECT DISTINCT ";

			// ------------- FROM, WHERE
			// Si critère = connecteur
			list($join_grabber, $configuration["criterias_tree"]) = searchInTree($configuration["criterias_tree"], "display_value", "/(notice_grabber_type)/", false);
			if ($join_grabber) {
				$from = $from.", configuration.grabber, configuration.harvest_grab_configuration";
				$where = $where." AND harvest_grab_configuration.grabber_id=grabber.id";
				$where = $where." AND harvest_configuration.grab_configuration_id=harvest_grab_configuration.id";
			}
			// Si critère = base de recherche
			list($join_search_base, $configuration["criterias_tree"]) = searchInTree($configuration["criterias_tree"], "display_value", "/(notice_search_base)/", false);
			if ($join_search_base) {
				$from = $from . ", configuration.search_base";
				$where = $where . " AND configuration.search_base.code=configuration.harvest_configuration.search_base_code";
			}
			// Si le champs de la table = "unnest(public.notice.date_publishing)"
			list($is_set_from_sub_query, $configuration["criterias_tree"]) = searchInTree($configuration["criterias_tree"], "display_value", "/(notice_publishing_year)/", false);
			if ($is_set_from_sub_query) {
				$from = $from . ", (SELECT id, unnest(date_publishing) AS annee FROM public.notice) rq";
				$where = $where . " AND rq.id=public.notice.id";
			}
			// Cas de base
			if ($configuration["criterias_tree"] != null)
				$where = $where . " AND " . buildQuery($configuration["criterias_tree"]);

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
				} else if ($dtd["data_table"] == "configuration.grabber") {
					if (!$join_grabber) {
						$join_grabber = true;
						$from = $from.", configuration.grabber, configuration.harvest_grab_configuration";
						$where = buildWhereFromString($where, "harvest_grab_configuration.grabber_id=grabber.id
															 AND harvest_configuration.grab_configuration_id=harvest_grab_configuration.id", $increment_non_vide);
						$increment_non_vide++;
					}
					if ($key == 0)
						$select = $select . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
					else
						$select = $select . ", " . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
				} else if ($dtd["data_table"] == "configuration.search_base") {
					if (!$join_search_base) {
						$join_search_base = true;
						$from = $from . ", configuration.search_base";
						$where = buildWhereFromString($where, "configuration.search_base.code=configuration.harvest_configuration.search_base_code",
							$increment_non_vide);
						$increment_non_vide++;
					}
					if ($key == 0)
						$select = $select . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
					else
						$select = $select . ", " . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
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
			$report["result"] = Gateway::selectNoError($requete_generee);

			if (!$report["result"] || $report["result"] == -1) {
				$query_empty_or_error = true;
			}
		}
		// --------------------------------- PROCESSUS
		if ($type == "processus") {

			$select = "SELECT configuration.harvest_task.id AS task_id";
			$from = " FROM configuration.harvest_task, configuration.harvest_configuration ";
			$where = " WHERE configuration.harvest_task.configuration_id = configuration.harvest_configuration.id ";
			$end_query = "";
			$join_external_link = false;
			$display_name_external_link = "";
			$join_notice = false;
			$display_name_notice = "";
			$join_grabber = false; // passe à vrai si la table grabber a déjà été ajoutée au from et au where
			$increment_non_vide = 0; // increment seulement si != cas 2 (pour construction de la requete)

			// ------------- FROM, WHERE et ORDER BY
			// Si critère = résultat distincts
			list($unique, $configuration["criterias_tree"]) = searchInTree($configuration["criterias_tree"], "display_value", "/(results_distinct)/");
			// Si critère = dernière moisson uniquement
			list($must_be_orber_by, $configuration["criterias_tree"]) = searchInTree($configuration["criterias_tree"], "display_value", "/(harvest_last_task)/");
			if ($must_be_orber_by) $end_query =  " ORDER BY harvest_task.id DESC LIMIT 1";
			// Si critère = connecteur
			list($join_grabber, $configuration["criterias_tree"]) = searchInTree($configuration["criterias_tree"], "display_value", "/(harvest_grabber_type)/", false);
			if ($join_grabber) {
				$from = $from.", configuration.grabber, configuration.harvest_grab_configuration";
				$where = $where." AND harvest_grab_configuration.grabber_id=grabber.id";
				$where = $where." AND harvest_configuration.grab_configuration_id=harvest_grab_configuration.id";
			}
			// Cas de base
			if ($configuration["criterias_tree"] != null)
				$where = $where . " AND " . buildQuery($configuration["criterias_tree"], 1);

			// ------------- SELECT, (et FROM, WHERE associés)
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
							$where = $where." AND harvest_grab_configuration.grabber_id=grabber.id";
							$where = $where." AND harvest_configuration.grab_configuration_id=harvest_grab_configuration.id";
							$increment_non_vide++;
						}
						$select = $select . ", " . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
					} else {
						$select = $select . ", " . $dtd["data_table"] . "." . $dtd["table_field"] . " AS \"" . $dtd["display_name"] . "\"";
					}
				}
			}

			//print_r($select.$from_where.$end_query);
			$requete_generee = $select.$from.$where.$end_query;
			$report["result"] = Gateway::selectNoError($requete_generee);
			//$report["result"] = -1;

			if ($report["result"] && $report["result"] != -1) {
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

				// ------------- DISTINCT OU NON
				if ($unique) {
					$report["result"] = array_unique($report["result"], SORT_REGULAR);
				}

			} else {
				$query_empty_or_error = true;
			}
		}

		$tab_header = [];
		if (!$query_empty_or_error) {
			if ($report["result"]) {
				foreach ($report["result"][0] as $key => $value) {
					$tab_header[] = $key;
				}
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