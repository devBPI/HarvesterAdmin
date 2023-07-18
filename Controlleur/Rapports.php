<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

if (isset($_POST["submit_type"]) && $_POST["submit_type"] == "Supprimer le rapport") {
	$report_name = Gateway::getReport($_POST["report_id"])["name"];
	Gateway::deleteReport($_POST["report_id"]);
	$msg_type = "action_success";
	$msg_title = "Suppression";
	$msg_text = "La suppression du rapport ". $report_name ." a bien été effectuée.";
} else if (isset($_POST["submit_type"]) && $_POST["submit_type"] == "duplicate") {
	$configuration = Gateway::getReport($_POST["report_id"]);
	if ($configuration != null) {
		if (Gateway::duplicateReport($_POST["report_id"]) > -1) {
			$report_name = Gateway::getReport($_POST["report_id"])["name"];
			$msg_type = "action_success";
			$msg_title = "Duplication";
			$msg_text = "Le rapport ". $report_name ." a bien été dupliqué.";
		} else {
			$msg_type = "action_success";
			$msg_title = "Duplication : erreur";
			$msg_text = "Le rapport n'a pas pu être dupliqué.";
		}

	}
}

if (isset($_GET["id"]) && $_GET["id"]=="processus") {
	$configurations = Gateway::getReports("PROCESS");
	$type = "processus";
	$section = "Rapports sur les processus";
} else {
	$configurations = Gateway::getReports("METADATA");
	$type = "donnees";
	$section = "Rapports sur les données collectées";
}


include "../Vue/rapports/RapportsAccueil.php";

?>