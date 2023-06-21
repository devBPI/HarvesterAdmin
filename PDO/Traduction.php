<?php

include_once("../PDO/Gateway.php");
class Traduction
{

	/** Permet d'obtenir un Rules_Set par son id
	 * @param $id integer identifiant du set
	 * @return mixed
	 */
	static function getTranslationRulesSet($id) {
		return pg_fetch_all(pg_query(Gateway::getConnexion(),
							"SELECT * FROM configuration.translation_rules_set WHERE id=" . $id)
							)[0];
	}

	static function getTranslationRulesBySet($id) {
		return pg_fetch_all(pg_query(Gateway::getConnexion(),
							"SELECT trsm.translation_rules_set_id AS set_id,
									tr.id AS rule_id, tr.input_value AS rule_input_value,
    								td.id AS cible_id, td.value AS cible_value, td.category_id AS cible_category
									FROM configuration.translation_rules_set_mapping trsm,
									     configuration.translation_rule tr, configuration.translation_destination td
									WHERE trsm.translation_rules_set_id = " . $id . " AND trsm.translation_rule_id = tr.id
									AND tr.destination_id = td.id ORDER BY  cible_value, rule_input_value ASC")
							);
	}

	static function getTranslationDestinations() {
		return pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.translation_destination"));
	}

	static function getTranslationDestinationsByCategory($id) {
		return pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT * FROM configuration.translation_destination WHERE category_id=" . $id . " ORDER BY value"));
	}


    /** Permet d'obtenir toutes les traductions
     * @return array de nom,id de chaque traduction trouvée | false si aucune traduction n'a été trouvée
     */
    static function getAllTranslations() // ancien code sans configuration_id en parametre pour les avoir tous (notamment appele dans ajoutConfig.php, voir CTLG-356)
    {
        $query = pg_query (Gateway::getConnexion(), "SELECT name, T.id FROM configuration.translation T, configuration.translation_rules_set R WHERE T.translation_rules_set_id=R.id");
        return pg_fetch_all($query);
    }

    /** Permet d'obtenir une traduction selon son id
     * @param $id
     * @return array de nom,id de la traduction à l'id correspondante | false si aucune traduction n'a été trouvée
     */
    static function getTranslation($id)
    {
        $query = pg_query (Gateway::getConnexion(), "SELECT name, R.id FROM configuration.translation T, configuration.translation_rules_set R WHERE T.translation_rules_set_id=R.id AND configuration_id=".$id);
        return pg_fetch_all($query);
    }

    static function updateTrad($data,$id)
    {
        pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation WHERE configuration_id=".$id);
        foreach($data['lang'] as $value)
        {
            if(isset($value['ignore']))
            {
                pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation(configuration_id, field_set, property, input_value, replacement_value,ignore_case) VALUES(".$id.",'LANGUE','notice.language','".$value['input']."','".$value['rep']."','true')");
            }
            else
            {
                pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation(configuration_id, field_set, property, input_value, replacement_value) VALUES(".$id.",'LANGUE','notice.language','".$value['input']."','".$value['rep']."')");
            }
        }
    }

    static function getTranslationCategory()
    {
        $query = pg_query(Gateway::getConnexion(),"SELECT name FROM configuration.translation_category ORDER BY name");
        if (!$query)
        {
            echo "Erreur durant la requête de getTranslationCategory .\n";
            exit;
        }
        return pg_fetch_all($query);
    }
    static function getDestination($category)
    {
        $query = pg_query(Gateway::getConnexion(),"SELECT value, id FROM configuration.translation_destination WHERE category_id=
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
                @pg_query(Gateway::getConnexion(),"UPDATE configuration.translation_destination SET value='".$var."' WHERE value='".$v."'");
            }
            else if($row!='')
            {
                @pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation_destination(value,category_id) 
					VALUES('".$var."',(SELECT id FROM configuration.translation_category WHERE name='".$category."'))");
            }
        }
    }

    static function deleteDestination($data)
    {
        foreach($data as $row)
        {
            $var = str_replace("'","''",$row);
            pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rule_id in 
				(SELECT id FROM configuration.translation_rule WHERE destination_id = (SELECT id FROM configuration.translation_destination WHERE value='".$var."'))");
            pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rule WHERE destination_id = (SELECT id FROM configuration.translation_destination WHERE value='".$var."')");
            pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_destination WHERE value='".$var."'");

        }
    }

    static function getTrads($name)
    {
        $query = pg_query(Gateway::getConnexion(),"select r.input_value AS input, d.value AS rep
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



    static function getRulesSet()
    {
        $query = pg_query(Gateway::getConnexion(),"SELECT * FROM configuration.translation_rules_set ORDER BY name");
        if (!$query)
        {
            echo "Erreur durant la requête de getRulesSet .\n";
            exit;
        }
        return pg_fetch_all($query);
    }

    static function updateTranslationRule($data,$id)
	{
		// Suppression de toutes les associations règle / set
		pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=".$id);
		// Suppression de toutes les règles qui ne sont pas dans la table configuration.translation_rules_set_mapping
		pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rule WHERE id not in 
			(SELECT translation_rule_id FROM configuration.translation_rules_set_mapping)");
		foreach ($data as $row) {
			pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation_rule(input_value,destination_id) 
				VALUES('".$row["input"] . "', " . $row["destination"] . ")");
		}
		$ids=self::getNewRules();
        foreach($ids as $rowid)
        {
            pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation_rules_set_mapping VALUES(".$id.",".$rowid['id'].")");
        }
    }
    static function getTranslationSetId($name)
    {
        $query = pg_query(Gateway::getConnexion(),"SELECT id from configuration.translation_rules_set WHERE name='".$name."'");
        if (!$query)
        {
            echo "Erreur durant la requête de getTranslationSetId .\n";
            exit;
        }
        return pg_fetch_all($query)[0]['id'];
    }

    static function getNewRules()
    {
        $query = pg_query(Gateway::getConnexion(),"SELECT id FROM configuration.translation_rule WHERE id not in 
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
		$array_error = array();
		$i = 0;
        pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation WHERE configuration_id=".$id);
        foreach($data as $value) {
            if(isset($value['property']) and isset($value['set'])) {
				$assoc_existe = @pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT configuration_id FROM configuration.translation WHERE property='" . $value['property'] ."' AND configuration_id=" . $id));
				if ($assoc_existe) {
					$array_error[$i]['msg'] = "Erreur : un ensemble est déjà associé au champ " . $value['property'] . " (problème d'unicité de l'association)";
					$array_error[$i++]['id'] = $value['property'];
				} else {
					pg_query(Gateway::getConnexion(), "INSERT INTO configuration.translation(configuration_id,property,translation_rules_set_id,ignore_case,trim) 
														VALUES(" . $id . ",'" . $value['property'] . "'," . $value['set'] . "," . json_encode($value['case']) . "," . json_encode($value['trim']) . ")");
				}
            }
        }
		return $array_error;
    }
    static function updateRulesSet($data,$cmp)
    {
        foreach($data as $key => $row)
        {
            $var = str_replace("'","''",$row);
            if(is_numeric($key) and $key>=0)
            {
                $v = str_replace("'","''",$cmp[$key]);
                @pg_query(Gateway::getConnexion(),"UPDATE configuration.translation_rules_set SET name='".$var."' WHERE name='".$v."'");
            }
            else if($row!='')
            {
                pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation_rules_set(name) VALUES('".$var."')");
            }
        }
    }
    static function deleteRulesSet($data)
    {
        foreach($data as $row)
        {
            $var = str_replace("'","''",$row);
			pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=
				(SELECT id FROM configuration.translation_rules_set WHERE name='".$var."')");
            pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rule WHERE id IN
				(SELECT translation_rule_id FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=
					(SELECT id FROM configuration.translation_rules_set WHERE name='".$var."'))");
			pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation WHERE translation_rules_set_id=
				(SELECT id FROM configuration.translation_rules_set WHERE name='".$var."')");
            pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rules_set WHERE name='".$var."'");

        }
    }

    static function getAllDestination()
    {
        $query = pg_query(Gateway::getConnexion(),"SELECT * FROM configuration.translation_destination");
        if (!$query)
        {
            echo "Erreur durant la requête de getAllDestination .\n";
            exit;
        }
        return pg_fetch_all($query);
    }

	static function deleteCategory($data)
	{
		foreach($data as $row)
		{
			$var = str_replace("'","''",$row);
			pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rule_id in
				(SELECT id FROM configuration.translation_rule WHERE destination_id in
				(SELECT id FROM configuration.translation_destination WHERE category_id=
				(SELECT id FROM configuration.translation_category WHERE name='".$var."')))");
			pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rule WHERE destination_id in
				(SELECT id FROM configuration.translation_destination WHERE category_id=
				(SELECT id FROM configuration.translation_category WHERE name='".$var."'))");
			pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_destination WHERE category_id=
				(SELECT id FROM configuration.translation_category WHERE name='".$var."')");
			pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_category WHERE name='".$var."'");

		}
	}

	static function getSetByConf($id)
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT E.property, entity, name, S.id, ignore_case AS case, trim FROM configuration.translation AS T, configuration.translation_rules_set AS S, configuration.entity_properties AS E
				WHERE S.id=translation_rules_set_id AND T.property=E.property AND configuration_id=".$id." ORDER BY entity,property");
		return pg_fetch_all($query);
	}

	static function getCategories()
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT * FROM configuration.translation_category");
		if (!$query)
		{
			echo "Erreur durant la requête de getCategory .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	static function getCategoryBySet($set)
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT DISTINCT C.name FROM configuration.translation_destination D, configuration.translation_category C,
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

	static function getCategoryBySetId($id)
	{
		$query = pg_query(Gateway::getConnexion(),
			"SELECT DISTINCT tc.id, tc.name
			FROM configuration.translation_destination td, configuration.translation_category tc,
			configuration.translation_rules_set_mapping trsm, configuration.translation_rule tr, configuration.translation_rules_set trs
			WHERE category_id=tc.id AND td.id=tr.destination_id AND tr.id=trsm.translation_rule_id AND trs.id=trsm.translation_rules_set_id
			AND trs.id='".$id."'");
		if (!$query)
		{
			echo "Erreur durant la requête de getCategoryBySet .\n";
			exit;
		}
		return pg_fetch_all($query)[0];
	}

	static function updateCategory($data,$cmp)
	{
		foreach($data as $key => $row)
		{
			$var = str_replace("'","''",$row);
			if(is_numeric($key) and $key>=0)
			{
				$v = str_replace("'","''",$cmp[$key]);
				@pg_query(Gateway::getConnexion(),"UPDATE configuration.translation_category SET name='".$var."' WHERE name='".$v."'");
			}
			else if($row!='')
			{
				pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation_category(name) VALUES('".$var."')");
			}
		}
	}

	static function getConfigurationBySet($id)
	{
		$query = pg_query(Gateway::getConnexion(),
			"SELECT DISTINCT hc.*
					FROM configuration.translation t, configuration.harvest_configuration hc, configuration.translation_rules_set trs
					WHERE t.configuration_id = hc.id AND trs.id = t.translation_rules_set_id AND trs.id = ".$id);
		if (!$query)
		{
			echo "Erreur durant la requête de getConfigurationBySet .\n";
			exit;
		}
		return pg_fetch_all($query);
	}
}

?>