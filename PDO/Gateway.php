<?php

include_once("../PDO/Alertes.php");
include_once("../PDO/Configuration.php");
include_once("../PDO/ExemplaryStatus.php");
include_once("../PDO/Filtre.php");
include_once("../PDO/Logs.php");
include_once("../PDO/Moisson.php");
include_once("../PDO/Rapport.php");
include_once("../PDO/TacheAnnexe.php");
include_once("../PDO/Traduction.php");

class Gateway
{
	private static $conn;

	/** Fonction de connexion à la base distante
	 * @return void
	 */
	static function connection()
	{
		$ini = @parse_ini_file("../etc/configuration.ini",true);
		if(!$ini)
		{
			$ini = @parse_ini_file("../etc/default.ini",true);
		}
		self::$conn = pg_connect("host=".$ini['host']." port=".$ini['port']." dbname=".$ini['dbname']." user=".$ini['user']." password=".$ini['password']."");
		if(!self::$conn)
		{
			echo "Erreur durant la connexion. \n";
		}
	}

	static function getConnexion() {
		if(!self::$conn)
			self::connection();
		return self::$conn;
	}

	// ------------------------------- Alertes
	static function getAlerts($order, $date=null) { return Alertes::getAlerts($order, $date); }
	static function getAlertsForCartridge($config_id) { return Alertes::getAlertsForCartridge($config_id); }
	static function deleteAlert($id) { Alertes::deleteAlert($id); }
	static function getAlertJobs() { return Alertes::getAlertJobs(); }
	static function updateAlertJobs($alert_jobs) { Alertes::updateAlertJobs($alert_jobs); }
	static function getMailingList() { return Alertes::getMailingList(); }
	static function getAlertParameters() { return Alertes::getAlertParameters(); }

	// ------------------------------- Configuration
	static function getIdFromCode($code) { return Configuration::getIdFromCode($code); }
	static function getCodeConfig($id) { return Configuration::getCodeConfig($id); }
	static function getConfigCodes() { return Configuration::getConfigCodes(); }
	static function getNomConfig($table, $id) { return Configuration::getNomConfig($table, $id); }
	static function getHarvestConfiguration() { return Configuration::getHarvestConfiguration(); }
	static function getHarvestConfigurationP() { return Configuration::getHarvestConfigurationP(); }
	static function getConfiguration($table, $id) { return Configuration::getConfiguration($table, $id); }
	static function getConfigurationName($id) { return Configuration::getConfigurationName($id); }
	static function getHarvestConfigurationDifferential($id){ return Configuration::getHarvestConfigurationDifferential($id); }
	static function getInfoConfig($id) { return Configuration::getInfoConfig($id); }
	static function getProfileConfig($id) { return Configuration::getProfileConfig($id); }
	static function getProfile() { return Configuration::getProfile(); }
	static function getConfigurationsWithFileToUpload() { return Configuration::getConfigurationsWithFileToUpload(); }
	static function getConfigurationsWithFilesToUpload() { return Configuration::getConfigurationsWithFilesToUpload(); }
	static function getConfigurationsWithoutFileToUpload() { return Configuration::getConfigurationsWithoutFileToUpload(); }
	static function countHarvestConfiguration($str) { return Configuration::countHarvestConfiguration($str); }
	static function getMapping() { return Configuration::getMapping(); }
	static function getMappingWithId($id) { return Configuration::getMappingWithId($id); }
	static function accesUpdate($code,$acces) { Configuration::accesUpdate($code, $acces); }
	static function getParcours($id) { return Configuration::getParcours($id); }
	static function updateParcours($parcours, $id) { Configuration::updateParcours($parcours, $id); }

	// ------------------------------- Traduction
	static function getTranslationDestinations() { return Traduction::getTranslationDestinations(); }
	static function getTranslationDestinationsByCategory($id) { return Traduction::getTranslationDestinationsByCategory($id); }
	static function getTranslationRulesSet($id) { return Traduction::getTranslationRulesSet($id); }
	static function getTranslationRulesBySet($id) { return Traduction::getTranslationRulesBySet($id); }

	static function getAllTranslations() // ancien code sans configuration_id en parametre pour les avoir tous (notamment appele dans ajoutConfig.php, voir CTLG-356)
	{
		return Traduction::getAllTranslations();
	}
	static function getTranslation($id) { return Traduction::getTranslation($id); }
	static function updateTrad($data,$id) { Traduction::updateTrad($data,$id); }
	static function getTranslationCategory() { return Traduction::getTranslationCategory(); }
	static function getDestination($category) { return Traduction::getDestination($category); }
	static function getAllDestination() { return Traduction::getAllDestination(); }
	static function updateDestination($data,$cmp,$category) { Traduction::updateDestination($data,$cmp,$category); }
	static function deleteDestination($data) { Traduction::deleteDestination($data); }
	static function getTrads($name) { return Traduction::getTrads($name); }
	static function getRulesSet() { return Traduction::getRulesSet(); }
	static function updateTranslationRule($data,$name) { Traduction::updateTranslationRule($data,$name); }
	static function getTranslationSetId($name) { return Traduction::getTranslationSetId($name); }
	static function getNewRules() { return Traduction::getNewRules(); }
	static function updateTranslationConfiguration($id,$data) { return Traduction::updateTranslationConfiguration($id,$data); }
	static function updateRulesSet($data,$cmp) { Traduction::updateRulesSet($data,$cmp); }
	static function deleteRulesSet($data) { Traduction::deleteRulesSet($data); }
	static function deleteCategory($data) { Traduction::deleteCategory($data); }
	static function getSetByConf($id) { return Traduction::getSetByConf($id); }
	static function getCategories() { return Traduction::getCategories(); }
	static function getCategoryBySet($set) { return Traduction::getCategoryBySet($set); }
	static function updateCategory($data,$cmp) { Traduction::updateCategory($data,$cmp); }
	static function getConfigurationBySet($id) { return Traduction::getConfigurationBySet($id); }
	static function getCategoryBySetId($id) { return Traduction::getCategoryBySetId($id); }

	// ------------------------------- Moisson
	static function getMoissonPlanifForEveryDayOfWeek($dow) { return Moisson::getMoissonPlanifForEveryDayOfWeek($dow); }
	static function deleteMoissonPlanning($id) { return Moisson::deleteMoissonPlanning($id);	}
	static function insertMoisson($id) { return Moisson::insertMoisson($id); }
	static function deleteMoisson($id) { return Moisson::deleteMoisson($id);	}
	static function insertMoissonWithStatus($id, $status) { return Moisson::insertMoissonWithStatus($id, $status); }
	static function getMoissonStatus($id) { return Moisson::getMoissonStatus($id); }
	static function countHarvests() { return Moisson::countHarvests(); }
	static function getTasks($order) { return Moisson::getTasks($order); }
	static function getTasksPagined($order, $size=20, $page=1) { return Moisson::getTasksPagined($order, $size, $page); }
	static function getProgress($id) { return Moisson::getProgress($id); }
	static function getGrabAdvancement($harvestTaskId) { return Moisson::getGrabAdvancement($harvestTaskId); }
	static function getImportAdvancement($harvestTaskId) { return Moisson::getImportAdvancement($harvestTaskId); }
	static function getIndexAdvancement($harvestTaskId) { return Moisson::getIndexAdvancement($harvestTaskId); }
	static function reprise($id) { Moisson::reprise($id); }
	static function getTasksForCartridge($confid) { return Moisson::getTasksForCartridge($confid); }
	static function getPlanifsForCartridge($confid) { return Moisson::getPlanifsForCartridge($confid); }
	static function insertDate($m, $h, $day, $jour, $id) { return Moisson::insertDate($m,$h,$day,$jour,$id); }
	static function getHarvestDate($id) { return Moisson::getHarvestDate($id); }
	static function reloadMoisson($id) { return Moisson::reloadMoisson($id); }
	static function getAllStatus() { return Moisson::getAllStatus(); }

	// ------------------------------- Filtre
	static function getRuleNameRootEntity($id) { return Filtre::getRuleNameRootEntity($id); }
	static function getRuleEntity($id) { return Filtre::getRuleEntity($id); }
	static function updateRuleName($name, $id) { Filtre::updateRuleName($name, $id); }
	static function getRuleTree($id) { return Filtre::getRuleTree($id); }
	static function grt($id) { return Filtre::grt($id); }
	static function getTreeNode($id) { return Filtre::getTreeNode($id); }
	static function deleteTree($id) { Filtre::deleteTree($id); }
	static function insertTreeNode($parent_id,$op,$id=NULL) { return Filtre::insertTreeNode($parent_id, $op, $id); }
	static function insertTreeLeaf($parent_id,$predicat,$id=NULL) { return Filtre::insertTreeLeaf($parent_id, $predicat, $id); }
	static function insertPredicate($property,$function,$value,$code=NULL) { return Filtre::insertPredicate($property,$function,$value,$code); }
	static function iT($data,$id) { Filtre::iT($data, $id); }
	static function insertTree($data,$id) { return Filtre::insertTree($data, $id); }
	static function getFilterRules() { return Filtre::getFilterRules(); }
	static function getFilterRuleOrderBy32() { return Filtre::getFilterRuleOrderBy32(); }
	static function getFilterByConf($id) { return Filtre::getFilterByConf($id); }
	static function updateFilterRule($data,$id) { Filtre::updateFilterRule($data, $id); }
	static function updatePredicats($data) { return Filtre::updatePredicats($data); }
	static function setRoot($id,$idR) { Filtre::setRoot($id, $idR); }
	static function updateRuleTree($data) { Filtre::updateRuleTree($data); }
	static function updateFilterRules($data) { return Filtre::updateFilterRules($data); }
	static function updateFilterConfiguration($id,$donnee) { return Filtre::updateFilterConfiguration($id, $donnee); }
	static function getFilterCode() { return Filtre::getFilterCode(); }
	static function getPredicat($id) { return Filtre::getPredicat($id); }
	static function getPredicatByCode($code) { return Filtre::getPredicatByCode($code); }
	static function getPredicats() { return Filtre::getPredicats(); }
	static function getPredicatsOrderByCode() { return Filtre::getPredicatsOrderByCode(); }
	static function getPredicatsOrderByEntityCode() { return Filtre::getPredicatsOrderByEntityCode(); }
	static function getPredicatsByEntity($entity) { return Filtre::getPredicatsByEntity($entity); }
	public static function getConfigurationByFilterRule($id) { return Filtre::getConfigurationByFilterRule($id); }

	// ------------------------------- Tâche annexe

	static function getSideTaskPlanifForEveryDayOfWeek($dow) { return TacheAnnexe::getSideTaskPlanifForEveryDayOfWeek($dow); }
	static function deleteSideTaskPlanif($id) { return TacheAnnexe::deleteSideTaskPlanif($id); }
	static function insertSideTask($name, $parameter) { return TacheAnnexe::insertSideTask($name, $parameter); }
	static function insertSideTaskDate($m, $h, $day, $jour, $name, $parameter) { return TacheAnnexe::insertSideTaskDate($m, $h, $day, $jour, $name, $parameter); }
	static function countSideTasks() { return TacheAnnexe::countSideTasks(); }
	static function getSideTasks($order) { return TacheAnnexe::getSideTasks($order); }
	static function getSideTasksPagined($order,$size,$page) { return TacheAnnexe::getSideTasksPagined($order,$size,$page); }


	// ------------------------------- Exemplary Status
	static function getStatus() { return ExemplaryStatus::getStatus(); }
	static function updateStatus($code, $dispo, $to_harvest, $label) { ExemplaryStatus::updateStatus($code, $dispo, $to_harvest, $label); }
	static function insertStatus($code, $dispo, $to_harvest, $label) { ExemplaryStatus::insertStatus($code, $dispo, $to_harvest, $label); }
	static function deleteStatus($code) { ExemplaryStatus::deleteStatus($code); }


	// ------------------------------- Logs

	static function countLogs($niv) { return Logs::countLogs($niv); }
	static function getLogs($niv,$start) { return Logs::getLogs($niv, $start); }
	static function getLog($limit,$start_from) { return Logs::getLog($limit, $start_from); }
	static function countLog() { return Logs::countLog(); }

	// ------------------------------- Rapports

	static function getReports($type=null) { return Rapport::getReports($type); }
	static function getReport($id) { return Rapport::getReport($id); }
	static function getOperators() { return Rapport::getOperators(); }
	static function getDataToShow($type, $is_null=true) { return Rapport::getDataToShow($type, $is_null); }
	static function getDataToShowByGroup($type, $is_null, $group) { return Rapport::getDataToShowByGroup($type, $is_null, $group); }
	static function getCriterias($report_id, $for=null) { return Rapport::getCriterias($report_id, $for); }
	static function getDataToDisplay($report_id) { return Rapport::getDataToDisplay($report_id); }
	static function getDataMappingByDisplay_Value($display_value) { return Rapport::getDataMappingByDisplay_Value($display_value); }
	static function insertReport($report) { return Rapport::insertReport($report); }
	static function updateReport($report) { return Rapport::updateReport($report); }
	static function deleteReport($id) { Rapport::deleteReport($id); }
	static function getNumberNotices($task_id) { return Rapport::getNumberNotices($task_id); }

	/**
	 * @return array de type id,'Nom' contenant l'id et une chaîne "Nom"
	 *         | false si la requête ne retourne aucun filtre
	 *         | void si erreur dans la requête
	 */
	static function getExclusion()
	{
		$query = pg_query (self::$conn, "SELECT filter.id, name
			FROM configuration.filter INNER JOIN configuration.filter_rule
			    ON filter.filter_rule_id=filter_rule.id
			ORDER BY filter.id ASC;");
		if (!$query)
		{
			echo "Erreur durant la requête de getExclusion.\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	

	static function getConfigurationGrabber()
	{
		$query = pg_query (self::$conn, "SELECT id,name FROM configuration.grabber ORDER BY id ASC;");
		/*if (!$query)
		{
			echo "Erreur durant la requête de getConfigurationGrabber .\n";
			exit;
		}*/
		return pg_fetch_all($query);
	}

	static function select($str)
	{
		$query = pg_query(self::getConnexion(), $str);
		if (!$query)
		{
			echo "Erreur durant la requête de select .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	static function getGraberName($id)
	{
		$query = pg_query (self::$conn, "SELECT G.name FROM configuration.grabber G, configuration.harvest_grab_configuration H
		WHERE G.id = H.grabber_id and H.id =".$id.";");
		/*if (!$query)
		{
			echo "Erreur durant la requête de getGraberName .\n";
			exit;
		}*/
		return pg_fetch_all($query)[0]['name'];
	}

	static function insert($str)
	{
		return pg_query (self::$conn, $str) or die ('Erreur connexion'. pg_last_error(self::$conn).'<br/>Avec la query: '.$str);
	}
	
	
	static function prepare($stmtname, $query)
	{
	    return pg_prepare(self::$conn, $stmtname, $query);
	}
	
	static function executeStatement($stmtname, $params)
	{
	    return pg_execute(self::$conn, $stmtname, $params);
	}

	static function getColor()
	{
		$query = pg_query(self::$conn,"
			SELECT nbr AS nbr_active, time_spent,
				CASE WHEN nbr = 0 THEN 'red'
				WHEN nbr = 1 THEN
				CASE WHEN minutes_spent > 20 THEN 'orange'
				ELSE 'green'
			END

			ELSE 'orange'
			END AS status,
			appli_version

			FROM
			(
				SELECT
				nbr,
				last_action_time,
				NOW() -  last_action_time AS time_spent,
				EXTRACT(epoch from (now() -  last_action_time))/60 AS minutes_spent,
				appli_version

				FROM
				(
					SELECT t9.status, t8.NBR, t9.last_action_time, appli_version
					FROM (
						SELECT count(*) AS nbr FROM configuration.scheduler_launch WHERE status = 'ACTIVE'
					) t8
					LEFT JOIN (
						SELECT status, MAX(last_action_time) AS last_action_time, MAX(appli_version) AS appli_version
						FROM configuration.scheduler_launch
						WHERE 1=1
						AND status = 'ACTIVE'
						GROUP BY status
					) t9 ON 1=1
				)t1
			)t2;
		");
		if (!$query)
		{
			echo "Erreur durant la requête de getColor .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getVersion()
	{
		$query = pg_query(self::$conn, "SELECT appli_version FROM configuration.scheduler_launch ORDER BY start_time DESC;");
		if (!$query)
		{
			echo "Erreur durant la requête de getVersion .\n";
			exit;
		}
		return pg_fetch_all($query)[0]['appli_version'];
	}
	
	static function reboot()
	{
		pg_query(self::$conn,"update configuration.scheduler_monitoring set has_to_be_shutdown = true") or die ('Erreur reboot'. pg_last_error(self::$conn));
	}

	static function getConf()
	{
		$query = pg_query(self::$conn,"SELECT hc.id, b.name AS name FROM configuration.harvest_configuration hc LEFT JOIN configuration.search_base b on b.code = hc.search_base_code ORDER BY id");
		if (!$query)
		{
			echo "Erreur durant la requête de getConf .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	
	static function getNotice($id)
	{
		$query = pg_query(self::$conn,"SELECT property from configuration.entity_properties WHERE entity='".$id."' ORDER BY property");
		if (!$query)
		{
			echo "Erreur durant la requête de getNotice .\n";
			exit;
		}
		$res_q  = pg_fetch_all($query);
		foreach($res_q as $i => $result){
			$res[$i] = $result["property"];
		}
		return $res;
	}
	static function getEntities()
	{
		$query = pg_query(self::$conn,"SELECT DISTINCT entity from configuration.entity_properties ORDER BY entity");
		if (!$query)
		{
			echo "Erreur durant la requête de getEntities .\n";
			exit;
		}
		$res_q  = pg_fetch_all($query);
		foreach($res_q as $i => $result){
			$res[$i] = $result["entity"];
		}
		return $res;
	}
	

	
	static function findSearchBaseAndConfigCodesForConfig($configId)
	{
	    // chargement des config sans fichier a uploader
	    $query = pg_query(self::$conn, "select c.code as config_code, b.code as base_code
                                        from configuration.harvest_configuration c
                                        join configuration.search_base b on b.code = c.search_base_code
                                        where c.id='".$configId."'");

	    if (! $query) {
	        echo "Erreur durant la requête findSearchBaseCodeForConfig .\n";
	        exit();
	    }
	    return pg_fetch_all($query);
	}
	
	
	static function getSearchBaseCodeForName($name)
	{
	    $query = pg_query (self::$conn, "select code from configuration.search_base where name = ".$name."");
	    if (!$query)
	    {
	        // CTLG-312 - on n'indique pas d'erreur si on ne trouve pas de donnee (pas de exit !!)
	       // echo "Erreur durant la requête de getSearchBaseCodeForName .\n";
	       // exit;
	    }
	    return pg_fetch_all($query)[0]['code'];
	}
	
	static function insertSearchBase($code, $name)
	{
	    return pg_query(self::$conn, "INSERT into configuration.search_base(code, name) values ('".$code."','".$name."');")or die ('Erreur insertSearchBase'. pg_last_error(self::$conn));
	}

	static function getResourceTypes() {
		return pg_fetch_all(
			pg_query(self::getConnexion(), "SELECT DISTINCT type FROM public.notice ORDER BY type")
		);
	}

}
?>


