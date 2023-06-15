<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

$section = "Paramétrage des rapports :</br>";

$type = "donnees";

$maj_type = "Donnees";

$data_type = "METADATA";

include "../Controlleur/RapportsEdition.php";

?>