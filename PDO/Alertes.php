<?php

include_once("../PDO/Gateway.php");

class Alertes
{


	/** Retourne les alertes
	 * @param $order string ordre d'affichage des résultats
	 * @param $date string date de création de l'alerte, facultatif
	 * @return array|false|void
	 */
	static function getAlerts($order, $date=null)
	{
		if (!$date) {
			$query = pg_query(Gateway::getConnexion(), "SELECT a.id, level, category, message, b.name as configuration_name, configuration_id,  creation_time, modification_time, status
        FROM monitoring.alert a
        LEFT JOIN configuration.harvest_configuration c on c.id = a.configuration_id		
        LEFT JOIN configuration.search_base b on b.code = c.search_base_code 	
        WHERE 1=1
		ORDER BY " . $order);
		} else {
			$query = pg_query(Gateway::getConnexion(), "SELECT a.id, level, category, message, b.name as configuration_name, configuration_id,  creation_time, modification_time, status
        FROM monitoring.alert a
        LEFT JOIN configuration.harvest_configuration c on c.id = a.configuration_id		
        LEFT JOIN configuration.search_base b on b.code = c.search_base_code 
        WHERE 1=1 AND to_date(creation_time::TEXT,'YYYY-mm-dd')=to_date('".$date."', 'YY-mm-dd')
		ORDER BY " . $order);
		}
		if (!$query)
		{
			echo "Erreur durant la requête de getAlerts .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	/** Retourne les alertes pour la cartouche (Fiche Individuelle)
	 * @param $config_id int identifiant de la configuration
	 * @return array|false
	 */
	static function getAlertsForCartridge($config_id) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT a.id, level, category, message, creation_time, modification_time, status
			FROM monitoring.alert a, configuration.harvest_configuration hc
			WHERE hc.id=a.configuration_id
			AND hc.id=".$config_id
			)
		);
	}

	/** Suppression d'une alerte
	 * @param $id int identifiant de l'alerte
	 * @return void
	 */
	static function deleteAlert($id)
	{
		pg_query(Gateway::getConnexion(), "DELETE FROM monitoring.alert where id='".$id."';");
	}

	static function getAlertJobs() {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(),
			"SELECT code AS id, name, is_enabled FROM monitoring.alert_job"
			)
		);
	}

	static function updateAlertJobs($alert_jobs) {
		foreach ($alert_jobs as $alert_job) {
			pg_query("UPDATE monitoring.alert_job SET is_enabled=". $alert_job["is_enabled"]." WHERE code=". $alert_job["id"]);
		}
	}

}