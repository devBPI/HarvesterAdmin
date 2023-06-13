<?php

include_once("../PDO/Gateway.php");

class Alertes
{

	static function getAlerts($order)
	{
		$query = pg_query (Gateway::getConnexion(), "SELECT a.id, level, category, message, b.name as configuration_name, configuration_id,  creation_time, modification_time, status
        FROM monitoring.alert a
        LEFT JOIN configuration.harvest_configuration c on c.id = a.configuration_id		
        LEFT JOIN configuration.search_base b on b.code = c.search_base_code 	
        WHERE 1=1
		ORDER BY ".$order);
		if (!$query)
		{
			echo "Erreur durant la requête de getAlerts .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	static function getAlertsForCartridge($config_id) {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT a.id, level, category, message, creation_time, modification_time, status
			FROM monitoring.alert a, configuration.harvest_configuration hc
			WHERE hc.id=a.configuration_id
			AND hc.id=".$config_id
			)
		);
	}

	static function deleteAlert($id)
	{
		pg_query(Gateway::getConnexion(), "DELETE FROM monitoring.alert where id='".$id."';");
	}



}