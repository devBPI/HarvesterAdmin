<?php

require_once("../Composant/ComboBox.php");
require_once("../PDO/Gateway.php");

// ------------------------------------------------------------------------------ Pour Ajax
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

// Remplir ComboBox avec les noms de types de ressources
if (isset($_POST["champs"]) && $_POST["champs"] == "resource_type") {
	$types_donnees = getResourceTypesFormates();
	echo ComboBox::makeComboBox($types_donnees);
}

// ----------------------------------------------------------------------- Fonctions utiles pour Ajax / insert
function getConfigurationsFormatees(): array
{
	$configurations = Gateway::getHarvestConfiguration();
	$configurations_formate = [];
	foreach ($configurations as $c)
		$configurations_formate[] = ["id" => $c["name"], "name" => $c["name"]];
	return $configurations_formate;
}

function getStatusFormates(): array
{
	$status = Gateway::getAllStatus();
	$status_formate = [];
	foreach ($status as $s) {
		$status_formate[] = ["id" => $s["status"], "name" => $s["status"]];
	}
	return $status_formate;
}

function getResourceTypesFormates(): array
{
	$types_donnees = Gateway::getResourceTypes();
	$types_donnees_formatees = [];
	foreach ($types_donnees as $type_donnees) {
		$types_donnees_formatees[] = [
			"id" => $type_donnees["type"],
			"name" => $type_donnees["type"]
		];
	}
	return $types_donnees_formatees;
}

// ------------------------------------------------------------------------------ Fonctions pour insertion

function insert_criterias($criterias, $data_to_show, $operators, $operators_short, $type): string
{
	if ($type == "processus") {
		return insert_criterias_processus($criterias, $data_to_show, $operators, $operators_short);
	} else {
		return insert_criterias_donnees($criterias, $data_to_show, $operators, $operators_short);
	}
}

function makeInputCbValeur($criteria, $i): string
{
	$cb = "";
	$display_input = false;
	if ($criteria["display_value"] == "harvest_last_task") {
		$cb = '<option value="Oui">Oui</option>';
	} else if ($criteria["display_value"] == "harvest_configuration_name" || $criteria["display_value"] == "harvest_status" || $criteria["display_value"] == "notice_type") {
		if ($criteria["display_value"] == "harvest_configuration_name") $data = getConfigurationsFormatees();
		else if($criteria["display_value"] == "harvest_status") $data = getStatusFormates();
		else $data = getResourceTypesFormates();
		$cb = ComboBox::makeComboBox($data, $criteria["value_to_compare"]);
	} else {
		$display_input = true;
		if (preg_match("/(date)/", $criteria["display_value"]) || preg_match("/(time)/", $criteria["display_value"]))
			$input_type = "datetime-local";
		else
			$input_type = "text";
		if ($criteria["display_value"] == "harvest_differences_notices") {
			$placeholder = "Si pourcentage, ne pas oublier le %";
			$pattern = "([0-9]*)(%*)";
		} else {
			$placeholder = "Valeur de comparaison";
			$pattern = "[0-9]*";
		}
	}
	// Fait en heredoc pour plus de lisibilité
	if($display_input) {
		return <<<HTML
<input type="{$input_type}" class="valeur" id="input_valeur_cond_{$i}" name="valeur_cond_{$i}"
			value="{$criteria["value_to_compare"]}" placeholder="{$placeholder}" pattern="{$pattern}" required />
	<select class="champ" id="cb_valeur_cond_{$i}" name="valeur_cond_{$i}" style="display:none"></select>
HTML;
	} else {
		return <<<HTML
<input type="text" class="valeur" id="input_valeur_cond_{$i}" name ="valeur_cond_{$i}" placeholder="Valeur de comparaison" style="display:none" />
<select class="champ" id="cb_valeur_cond_{$i}" name="valeur_cond_{$i}">{$cb}</select>
HTML;
	}
}

function makeCriteria($criteria, $i, $report_type, $data_to_show, $operators): string
{
	$cb_general_infos = ComboBox::makeComboBox($data_to_show['general_infos'], $criteria['display_value']);
	$cb_follow_up = ComboBox::makeComboBox($data_to_show["follow_up"], $criteria["display_value"]);
	$cb_number_of_results_infos = ComboBox::makeComboBox($data_to_show["number_of_results_infos"], $criteria["display_value"]);
	$cb_operators = ComboBox::makeComboBox($operators, $criteria["code"]);
	$cb_valeur = makeInputCbValeur($criteria, $i);
	$str = "";
	// Fait en heredoc pour plus de lisibilité
	return <<< HTML
<div class="critere_rapport" id="critere_rapport_{$i}">
		<input type="hidden" id="input_id_cond_{$i}" name="id_cond_{$i}" value="{$criteria["id"]}" />
		<select class="champ" id="cb_champ_cond_{$i}" name="champ_cond_{$i}" onchange="display_related_operator(this)" required>
			<option value="">Sélectionnez un champ</option>
			<optgroup label="Informations sur la {$report_type}">
			{$cb_general_infos}
			</optgroup>
			<optgroup label="Suivi de la {$report_type}">
			{$cb_follow_up}
			</optgroup>
			<optgroup label="Nombre de {$report_type}">
			{$cb_number_of_results_infos}
			</optgroup>
		</select>
		<select class="operateur" id="cb_operateur_cond_{$i}" name="operateur_cond_{$i}">
			{$cb_operators}
		</select>
		{$cb_valeur}
		<button class="but delete" type="button" title="Supprimer un critère" style="cursor:pointer;" onclick="delete_critere_or_donnee(this.parentElement, 'critere')">
			<img alt="Supprimer un critère" src="../ressources/cross.png" width="30px" height="30px">
		</button>
	</div>
HTML;
}

function insert_criterias_processus($criterias, $data_to_show, $operators, $operators_short): string
{
	$str = "";
	$j = 1;
	$i = "00" . $j;
	foreach ($criterias as $criteria) {
		if ($criteria["display_value"] == "harvest_last_task") {
			$str = $str . makeCriteria($criteria, $i, "moisson", $data_to_show, [["id" => "equals", "name" => "="]]);
		} else if ($criteria["display_value"] == "harvest_configuration_name" || $criteria["display_value"] == "harvest_status") {
			$str = $str . makeCriteria($criteria, $i, "moisson", $data_to_show, $operators_short);
		} else {
			$str = $str . makeCriteria($criteria, $i, "moisson", $data_to_show, $operators);
		}
		$j +=1;
		$i = "00" . $j;
	}
	return $str;
}

function insert_criterias_donnees($criterias, $data_to_show, $operators, $operators_short): string
{
	$str = "";
	$j = 1;
	$i = "00" . $j;
	foreach ($criterias as $criteria) {
		if($criteria["display_value"] == "notice_type") {
			$str = $str . makeCriteria($criteria, $i, "ressource", $data_to_show, $operators_short);
		} else {
			$str = $str . makeCriteria($criteria, $i, "ressource", $data_to_show, $operators);
		}
		$j +=1;
		$i = "00" . $j;
	}
	return $str;
}

function insert_display_values($datas, $data_to_show_for_display, $type): string
{
	$str = "";
	$j = 1;
	$i = "00" . $j;
	foreach ($datas as $data) {
		$str = $str . '
		<div class="donnee_affichee" id="donnee_affichee_' . $i . '">
		<input type="hidden" id="input_id_champ_aff_'. $i .'" name="id_champ_aff_'. $i .'" value="'. $data["id"] .'" />';
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
		$str = $str .
			'</div>';

		$j +=1;
		$i = "00" . $j;
	}
	return $str;
}

?>