<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$special_char = array(1=>'(',2=>')');
$configurations = [];
require_once ("../PDO/Gateway.php");

if (isset($_GET["id"]) && isset($_GET["modify"]) && $_GET["modify"] == "true") {
	$donnees = array();
	$nb = 0;
	foreach ($_POST as $key => $value) {
		$k = substr($key, strlen("rule_input_value_"));
		if(is_numeric($k)) {
			$donnees[$nb]['input']=$value;
		} else {
			$k = substr($key, strlen("destination_"));
			if (is_numeric($k)) {
				$donnees[$nb++]['destination']=$value;
			}
		}
	}
	if (isset($_POST["sent_via_form"]))
		Gateway::updateTranslationRule($donnees,$_GET["id"]);
}
if (isset($_GET["id"])) {
	$configurations = Gateway::getConfigurationBySet($_GET["id"]);
	$rules_set = Gateway::getTranslationRulesSet($_GET["id"]);
	$rules = Gateway::getTranslationRulesBySet($_GET["id"]);
	if (count($rules) > 0) {
		$rules_set["category"] = Gateway::getCategoryBySetId($_GET["id"]);
	} else {
		$rules_set["category"] = null;
		$rules_set["category"]["id"] = -1;
	}
}
$categories = Gateway::getCategories();
$cibles = Gateway::getTranslationDestinations();

$section = "Traduction";
include ('../Vue/traduction/TraductionSet.php');
?>


