<?php

require_once("../Composant/ComboBox.php");
require_once("../PDO/Gateway.php");

// Remplir ComboBox avec les noms de configuration
if (isset($_POST["champs"]) && $_POST["champs"] == "id_name") {
	$configurations = getConfigurationsFormatees();
	echo ComboBox::makeComboBox($configurations);
}
// Remplir ComboBox avec les statuts
else if (isset($_POST["champs"]) && $_POST["champs"] == "status") {
	$status = getStatusFormates();
	echo ComboBox::makeComboBox($status);
}

function getConfigurationsFormatees() {
	$configurations = Gateway::getHarvestConfiguration();
	$configurations_formate = [];
	foreach ($configurations as $c)
		$configurations_formate[] = ["id" => $c["name"], "name" => $c["name"]];
	return $configurations_formate;
}

function getStatusFormates() {
	$status = Gateway::getAllStatus();
	$status_formate = [];
	foreach ($status as $s) {
		$status_formate[] = ["id" => $s["status"], "name" => $s["status"]];
	}
	return $status_formate;
}

function insert_criterias($criterias, $data_to_show, $operators, $operators_short, $type) {
	$str = "";
	$j = 1;
	$i = "00" . $j;
	foreach ($criterias as $criteria) {
		$str = $str . '
	<div class="critere_rapport" id="critere_rapport_' . $i . '">
		<input type="hidden" id="input_id_cond_'. $i .'" name="id_cond_'. $i .'" value="'. $criteria["id"] .'" />
		<select class="champ formRapportLeft" id="cb_champ_cond_' . $i . '" name="champ_cond_' . $i . '" onchange="display_related_operator(this)" required>
			<option value="">Sélectionnez un champ</option>
			<optgroup label="Informations sur la moisson">';
		$str = $str . ComboBox::makeComboBox($data_to_show["general_infos"], $criteria["display_value"]);
		$str = $str . '
			</optgroup>
			<optgroup label="Suivi de la moisson">';
		$str = $str . ComboBox::makeComboBox($data_to_show["follow_up"], $criteria["display_value"]);
		$str = $str . '
				<option value="inserted_external_link" disabled>Nombre d\'insertions dans external_link</option>
				<option value="inserted_solr" disabled>Nombre d\'insertions dans Solr\'</option>
			</optgroup>
				<optgroup label="Nombre de moissons">';
		$str = $str . ComboBox::makeComboBox($data_to_show["number_of_results_infos"], $criteria["display_value"]);
		$str = $str . '
			</optgroup>
		</select>';
		if ($criteria["display_value"] == "harvest_last_task") {
			$str = $str . '
		<select class="operateur" id="cb_operateur_cond_' . $i . '" name="operateur_cond_' . $i . '">
		<option value="equals">=</option>
		</select>
		<input type="text" class="valeur" id="input_valeur_cond_' . $i . '" name ="valeur_cond_' . $i . '" placeholder="Valeur de comparaison" style="display:none"/>
		<select class="champ" id="cb_valeur_cond_' . $i . '" name="valeur_cond_' . $i . '">
			<option value="Oui">Oui</option>
		</select>';
		} else if ($criteria["display_value"] == "harvest_configuration_name" || $criteria["display_value"] == "harvest_status") {
			$str = $str . '
		<select class="operateur" id="cb_operateur_cond_' . $i . '" name="operateur_cond_' . $i . '">' .
				ComboBox::makeComboBox($operators_short, $criteria["code"]) .'
		</select>';
			$str = $str . '
				<input type="text" class="valeur" id="input_valeur_cond_' . $i . '" name ="valeur_cond_' . $i . '" placeholder="Valeur de comparaison" style="display:none"/>
				<select class="champ" id="cb_valeur_cond_' . $i . '" name="valeur_cond_' . $i . '" required>';
			if ($criteria["display_value"] == "harvest_configuration_name") {
				$data = getConfigurationsFormatees();
			} else {
				$data = getStatusFormates();
			}
			$str = $str . ComboBox::makeComboBox($data, $criteria["value_to_compare"]);
			$str = $str . '
				</select>';
		} else {
			$str = $str . '
		<select class="operateur" id="cb_operateur_cond_' . $i . '" name="operateur_cond_' . $i . '">' .
				ComboBox::makeComboBox($operators, $criteria["code"]) .'
		</select>';
			if (preg_match("/(date)/", $criteria["display_value"]) || preg_match("/(time)/", $criteria["display_value"])) {
				$input_type = "datetime-local";
			} else {
				$input_type = "text";
			}
			$str = $str . '
			<input type="'. $input_type .'" class="valeur" id="input_valeur_cond_' . $i . '" name="valeur_cond_' . $i . '"
			value="'. $criteria["value_to_compare"] . '" placeholder="Valeur de comparaison" pattern="[0-9]*" required/>
			<select class="champ" id="cb_valeur_cond_' . $i . '" name="valeur_cond_' . $i . '" style="display:none">
			</select >';
			}
		$str = $str . '
		<button class="but delete" type="button" title="Supprimer un critère" style="cursor:pointer;" onclick="delete_critere_or_donnee(this.parentElement, \'critere\')">
			<img alt="Supprimer un critère" src="../ressources/cross.png" width="30px" height="30px">
		</button>
	</div>';
		$j +=1;
		$i = "00" . $j;
	}
	return $str;
}

function insert_display_values($datas, $data_to_show_for_display, $type) {
	$str = "";
	$j = 1;
	$i = "00" . $j;
	foreach ($datas as $data) {
		$str = $str . '
		<div class="donnee_affichee" id="donnee_affichee_' . $i . '">
		<input type="hidden" id="input_id_champ_aff_'. $i .'" name="id_champ_aff_'. $i .'" value="'. $data["id"] .'" />';
		if(isset($type) && $type == "processus") {
			$str = $str . '
			<select class="champ_donnee" id="cb_champ_aff_' . $i . '" name="display_champ_aff_' . $i . '" onchange="change_value_input(this)">
				<option value="">Sélectionnez un champ</option>' .
				ComboBox::makeComboBox($data_to_show_for_display, $data["display_value"]) . '
			</select>
			<input type="text" class="champ_donnee" id="input_name_champ_aff_' . $i . '" name="name_champ_aff_' . $i . '"
			 		value="'. $data["display_name"] .'" placeholder="Dénomination de la donnée"/>
			<button class="but delete" type="button" title="Supprimer une donnée à afficher" onclick="delete_critere_or_donnee(this.parentElement, \'donnee\')">
				<img alt="Supprimer un critère" src="../ressources/cross.png" width="30px" height="30px">
			</button>';
		}
		$str = $str .
			'</div>';

		$j +=1;
		$i = "00" . $j;
	}
	return $str;
}

?>