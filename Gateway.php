<?php
class Gateway
{
	private static $conn;

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

  static function getAllTranslations() // ancien code sans configuration_id en parametre pour les avoir tous (notamment appele dans ajoutConfig.php, voir CTLG-356)
	{
		$query = pg_query (self::$conn, "SELECT name, T.id FROM configuration.translation T, configuration.translation_rules_set R WHERE T.translation_rules_set_id=R.id");
		return pg_fetch_all($query);
	}
  
	static function getTranslation($id)
	{
		$query = pg_query (self::$conn, "SELECT name, T.id FROM configuration.translation T, configuration.translation_rules_set R WHERE T.translation_rules_set_id=R.id AND configuration_id=".$id);
		return pg_fetch_all($query);
	}

	static function getConfiguration($table, $id)
	{
		 $query = pg_query (self::$conn, "SELECT configuration.harvest_configuration.name, configuration.mapping.definition FROM configuration.harvest_configuration
LEFT JOIN configuration.mapping ON configuration.harvest_configuration.mapping_id = configuration.mapping.id
where configuration.harvest_configuration.".$table."_id=".$id.";");
		/*if (!$query)
		{
			echo "Erreur durant la requête de getConfiguration.\n";
			exit;
		}*/
		return  pg_fetch_all($query)[0];
	}

	static function getConfigurationName($id)
	{
		return @pg_fetch_all(pg_query(self::$conn,"SELECT name FROM configuration.harvest_configuration WHERE id=".$id))[0];
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
	
	static function getNomConfig($table, $id)
	{
		$query = pg_query (self::$conn, "SELECT * FROM configuration.harvest_configuration where ".$table."_id=".$id." ORDER BY name ASC;");
		return pg_fetch_all($query);
	}

	static function getMoissonPlanifForEveryDayOfWeek($dow)
	{
		$query = pg_query (self::$conn, "SELECT cron.configuration_id,cron.id,h,m,dow,dom,name FROM configuration.harvest_task_cron_line cron, configuration.harvest_configuration harvest
			WHERE ((dow IS NULL AND dom IS NULL) OR dow=".$dow.") and cron.configuration_id = harvest.id ORDER BY h,m ASC");
		if (!$query)
		{
			echo "Erreur durant la requête de getMoisson .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	static function deleteMoisson($id)
	{
		return pg_query (self::$conn, "DELETE FROM configuration.harvest_task_cron_line WHERE id=".$id);
	}

	static function insertMoisson($id)
	{
	    return insertMoissonWithStatus($id, 'TO_HARVEST');
	}
	
	static function insertMoissonWithStatus($id, $status)
	{
	    return pg_query(self::$conn, "INSERT into configuration.harvest_task( configuration_id, status,creation_date, modification_date) values (".$id.",'".$status."', NOW(),NOW());")or die ('Erreur insertMoisson'. pg_last_error(self::$conn));
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
	
	static function deleteSideTaskPlanif($id)
	{
	    return pg_query (self::$conn, "DELETE FROM configuration.side_task_cron_line WHERE id=".$id);
	}
	
	static function insertSideTask($name, $parameter)
	{
	    return pg_query(self::$conn, "INSERT into configuration.side_task(name, parameter, status,creation_date, modification_date) values ('".$name."','".$parameter."','TO_PROCESS', NOW(),NOW());")or die ('Erreur insertSideTask'. pg_last_error(self::$conn));
	}

	static function getIdFromCode($code)
	{
		$query = pg_query(self::$conn, "SELECT id FROM configuration.harvest_configuration WHERE code='".$code."'");
		if (!$query)
		{
			echo "Erreur durant la requête de getIdFromCode .\n";
			exit;
		}
		return pg_fetch_all($query)[0]['id'];
	}
	

	static function insertDate($m, $h, $day, $jour, $id)
	{
		return pg_query (self::$conn, "INSERT INTO configuration.harvest_task_cron_line(m,h,dom,mon,dow,configuration_id) VALUES (".$m.",".$h.",".$day.",NULL,".$jour.",".$id.") RETURNING id")or die ('Erreur insertDate'. pg_last_error(self::$conn));

	}
	
	static function insertSideTaskDate($m, $h, $day, $jour, $name, $parameter)
	{
	    return pg_query (self::$conn, "INSERT INTO configuration.side_task_cron_line(m,h,dom,mon,dow,name, parameter) VALUES (".$m.",".$h.",".$day.",NULL,".$jour.",'".$name."','".$parameter."') RETURNING id")or die ('Erreur insertSideTaskDate'. pg_last_error(self::$conn));
	    
	}
	
	static function getConfigCodes()
	{
		$query = pg_query (self::$conn, "SELECT code FROM configuration.harvest_configuration");
		if (!$query)
		{
			echo "Erreur durant la requête de getConfigCodes .\n";
			exit;
		}
		$resultats = pg_fetch_all($query);
		foreach($resultats as $resultat){
			$res[] = $resultat['code'];
		}
		return $res;
	}
	
	static function getMoissonStatus($id)
	{
		$query = pg_query (self::$conn, "SELECT status FROM configuration.harvest_task WHERE id = ".$id."ORDER BY modification_date DESC");
		if (!$query)
		{
			echo "Erreur durant la requête de getMoissonStatus .\n";
			exit;
		}
		return pg_fetch_all($query)[0]['status'];
	}

	static function getTasks($order)
	{
		$query = pg_query (self::$conn, 
              "SELECT 
                	t1.status,
                	t1.id,
                	t1.configuration_id,creation_date,modification_date,
                	name,differential,message,expected_notices_number,
                	notices_number,start_time,end_time, total_effective_duration_sec,
                	not EXISTS(
                			select 1 
                			from configuration.harvest_task t2 
                			where t2.id <> t1.id 
                			and t2.configuration_id = t1.configuration_id 
                			and t2.status = 'INDEXED' 
                			and t2.modification_date > t1.modification_date
                	) as has_no_more_recent_indexed
                	
                FROM configuration.harvest_task t1
                LEFT JOIN configuration.harvest_configuration h on t1.configuration_id=h.id
		ORDER BY ".$order);
		if (!$query)
		{
			echo "Erreur durant la requête de getTasks .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getTasksPagined($order, $size=20, $page=1)
	{
		$offset = ($size*($page-1));

		$query = pg_query (self::$conn, 
		"SELECT 
			t1.status,
			t1.id,
			t1.configuration_id,creation_date,modification_date,
			name,differential,message,expected_notices_number,
			notices_number,start_time,end_time, total_effective_duration_sec,
			not EXISTS(
				select 1 
				from configuration.harvest_task t2 
				where t2.id <> t1.id 
				and t2.configuration_id = t1.configuration_id 
				and t2.status = 'INDEXED' 
				and t2.modification_date > t1.modification_date
			) as has_no_more_recent_indexed

		FROM configuration.harvest_task t1
		LEFT JOIN configuration.harvest_configuration h on t1.configuration_id=h.id
		ORDER BY ".$order.
		" LIMIT ".$size.
		" OFFSET ".$offset);
		if (!$query)
		{
			echo "Erreur durant la requête de getTasks .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	
	static function getTasksForCartridge($confid)
	{
	   $query = pg_query (self::$conn, "SELECT * FROM configuration.harvest_task WHERE configuration_id=" . $confid . " ORDER BY modification_date DESC LIMIT 3;");
	   if (!$query)
	   {
	       echo "Erreur durant la requête de getTasksForCartridge .\n";
	       exit;
	   }
	    
	   return pg_fetch_all($query);
	}
	
	
	static function getPlanifsForCartridge($confid)
	{
	    $query = pg_query (self::$conn, "SELECT h,m,dow FROM configuration.harvest_task_cron_line WHERE configuration_id=" . $confid . " ORDER BY h,m ASC");
	    if (!$query)
	    {
	        echo "Erreur durant la requête de getPlanifsForCartridge .\n";
	        exit;
	    }
	    
	    return pg_fetch_all($query);
	}
	

	static function countHarvests()
	{
		$query = "SELECT COUNT(*) FROM configuration.harvest_task";
		$sql = pg_query(self::$conn, $query);
		if (!$sql)
		{
			echo "Erreur durant la requête de countHarvestConfiguration .\n";
			echo $str;
			exit;
		}
		return pg_fetch_all($sql)[0]['count'];
	}

	static function countSideTasks()
	{
		$query = "SELECT COUNT(*) FROM configuration.side_task";
		$sql = pg_query(self::$conn, $query);
		if (!$sql)
		{
			echo "Erreur durant la requête de countSideTasksConfiguration .\n";
			echo $str;
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

	static function getHarvestConfigurationDifferential($id)
	{
		$query = pg_query(self::$conn, "SELECT differential,name FROM configuration.harvest_configuration where configuration.harvest_configuration.id=".$id);
		if (!$query)
		{
			echo "Erreur durant la requête de getHarvestConfigurationDifferential .\n";
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

	static function getHarvestDate($id)
	{
		$query = pg_query(self::$conn,"SELECT modification_date FROM configuration.harvest_task where configuration_id=".$id." AND status='INDEXED' ORDER BY modification_date DESC");
		if (!$query)
		{
			echo "Erreur durant la requête de getHarvestDate .\n";
			exit;
		}
		return pg_fetch_all($query)[0]['modification_date'];
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

	static function getHarvestConfiguration()
	{
		return self::select("SELECT id,name FROM configuration.harvest_configuration ORDER BY id ASC;");
	}
	static function getHarvestConfigurationP()
	{
		return self::select("SELECT h.id,name,(Select count(*) 
		FROM configuration.harvest_task_cron_line where configuration_id=h.id)moissons 
		FROM configuration.harvest_configuration h ORDER BY id ASC;");
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
	static function getProfile()
	{
		$query = pg_query(self::$conn, "SELECT m.configuration_id, b.name as public_name, p.description
	FROM configuration.configuration_profile_mapping m
	JOIN configuration.user_profile p on M.profile_code = p.code
	JOIN configuration.harvest_configuration h on h.id = m.configuration_id
    LEFT JOIN configuration.search_base b on b.code = h.search_base_code;");
		if (!$query)
		{
			echo "Erreur durant la requête de getProfile .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	
	static function getInfoConfig($id)
	{
		$query = pg_query(self::$conn, "SELECT
		hc.id,
		hc.code as code, 
		hc.name AS name,
		b.name AS public_name,
		hc.business_base_prefix AS business_base_prefix,
		hc.additional_configuration_of AS additional_configuration_of,
		hc.grab_configuration_id AS grab_configuration_id,
		hc.mapping_id AS mapping_id,
		hc.updated_tables AS updated_tables,
		hc.differential AS differential,
		hc.public_url AS public_url,
		hc.note AS note,
		hc.default_document_type AS default_document_type,
		hgc.grabber_id AS grabber_id,
		hgc.url AS url,
		hgc.url_addition AS url_addition,
		hgc.url_set AS url_set,
		hgc.csv_separator AS csv_separator,
		hgc.max_attempts_number AS max_attempts_number,
		hgc.timeout_sec AS timeout_sec,
		(SELECT name FROM configuration.grabber where configuration.grabber.id=grabber_id) AS grabber_name,
		(SELECT name FROM configuration.mapping where configuration.mapping.id=mapping_id) AS mapping_name
		FROM configuration.harvest_configuration AS hc
		LEFT JOIN configuration.search_base b on b.code = hc.search_base_code
		INNER JOIN configuration.harvest_grab_configuration AS hgc ON hc.grab_configuration_id = hgc.id
		WHERE hc.id = " . $id . ";");
		if (!$query)
		{
			echo "Erreur durant la requête de getInfoConfig .\n";
			exit;
		}
		$data = pg_fetch_all($query)[0];
		$data['profile'] = self::getProfileConfig($id);
		return $data;
	}
	
	static function getProfileConfig($id)
	{
		$query = pg_query(self::$conn, "SELECT p.description,p.code 
			FROM configuration.configuration_access_mapping m, configuration.user_profile p
			WHERE p.code=m.configuration_code AND m.configuration_code=".$id);
		return pg_fetch_all($query);
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

	static function getProgress($id)
	{
		$status = pg_fetch_all(pg_query(self::$conn,"SELECT status FROM configuration.harvest_task where id=".$id))[0]['status'];
		
		$grabadv = self::getGrabAdvancement($id);
		$impradv = self::getImportAdvancement($id);
		$indxadv = self::getIndexAdvancement($id);

		$advdetails = ceil($grabadv)."-".ceil($impradv)."-".ceil($indxadv);

		//var_dump($advdetails);

		$result = 0;

		if($status=='TO_HARVEST') $result = 0;
		else if(preg_match('/(GRAB)/',$status))	// GRAB_PENDING ou GRAB_ERROR	
		{
			$result = (40*$grabadv)/100;
		}
		else if($status=='GRABBED') $result = 40;
		else if(preg_match('/(IMPORT)/',$status)) // IMPORT_PENDING ou IMPORT_ERROR		
		{
			$result = (40 + ((30*$impradv)/100));
		}
		else if($status=='IMPORTED') $result = 70;
		else if(preg_match('/(INDEX)/',$status)) // INDEX_PENDING ou INDEX_ERROR		
		{
			$result = (70 + ((30*$indxadv)/100));
		}
		else if($status=='INDEXED') $result = 100;
	
	
		return ceil($result);
	}


	static function getGrabAdvancement($harvestTaskId)
	{
		return @pg_fetch_all(pg_query(self::$conn,"SELECT COALESCE(g.advancement, 0) as advancement FROM configuration.harvest_task t LEFT JOIN configuration.grab_task g on g.harvest_task_id = t.id WHERE t.id='".$harvestTaskId."' order by g.modification_date DESC"))[0]['advancement'];
	}

	static function getImportAdvancement($harvestTaskId)
	{
		return @pg_fetch_all(pg_query(self::$conn,"SELECT COALESCE(p.advancement, 0) as advancement FROM configuration.harvest_task t LEFT JOIN configuration.import_task p on p.harvest_task_id_list = ''||t.id WHERE t.id='".$harvestTaskId."' order by p.modification_date DESC"))[0]['advancement'];
	}

	static function getIndexAdvancement($harvestTaskId)
	{
		return @pg_fetch_all(pg_query(self::$conn,"SELECT COALESCE(d.advancement, 0) as advancement FROM configuration.harvest_task t LEFT JOIN configuration.index_task d on d.harvest_task_id_list = ''||t.id WHERE t.id='".$harvestTaskId."' order by d.modification_date DESC"))[0]['advancement'];
	}




	
	static function reprise($id)
	{
		$status = pg_query(self::$conn,"SELECT status FROM configuration.harvest_task WHERE id='".$id."'");
		$status = pg_fetch_all($status)[0];
		if($status['status']=="GRAB_ERROR")
		{
			pg_query(self::$conn,"UPDATE configuration.harvest_task SET status = 'TO_HARVEST', message = 'Reprise Manuelle sur erreur' WHERE id='".$id."'"); 
		}
		if($status['status']=="IMPORT_ERROR")
		{
				pg_query(self::$conn,"UPDATE configuration.harvest_task SET status = 'GRABBED', message = 'Reprise Manuelle sur erreur' WHERE id='".$id."'");
		}
		if($status['status']=="INDEX_ERROR")
		{
				pg_query(self::$conn,"UPDATE configuration.harvest_task SET status = 'IMPORTED', message = 'Reprise Manuelle sur erreur' WHERE id='".$id."'");
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
	
	static function updateTrad($data,$id)
	{
		pg_query(self::$conn,"DELETE FROM configuration.translation WHERE configuration_id=".$id);
		foreach($data['lang'] as $value)
		{
			if(isset($value['ignore']))
			{
				pg_query(self::$conn,"INSERT INTO configuration.translation(configuration_id, field_set, property, input_value, replacement_value,ignore_case) VALUES(".$id.",'LANGUE','notice.language','".$value['input']."','".$value['rep']."','true')");
			}
			else
			{
				pg_query(self::$conn,"INSERT INTO configuration.translation(configuration_id, field_set, property, input_value, replacement_value) VALUES(".$id.",'LANGUE','notice.language','".$value['input']."','".$value['rep']."')");
			}
		}
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
	static function getRulesSet()
	{
		$query = pg_query(self::$conn,"SELECT * FROM configuration.translation_rules_set");
		if (!$query)
		{
			echo "Erreur durant la requête de getRulesSet .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	static function getTranslationCategory()
	{
		$query = pg_query(self::$conn,"SELECT name FROM configuration.translation_category");
		if (!$query)
		{
			echo "Erreur durant la requête de getTranslationCategory .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	static function getDestination($category)
	{
		$query = pg_query(self::$conn,"SELECT value, id FROM configuration.translation_destination WHERE category_id=
			(SELECT id FROM configuration.translation_category WHERE name='".$category."') ORDER BY value");
		return pg_fetch_all($query);
		 
	}
	static function updateDestination($data,$cmp,$category)
	{		
		foreach($data as $key => $row)
		{
			$var = str_replace("'","''",$row);
			if(is_numeric($key) and $key>=0)
			{
				$v = str_replace("'","''",$cmp[$key]);
				@pg_query(self::$conn,"UPDATE configuration.translation_destination SET value='".$var."' WHERE value='".$v."'");
			}
			else if($row!='')
			{
				@pg_query(self::$conn,"INSERT INTO configuration.translation_destination(value,category_id) 
					VALUES('".$var."',(SELECT id FROM configuration.translation_category WHERE name='".$category."'))");
			}
		}
	}
	
	static function deleteDestination($data)
	{		
		foreach($data as $row)
		{
			$var = str_replace("'","''",$row);
			pg_query(self::$conn,"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rule_id in 
				(SELECT id FROM configuration.translation_rule WHERE destination_id = (SELECT id FROM configuration.translation_destination WHERE value='".$var."'))");
			pg_query(self::$conn,"DELETE FROM configuration.translation_rule WHERE destination_id = (SELECT id FROM configuration.translation_destination WHERE value='".$var."')");
			pg_query(self::$conn,"DELETE FROM configuration.translation_destination WHERE value='".$var."'");

		}
	}
	
	static function getTrads($name)
	{
		$query = pg_query(self::$conn,"select r.input_value AS input, d.value AS rep
			from configuration.translation_rules_set s
			join configuration.translation_rules_set_mapping m on m.translation_rules_set_id = s.id
			join configuration.translation_rule r on m.translation_rule_id = r.id
			join configuration.translation_destination d on d.id = r.destination_id
			where s.name = '".$name."'");
		if (!$query)
		{
			echo "Erreur durant la requête de getTrads .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	static function updateTranslationRule($data,$name)
	{
		$id = self::getTranslationSetId($name);
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
	static function getTranslationSetId($name)
	{
		$query = pg_query(self::$conn,"SELECT id from configuration.translation_rules_set WHERE name='".$name."'");
		if (!$query)
		{
			echo "Erreur durant la requête de getTranslationSetId .\n";
			exit;
		}
		return pg_fetch_all($query)[0]['id'];
	}

	static function getNewRules()
	{
		$query = pg_query(self::$conn,"SELECT id FROM configuration.translation_rule WHERE id not in 
			(SELECT translation_rule_id FROM configuration.translation_rules_set_mapping)");
		if (!$query)
		{
			echo "Erreur durant la requête de getNewRules .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
	static function updateTranslationConfiguration($id,$data)
	{
		pg_query(self::$conn,"DELETE FROM configuration.translation WHERE configuration_id=".$id);
		foreach($data as $value)
		{
			if(isset($value['property']) and isset($value['set']))
			{
				$q=
				pg_query(self::$conn,"INSERT INTO configuration.translation(configuration_id,property,translation_rules_set_id,ignore_case,trim) 
				VALUES(".$id.",'".$value['property']."',".$value['set'].",".((isset($value['case']))?'true':'false').",".((isset($value['trim']))?'true':'false').")");				
			}
		}
	}
	static function updateRulesSet($data,$cmp)
	{		
		foreach($data as $key => $row)
		{
			$var = str_replace("'","''",$row);
			if(is_numeric($key) and $key>=0)
			{
				$v = str_replace("'","''",$cmp[$key]);
				@pg_query(self::$conn,"UPDATE configuration.translation_rules_set SET name='".$var."' WHERE name='".$v."'");
			}
			else if($row!='')
			{
				pg_query(self::$conn,"INSERT INTO configuration.translation_rules_set(name) VALUES('".$var."')");
			}
		}
	}
	static function deleteRulesSet($data)
	{		
		foreach($data as $row)
		{
			$var = str_replace("'","''",$row);
			pg_query(self::$conn,"DELETE FROM configuration.translation_rule WHERE id=
				(SELECT translation_rule_id FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=
					(SELECT id FROM configuration.translation_rules_set WHERE name='".$var."'))");
			pg_query(self::$conn,"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=
				(SELECT id FROM configuration.translation_rules_set WHERE name='".$var."')");
			pg_query(self::$conn,"DELETE FROM configuration.translation_rules_set WHERE name='".$var."'");

		}
	}
	
	static function getAllDestination()
	{
		$query = pg_query(self::$conn,"SELECT * FROM configuration.translation_destination");
		if (!$query)
		{
			echo "Erreur durant la requête de getAllDestination .\n";
			exit;
		}					
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
		$query = pg_query(self::$conn,"SELECT code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate WHERE id=".$id);
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
	
	static function getConfigurationsWithFileToUpload()
	{
	    // chargement des config avec fichier a uploader ( ATTENTION pour multiple)
	    $query = pg_query(self::$conn, "select c.id, c.name, b.name as public_name, t.name as grabber_name
                                            from configuration.harvest_configuration c
                                            left join configuration.search_base b on b.code = c.search_base_code
                                            join configuration.harvest_grab_configuration g on g.id = c.grab_configuration_id
                                            join configuration.grabber t on t.id = g.grabber_id
											where t.name = 'single_csv_grabber'
                                            order by c.name;");
	    if (! $query) {
	        echo "Erreur durant la requête de getConfigurationsWithFileToUpload .\n";
	        exit();
	    }
	    return pg_fetch_all($query);
	}

	static function getConfigurationsWithFilesToUpload()
	{
	    // chargement des config avec fichier a uploader ( ATTENTION pour multiple)
	    $query = pg_query(self::$conn, "select c.id, c.name, b.name as public_name, t.name as grabber_name
                                            from configuration.harvest_configuration c
                                            left join configuration.search_base b on b.code = c.search_base_code
                                            join configuration.harvest_grab_configuration g on g.id = c.grab_configuration_id
                                            join configuration.grabber t on t.id = g.grabber_id
											where t.name = 'multiple_csv_grabber'
                                            order by c.name;");
	    if (! $query) {
	        echo "Erreur durant la requête de getConfigurationsWithFilesToUpload .\n";
	        exit();
	    }
	    return pg_fetch_all($query);
	}
	
	static function getConfigurationsWithoutFileToUpload()
	{
	    // chargement des config sans fichier a uploader
	    $query = pg_query(self::$conn, "select c.id, c.name, b.name as public_name, t.name as grabber_name
                                            from configuration.harvest_configuration c
                                            left join configuration.search_base b on b.code = c.search_base_code
                                            join configuration.harvest_grab_configuration g on g.id = c.grab_configuration_id
                                            join configuration.grabber t on t.id = g.grabber_id
                                            where 1=1 order by c.name;"); 
	                                       // Remarque : on veut pouvoir lancer des moissons kbart a nouveau meme sans nouveau fichier
	                                       // AND t.name not in ('single_csv_grabber', 'multiple_csv_grabber')
 	    if (! $query) {
	        echo "Erreur durant la requête de getConfigurationsWithoutFileToUpload .\n";
	        exit();
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


