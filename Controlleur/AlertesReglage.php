<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once("../PDO/Gateway.php");

if (isset($_POST["submit_value"])) {
	$tab_alert_parameters = [];
	for ($i = 0; $i < count(Gateway::getAlertParameters()); $i++) {
		$tab_alert_parameters[] = ["id" => $_POST["id_".$i], "value" => $_POST["value_".$i]];
	}
	Gateway::updateAlertParameters($tab_alert_parameters);
}

$alert_parameters_tmp = Gateway::getAlertParameters();
$parameters_threshold = [];
$parameters_percentage = [];
foreach ($alert_parameters_tmp as $alert_parameter) {
	if (preg_match("/(THRESHOLD)/", $alert_parameter["code"])) $parameters_threshold[] = $alert_parameter;
	else $parameters_percentage[] = $alert_parameter;
}

//var_dump($parameters_threshold);
//var_dump($parameters_percentage);


$section = "Réglage des seuils d'alerte";

$j_threshold = 0;
$j_percentage = 0;

include("../Vue/alerts_logs/AlertesReglage.php");