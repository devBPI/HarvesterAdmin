<?php

include_once("../PDO/Gateway.php");
class Moisson
{

    /**
     * @param $id id de la configuration
     * @return bool|null
     */
    static function insertMoisson($id)
    {
        return Gateway::insertMoissonWithStatus($id, 'TO_HARVEST');
    }

    /**
     * @param $id id de la configuration
     * @param $status status de la moisson
     * @return bool|void
     */
    static function insertMoissonWithStatus($id, $status)
    {
        return pg_query(Gateway::getConnexion(), "INSERT into configuration.harvest_task( configuration_id, status,creation_date, modification_date) values (".$id.",'".$status."', NOW(),NOW());")or die ('Erreur insertMoisson'. pg_last_error(Gateway::getConnexion()));
    }

    /** Supprime une moisson de l'historique des moissons
     * @param $id id de la moisson
     * @return bool|void
     */
    static function deleteMoisson($id)
    {
        return pg_query(Gateway::getConnexion(), "DELETE FROM configuration.harvest_task WHERE id=".$id);;
    }

    /** Supprime une moisson du planning
     * @param $id
     * @return false|resource
     */
    static function deleteMoissonPlanning($id)
    {
        return pg_query (Gateway::getConnexion(), "DELETE FROM configuration.harvest_task_cron_line WHERE id=".$id);
    }

    static function getMoissonPlanifForEveryDayOfWeek($dow)
    {
        $query = pg_query (Gateway::getConnexion(), "SELECT cron.configuration_id,cron.id,h,m,dow,dom,name FROM configuration.harvest_task_cron_line cron, configuration.harvest_configuration harvest
			WHERE ((dow IS NULL AND dom IS NULL) OR dow=".$dow.") and cron.configuration_id = harvest.id ORDER BY h,m ASC");
        if (!$query)
        {
            echo "Erreur durant la requête de getMoisson .\n";
            exit;
        }
        return pg_fetch_all($query);
    }
    /** Retourne le dernier status de la moisson
     * @param $id l'id de la moisson dont on souhaite le status
     * @return chaîne de caractère correspondant au status | void si échec de la requête
     */
    static function getMoissonStatus($id)
    {
        $query = pg_query (Gateway::getConnexion(), "SELECT status FROM configuration.harvest_task WHERE id = ".$id."ORDER BY modification_date DESC");
        if (!$query)
        {
            echo "Erreur durant la requête de getMoissonStatus .\n";
            exit;
        }
        return pg_fetch_all($query)[0]['status'];
    }

    /** Compte le nombre de moissonnages
     * @return int le nombre de moissonnages trouvés | void si erreur dans la requête
     */
    static function countHarvests()
    {
        $sql = pg_query(Gateway::getConnexion(), "SELECT COUNT(*) FROM configuration.harvest_task");
        if (!$sql)
        {
            echo "Erreur durant la requête de countHarvestConfiguration .\n";
            exit;
        }
        return pg_fetch_all($sql)[0]['count'];
    }

    static function getTasks($order)
    {
        $query = pg_query (Gateway::getConnexion(),
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

    /** Renvoie une page de tâches de moissonnages
     * @param $order ordre d'affichage souhaité
     * @param $size nombre de résultats par page
     * @param $page page à récupérer (sert à calculer l'offset correspondant)
     * @return array|false|void selon la réussite de la requête
     */
    static function getTasksPagined($order, $size=20, $page=1)
    {
        $offset = ($size*($page-1));

        $query = pg_query (Gateway::getConnexion(),
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

    static function getProgress($id)
    {
        $status = pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT status FROM configuration.harvest_task where id=".$id))[0]['status'];

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
        return @pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT COALESCE(g.advancement, 0) as advancement FROM configuration.harvest_task t LEFT JOIN configuration.grab_task g on g.harvest_task_id = t.id WHERE t.id='".$harvestTaskId."' order by g.modification_date DESC"))[0]['advancement'];
    }

    static function getImportAdvancement($harvestTaskId)
    {
        return @pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT COALESCE(p.advancement, 0) as advancement FROM configuration.harvest_task t LEFT JOIN configuration.import_task p on p.harvest_task_id_list = ''||t.id WHERE t.id='".$harvestTaskId."' order by p.modification_date DESC"))[0]['advancement'];
    }

    static function getIndexAdvancement($harvestTaskId)
    {
        return @pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT COALESCE(d.advancement, 0) as advancement FROM configuration.harvest_task t LEFT JOIN configuration.index_task d on d.harvest_task_id_list = ''||t.id WHERE t.id='".$harvestTaskId."' order by d.modification_date DESC"))[0]['advancement'];
    }

    static function reprise($id)
    {
        $status = pg_query(Gateway::getConnexion(),"SELECT status FROM configuration.harvest_task WHERE id='".$id."'");
        $status = pg_fetch_all($status)[0];
        if($status['status']=="GRAB_ERROR")
        {
            pg_query(Gateway::getConnexion(),"UPDATE configuration.harvest_task SET status = 'TO_HARVEST', message = 'Reprise Manuelle sur erreur' WHERE id='".$id."'");
        }
        if($status['status']=="IMPORT_ERROR")
        {
            pg_query(Gateway::getConnexion(),"UPDATE configuration.harvest_task SET status = 'GRABBED', message = 'Reprise Manuelle sur erreur' WHERE id='".$id."'");
        }
        if($status['status']=="INDEX_ERROR")
        {
            pg_query(Gateway::getConnexion(),"UPDATE configuration.harvest_task SET status = 'IMPORTED', message = 'Reprise Manuelle sur erreur' WHERE id='".$id."'");
        }
    }

    static function insertDate($m, $h, $day, $jour, $id)
    {
        return pg_query (Gateway::getConnexion(), "INSERT INTO configuration.harvest_task_cron_line(m,h,dom,mon,dow,configuration_id) VALUES (".$m.",".$h.",".$day.",NULL,".$jour.",".$id.") RETURNING id")or die ('Erreur insertDate'. pg_last_error(Gateway::getConnexion()));

    }

    static function getHarvestDate($id)
    {
        $query = pg_query(Gateway::getConnexion(),"SELECT modification_date FROM configuration.harvest_task where configuration_id=".$id." AND status='INDEXED' ORDER BY modification_date DESC");
        if (!$query)
        {
            echo "Erreur durant la requête de getHarvestDate .\n";
            exit;
        }
        return pg_fetch_all($query)[0]['modification_date'];
    }

    static function reloadMoisson($id) {
        $suppr_ok = self::deleteMoisson($id);
        if (self::deleteMoisson($id)) return self::insertMoisson($id);
        else return $suppr_ok;
    }

	/** Récupère la liste des statuts de moissons
	 * @return array Liste des statuts possibles pour une moisson
	 */
	static function getAllStatus() {
		return pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT DISTINCT status FROM configuration.harvest_task ORDER BY 1")
		);
	}

	static function getTasksForCartridge($confid)
	{
		$query = pg_query (Gateway::getConnexion(), "SELECT * FROM configuration.harvest_task WHERE configuration_id=" . $confid . " ORDER BY modification_date DESC LIMIT 3;");
		if (!$query)
		{
			echo "Erreur durant la requête de getTasksForCartridge .\n";
			exit;
		}

		return pg_fetch_all($query);
	}

	static function getPlanifsForCartridge($confid)
	{
		$query = pg_query (Gateway::getConnexion(), "SELECT h,m,dow FROM configuration.harvest_task_cron_line WHERE configuration_id=" . $confid . " ORDER BY h,m ASC");
		if (!$query)
		{
			echo "Erreur durant la requête de getPlanifsForCartridge .\n";
			exit;
		}

		return pg_fetch_all($query);
	}
}