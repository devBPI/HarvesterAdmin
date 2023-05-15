<?php

include_once("../PDO/Gateway.php");
class Traduction
{
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
        $query = pg_query (Gateway::getConnexion(), "SELECT name, T.id FROM configuration.translation T, configuration.translation_rules_set R WHERE T.translation_rules_set_id=R.id AND configuration_id=".$id);
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
        $query = pg_query(Gateway::getConnexion(),"SELECT name FROM configuration.translation_category");
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
        $query = pg_query(Gateway::getConnexion(),"SELECT * FROM configuration.translation_rules_set");
        if (!$query)
        {
            echo "Erreur durant la requête de getRulesSet .\n";
            exit;
        }
        return pg_fetch_all($query);
    }

    static function updateTranslationRule($data,$name)
    {
        $id = self::getTranslationSetId($name);
        pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=".$id);
        pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rule WHERE id not in 
			(SELECT translation_rule_id FROM configuration.translation_rules_set_mapping)");
        foreach($data as $row)
        {
            $var = str_replace("'","''",$row['rep']);
            $input = str_replace("'","''",$row['input']);
            pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation_rule(input_value,destination_id) 
				VALUES('".$input."',(SELECT id FROM configuration.translation_destination WHERE value = '".$var."'))");
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
        pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation WHERE configuration_id=".$id);
        foreach($data as $value)
        {
            if(isset($value['property']) and isset($value['set']))
            {
                $q=
                    pg_query(Gateway::getConnexion(),"INSERT INTO configuration.translation(configuration_id,property,translation_rules_set_id,ignore_case,trim) 
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
            pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rule WHERE id=
				(SELECT translation_rule_id FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=
					(SELECT id FROM configuration.translation_rules_set WHERE name='".$var."'))");
            pg_query(Gateway::getConnexion(),"DELETE FROM configuration.translation_rules_set_mapping WHERE translation_rules_set_id=
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
}

?>