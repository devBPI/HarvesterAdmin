<?php

include_once("../PDO/Gateway.php");
class Configuration
{

    static function getConfiguration($table, $id)
    {
        $query = pg_query (Gateway::getConnexion(), "SELECT configuration.harvest_configuration.name, configuration.mapping.definition FROM configuration.harvest_configuration
LEFT JOIN configuration.mapping ON configuration.harvest_configuration.mapping_id = configuration.mapping.id
where configuration.harvest_configuration.".$table."_id=".$id.";");
        /*if (!$query)
        {
            echo "Erreur durant la requête de getConfiguration.\n";
            exit;
        }*/
        return  pg_fetch_all($query)[0];
    }

    /** Retourne le nom de la configuration
     * @param $id L'identifiant de la configuration
     * @return null si le nom n'a pas été trouvé / tab['name']: où name vaut le nom de la configuration sinon
     */
    static function getConfigurationName($id)
    {
        return @pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT name FROM configuration.harvest_configuration WHERE id=".$id))[0];
    }

    static function getNomConfig($table, $id)
    {
        $query = pg_query (Gateway::getConnexion(), "SELECT * FROM configuration.harvest_configuration where ".$table."_id=".$id." ORDER BY name ASC;");
        return pg_fetch_all($query);
    }

	static function getCodeConfig($id)
	{
		return @pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT code FROM configuration.harvest_configuration WHERE id=".$id))[0]["code"];
	}

    /** Retourne la configuration selon son code
     * @param $code
     * @return mixed|void
     */
    static function getIdFromCode($code)
    {
        $query = pg_query(Gateway::getConnexion(), "SELECT id FROM configuration.harvest_configuration WHERE code='".$code."'");
        if (!$query)
        {
            echo "Erreur durant la requête de getIdFromCode .\n";
            exit;
        }
        return pg_fetch_all($query)[0]['id'];
    }

    /** Retourne les codes de toutes les configurations
     * @return array de chaînes de caractères comprenant les codes | void échec de la requête
     */
    static function getConfigCodes()
    {

        $query = pg_query (Gateway::getConnexion(), "SELECT code FROM configuration.harvest_configuration");

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

    static function getHarvestConfiguration()
    {
        $query = pg_query(Gateway::getConnexion(),
            "SELECT id,name FROM configuration.harvest_configuration ORDER BY id ASC;");
        if (!$query)
        {
            echo "Erreur durant la requête getHarvestConfiguration.\n";
            exit;
        }
        return pg_fetch_all($query);
    }

    static function getHarvestConfigurationP()
    {
        $query = pg_query(Gateway::getConnexion(), "SELECT h.id,name,(Select count(*) 
		FROM configuration.harvest_task_cron_line where configuration_id=h.id)moissons 
		FROM configuration.harvest_configuration h ORDER BY id ASC;");
        if (!$query)
        {
            echo "Erreur durant la requête getHarvestConfigurationP.\n";
            exit;
        }
        return pg_fetch_all($query);
    }

    /** Retourne le différentiel et le nom de la configuration $id
     * @param $id id de la configuration
     * @return array|false|void
     */
    static function getHarvestConfigurationDifferential($id)
    {
        $query = pg_query(Gateway::getConnexion(), "SELECT differential,name FROM configuration.harvest_configuration where configuration.harvest_configuration.id=".$id);
        if (!$query)
        {
            echo "Erreur durant la requête de getHarvestConfigurationDifferential .\n";
            exit;
        }
        return pg_fetch_all($query);
    }

    static function getInfoConfig($id)
    {
        $query = pg_query(Gateway::getConnexion(), "SELECT
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
        $data['profile'] = Gateway::getProfileConfig($id);
        return $data;
    }

	/** Retourne le profil de la configuration
	 * @param $id id de la configuration
	 * @return array|false|void
	 */
    static function getProfileConfig($id)
    {
        /*$query = pg_query(Gateway::getConnexion(), "SELECT p.description,p.code
			FROM configuration.configuration_access_mapping m, configuration.user_profile p
			WHERE p.code=m.configuration_code AND m.configuration_code='".$id."';");*/
		$code = Gateway::getCodeConfig($id);
		$query = pg_query(Gateway::getConnexion(), "SELECT is_external_access_allowed, is_internal_virtualized_access_allowed, is_internal_wifi_access_allowed
															FROM configuration.configuration_access_mapping m WHERE m.configuration_code='".$code."';");
        if (!$query) {
            echo "Erreur durant la requête de getProfileConfig .\n";
            exit;
        }
		$queryResult = pg_fetch_all($query);
		$resultat = array();
		if ($queryResult) {
			$queryResult = $queryResult[0];
			if ($queryResult['is_external_access_allowed'] == "t")
				$resultat[] = "EXTERNAL";
			if ($queryResult['is_internal_virtualized_access_allowed'] == "t")
				$resultat[] = "INTERNAL";
			if ($queryResult['is_internal_wifi_access_allowed'] == "t")
				$resultat[] = "WIFI-BPI";
		}
        return $resultat;
    }

    static function getProfile()
    {
        $query = pg_query(Gateway::getConnexion(), "SELECT m.configuration_id, b.name as public_name, p.description
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

    /** Chargement des configurations avec un fichier à uploader
     * @return array|false|void
     */
    static function getConfigurationsWithFileToUpload()
    {
        // chargement des config avec fichier a uploader ( ATTENTION pour multiple)
        $query = pg_query(Gateway::getConnexion(), "select c.id, c.name, b.name as public_name, t.name as grabber_name
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

    /** Chargement des configurations avec multiples fichiers à uploader
     * @return array|false|void
     */
    static function getConfigurationsWithFilesToUpload()
    {
        // chargement des config avec fichier a uploader ( ATTENTION pour multiple)
        $query = pg_query(Gateway::getConnexion(), "select c.id, c.name, b.name as public_name, t.name as grabber_name
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

    /** Chargement des configurations sans fichier à uploader
     * @return array|false|void
     */
    static function getConfigurationsWithoutFileToUpload()
    {
        // chargement des config sans fichier a uploader
        $query = pg_query(Gateway::getConnexion(), "select c.id, c.name, b.name as public_name, t.name as grabber_name
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

	/** Compte le nombre de configurations
	 * @param $str requête SQL
	 * @return mixed|void le nombre de configurations trouvées
	 */
	static function countHarvestConfiguration($str)
	{
		$query = pg_query(Gateway::getConnexion(), $str);
		if (!$query)
		{
			echo "Erreur durant la requête de countHarvestConfiguration .\n";
			echo $str;
			exit;
		}
		return pg_fetch_all($query)[0]['count'];
	}

	/** Retourne tous les mappings de la base
	 * @return array contenant les id et name des mappings
	 * 			| false s'il n'y a aucun mapping dans la base
	 * 			| void si erreur dans la requête
	 */
	static function getMapping()
	{
		$query = pg_query (Gateway::getConnexion(), "SELECT id,name FROM configuration.mapping ORDER BY id ASC;");
		if (!$query)
		{
			echo "Erreur durant la requête de getMapping.\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	/* cree pour CTLG-378 */
	static function getMappingWithId($id)
	{
		$query = pg_query(Gateway::getConnexion(), "SELECT configuration.mapping.name, configuration.mapping.definition FROM configuration.mapping WHERE id=" . $id . ";");
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

	/** Met à jour les modalités d'accès à la configuration
	 * Ne fonctionne pas car droits d'ajout et modification à configuration.configuration_profile_mapping refusé
	 * @param $code string code de la configuration
	 * @param $acces array liste des accès
	 * @return void
	 */
	static function accesUpdate($code,$acces) {
		/* pg_query(Gateway::getConnexion(),"DELETE FROM configuration.configuration_profile_mapping where configuration_id=".$id);
		foreach($acces as $a)
		{
			if(!empty($a)){self::insert("INSERT INTO configuration.configuration_profile_mapping VALUES(".$id.",'".$a."')");}
		}
		*/
		foreach($acces as $a) {
			if (!empty($a)){
				switch ($a) {
					case "EXTERNAL":
						pg_query(Gateway::getConnexion(),"UPDATE configuration.configuration_access_mapping
											SET is_external_access_allowed=true WHERE configuration_code='" . $code . "'");
						break;
					case "INTERNAL":
						pg_query(Gateway::getConnexion(),"UPDATE configuration.configuration_access_mapping
											SET is_internal_virtualized_access_allowed=true WHERE configuration_code='" . $code . "'");
						break;
					default:
						pg_query(Gateway::getConnexion(),"UPDATE configuration.configuration_access_mapping
											SET is_internal_wifi_access_allowed=true WHERE configuration_code='" . $code . "'");
						break;
				}

			}
		}
	}

	static function updateParcours($parcours, $id)
	{
		pg_query(Gateway::getConnexion(),"DELETE FROM configuration.search_base_parcours_mapping sbpm WHERE exists (select * from configuration.harvest_configuration hc WHERE sbpm.search_base_code = hc.search_base_code AND hc.id = ".$id.")");
		foreach($parcours as $parcour)
		{
			// pg_query(Gateway::getConnexion(),"INSERT INTO configuration.configuration_parcours_mapping VALUES('".$id."','".$parcour."')");
			pg_query(Gateway::getConnexion(),"insert into configuration.search_base_parcours_mapping(search_base_code, parcours_code) (SELECT search_base_code , '".$parcour."' as parcours_code FROM configuration.harvest_configuration hc where hc.id = ".$id.")");
		}
	}

	static function getParcours($id)
	{
		$query = pg_query(Gateway::getConnexion(), "SELECT parcours_code as parcours FROM configuration.harvest_configuration hc JOIN configuration.search_base_parcours_mapping sbpm ON sbpm.search_base_code = hc.search_base_code WHERE hc.id = " . $id . ";");
		if (!$query)
		{
			echo "Erreur durant la requête de getParcours .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	static function getConfigurationGrabber()
	{
		$query = pg_query (Gateway::getConnexion(), "SELECT id,name FROM configuration.grabber ORDER BY id ASC;");
		/*if (!$query)
		{
			echo "Erreur durant la requête de getConfigurationGrabber .\n";
			exit;
		}*/
		return pg_fetch_all($query);
	}

}


?>