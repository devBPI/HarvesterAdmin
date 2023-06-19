<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once("../PDO/Gateway.php");

$alert_jobs = Gateway::getAlertJobs();

if (isset($_POST["submit_value"])) {
	$tab_alert_jobs = [];
	for ($i = 0; $i < 5; $i++) {
		$tab_alert_jobs[] = ["id" => $_POST["id_".$i], "is_enabled" => isset($_POST["is_enabled_".$i])?"t":"f"];
	}
	//var_dump($tab_alert_jobs);
	// Deux possibilités : formulaire classique avec bouton de validation
	// ou
	// Façon suppression d'alerte dans Vue/AlertesReporting.php : changement immédiatement pris en compte via envoi
	// de formulaire JavaScript
}

$section = "Activation des tâches de surveillance";

include("../Vue/alerts_logs/AlertesParametrage.php");

?>