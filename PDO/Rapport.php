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

	/** Retourne l'id, le nom et le type du rapport dont l'id est passé en paramètre
	 * @param $id int id du rapport
	 * @return mixed|null
	 */
	static function getReport($id) {
		$query = pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.interface_report WHERE id=" . $id);
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
	 * @param $is_null boolean a vrai si la colonne data_group peut être number_of_results_infos
	 * @return array|false
	 */
	static function getDataToShow($type, $is_null=true) {
		if ($is_null)
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(), "SELECT display_value AS id, default_name as name, data_group
														 FROM configuration.interface_data_mapping WHERE data_type='". $type . "' ORDER BY default_name")
			);
		else
			return pg_fetch_all(
				pg_query(Gateway::getConnexion(), "SELECT display_value AS id, default_name as name, data_group
														 FROM configuration.interface_data_mapping
														 WHERE data_type='". $type . "' AND data_group !='number_of_results_infos'
														 ORDER BY default_name")
			);
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
					"SELECT idm.table_field, idm.data_table, ico.query_code, ic.value_to_compare, idm.data_group
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
			")
		);
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
		pg_query(Gateway::getConnexion(),
				"INSERT INTO configuration.interface_criteria(value_to_compare, interface_data_mapping_id, interface_criteria_operator_id, interface_report_id)
					   VALUES ('". $criteria["value_to_compare"] ."', ". $data_mapping_id .", ". $operator_id .", " .$criteria["report_id"] .")"
		);
	}

	static function deleteCriteria($id) {
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_criteria WHERE id=" . $id);
	}

	static function updateDataToDisplay($data) {
		var_dump($data);
		$data_mapping_id = self::getDataMappingByDisplay_Value($data["display_value"])["id"];
		pg_query(Gateway::getConnexion(),
			"UPDATE configuration.interface_data_to_display
				   SET display_name='". $data["display_name"] ."', interface_data_mapping_id=". $data_mapping_id ."
				   WHERE id=". $data["id"]);
	}

	static function insertDataToDisplay($data) {
		$data_mapping_id = self::getDataMappingByDisplay_Value($data["display_value"])["id"];
		pg_query(Gateway::getConnexion(),
			"INSERT INTO configuration.interface_data_to_display(display_name, interface_data_mapping_id, interface_report_id)
					VALUES ('". $data["display_name"] ."', ". $data_mapping_id .", ". $data["report_id"] . ")"
		);
	}

	static function deleteDataToDisplay($id) {
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_data_to_display WHERE id=". $id);
	}

	static function insertReport($report) {
		$name_exists = pg_fetch_all(pg_query(Gateway::getConnexion(),
			"SELECT name FROM configuration.interface_report WHERE name='". $report["infos"]["name"] ."'")
		);
		if (count($name_exists) > 0)
			return -1;
		$query = pg_query(Gateway::getConnexion(), "INSERT INTO configuration.interface_report(name, type)
												 VALUES ('".$report["infos"]["name"]."', '".$report["infos"]["type"]."')
												 RETURNING id");
		if (!$query) {
			return -1;
		}
		$report_id = pg_fetch_all($query)[0]["id"];
		foreach($report["criterias_to_insert"] as $criteria) {
			$criteria["report_id"] = $report_id;
			self::insertCriteria($criteria);
		}
		foreach($report["data_to_insert"] as $data) {
			$data["report_id"] = $report_id;
			self::insertDataToDisplay($data);
		}
		return $report_id;
	}

	static function updateReport($report) {

		$name_exists = pg_fetch_all(pg_query(Gateway::getConnexion(),
				"SELECT name FROM configuration.interface_report WHERE name='". $report["infos"]["name"] ."' AND id!=". $report["infos"]["id"])
		);
		if ($name_exists && count($name_exists) > 0)
			return -1;

		$criterias_to_delete = [];
		$data_to_delete = [];
		if (count($report["criteria_id_list"]) > 0) {
			$criterias_to_delete = pg_fetch_all(pg_query(Gateway::getConnexion(),
					"SELECT id FROM configuration.interface_criteria
          					WHERE id NOT IN ('" . implode("','", $report["criteria_id_list"]) . "')
          					AND interface_report_id=". $report["infos"]["id"])
			);
		}
		if (count($report["data_id_list"]) > 0) {
			$data_to_delete = pg_fetch_all(pg_query(Gateway::getConnexion(),
					"SELECT id FROM configuration.interface_data_to_display
          					WHERE id NOT IN ('" . implode("','", $report["data_id_list"]) . "')
          					AND interface_report_id=". $report["infos"]["id"])
			);
		}
		self::setReportName($report["infos"]["id"], $report["infos"]["name"]);
		foreach ($report["criterias_to_update"] as $criteria)
			self::updateCriteria($criteria);
		foreach($report["criterias_to_insert"] as $criteria)
			self::insertCriteria($criteria);
		foreach($criterias_to_delete as $criteria)
			self::deleteCriteria($criteria["id"]);
		foreach($report["data_to_update"] as $data)
			self::updateDataToDisplay($data);
		foreach($report["data_to_insert"] as $data)
			self::insertDataToDisplay($data);
		foreach($data_to_delete as $data)
			self::deleteDataToDisplay($data["id"]);
		return 0;
	}

	static function deleteReport($id) {
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_criteria WHERE interface_report_id=". $id);
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_data_to_display WHERE interface_report_id=". $id);
		@pg_query(Gateway::getConnexion(), "DELETE FROM configuration.interface_report WHERE id=". $id);
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

}