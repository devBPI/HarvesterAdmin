<?php

include_once("../Composant/ComboBox.php");
include_once("../PDO/Gateway.php");

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

// Remplir ComboBox avec les noms des connecteurs
if (isset($_POST["champs"]) && $_POST["champs"] == "grabber_type") {
	$grabbers = getGrabbersFormates();
	echo ComboBox::makeComboBox($grabbers);
}

// Remplir ComboBox avec les codes des bases de recherche
if (isset($_POST["champs"]) && $_POST["champs"] == "search_base") {
	$search_bases = getSearchBasesFormates();
	echo ComboBox::makeComboBox($search_bases);
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
	//$status = Gateway::getAllStatus();
	//$status_formate = [];
	//foreach ($status as $s)
	//	$status_formate[] = ["id" => $s["status"], "name" => $s["status"]];
	//return $status_formate;
	return [
		["id" => "INDEXED", "name" => "INDEXED"],
		["id" => "GRAB_ERROR", "name" => "GRAB_ERROR"],
		["id" => "INDEX_ERROR", "name" => "INDEX_ERROR"],
		["id" => "CANCELED", "name" => "CANCELED"]
	];
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

function getGrabbersFormates(): array
{
	$grabbers = Gateway::getConfigurationGrabber();
	$grabbers_formates = [];
	foreach ($grabbers as $grabber) {
		$grabbers_formates[] = ["id" => $grabber["name"], "name" => $grabber["name"]];
	}
	return $grabbers_formates;
}

function getSearchBasesFormates(): array
{
	$search_bases = Gateway::getSearchBases();
	$search_bases_formates = [];
	foreach($search_bases as $sb) {
		$search_bases_formates[] = ["id" => str_replace("'", "\'", $sb["name"]), "name" => $sb["name"]];
	}
	return $search_bases_formates;
}

// ------------------------------------------------------------------------------ Fonctions pour insertion

function makeInputCbValeur($criteria, $i): string
{
	$cb = "";
	$display_input = false;
	if ($criteria["display_value"] == "harvest_last_task" || preg_match("/(results_distinct)/", $criteria["display_value"])) {
		$cb = '<option value="Oui">Oui</option>';
	} else if (preg_match("/(configuration_name)/", $criteria["display_value"]) || $criteria["display_value"] == "harvest_status"
		|| $criteria["display_value"] == "notice_type" || preg_match("/(grabber_type)/",$criteria["display_value"])
		|| preg_match("/(search_base)/", $criteria["display_value"])) {
		if($criteria["display_value"] == "harvest_status") $data = getStatusFormates();
		else if ($criteria["display_value"] == "notice_type") $data = getResourceTypesFormates();
		else if (preg_match("/(grabber_type)/",$criteria["display_value"])) $data = getGrabbersFormates();
		else if (preg_match("/(search_base)/",$criteria["display_value"],$criteria["display_value"])) $data = getSearchBasesFormates();
		else $data = getConfigurationsFormatees();
		$cb = ComboBox::makeComboBox($data, str_replace("'", "\'", $criteria["value_to_compare"]));
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
			value="{$criteria["value_to_compare"]}" placeholder="{$placeholder}" pattern="{$pattern}" max="9999-12-31T23:59" required />
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
	$cb_general_infos = ComboBox::makeComboBox($data_to_show["general_infos"], $criteria["display_value"]);
	$cb_follow_up = ComboBox::makeComboBox($data_to_show["follow_up"], $criteria["display_value"]);
	$cb_number_of_results_infos = ComboBox::makeComboBox($data_to_show["number_of_results_infos"], $criteria["display_value"]);
	$cb_operators = ComboBox::makeComboBox($operators, $criteria["code"]);
	$cb_valeur = makeInputCbValeur($criteria, $i);
	$criteria_id = $criteria["id"] ?? "";
	// Fait en heredoc pour plus de lisibilité
	return <<< HTML
<div class="critere_rapport" id="critere_rapport_{$i}">
		<input type="hidden" id="input_id_cond_{$i}" name="id_cond_{$i}" value="{$criteria_id}" />
		<select class="champ" id="cb_champ_cond_{$i}" name="champ_cond_{$i}" onchange="display_related_operator(this)" required>
			<option value="">Sélectionnez un champ</option>
			<optgroup label="Informations sur la {$report_type}">
			{$cb_general_infos}
			</optgroup>
			<optgroup label="Suivi de la {$report_type}">
			{$cb_follow_up}
			</optgroup>
			<optgroup label="Nombre de {$report_type}s">
			{$cb_number_of_results_infos}
			</optgroup>
		</select>
		<select class="operateur" id="cb_operateur_cond_{$i}" name="operateur_cond_{$i}">
			{$cb_operators}
		</select>
		{$cb_valeur}
		<button class="but delete" type="button" title="Supprimer un critère" onclick="delete_critere_or_donnee(this.parentElement, 'critere')">
			<img alt="Supprimer un critère" src="../ressources/cross.png" style="width:30px;height:30px">
		</button>
	</div>
HTML;
}

function makeDataToDisplay($data, $i, $dtsfd, $report_type) {
	$cb_general_infos = ComboBox::makeComboBox($dtsfd["general_infos"], $data["display_value"]);
	$cb_follow_up = ComboBox::makeComboBox($dtsfd["follow_up"], $data["display_value"]);
	$data_id = $data["id"] ?? "";
	$data_display_name = htmlspecialchars($data["display_name"]);
	return <<<HTML
<div class="donnee_affichee" id="donnee_affichee_{$i}">
		<input type="hidden" id="input_id_champ_aff_{$i}" name="id_champ_aff_{$i}" value="{$data_id}" />
		<select class="champ_donnee" id="cb_champ_aff_{$i}" name="display_champ_aff_{$i}" onchange="change_value_input(this)" required>
			<option value="">Sélectionnez un champ</option>
			<optgroup label="Informations sur la {$report_type}">
			{$cb_general_infos}
			</optgroup>
			<optgroup label="Suivi de la {$report_type}">
			{$cb_follow_up}
			</optgroup>
		</select>
		<input type="text" class="champ_donnee" id="input_name_champ_aff_{$i}" name="name_champ_aff_{$i}" title="Les caractères interdits sont . , ; \ /"
			 		value="{$data_display_name}" pattern="[^.,;/\\]*" placeholder="Dénomination de la donnée" required/>
		<div class="reporting_arrow_div" title="Glisser-déposer pour changer l'ordre des données (colonnes du rapport)">
			<img alt="Glisser-déposer" src="../ressources/move.png">
		</div>
		<button class="but delete" type="button" title="Supprimer une donnée à afficher" onclick="delete_critere_or_donnee(this.parentElement, 'donnee')">
			<img alt="Supprimer un critère" src="../ressources/cross.png" style="width:30px;height:30px">
		</button>
</div>
HTML;

}

function insert_criterias_processus($criterias, $data_to_show, $operators, $operators_short, $j=1): string
{
	$str = "";
	if ($j < 10) { $i = "00" . $j; }
	else if ($j < 100) { $i = "0" . $j; }
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

function insert_criterias_donnees($criterias, $data_to_show, $operators, $operators_short, $j=1): string
{
	$str = "";
	if ($j < 10) { $i = "00" . $j; }
	else if ($j < 100) { $i = "0" . $j; }
	foreach ($criterias as $criteria) {
		if($criteria["display_value"] == "notice_type") {
			$str = $str . makeCriteria($criteria, $i, "ressource", $data_to_show, $operators_short);
		} else if ($criteria["display_value"] == "results_distinct") {
			$str = $str . makeCriteria($criteria, $i, "moisson", $data_to_show, [["id" => "equals", "name" => "="]]);
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
		if ($type == "processus")
			$str = $str . makeDataToDisplay($data, $i, $data_to_show_for_display, "moisson");
		else
			$str = $str . makeDataToDisplay($data, $i, $data_to_show_for_display, "ressource");
		$j +=1;
		$i = "00" . $j;
	}
	return $str;
}

?>