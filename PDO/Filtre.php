<?php

include_once("../PDO/Gateway.php");
class Filtre {
	/** Retourne le nom, l'entité ainsi que l'id du noeud racine d'une règle
	 * @param $id int id de la règle
	 * @return array val['name'] : nom de la règle; val['id'] : id de la racine de l'arbre
	 */
	static function getRuleNameRootEntity($id)
	{
		return @pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT name,filter_rule_tree_node_id AS id,entity  FROM configuration.filter_rule WHERE id=".$id))[0];
	}

	static function getRuleEntity($id)
	{
		return @pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT entity,filter_rule_tree_node_id AS id  FROM configuration.filter_rule WHERE id=".$id))[0];
	}

	static function updateRuleName($name, $id)
	{
		pg_query(Gateway::getConnexion(),"UPDATE configuration.filter_rule SET name='".$name."' WHERE filter_rule_tree_node_id=".$id);
	}

	static function getFilterRules()
	{
		return pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT * FROM configuration.filter_rule"));
	}

	static function getFilterRuleOrderBy32()
	{
		return pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT * FROM configuration.filter_rule ORDER BY 3,2"));
	}

	static function getFilterByConf($id)
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT F.entity, name, R.id FROM configuration.filter F, configuration.filter_rule R WHERE F.filter_rule_id=R.id AND configuration_id=".$id);
		return pg_fetch_all($query);
	}

	/** Insert/Modifie/Supprime des regles de filtrage
	 * @param $data array de noeuds
	 * @return array liste des erreurs
	 */
	static function updateFilterRules($data)
	{
		$array_error = array();
		$i = 0;
		$ids = pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT id FROM configuration.filter_rule"));
		foreach($ids as $id)
		{
			// Si modification
			if(array_key_exists($id['id'],$data)) {

				$value=$data[$id['id']];
				$name_existe = @pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT name FROM configuration.filter_rule WHERE name='" . $value['name'] ."' AND id!=" . $id['id']));
				if ($name_existe) {
					$array_error[$i]['msg'] = "Erreur : le nom " . $value['name'] . " n'est pas unique";
					$array_error[$i++]['id'] = $value['name'];
				} else {
					pg_query(Gateway::getConnexion(),
						"UPDATE configuration.filter_rule SET name='" . $value['name'] . "', entity='" . $value['entity'] . "' WHERE id=" . $id['id']
					);
				}
			}
			// Sinon, suppression
			else
			{
				$racine = self::getRuleNameRootEntity($id['id'])['id'];
				pg_query(Gateway::getConnexion(),"DELETE FROM configuration.filter WHERE filter_rule_id =".$id['id']);
				pg_query(Gateway::getConnexion(),"DELETE FROM configuration.filter_rule WHERE id =".$id['id']);
				if ($racine != null) {
					self::deleteTree($racine); // Suppression de l'arbre sans sa racine
					self::deleteRoot($racine); // Suppression de la racine
				}
			}
		}
		foreach($data as $k => $value)
		{
			if($k<0)
			{
				$name_existe = @pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT name FROM configuration.filter_rule WHERE name='" . $value['name'] ."' AND id=" . $id['id']));
				if ($name_existe) {
					$array_error[$i]['msg'] = "Erreur : le nom " . $value['name'] . " n'est pas unique";
					$array_error[$i++]['id'] = $value['name'];
				} else {
					pg_query(Gateway::getConnexion(), "INSERT INTO configuration.filter_rule(name,entity) VALUES('" . $value['name'] . "', '" . $value['entity'] . "')");
				}
			}
		}
		return $array_error;
	}

	static function getFilterCode()
	{
		$query=pg_query(Gateway::getConnexion(),"SELECT * FROM configuration.filter_function");
		if (!$query)
		{
			echo "Erreur durant la requête de getFilterCode .\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	// --------------------------------------------------------------------------- Fonctions sur les arbres

	static function getRuleTree($id)
	{
		$query = @pg_query(Gateway::getConnexion(),"SELECT id, filter_predicate_id AS pred, boolean_operator AS operator FROM configuration.filter_rule_tree_node WHERE id=".intval($id));
		$d = pg_fetch_all($query)[0];
		if(!empty($d)){
			if(!empty($d['operator']))
			{
				$data=self::grt($id);
			}
			$data['operator']=$d['operator'];
			$data['pred']=$d['pred'];
			$data['id']=$d['id'];
			return $data;
		} else {
			return null;
		}
	}

	static function grt($id)
	{
		$data=self::getTreeNode($id);
		if ($data) {
			foreach ($data as $k => $d) {
				if (!empty($data[$k]['operator'])) {
					$data[$k] = self::grt($d['id']);
					$data[$k]['operator'] = $d['operator'];
					$data[$k]['id'] = $d['id'];
				}
			}
		}

		return $data;
	}

	/** Supprime l'arbre dont la racine est $id, sans supprimer la racine
	 * @param $id id de la racine
	 * @return void
	 */
	static function deleteTree($id)
	{
		if($id==null)
		{
			return;
		}
		$ids=pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT id from configuration.filter_rule_tree_node WHERE parent_id=".$id));
		if($ids!=null){
			self::deleteTree($ids[0]['id']);
			self::deleteTree($ids[1]['id']);
			pg_query(Gateway::getConnexion(),"DELETE FROM configuration.filter_rule_tree_node WHERE parent_id=".$id);
		}
	}

	/** Fonction de suppression d'un sous-arbre à partir du parent d'une feuille
	 * @param $node_id int id du noeud de la feuille
	 * @return void
	 */
	static function deleteSubTree($node_id) {
		if (!self::isRoot($node_id)) {
			$parent = self::getParentNodeId($node_id);
			//var_dump($parent);
			$other_child = self::getChildNotMe($parent, $node_id)["id"];
			$grandparent = self::getParentNodeId($parent);
			//var_dump($grandparent);
			if (!self::isRoot($parent)) {
				self::setNewParent($grandparent, $other_child);
			} else {
				$rule_id = Gateway::select("SELECT id FROM configuration.filter_rule WHERE filter_rule_tree_node_id=".$parent)[0]["id"];
				self::setRoot($rule_id, $other_child);
			}
			self::deleteNode($node_id);
			self::deleteNode($parent);
		} else {
			self::deleteRoot($node_id);
		}
	}

	static function setRoot($rule_id, $node_id) {
		pg_query(Gateway::getConnexion(), "UPDATE configuration.filter_rule SET filter_rule_tree_node_id=".intval($node_id)." WHERE id=".intval($rule_id));
	}

	/** Fonction de définition du nouveau parent d'un noeud
	 * @param $parent int id du nouveau parent
	 * @param $node_id int id du noeud qui doit changer de parent
	 * @return void
	 */
	static function setNewParent($parent, $node_id) {
		pg_query(Gateway::getConnexion(), "UPDATE configuration.filter_rule_tree_node SET parent_id=".intval($parent)." WHERE id=".intval($node_id));
	}


	static function iT($data,$id)
	{
		if(empty($data))
		{
			return;
		}
		if($data["operator"]!='OPERATION') {
			$id=self::insertTreeNode($id,$data["operator"]);
			self::iT($data[0],$id); // Gauche
			self::iT($data[1],$id); // Droite
		} else {
			self::insertTreeLeaf($id,$data["code"]);
		}
	}

	static function insertTree($data,$id)
	{
		self::deleteTree($id); // Supprime l'arborescence sans supprimer la racine
		// Cas où la racine n'existe pas encore
		if (empty($id)) {
			if ($data["operator"] != "OPERATION") {
				$idR = self::insertTreeNode('',$data["operator"]);
				self::iT($data[0],$idR); // Gauche
				self::iT($data[1],$idR); // Droite
				$id=$idR;
			} else {
				$id=self::insertTreeLeaf("",$data["code"]); // Code = prédicat
			}
			return $id;
		}
		// Cas où la racine existe
		else {
			if ($data["operator"] != "OPERATION") {
				self::insertTreeNode('',$data["operator"],$id);
				self::iT($data[0],$id); // Gauche
				self::iT($data[1],$id); // Droite
			} else {
				//var_dump($data['predicat'] . $id);
				self::insertTreeLeaf("",$data["code"],$id); // Code = prédicat
			}
			return null;
		}
	}

	static function insertTreeNode($parent_id,$op,$id=NULL)
	{
		if($id==NULL)
		{
			if($parent_id=='')
			{
				pg_query(Gateway::getConnexion(),"INSERT INTO configuration.filter_rule_tree_node(boolean_operator) 
				VALUES('".$op."')");
			} else {
				pg_query(Gateway::getConnexion(),"INSERT INTO configuration.filter_rule_tree_node(parent_id, boolean_operator) 
				VALUES(".$parent_id.",'".$op."')");
			}
		}
		else
		{
			pg_query(Gateway::getConnexion(),"UPDATE configuration.filter_rule_tree_node SET boolean_operator='".$op."', filter_predicate_id=NULL WHERE id=".$id);
		}
		return pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT max(id) AS id FROM configuration.filter_rule_tree_node"))[0]['id'];
	}

	static function insertTreeLeaf($parent_id,$predicat,$id=NULL)
	{
		$pred=self::getPredicatByCode($predicat); // $pred : id du prédicat
		if($parent_id==NULL || $parent_id=="") { // Si pas de parent (racine ou élément "OPERATION")
			if ($id != NULL) { // Si on modifie la racine
				pg_query(Gateway::getConnexion(), "UPDATE configuration.filter_rule_tree_node
										SET filter_predicate_id=" . intval($pred) . ",boolean_operator=null WHERE id=" . intval($id));
				return $id;
			} else { // Si on ajoute un élément
				pg_query(Gateway::getConnexion(), "INSERT INTO configuration.filter_rule_tree_node(filter_predicate_id)
										VALUES (" . $pred . ")");
				return pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT max(id) AS id FROM configuration.filter_rule_tree_node"))[0]['id'];
			}
		}
		else if($id==NULL)
		{
			pg_query(Gateway::getConnexion(),"INSERT INTO configuration.filter_rule_tree_node(parent_id, filter_predicate_id) 
				VALUES(".$parent_id.",".$pred.")");
		}
		else
		{
			pg_query(Gateway::getConnexion(),"UPDATE configuration.filter_rule_tree_node SET boolean_operator=NULL, filter_predicate_id=".$pred." WHERE id=".$id);
		}

	}

	static function getTreeNode($id)
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT id, filter_predicate_id AS pred, boolean_operator AS operator FROM configuration.filter_rule_tree_node WHERE parent_id=".$id);
		if (!$query)
		{
			return null;
		}
		return pg_fetch_all($query);
	}


	/** Renvoie le deuxieme enfant d'un noeud parent
	 * @param $parent int id du noeud parent
	 * @param $node_id int id du noeud enfant a ne pas retourner
	 * @return mixed
	 */
	static function getChildNotMe($parent, $node_id) {
		$children = self::getRuleTree($parent);
		if ($children[0]["id"] == $node_id)
			return $children[1];
		else
			return $children[0];
	}

	/** Verifie si un noeud donne est une racine ou non
	 * @param $node_id int id du noeud
	 * @return bool vrai/faux si le noeud est une racine
	 */
	static function isRoot($node_id) {
		$result = pg_fetch_all(
			pg_query(
				Gateway::getConnexion(), "SELECT filter_rule_tree_node_id FROM configuration.filter_rule WHERE filter_rule_tree_node_id=".intval($node_id)
			)
		);
		return $result && (count($result) > 0);
	}

	/** Recupere le parent d'un noeud enfant
	 * @param $node_id int id d'un noeud
	 * @return mixed le parent si le noeud n'est pas une racine, null sinon
	 */
	static function getParentNodeId($node_id) {
		if (!self::isRoot($node_id)) {
			return pg_fetch_all(
				pg_query(
					Gateway::getConnexion(), "SELECT parent_id FROM configuration.filter_rule_tree_node WHERE id=" . $node_id
				)
			)[0]["parent_id"];
		}
		return null;
	}

	/** Supprime un noeud quelconque
	 * @param $node_id int id du noeud
	 * @return void
	 */
	static function deleteNode($node_id) {
		pg_query(Gateway::getConnexion(), "DELETE FROM configuration.filter_rule_tree_node WHERE id =".intval($node_id));
	}

	/** Supprime la racine
	 * @param $racine int id de la racine a supprimer
	 * @return void
	 */
	static function deleteRoot($racine) {
		$result = Gateway::select("SELECT id FROM configuration.filter_rule WHERE filter_rule_tree_node_id=".intval($racine));
		if ($result) {
			foreach ($result as $rule) {
				self::setRoot($rule["id"], null);
			}
		}
		pg_query(Gateway::getConnexion(), "DELETE FROM configuration.filter_rule_tree_node WHERE id =".intval($racine));
	}

	// --------------------------------------------------------------------------- Fonctions sur les prédicats

	static function getPredicat($id)
	{
		$query = @pg_query(Gateway::getConnexion(),"SELECT code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate WHERE id=".$id);
		if(!$query) {
			return null;
		}
		return pg_fetch_all($query);
	}

	/** Met à jour les prédicats.
	 * Vérifie l'unicité du code et rejette la mise à jour / L'ajout du prédicat qui ne peut être mis à jour
	 * @param $data array de prédicats et leurs attributs
	 * @return array des erreurs d'unicité du code du prédicat
	 */
	static function updatePredicats($data)
	{
		$array_error = array();
		$i = 0;
		$ids = @pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT id FROM configuration.filter_predicate"));
		foreach($ids as $id) { // Boucle sur tous les ids de prédicats issus de la base
			if(array_key_exists($id['id'],$data)) { // Si le prédicat a été modifié (est présent dans $data)
				$d=$data[$id['id']]; // Récupération des données à l'indice $id['id']
				// Vérification de l'unicité du code du prédicat
				$code_existe = @pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT code FROM configuration.filter_predicate WHERE id !=" . $id['id'] . " AND code='" . $d['code'] . "'"));
				if ($code_existe) {
					$array_error[$i]['msg'] = "Erreur lors de la mise à jour du prédicat " . $id['id'] . " : le code " . $d['code'] . " n'est pas unique";
					$array_error[$i++]['id'] = $id['id'];
				} else {
					@pg_query(Gateway::getConnexion(), "UPDATE configuration.filter_predicate SET code='" . $d['code'] . "' , property='" . $d['property'] . "' , 
				entity ='" . $d['entity'] . "' , function_code='" . $d['function_code'] . "' , value_to_compare=E'" . $d['value'] . "' WHERE id=" . $id['id']);
				}
			}
			else
			{
				self::deletePredicat($id['id']);
			}
		}
		foreach($data as $k => $d)
		{
			if($k<0)
			{
				// Vérification de l'unicité du code du prédicat
				$code_existe = @pg_fetch_all(pg_query(Gateway::getConnexion(), "SELECT code FROM configuration.filter_predicate WHERE code='" . $d['code'] . "'"));
				if ($code_existe) {
					$array_error[$i]['msg'] = "Erreur lors de l'ajout du prédicat dont le code est " . $d['code'] . ". Le code n'est pas unique";
					$array_error[$i++]['id'] = $d['code'];
				} else {
					@pg_query(Gateway::getConnexion(), "INSERT INTO configuration.filter_predicate(code,property,entity,function_code,value_to_compare) VALUES('" . $d['code'] . "', '" . $d['property'] . "', 
				'" . $d['entity'] . "' , '" . $d['function_code'] . "', '" . $d['value'] . "')");
				}
			}
		}
		return $array_error;
	}

	static function deletePredicat($predicate_id) {
		$query = pg_query(Gateway::getConnexion(), "SELECT id FROM configuration.filter_rule_tree_node WHERE filter_predicate_id=".intval($predicate_id));
		// Suppression de tous les sous-arbres ou apparaissent le predicat
		if ($query) {
			$nodes_id = pg_fetch_all($query);
			if ($nodes_id) {
				foreach ($nodes_id as $node_id) {
					self::deleteSubTree($node_id["id"]);
				}
			}
		} else {
			var_dump("Erreur de la requête deletePredicat");
		}
		// Suppression du predicat
		@pg_query(Gateway::getConnexion(),"DELETE FROM configuration.filter_predicate WHERE id =".intval($predicate_id));
	}

	static function getPredicatByCode($code){
		$query = pg_query(Gateway::getConnexion(),"SELECT id FROM configuration.filter_predicate WHERE code='".$code."'");
		return pg_fetch_all($query)[0]["id"];
	}

	static function getPredicatsOrderByCode()
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT id,code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate ORDER BY code");
		if (!$query)
		{
			echo "Erreur durant la requête de getPredicatsOrderByCode.\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	static function getPredicatsOrderByEntityCode()
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT id,code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate ORDER BY entity,code");
		if (!$query)
		{
			echo "Erreur durant la requête de getPredicatsOrderByEntityCode.\n";
			exit;
		}
		return pg_fetch_all($query);
	}

	static function getPredicatsByEntity($entity)
	{
		$query = pg_query(Gateway::getConnexion(),"SELECT code, entity, property, function_code, value_to_compare AS val FROM configuration.filter_predicate WHERE entity='".$entity."'");
		if (!$query)
		{
			echo "Erreur durant la requête de getPredicatsByEntity .\n";
			exit;
		}
		return pg_fetch_all($query);
	}


	// --------------------------------------------------------------------------- Fonction sur les configurations
	public static function getConfigurationByFilterRule($id) {
		return @pg_fetch_all(
			pg_query(Gateway::getConnexion(), "SELECT DISTINCT hc.*
													FROM configuration.harvest_configuration hc, configuration.filter f, configuration.filter_rule fr
													WHERE f.configuration_id = hc.id AND f.filter_rule_id = fr.id AND fr.id = " . $id)
		);
	}

	static function updateFilterConfiguration($id,$donnee)
	{
		// Sélection de "filter" : association règle-configuration
		$entries = pg_fetch_all(pg_query(Gateway::getConnexion(),"SELECT id,filter_rule_id,configuration_id FROM configuration.filter"));
		$used_filter = array();
		$used_id = array();
		$i=0;
		$array_error = array();
		$incr_id=1;
		if(!empty($entries)){
			// Boucle sur les entrées
			foreach($entries as $entry) {
				// S'il s'agit d'un filtre associé à la configuration $id
				if($entry['configuration_id']==$id) {
					$delete = true;
					if(!empty($donnee)) {
						// Boucle sur les règles à associer
						foreach($donnee as $d) {
							// Si la règle correspond à une règle de l'entrée, alors il ne s'agit pas d'une suppression de règle
							if($d['rule']==$entry['filter_rule_id']) {
								$delete = false;
							}
						}
					}
					// S'il y a suppression d'association
					if($delete == true) {
						pg_query(Gateway::getConnexion(),"DELETE FROM configuration.filter WHERE filter_rule_id =".$entry['filter_rule_id']." AND configuration_id=".$id);
					}
					// Sinon on met l'id de l'association dans $used_id
					else {
						array_push($used_id,$entry['id']);
					}
					// On met l'id de la règle dans $used_filter
					array_push($used_filter,$entry['filter_rule_id']);
				}
				// S'il ne s'agit pas d'un filtre associé à la configuration $id, on met l'id de l'association dans $used_id
				else {
					array_push($used_id,$entry['id']);
				}
			}
		}
		if(!empty($donnee)) {
			// Boucle sur les données
			foreach($donnee as $d) {
				// Si la règle n'est pas dans $used_filter
				if(!in_array($d['rule'],$used_filter)) {
					while(in_array($incr_id,$used_id)) {
						$incr_id++;
					}
					if (!@pg_query(Gateway::getConnexion(), "INSERT INTO configuration.filter VALUES('" . $incr_id . "', '" . $id . "', '" . $d['entity'] . "', '" . $d['rule'] . "')")) {
						$array_error[$i]["msg"] = pg_last_error();
						$array_error[$i]["entity"] = $d["entity"];
					}
					array_push($used_filter,$incr_id);
					$incr_id++;
				}
			}
		}
		return $array_error;
	}

}