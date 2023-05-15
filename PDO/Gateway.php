<?php

include_once("../PDO/Configuration.php");
include_once("../PDO/Traduction.php");
include_once("../PDO/Moisson.php");

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

	// ------------------------------- Configuration
	static function getIdFromCode($code) { return Configuration::getIdFromCode($code); }
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


	// ------------------------------- Traduction
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
	static function updateTranslationConfiguration($id,$data) { Traduction::updateTranslationConfiguration($id,$data); }
	static function updateRulesSet($data,$cmp) { Traduction::updateRulesSet($data,$cmp); }
	static function deleteRulesSet($data) { Traduction::deleteRulesSet($data); }


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




	/** Retourne tous les mappings de la base
	 * @return array contenant les id et name des mappings
	 * 			| false s'il n'y a aucun mapping dans la base
	 * 			| void si erreur dans la requête
	 */
	static function getMapping()
	{
		$query = pg_query (self::$conn, "SELECT id,name FROM configuration.mapping ORDER BY id ASC;");
		if (!$query)
		{
			echo "Erreur durant la requête de getMapping.\n";
			exit;
		}
		return pg_fetch_all($query);
	}


	/**
	 * @return array de type id,'Nom' contenant l'id et une chaîne "Nom"
	 *         | false si la requête ne retourne aucun filtre
	 *         | void si erreur dans la requête
	 */
	static function getExclusion()
	{
		$query = pg_query (self::$conn, "SELECT id, 'Nom' as name FROM configuration.filter ORDER BY id ASC;");
		if (!$query)
		{
			echo "Erreur durant la requête de getExclusion.\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	/* cree pour CTLG-378 */
	static function getMappingWithId($id)
	{
        $query = pg_query(self::$conn, "SELECT configuration.mapping.name, configuration.mapping.definition FROM configuration.mapping WHERE id=" . $id . ";");
        if (! $query) {
            echo "Erreur durant la requête de getMappingWithId.\n";
            exit();
        }
		$res = pg_fetch_all($query);
		if($res!=null){
			$res = $res[0];
		}
        return $res;
	}
	
	static function getSideTaskPlanifForEveryDayOfWeek($dow)
	{
	    $query = pg_query (self::$conn, "SELECT cron.name, cron.parameter,cron.id,h,m,dow,dom FROM configuration.side_task_cron_line cron
			WHERE ((dow IS NULL AND dom IS NULL)  OR dow=".$dow.") ORDER BY h,m ASC");
	    if (!$query)
	    {
	        echo "Erreur durant la requête de getSideTaskPlanif .\n";
	        exit;
	    }
	    return pg_fetch_all($query);
	}

	/** Supprime la tâche annexe
	 * @param $id id de la tâche annexe a supprimer
	 * @return false si la requête a échoué | resource sinon
	 */
	static function deleteSideTaskPlanif($id)
	{
	    return pg_query (self::$conn, "DELETE FROM configuration.side_task_cron_line WHERE id=".$id);
	}
	
	static function insertSideTask($name, $parameter)
	{
	    return pg_query(self::$conn, "INSERT into configuration.side_task(name, parameter, status,creation_date, modification_date) values ('".$name."','".$parameter."','TO_PROCESS', NOW(),NOW());")or die ('Erreur insertSideTask'. pg_last_error(self::$conn));
	}
	
	static function insertSideTaskDate($m, $h, $day, $jour, $name, $parameter)
	{
	    return pg_query (self::$conn, "INSERT INTO configuration.side_task_cron_line(m,h,dom,mon,dow,name, parameter) VALUES (".$m.",".$h.",".$day.",NULL,".$jour.",'".$name."','".$parameter."') RETURNING id")or die ('Erreur insertSideTaskDate'. pg_last_error(self::$conn));

	}

	/** Compte les tâches annexes
	 * @return int le nombre de tâches trouvées | void si erreur dans la requête
	 */
	static function countSideTasks()
	{
		$query = "SELECT COUNT(*) FROM configuration.side_task";
		$sql = pg_query(self::$conn, $query);
		if (!$sql)
		{
			echo "Erreur durant la requête de countSideTasksConfiguration .\n";
			exit;
		}
		return pg_fetch_all($sql)[0]['count'];
	}
	
	static function getSideTasks($order)
	{
	    $query = pg_query (self::$conn, "SELECT status,t.id, name, parameter, creation_date, modification_date, message, start_time,end_time, total_effective_duration_sec FROM configuration.side_task t
		ORDER BY ".$order);
	    if (!$query)
	    {
	        echo "Erreur durant la requête de getSideTasks .\n";
	        exit;
	    }
	    return pg_fetch_all($query);
	}

	/** Retourne une page des tâches annexes
	 * @param $order ordre souhaité pour l'affichage des données
	 * @param $size nombre de résultats par page
	 * @param $page page à afficher (sert à calculer l'offset)
	 * @return array|false|void selon les tâches annexes trouvées
	 */
	static function getSideTasksPagined($order,$size,$page)
	{
		$offset = ($size*($page-1));
	    $query = pg_query (self::$conn, "SELECT status,t.id, name, parameter, creation_date, modification_date, message, start_time,end_time, total_effective_duration_sec FROM configuration.side_task t
		ORDER BY ".$order." LIMIT ".$size." OFFSET ".$offset);
	    if (!$query)
	    {
	        echo "Erreur durant la requête de getSideTasks .\n";
	        exit;
	    }
	    return pg_fetch_all($query);
	}
	
	static function getAlerts($order)
	{
	    $query = pg_query (self::$conn, "SELECT a.id, level, category, message, b.name as configuration_name, configuration_id,  creation_time, modification_time, status
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

	static function deleteAlert($id)
	{
		pg_query(self::$conn, "DELETE FROM monitoring.alert where id='".$id."';");
	}

	static function getLog($limit,$start_from)
	{
		$query = pg_query (self::$conn, "SELECT * FROM logging.logs ORDER BY id DESC LIMIT ".$limit." OFFSET ".$start_from.";");
		/*if (!$query)
		{
			echo "Erreur durant la requête de getLog .\n";
			exit;
		}*/
		return pg_fetch_all($query);
	}

	static function countLog()
	{
		$query = pg_query(self::$conn,"SELECT COUNT(id) FROM logging.logs");
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

	static function countHarvestConfiguration($str)
	{
		$query = pg_query(self::$conn, $str);
		if (!$query)
		{
			echo "Erreur durant la requête de countHarvestConfiguration .\n";
			echo $str;
			exit;
		}
		return pg_fetch_all($query)[0]['count'];
	}

	static function select($str)
	{
		$query = pg_query(self::$conn, $str);
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

	static function getStatus()
	{
		$query = pg_query(self::$conn, "SELECT * FROM configuration.exemplary_status_configuration ORDER BY code;");
		if (!$query)
		{
			echo "Erreur durant la requête de getStatus .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	static function updateStatus($code, $dispo, $to_harvest, $label)
	{
		pg_query(self::$conn, "UPDATE configuration.exemplary_status_configuration
			SET dispo_flag = '".$dispo."', select_to_harvest = '".$to_harvest."', label = '".$label."'
			WHERE code = '".$code."';");
	}
	static function insertStatus($code, $dispo, $to_harvest, $label)
	{
		pg_query(self::$conn, "INSERT INTO configuration.exemplary_status_configuration
			VALUES('".$code."','".$dispo."','".$to_harvest."','".$label."');");
	}
	static function deleteStatus($code)
	{
		pg_query(self::$conn, "DELETE FROM configuration.exemplary_status_configuration where code='".$code."';");
	}
	
	static function getParcours($id)
	{
		$query = pg_query(self::$conn, "SELECT parcours_code as parcours FROM configuration.harvest_configuration hc JOIN configuration.search_base_parcours_mapping sbpm ON sbpm.search_base_code = hc.search_base_code WHERE hc.id = " . $id . ";");
		if (!$query)
		{
			echo "Erreur durant la requête de getParcours .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	static function accesUpdate($id,$acces)
	{
		pg_query(self::$conn,"DELETE FROM configuration.configuration_profile_mapping where configuration_id=".$id);
		foreach($acces as $a)
		{
			if(!empty($a)){self::insert("INSERT INTO configuration.configuration_profile_mapping VALUES(".$id.",'".$a."')");}
		}

	}
	
	static function updateParcours($parcours, $id)
	{
		pg_query(self::$conn,"DELETE FROM configuration.search_base_parcours_mapping sbpm WHERE exists (select * from configuration.harvest_configuration hc WHERE sbpm.search_base_code = hc.search_base_code AND hc.id = ".$id.")");
		foreach($parcours as $parcour)
		{
			// pg_query(self::$conn,"INSERT INTO configuration.configuration_parcours_mapping VALUES('".$id."','".$parcour."')");
		    pg_query(self::$conn,"insert into configuration.search_base_parcours_mapping(search_base_code, parcours_code) (SELECT search_base_code , '".$parcour."' as parcours_code FROM configuration.harvest_configuration hc where hc.id = ".$id.")");
		}
	}
	
	static function getLogs($niv,$start)
	{
		if($niv!="")
		{
			$query = pg_query(self::$conn,"SELECT * FROM logging.logs WHERE level='".$niv."' 
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week')) ORDER BY date DESC LIMIT 15 OFFSET ".$start*15);
		}
		else
		{
			$query = pg_query(self::$conn,"SELECT * FROM logging.logs WHERE level!='INFO'
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week')) ORDER BY date DESC LIMIT 15 OFFSET ".$start*15);
		}
		return @pg_fetch_all($query); 
	}
	
	static function countLogs($niv)
	{
		if($niv!="")
		{
			$query = pg_query(self::$conn,"SELECT count(*) FROM logging.logs WHERE level='".$niv."' 
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week'))");
		}
		else
		{
			$query = pg_query(self::$conn,"SELECT count(*) FROM logging.logs WHERE level!='INFO'
			AND (date >= date_trunc('week', CURRENT_TIMESTAMP - interval '1 week'))");
		}
		return @pg_fetch_all($query)[0]['count']; 
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
			echo "Erreur durant la requête de getTradConf .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getSetByConf($id)
	{
			$query = pg_query(self::$conn,"SELECT E.property, entity, name, S.id, ignore_case AS case, trim FROM configuration.translation AS T, configuration.translation_rules_set AS S, configuration.entity_properties AS E
				WHERE S.id=translation_rules_set_id AND T.property=E.property AND configuration_id=".$id." ORDER BY entity,property");
		return pg_fetch_all($query);
	}

	static function getCategory()
	{
		$query = pg_query(self::$conn,"SELECT * FROM configuration.translation_category");
		if (!$query)
		{
			echo "Erreur durant la requête de getCategory .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getCategoryBySet($set)
	{
		$query = pg_query(self::$conn,"SELECT DISTINCT C.name FROM configuration.translation_destination D, configuration.translation_category C,
			configuration.translation_rules_set_mapping M, configuration.translation_rule R, configuration.translation_rules_set S
			WHERE category_id=C.id AND D.id=R.destination_id AND R.id=M.translation_rule_id AND S.id=M.translation_rules_set_id
			AND S.name='".$set."'");
		if (!$query)
		{
			echo "Erreur durant la requête de getCategoryBySet .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function updateCategory($data,$cmp)
	{		
		foreach($data as $key => $row)
		{
			$var = str_replace("'","''",$row);
			if(is_numeric($key) and $key>=0)
			{
				$v = str_replace("'","''",$cmp[$key]);
				@pg_query(self::$conn,"UPDATE configuration.translation_category SET name='".$var."' WHERE name='".$v."'");
			}
			else if($row!='')
			{
				pg_query(self::$conn,"INSERT INTO configuration.translation_category(name) VALUES('".$var."')");
			}
		}
	}
	static function deleteCategory($data)
	{		
		foreach($data as $row)
		{
			$var = str_replace("'","''",$row);
			pg_query(self::$conn,"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rule_id in
				(SELECT id FROM configuration.translation_rule WHERE destination_id in
				(SELECT id FROM configuration.translation_destination WHERE category_id=
				(SELECT id FROM configuration.translation_category WHERE name='".$var."')))");
			pg_query(self::$conn,"DELETE FROM configuration.translation_rule WHERE destination_id in
				(SELECT id FROM configuration.translation_destination WHERE category_id=
				(SELECT id FROM configuration.translation_category WHERE name='".$var."'))");
			pg_query(self::$conn,"DELETE FROM configuration.translation_destination WHERE category_id=
				(SELECT id FROM configuration.translation_category WHERE name='".$var."')");
			pg_query(self::$conn,"DELETE FROM configuration.translation_category WHERE name='".$var."'");

		}
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
	
	static function getPredicat($id)
	{
		$query = @pg_query(self::$conn,"SELECT code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate WHERE id=".$id);
		if(!$query) {
			return null;
		}
		return pg_fetch_all($query);
	}

	static function getPredicatByCode($code){
		$query = pg_query(self::$conn,"SELECT id FROM configuration.filter_predicate WHERE code='".$code."'");
		return pg_fetch_all($query)[0]["id"];
	}
	
	static function getPredicats()
	{
		$query = pg_query(self::$conn,"SELECT id,code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate");
		if (!$query)
		{
			echo "Erreur durant la requête de getPredicats .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getPredicatsByEntity($entity)
	{
		$query = pg_query(self::$conn,"SELECT code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate WHERE entity='".$entity."'");
		if (!$query)
		{
			echo "Erreur durant la requête de getPredicatsByEntity .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getConfBySet($set)
	{
		$query = pg_query(self::$conn,"SELECT DISTINCT name FROM configuration.translation T, configuration.harvest_configuration C WHERE T.configuration_id = C.id AND
		translation_rules_set_id = (SELECT id FROM configuration.translation_rules_set WHERE name='".$set."')");
		if (!$query)
		{
			echo "Erreur durant la requête de getConfBySet .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getTreeNode($id)
	{
		$query = pg_query(self::$conn,"SELECT id, filter_predicate_id AS pred, boolean_operator AS operator FROM configuration.filter_rule_tree_node WHERE parent_id=".$id);
		if (!$query)
		{
			return null;
		}
		return pg_fetch_all($query);
	}
	static function getRuleName($id)
	{
		return @pg_fetch_all(pg_query(self::$conn,"SELECT name,filter_rule_tree_node_id AS id  FROM configuration.filter_rule WHERE id=".$id))[0];
	}

	static function getRuleEntity($id)
	{
		return @pg_fetch_all(pg_query(self::$conn,"SELECT entity,filter_rule_tree_node_id AS id  FROM configuration.filter_rule WHERE id=".$id))[0];
	}
	
	static function updateRuleName($name, $id)
	{
		pg_query(self::$conn,"UPDATE configuration.filter_rule SET name='".$name."' WHERE filter_rule_tree_node_id=".$id);
	}
	
	static function getRuleTree($id)
	{
		$query = @pg_query(self::$conn,"SELECT filter_predicate_id AS pred, boolean_operator AS operator FROM configuration.filter_rule_tree_node WHERE id=".$id);
		$d = @pg_fetch_all($query)[0];
		if(!empty($d)){
			if(!empty($d['operator']))
			{
				$data=self::grt($id);
			}
			$data['operator']=$d['operator'];
			$data['pred']=$d['pred'];
			return $data;
		} else {
			return null;
		}
	}
	
	static function grt($id)
	{
		$data=self::getTreeNode($id);
		foreach($data as $k => $d)
		{
			if(!empty($data[$k]['operator']))
			{
				$data[$k]=self::grt($d['id']);
				$data[$k]['operator']=$d['operator'];
			}
		}
		
		return $data;
	}
	
	static function deleteTree($id)
	{
		if($id==null)
		{
			return;
		}
		$ids=pg_fetch_all(pg_query(self::$conn,"SELECT id from configuration.filter_rule_tree_node WHERE parent_id=".$id));
		if($ids!=null){
			self::deleteTree($ids[0]['id']);
			self::deleteTree($ids[1]['id']);
			pg_query(self::$conn,"DELETE FROM configuration.filter_rule_tree_node WHERE parent_id=".$id);
		}
	}
	
	static function insertTreeNode($parent_id,$op,$id=NULL)
	{
		if($id==NULL)
		{
			if($parent_id=='')
			{
				pg_query(self::$conn,"INSERT INTO configuration.filter_rule_tree_node(boolean_operator) 
				VALUES('".$op."')");
			} else {
				pg_query(self::$conn,"INSERT INTO configuration.filter_rule_tree_node(parent_id, boolean_operator) 
				VALUES(".$parent_id.",'".$op."')");
			}
		}
		else
		{
			pg_query(self::$conn,"UPDATE configuration.filter_rule_tree_node SET boolean_operator='".$op."', filter_predicate_id=NULL WHERE id=".$id);
		}
		return pg_fetch_all(pg_query(self::$conn,"SELECT max(id) AS id FROM configuration.filter_rule_tree_node"))[0]['id'];
	}

	static function insertTreeLeaf($parent_id,$predicat,$id=NULL)
	{
		$pred=self::getPredicatByCode($predicat);
		if($parent_id==NULL)
		{
			pg_query(self::$conn,"INSERT INTO configuration.filter_rule_tree_node(filter_predicate_id) 
				VALUES(".$pred.")");
			return pg_fetch_all(pg_query(self::$conn,"SELECT max(id) AS id FROM configuration.filter_rule_tree_node"))[0]['id'];
		}
		else if($id==NULL)
		{
			pg_query(self::$conn,"INSERT INTO configuration.filter_rule_tree_node(parent_id, filter_predicate_id) 
				VALUES(".$parent_id.",".$pred.")");
		}
		else
		{
			pg_query(self::$conn,"UPDATE configuration.filter_rule_tree_node SET boolean_operator=NULL, filter_predicate_id=".$pred." WHERE id=".$id);
		}

	}
	
	static function insertPredicate($property,$function,$value,$code=NULL)
	{
		$id=@pg_fetch_all(pg_query(self::$conn,"SELECT id FROM configuration.filter_predicate WHERE
			property='".$property."' AND function_code='".$function."' AND value_to_compare='".$value."'"))[0]['id'];
		if(empty($id))
		{
			if($code==null)
			{
				pg_query(self::$conn,"INSERT INTO configuration.filter_predicate(entity,property,function_code,value_to_compare) VALUES(
				(SELECT entity FROM configuration.entity_properties WHERE property='".$property."'),'".$property."','".$function."','".$value."')");
				$id=pg_fetch_all(pg_query(self::$conn,"SELECT max(id) FROM configuration.filter_predicate"))[0]['max'];
			}
			else
			{
				pg_query(self::$conn,"INSERT INTO configuration.filter_predicate(code,entity,property,function_code,value_to_compare) VALUES(".$code."
					(SELECT entity FROM configuration.entity_properties WHERE property='".$property."'),'".$property."','".$function."','".$value."')");
				return;
			}
		}
		return $id;
	}
	
	static function iT($data,$id)
	{
		if(empty($data))
		{
			return;
		}
		if($data['operator']!='OPERATION')
		{
			$id=self::insertTreeNode($id,$data['operator']);
			self::iT($data['gauche'],$id);
			self::iT($data['droite'],$id);
		}
		else
		{
			self::insertTreeLeaf($id,$data['predicat']);
		}
	}
	
	static function insertTree($data,$id)
	{
		self::deleteTree($id);
		if(empty($id))
		{
			if($data['operator']!='OPERATION')
			{
				$idR=self::insertTreeNode('',$data['operator']);
				self::iT($data['gauche'],$idR);
				self::iT($data['droite'],$idR);
				$id=$idR;
			}
			else
			{
				$id=self::insertTreeLeaf("",$data['predicat']);
			}
			return $id;
		}
		else
		{
			if($data['operator']!='OPERATION')
			{
				self::insertTreeNode('',$data['operator'],$id);
				self::iT($data['gauche'],$id);
				self::iT($data['droite'],$id);
			}
			else
			{
				self::insertTreeLeaf("",$data['predicat'],$id);
			}
			return null;
		}
	}
	
	static function getFilterRule()
	{
		return pg_fetch_all(pg_query(self::$conn,"SELECT * FROM configuration.filter_rule"));
	}	
	
	static function getFilterByConf($id)
	{
		$query = pg_query(self::$conn,"SELECT F.entity, name, R.id FROM configuration.filter F, configuration.filter_rule R WHERE F.filter_rule_id=R.id AND configuration_id=".$id);
		return pg_fetch_all($query);
	}
	
	static function updateFilterRule($data,$id)
	{
		pg_query(self::$conn,"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=".$id);
		pg_query(self::$conn,"DELETE FROM configuration.translation_rule WHERE id not in 
			(SELECT translation_rule_id FROM configuration.translation_rules_set_mapping)");
		foreach($data as $row)
		{
			$var = str_replace("'","''",$row['rep']);
			$input = str_replace("'","''",$row['input']);
			pg_query(self::$conn,"INSERT INTO configuration.translation_rule(input_value,destination_id) 
				VALUES('".$input."',(SELECT id FROM configuration.translation_destination WHERE value = '".$var."'))");
		}
		$ids=self::getNewRules();
		foreach($ids as $rowid)
		{
			pg_query(self::$conn,"INSERT INTO configuration.translation_rules_set_mapping VALUES(".$id.",".$rowid['id'].")");
		}
	}
	
	static function updatePredicats($data)
	{
		$ids = pg_fetch_all(pg_query(self::$conn,"SELECT id FROM configuration.filter_predicate"));
		foreach($ids as $id)
		{
			if(array_key_exists($id['id'],$data))
			{
				$d=$data[$id['id']];
				pg_query(self::$conn,"UPDATE configuration.filter_predicate SET code='".$d['code']."' , property='".$d['property']."' , 
				entity ='".$d['entity']."' , function_code='".$d['function_code']."' , value_to_compare='".$d['value']."' WHERE id=".$id['id']);
			}
			else
			{
				pg_query(self::$conn,"UPDATE configuration.filter_rule_tree_node SET filter_predicate_id=NULL WHERE filter_predicate_id =".$id['id']);
				pg_query(self::$conn,"DELETE FROM configuration.filter_predicate WHERE id =".$id['id']);
			}
		}
		foreach($data as $k => $d)
		{
			if($k<0)
			{
				pg_query(self::$conn,"INSERT INTO configuration.filter_predicate(code,property,entity,function_code,value_to_compare) VALUES('".$d['code']."', '".$d['property']."', 
				'".$d['entity']."' , '".$d['function_code']."', '".$d['value']."')");
			}
		}
	}
	
	static function getFilterRules()
	{
		$query = pg_query(self::$conn,"SELECT id, name, entity FROM configuration.filter_rule");
		return pg_fetch_all($query);
	}
	
	static function setRuleTreeRoot($id,$idR)
	{
		pg_query(self::$conn,"UPDATE configuration.filter_rule SET filter_rule_tree_node_id=".$idR." WHERE id=".$id);
	}

	static function updateRuleTree($data)
	{
		$ids = pg_fetch_all(pg_query(self::$conn,"SELECT id FROM configuration.filter_predicate"));
		foreach($ids as $id)
		{
			if(array_key_exists($id['id'],$data))
			{
				$d=$data[$id['id']];
				pg_query(self::$conn,"UPDATE configuration.filter_predicate SET code='".$d['code']."' , property='".$d['property']."' , entity = 
					(SELECT entity FROM configuration.entity_properties WHERE property='".$d['code']."') , function_code='".$d['function_code']."' 
					, value_to_compare='".$d['value']."' WHERE id=".$id['id']);
			}
			else
			{
				pg_query(self::$conn,"UPDATE configuration.filter_rule_tree_node SET filter_predicate_id=NULL WHERE filter_predicate_id =".$id['id']);
				pg_query(self::$conn,"DELETE FROM configuration.filter_predicate WHERE id =".$id['id']);
			}
		}
		foreach($data as $k => $d)
		{
			if($k<0)
			{
				pg_query(self::$conn,"INSERT INTO configuration.filter_predicate(code,property,entity,function_code,value_to_compare) VALUES('".$d['code']."', '".$d['property']."', 
					(SELECT entity FROM configuration.entity_properties WHERE property='".$d['code']."'), '".$d['function_code']."', '".$d['value']."')");
			}
		}
	}

	static function updateFilterRules($data)
	{
		$ids = pg_fetch_all(pg_query(self::$conn,"SELECT id FROM configuration.filter_rule"));
		foreach($ids as $id)
		{
			if(array_key_exists($id['id'],$data))
			{
				$d=$data[$id['id']];		
				pg_query(self::$conn,"UPDATE configuration.filter_predicate SET name='".$d['name']."', entity='".$d['entity']."' WHERE id=".$id['id']);
			}
			else
			{
				pg_query(self::$conn,"DELETE FROM configuration.filter WHERE filter_rule_id =".$id['id']);
				pg_query(self::$conn,"DELETE FROM configuration.filter_rule WHERE id =".$id['id']);
			}
		}
		foreach($data as $k => $d)
		{
			if($k<0)
			{
				pg_query(self::$conn,"INSERT INTO configuration.filter_rule(name,entity) VALUES('".$d['name']."', '".$d['entity']."')");
			}
		}
	}

	static function updateFilterConfiguration($id,$donnee)
	{
		$entries = pg_fetch_all(pg_query(self::$conn,"SELECT id,filter_rule_id,configuration_id FROM configuration.filter"));
		$used_filter = array();
		$used_id = array();
		$incr_id=1;
		if(!empty($entries)){
			foreach($entries as $entry)
			{
				if($entry['configuration_id']==$id)
				{
					$delete = true;
					if(!empty($donnee)){
						foreach($donnee as $d)
						{
							if($d['rule']==$entry['filter_rule_id'])
							{
								$delete = false;
							}
						}
					}
					if($delete == true)
					{
						pg_query(self::$conn,"DELETE FROM configuration.filter WHERE filter_rule_id =".$entry['filter_rule_id']." AND configuration_id=".$id);
					} else {
						array_push($used_id,$entry['id']);
					}
					array_push($used_filter,$entry['filter_rule_id']);
				} else {
					array_push($used_id,$entry['id']);
				}
			}
		}
		if(!empty($donnee)){
			foreach($donnee as $d)
			{
				if(!in_array($d['rule'],$used_filter))
				{
					while(in_array($incr_id,$used_id))
					{
						$incr_id++;
					}
					pg_query(self::$conn,"INSERT INTO configuration.filter VALUES('".$incr_id."', '".$id."', '".$d['entity']."', '".$d['rule']."')");
					array_push($used_filter,$incr_id);
					$incr_id++;
				}
			}
		}
	}
	
	static function getFilterCode()
	{
		$query=pg_query(self::$conn,"SELECT * FROM configuration.filter_function");
		if (!$query)
		{
			echo "Erreur durant la requête de getFilterCode .\n";
			exit;
		}
		return pg_fetch_all($query);
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
}
?>


