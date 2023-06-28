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
			$query = pg_query(Gateway::getConnexion(), "SELECT a.id, level, category, message, CONCAT( c.name ,' (', b.name, ')') as configuration_name,
       															configuration_id,  creation_time, modification_time, status
        FROM monitoring.alert a
        LEFT JOIN configuration.harvest_configuration c on c.id = a.configuration_id		
        LEFT JOIN configuration.search_base b on b.code = c.search_base_code 	
        WHERE 1=1
		ORDER BY " . $order);
		} else {
			$query = pg_query(Gateway::getConnexion(), "SELECT a.id, level, category, message, CONCAT( c.name ,' (', b.name, ')') as configuration_name,
       															configuration_id,  creation_time, modification_time, status
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
			WHERE hc.id=a.configuration_id AND hc.id=".$config_id."
			ORDER BY creation_time DESC"
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
			pg_query(Gateway::getConnexion(), "UPDATE monitoring.alert_job SET is_enabled='". $alert_job["is_enabled"]."' WHERE code='". $alert_job["id"]."'");
		}
	}

	/** Liste des noms de domaine des adresses mail, précédés d'un @
	 * @return array|false liste où les éléments sont de la forme "@nomdedomaine"
	 */
	static function getMailingListDomainNames() {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(),
			"SELECT DISTINCT SUBSTRING(rq.recipients, rq.pos, LENGTH(rq.recipients)) AS dn
					FROM (SELECT recipients, POSITION('@' in recipients) AS pos FROM monitoring.mail_sender) rq
					ORDER BY dn")
		);
	}

	/** Retourne la liste des destinataires, avec les adresses mails bpi en tête
	 * @return array liste des adresses mail et de leur statut, selon l'ordre suivant : adresses bpi, puis autres adresses en ordre alphabétique
	 */
	static function getMailingList() {
		$head_mailinglist = [];
		$mailinglist = [];
		$domain_names = self::getMailingListDomainNames();
		if ($domain_names) {
			foreach ($domain_names as $dn) {
				$res = pg_fetch_all(pg_query(Gateway::getConnexion(),
						"SELECT recipients AS mail, is_enabled FROM monitoring.mail_sender WHERE recipients LIKE '%".$dn["dn"]."' ORDER BY recipients"
				));
				if ($res) {
					if ($dn == "@bpi.fr") $head_mailinglist = $res;
					else array_push($mailinglist, ...$res);
				}
			}
		}
		array_push($head_mailinglist, ...$mailinglist);
		return $head_mailinglist;
	}

	static function getAlertParameters() {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT code, name, value FROM monitoring.alert_rule_parameter ORDER BY code")
		);
	}

	static function updateAlertParameters($alert_parameters) {
		foreach ($alert_parameters as $alert_parameter) {
			pg_query(Gateway::getConnexion(), "UPDATE monitoring.alert_rule_parameter SET value=". $alert_parameter["value"]." WHERE code='". $alert_parameter["id"]."'");
		}
	}

}