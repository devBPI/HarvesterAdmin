<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once("../PDO/Gateway.php");

if (isset($_POST["submit_value"])) {
	if ($_POST["submit_value"] == "submit_jobs") {
		$tab_alert_jobs = [];
		for ($i = 0; $i < count(Gateway::getAlertJobs()); $i++) {
			$tab_alert_jobs[] = ["id" => $_POST["id_".$i], "is_enabled" => isset($_POST["is_enabled_".$i])?"t":"f"];
		}
		var_dump($tab_alert_jobs);
	} else if ($_POST["submit_value"] == "submit_parameters") {
		$tab_alert_parameters = [];
		for ($i = 0; $i < count(Gateway::getAlertParameters()); $i++) {
			$tab_alert_parameters[] = ["code" => $_POST["id_".$i], "value" => $_POST["value_".$i]];
		}
		var_dump($tab_alert_parameters);
	} else {
		unset($_POST["old_id_"]);
		unset($_POST["id_"]);
		unset($_POST["submit_value"]);
		$tab_mailing_list = [];
		$ind = -1;

		foreach ($_POST as $key => $value) {
			if (preg_match("/(old_id_)/", $key)) $tab_mailing_list[++$ind]["old_code"] = $value;
			else if (preg_match("/(id_)/", $key)) $tab_mailing_list[$ind]["code"] = $value;
			else $tab_mailing_list[$ind]["is_enabled"] = "t";
		}

		$tab_to_insert = [];
		$tab_to_update = [];

		for ($i = 0; $i < count($tab_mailing_list); $i++) {
			if (!isset($tab_mailing_list[$i]["is_enabled"])) // On commence par ajouter is_enabled là où il n'y est pas
				$tab_mailing_list[$i]["is_enabled"] = "f";
			if ($tab_mailing_list[$i]["old_code"] == "") // Si old_code == "", alors donnée à insérer
				$tab_to_insert[] = $tab_mailing_list[$i];
			else // Sinon, donnée à mettre à jour
				$tab_to_update[] = $tab_mailing_list[$i];
		}

		var_dump($tab_to_update);
		var_dump($tab_to_insert);
	}
}

$alert_jobs = Gateway::getAlertJobs(); // Activation / Désactivation des alertes
$alert_parameters = Gateway::getAlertParameters(); // Seuils et pourcentages des alertes
$mailing_list = Gateway::getMailingList(); // Destinataires des alertes

$section = "Paramétrage des tâches de surveillance";

include("../Vue/alerts_logs/AlertesParametrage.php");

?>