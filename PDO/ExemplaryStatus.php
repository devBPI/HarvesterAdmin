<?php

include_once("../PDO/Gateway.php");

class ExemplaryStatus
{
	static function getStatus()
	{
		$query = pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.exemplary_status_configuration ORDER BY code;");
		if (!$query)
		{
			echo "Erreur durant la requête de getStatus .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	static function updateStatus($code, $dispo, $to_harvest, $label)
	{
		pg_query(Gateway::getConnexion(), "UPDATE configuration.exemplary_status_configuration
			SET dispo_flag = '".$dispo."', select_to_harvest = '".$to_harvest."', label = '".$label."'
			WHERE code = '".$code."';");
	}
	static function insertStatus($code, $dispo, $to_harvest, $label)
	{
		pg_query(Gateway::getConnexion(), "INSERT INTO configuration.exemplary_status_configuration
			VALUES('".$code."','".$dispo."','".$to_harvest."','".$label."');");
	}
	static function deleteStatus($code)
	{
		pg_query(Gateway::getConnexion(), "DELETE FROM configuration.exemplary_status_configuration where code='".$code."';");
	}
}