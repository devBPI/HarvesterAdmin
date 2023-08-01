<?php
require_once ("../PDO/Gateway.php");

class PredicatClass {
	/**
	 * @var array|false|null
	 */
	private static $functions;
	private static $entities;
	private static $ruleEntity;
	private static $predicats;

	public function __construct($id) {
		self::$functions = Gateway::getFilterCode();
		self::$entities = Gateway::getEntities();
		self::$ruleEntity = Gateway::getRuleEntity($id)["entity"];
		self::$predicats = Gateway::getPredicatsByEntity(self::$ruleEntity);
	}

	public function display_predicate($donnee, $nb) {
		if (!is_array($donnee))
			return;

		$predicat_v = Gateway::getPredicat($donnee["pred"]);
		$value = $predicat_v? $predicat_v[0] : null;
		$predicate_list = "";
		foreach(self::$predicats as $p) {
			if ($p["code"] == $value["code"]) $selected = "selected";
			else $selected = "";
				$predicate_list = <<<HTML
					$predicate_list
					<option value="{$p["code"]}" {$selected}>{$p["code"]}</option>
HTML;
		}
		$json_list = json_encode(self::$predicats, JSON_HEX_APOS);
		echo <<<HTML
\n
<div id="predicat_{$nb}" class="div_predicate">
	<table class="table-config">
		<tr>
			<th style="width:40%">Prédicat</th>
			<th>Entité</th>
			<th>Champ</th>
			<th>Fonction</th>
			<th>Valeur</th>
		</tr>
		<tr class="entity" id="{$value['property']}">
			<td>
				<select name="entity_{$nb}" onchange='update_predicat(this, {$json_list}, true)' required>
					<option value="">Choississez un prédicat</option>
					{$predicate_list}
				</select>
			</td>
			<td>{$value["entity"]}</td>
			<td>{$value["property"]}</td>
			<td>{$value["function_code"]}</td>
			<td>{$value["val"]}</td>
		</tr>
	</table>
	<button class="but delete" type="button" title="Supprimer un critère" onclick="delete_critere_or_donnee(this.parentElement, 'critere')">
		<img alt="Supprimer un prédicat" src="../ressources/cross.png" style="width:30px;height:30px">
	</button>
</div>
HTML;

	}
}