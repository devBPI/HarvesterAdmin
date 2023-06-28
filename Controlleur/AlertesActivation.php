<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once("../PDO/Gateway.php");

if (isset($_POST["submit_value"])) {
	$tab_alert_jobs = [];
	for ($i = 0; $i < count(Gateway::getAlertJobs()); $i++) {
		$tab_alert_jobs[] = ["id" => $_POST["id_".$i], "is_enabled" => isset($_POST["is_enabled_".$i])?"t":"f"];
	}
	Gateway::updateAlertJobs($tab_alert_jobs);
}

$alert_jobs = Gateway::getAlertJobs();

$section = "Activation des tâches de surveillance";

include("../Vue/alerts_logs/AlertesActivation.php");

?>