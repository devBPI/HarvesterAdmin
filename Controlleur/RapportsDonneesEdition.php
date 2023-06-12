<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

$section = "Paramétrage des rapports :</br>";

$type = "donnees";

if (isset($_GET["viewonly"])) {
	$section = $section . "détails de la configuration";
} else if (isset($_GET["id"])) {
	$section = $section . "modification du rapport sur les données collectées";
	$configuration = Gateway::getReport($_GET["id"]);
} else {
	$section = $section . "nouveau rapport sur les données collectées";
}

$operators_old = Gateway::getOperators();
$operators = [];
foreach ($operators_old as $operator) {
	$operators[] = [
		"id" => $operator["code"],
		"name" => $operator["label"]
	];
}

$data_to_show = Gateway::getDataToShow("METADATA");


include "../Vue/rapports/RapportEdition.php";

?>