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
        $query = pg_query(Gateway::getConnexion(), "SELECT p.description,p.code 
			FROM configuration.configuration_access_mapping m, configuration.user_profile p
			WHERE p.code=m.configuration_code AND m.configuration_code='".$id."';");
        if (!$query)
        {
            echo "Erreur durant la requête de getProfileConfig .\n";
            exit;
        }
        return pg_fetch_all($query);
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
}


?>