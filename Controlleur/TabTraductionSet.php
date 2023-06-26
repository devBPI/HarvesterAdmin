<?php

require_once("../PDO/Gateway.php");
$id_category = intval($_GET["id_category"]);
$rules_set = Gateway::getTranslationRulesSet($_GET["id"]);
$rules = Gateway::getTranslationRulesBySet($_GET["id"]);
if (count($rules) > 0) {
	$rules_set["category"] = Gateway::getCategoryBySetId($_GET["id"]);
} else {
	$rules_set["category"] = null;
	$rules_set["category"]["id"] = -1;
}
$cibles = Gateway::getTranslationDestinationsByCategory($id_category);
echo makeTab($cibles, $rules, $rules_set, $id_category);

function makeTab($cibles, $rules, $rules_set, $id_category)
{
	$str = '<tr class="hidden_field" id="new">
					<td><input type="text" name="rule_input_value_"/></td>
					<td>
						<select name="destination_"><option value="">Sélectionnez une cible</option>';
	foreach ($cibles as $cible) {
		if ($cible["category_id"] == $id_category) {
			$str = $str . '<option value="' . $cible["id"]  . '">' . $cible["value"] . '</option>';
		}
	}
	$str = $str . '</select></td></tr>';
	$str = $str . '<tr class="hidden_field" id="new">
					<td><input type="text" name="rule_input_value_"/></td>
					<td>
						<select name="destination_"><option value="">Sélectionnez une cible</option>';
	foreach ($cibles as $cible) {
		if ($cible["category_id"] == $id_category) {
			$str = $str . '<option value="' . $cible["id"]  . '">' . $cible["value"] . '</option>';
		}
	}
	$str = $str . '</select></td>
					<td class="td_cross"><button class="but" type="button" title="Supprimer une cible" onclick="delete_field(this.parentElement.parentElement)"><img src="../ressources/cross.png" width="30px" height="30px"/>
								</button>
							</td></tr>';
	if ($_GET["id_category"] == $rules_set["category"]["id"]) {
		if (!empty($rules)) {
			foreach ($rules as $key => $rule) {
				$str = $str . '<tr>
						<td>
							<input type="text" name="rule_input_value_' .  $key . '" value="' . $rule["rule_input_value"] . '"/>
						</td>
						<td>
							<select name="destination_' . $key. '" required>
							<option value="">Sélectionnez une cible</option>';
				foreach ($cibles as $cible) {
					if ($cible["category_id"] == $id_category) {
						if ($cible["id"] == $rule["cible_id"]) {
							$str = $str . '<option value="' . $cible["id"] . '" selected>' . $cible["value"] . '</option>';
						} else {
							$str = $str . '<option value="' . $cible["id"] . '">' . $cible["value"] . '</option>';
						}
					}
				}
				$str = $str . '
							</select>
							<td class="td_cross">
								<button class="but" type="button" title="Supprimer une cible"
										onclick="delete_field(this.parentElement.parentElement)"><img src="../ressources/cross.png"
																						width="30px" height="30px"/>
								</button>
							</td>
						</td>
					</tr>';
			}
		}
	}
	$str = $str . '<tr style="background-color:rgba(0,0,0,0);" id="add_row">
					<td></td>
					<td></td>
						<td class="td_cross">
							<button class="ajout but" type="button" title="Ajouter une ligne"
									onclick="add_new_field(this.parentElement.parentElement.parentElement.parentElement)">
								<img src="../ressources/add.png" width="30px" height="30px"/></button>
						</td>
					</tr>';
	return $str;
}

?>