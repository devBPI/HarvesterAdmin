<?php

include_once("../PDO/Gateway.php");

class Rapport
{
	/** Retourne les id, noms et types des rapports
	 * @param $type string type du rapport (PROCESS ou METADATA)
	 * @return array|false les résultats trouvés
	 */
	static function getReports($type=null) {
		if ($type == "PROCESS") {
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.interface_report WHERE type='PROCESS' ORDER BY name")
			);
		} else if ($type == "METADATA") {
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.interface_report WHERE type='METADATA' ORDER BY name")
			);
		} else { // type == null
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.interface_report ORDER BY name")
			);
		}
	}

	/** Retourne l'id, le nom, la date de création, la racine et le type du rapport dont l'id est passé en paramètre
	 * @param $id int id du rapport
	 * @return mixed|null
	 */
	static function getReport($id) {
		$query = pg_query(Gateway::getConnexion(), "SELECT id, name, type, creation_date, interface_criteria_tree_node_id AS tree_root
							FROM configuration.interface_report WHERE id=" . $id);
		if (!$query)
			return null;
		$result = pg_fetch_all($query);
		if ($result) return $result[0];
		else return null;
	}

	/** Met à jour le nom du rapport
	 * @param $id int identifiant le rapport
	 * @param $name string nouveau nom du rapport
	 * @return void
	 */
	static function setReportName($id, $name) {
		pg_query(Gateway::getConnexion(), "UPDATE configuration.interface_report SET name='". $name ."' WHERE id=". $id);
	}

	/** Retourne l'id, le code, le label et le code en format "bdd" des opérateurs
	 * @return array|false
	 */
	static function getOperators() {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.interface_criteria_operator")
		);
	}

	/** Retourne l'id, le code, le label et le code en format "bdd" de l'opérateur
	 * @param $code string de l'opérateur dont on souhaite récupérer les informations
	 * @return mixed
	 */
	static function getOperatorByCode($code) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.interface_criteria_operator WHERE code='". $code . "'")
		)[0];
	}

	/** Retourne les données selon le type (PROCESS ou METADATA)
	 * @param $type string PROCESS ou METADATA
	 * @param $is_null boolean a vrai si peut être de type number_of_results_infos
	 * @param $group string groupe de la donnée (pour division de la liste des données à afficher)
	 * @return array|false
	 */
	static function getDataToShowByGroup($type, $is_null, $group) {
		if ($is_null)
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(), "SELECT display_value AS id, default_name as name, data_group
														 FROM configuration.interface_data_mapping
														 WHERE data_type='". $type . "' AND data_group='".$group."'
														 ORDER BY default_name")
			);
		else
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(), "SELECT display_value AS id, default_name as name, data_group
														 FROM configuration.interface_data_mapping
														 WHERE data_type='". $type . "' AND data_group='".$group."'
														 AND table_field IS NOT NULL
														 ORDER BY default_name")
			);
	}

	/** Retourne les critères du rapport
	 * @param $report_id int identifiant du rapport
	 * @param $for string à quoi serviront ces critères : requête, affichage ou modification
	 * @return array|false
	 */
	static function getCriterias($report_id, $for=null) {
		if ($for=="query") {
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(),
					"SELECT idm.table_field, idm.data_table, ico.query_code, ic.value_to_compare, idm.display_value, idm.data_group
					   FROM configuration.interface_criteria ic, configuration.interface_criteria_operator ico, configuration.interface_data_mapping idm
					   WHERE ic.interface_data_mapping_id=idm.id
				       AND ic.interface_criteria_operator_id=ico.id
				       AND ic.interface_report_id=". $report_id ."
				       ")
			);
		} else if ($for=="poster") {
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(),
					"SELECT idm.default_name, ico.label, ic.value_to_compare, idm.data_group
					   FROM configuration.interface_criteria ic, configuration.interface_criteria_operator ico, configuration.interface_data_mapping idm
					   WHERE ic.interface_data_mapping_id=idm.id
				       AND ic.interface_criteria_operator_id=ico.id
				       AND ic.interface_report_id=". $report_id ."
				       ")
			);
		}
		else {
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(),
					"SELECT ic.id, idm.display_value, ico.code, ic.value_to_compare, idm.data_group
				FROM configuration.interface_criteria ic, configuration.interface_criteria_operator ico, configuration.interface_data_mapping idm
				WHERE ic.interface_data_mapping_id=idm.id
				  AND ic.interface_criteria_operator_id=ico.id
				  AND ic.interface_report_id=" . $report_id . "
			")
			);
		}
	}

	static function getCriteria($criteria_id) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(),
				"SELECT ic.id, idm.table_field, idm.data_table, ico.code, ico.label, ic.value_to_compare, idm.display_value, idm.default_name, idm.data_group
					   FROM configuration.interface_criteria ic, configuration.interface_criteria_operator ico, configuration.interface_data_mapping idm
					   WHERE ic.interface_data_mapping_id=idm.id
				       AND ic.interface_criteria_operator_id=ico.id
				       AND ic.id=". $criteria_id ."
			")
		);
	}

	/** Retourne les données à afficher du rapport
	 * @param $report_id int identifiant du rapport
	 * @return array|false l'identifiant, la valeur d'affichage et la chaîne de caractère d'affichage de la donnée
	 */
	static function getDataToDisplay($report_id) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(),
				"SELECT idtd.id, idm.display_value, idtd.display_name, idm.data_group
				FROM configuration.interface_data_to_display idtd, configuration.interface_data_mapping idm
				WHERE idtd.interface_data_mapping_id=idm.id
				  AND idtd.interface_report_id=". $report_id ."
				  ORDER BY idtd.id
			")
		); // ORDER BY id pour afficher les données dans l'ordre déterminé par l'utilisateur
	}

	/** Retourne le data_mapping à partir de la valeur d'affichage (possible car celle-ci est unique)
	 * @param $display_value string la valeur d'affichage
	 * @return mixed
	 */
	static function getDataMappingByDisplay_Value($display_value) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT idm.* FROM configuration.interface_data_mapping idm WHERE idm.display_value='". $display_value."'")
		)[0];
	}
	
	static function updateCriteria($criteria) {
		$operator_id = self::getOperatorByCode($criteria["code"])["id"];
		$data_mapping_id = self::getDataMappingByDisplay_Value($criteria["display_value"])["id"];
		pg_query(Gateway::getConnexion(),
		"UPDATE configuration.interface_criteria
				SET value_to_compare='". $criteria["value_to_compare"] ."',
				interface_data_mapping_id=" . $data_mapping_id . ",
				interface_criteria_operator_id=" . $operator_id . "
				WHERE id=" . $criteria["id"]);
	}

	static function insertCriteria($criteria) {
		$operator_id = self::getOperatorByCode($criteria["code"])["id"];
		$data_mapping_id = self::getDataMappingByDisplay_Value($criteria["display_value"])["id"];
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(),
				"INSERT INTO configuration.interface_criteria(value_to_compare, interface_data_mapping_id, interface_criteria_operator_id)
					   VALUES ('". $criteria["value_to_compare"] ."', ". $data_mapping_id .", ". $operator_id .")
					   	RETURNING id"
			)
		)[0]["id"];
	}

	static function deleteCriteria($id) {
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_criteria WHERE id=" . $id);
	}

	static function updateDataToDisplay($data) {
		$data_mapping_id = self::getDataMappingByDisplay_Value($data["display_value"])["id"];
		pg_query(Gateway::getConnexion(),
			"UPDATE configuration.interface_data_to_display
				   SET display_name=E'". $data["display_name"] ."', interface_data_mapping_id=". $data_mapping_id ."
				   WHERE id=". $data["id"]);
	}

	static function insertDataToDisplay($data) {
		$data_mapping_id = self::getDataMappingByDisplay_Value($data["display_value"])["id"];
		pg_query(Gateway::getConnexion(),
			"INSERT INTO configuration.interface_data_to_display(display_name, interface_data_mapping_id, interface_report_id)
					VALUES (E'". $data["display_name"] ."', ". $data_mapping_id .", ". $data["report_id"] . ")"
		);
	}

	static function deleteDataToDisplay($id) {
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_data_to_display WHERE id=". $id);
	}

	static function insertReport($report) {
		$name_exists = pg_fetch_all(pg_query(Gateway::getConnexion(),
			"SELECT name FROM configuration.interface_report WHERE name='". $report["infos"]["name"] ."'")
		);
		if ($name_exists && count($name_exists) > 0)
			return -1;
		$query = pg_query(Gateway::getConnexion(), "INSERT INTO configuration.interface_report(name, type)
												 VALUES ('".$report["infos"]["name"]."', '".$report["infos"]["type"]."')
												 RETURNING id");
		if (!$query) {
			return -1;
		}
		$report_id = pg_fetch_all($query)[0]["id"];
		// Insertion des données à afficher
		foreach($report["data_to_insert"] as $data) {
			$data["report_id"] = $report_id;
			self::insertDataToDisplay($data);
		}
		// Insertion des critères
		$root_id = self::insertCriteriaTree($report["criterias_tree"]);
		pg_query(Gateway::getConnexion(), "UPDATE configuration.interface_report
				SET interface_criteria_tree_node_id=".$root_id." WHERE id=".$report_id
		);

		return $report_id;
	}

	static function insertCriteriaTree($criterias_tree, $parent=null) {
		if (isset($criterias_tree["operator"]) && $criterias_tree["operator"] != null) { // Si on est en présence d'un noeud
			$operator = $criterias_tree["operator"];
			unset($criterias_tree["operator"]);
			if ($parent != null) {
				$parent_id = pg_fetch_all(
					pg_query(Gateway::getConnexion(),
						"INSERT INTO configuration.interface_criteria_tree_node(parent_id, boolean_operator)
						VALUES (".$parent.", '".$operator."') RETURNING id"
					)
				)[0]["id"];
			} else {
				$parent_id = pg_fetch_all(
					pg_query(Gateway::getConnexion(),
						"INSERT INTO configuration.interface_criteria_tree_node(boolean_operator)
						VALUES ('".$operator."') RETURNING id"
					)
				)[0]["id"];
			}
			foreach ($criterias_tree as $sub_tree) {
				self::insertCriteriaTree($sub_tree, $parent_id);
			}
			return $parent_id;
		} else { // On est en présence de feuilles
			foreach ($criterias_tree as $sub_tree) {
				$leaf_id = self::insertCriteria($sub_tree);
				pg_fetch_all(
					pg_query(Gateway::getConnexion(),
						"INSERT INTO configuration.interface_criteria_tree_node(parent_id, interface_criteria_id)
						VALUES (" . $parent . ", " . $leaf_id . ")"
					)
				);
			}
		}
		return null;
	}

	static function updateReport($report) {
		$name_exists = pg_fetch_all(pg_query(Gateway::getConnexion(),
				"SELECT name FROM configuration.interface_report WHERE LOWER(name)='". strtolower($report["infos"]["name"]) ."' AND id!=". $report["infos"]["id"])
		);
		if ($name_exists && count($name_exists) > 0)
			return -1;

		$data_to_delete = [];

		if (count($report["data_id_list"]) > 0) {
			$data_to_delete = pg_fetch_all(pg_query(Gateway::getConnexion(),
					"SELECT id FROM configuration.interface_data_to_display
          					WHERE id NOT IN ('".implode("','", $report["data_id_list"])."')
          					AND interface_report_id=".$report["infos"]["id"])
			);
		}
		// Màj du nom du rapport
		self::setReportName($report["infos"]["id"], $report["infos"]["name"]);
		// Suppression de l'ancien arbre de critères
		$tree_root_id = Gateway::getReport($report["infos"]["id"])["tree_root"]; // Id de la racine de l'arbre
		self::deleteTree($tree_root_id);
		self::deleteRoot($tree_root_id);
		// Insertion des nouveaux critères
		$root_id = self::insertCriteriaTree($report["criterias_tree"]);
		pg_query(Gateway::getConnexion(), "UPDATE configuration.interface_report
				SET interface_criteria_tree_node_id=".$root_id." WHERE id=".$report["infos"]["id"]
		);
		// Màj des données à afficher
		if (count($report["data_to_update"]) > 0)
			foreach($report["data_to_update"] as $data)
				self::updateDataToDisplay($data);
		if (count($report["data_to_insert"]) > 0)
			foreach($report["data_to_insert"] as $data)
				self::insertDataToDisplay($data);
		if ($data_to_delete && count($data_to_delete) > 0)
			foreach($data_to_delete as $data)
				self::deleteDataToDisplay($data["id"]);
		return 0;
	}

	static function deleteTree($parent_id) {
		if($parent_id == null) {
			return;
		}
		$children_ids=pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT id FROM configuration.interface_criteria_tree_node WHERE parent_id=".$parent_id)
		);
		if($children_ids != null){
			foreach($children_ids as $id) {
				self::deleteTree($id["id"]);
				pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_criteria_tree_node WHERE id=" . $id["id"]);
			}
		}
	}

	static function deleteRoot($tree_root_id) {
		pg_query(Gateway::getConnexion(), "UPDATE configuration.interface_report
		 					SET interface_criteria_tree_node_id=null
		 					WHERE interface_criteria_tree_node_id=".intval($tree_root_id)
		);
		pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_criteria_tree_node
       											WHERE id=".intval($tree_root_id));
	}

	static function deleteReport($report_id) {
		$tree_root_id = Gateway::getReport($report_id)["tree_root"];
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_data_to_display WHERE interface_report_id=". $report_id);
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_report WHERE id=". $report_id);
		self::deleteTree($tree_root_id);
		self::deleteRoot($tree_root_id);
	}

	/** Nombre de notices insérées dans la table "notice" par la moisson
	 * @param $task_id int identifiant de la moisson
	 * @return int
	 */
	static function getNumberNotices($task_id) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(),
				"SELECT COUNT(n.id)
						FROM configuration.harvest_task ht, configuration.harvest_configuration hc, public.notice n
    					WHERE ht.configuration_id=hc.id
    					AND hc.id=n.configuration_id
    					AND (n.harvesting_date BETWEEN ht.start_time AND ht.end_time)
    					AND ht.id=".$task_id
			)
		)[0]["count"];
	}

	/** Nombre de notices insérées dans la table "external_link" par la moisson
	 * @param $task_id int identifiant de la moisson
	 * @return int
	 */
	static function getNumberExternalLink($task_id) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(),
				"SELECT COUNT(el.id)
						FROM configuration.harvest_task ht, configuration.harvest_configuration hc, public.external_link el
    					WHERE ht.configuration_id=hc.id
    					AND hc.id=el.configuration_id
    					AND (el.harvesting_date BETWEEN ht.start_time AND ht.end_time)
    					AND ht.id=".$task_id
			)
		)[0]["count"];
	}


	static function duplicateReport($report_id) {
		$configuration = Gateway::getReport($report_id);
		if ($configuration == null)
			return -1;
		// -- Détermination du nom du rapport
		$cpt = 0;
		$name_exists = true;
		while ($name_exists) {
			$cpt++;
			$name_exists = pg_fetch_all(pg_query(Gateway::getConnexion(),
					"SELECT name FROM configuration.interface_report
            		WHERE LOWER(name)='" . utf8_encode(strtolower(utf8_decode($configuration["name"] . " (" . $cpt . ")"))) . "'")
			);
		}
		// -- Formattage du rapport pour la fonction insertReport
		$report["infos"]["name"] = $configuration["name"] . " (".$cpt.")";
		$report["infos"]["type"] = $configuration["type"];
		$report["data_to_insert"] = Gateway::getDataToDisplay($report_id);
		$report["criterias_tree"] = [];
		$criterias_tmp = Gateway::getCriteriasTree(self::getReport($report_id)["tree_root"]);
		$report["criterias_tree"] = self::recursiveCriteriasFormatting($criterias_tmp);
		// -- Insertion du rapport
		return self::insertReport($report);
	}

	static function recursiveCriteriasFormatting($criterias_tmp) {
		$criterias_tree = [];
		foreach ($criterias_tmp as $node) {
			if (is_array($node)) {
				if ($node["leaf_id"] != null) {
					$criteria = self::getCriteria($node["leaf_id"])[0];
					$criterias_tree["criterias"][] = [
						"display_value" => $criteria["display_value"],
						"code" => $criteria["code"],
						"value_to_compare" => $criteria["value_to_compare"]
					];
				} else {
					$criterias_tree [] = self::recursiveCriteriasFormatting($node);
				}
			}
		}
		$criterias_tree["operator"] = $criterias_tmp["operator"];
		return $criterias_tree;
	}

	static function getCriteriasTree($tree_root_id) {
		$query = @pg_query(Gateway::getConnexion(),"SELECT id, interface_criteria_id AS leaf_id, boolean_operator AS operator
					FROM configuration.interface_criteria_tree_node WHERE id=".intval($tree_root_id));
		$donnee = pg_fetch_all($query)[0];
		if(!empty($donnee)) {
			if(!empty($donnee["operator"])) {
				$data = self::grt($tree_root_id);
			}
			$data["operator"]=$donnee["operator"];
			$data["leaf_id"]=$donnee["leaf_id"];
			$data["id"]=$donnee["id"];
			return $data;
		} else {
			return null;
		}
	}

	static function grt($parent_id) {
		$data = self::getTreeNodeByParent($parent_id);
		if ($data) {
			foreach ($data as $k => $d) {
				if (!empty($data[$k]["operator"])) {
					$data[$k] = self::grt($d["id"]);
					$data[$k]["operator"] = $d["operator"];
					$data[$k]["leaf_id"]=$d["leaf_id"];
					$data[$k]["id"] = $d["id"];
				}
			}
		}
		return $data;
	}

	static function getTreeNodebyParent($parent_id)
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT id, interface_criteria_id AS leaf_id, boolean_operator AS operator
							FROM configuration.interface_criteria_tree_node WHERE parent_id=".$parent_id);
		if (!$query) {
			return null;
		}
		return pg_fetch_all($query);
	}

}