<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once ("../PDO/Gateway.php");

$section = "Paramétrage des rapports :<br>";

$type = "processus";

$maj_type = "Processus";

$data_type = "PROCESS";

include "../Controlleur/RapportsEdition.php";

?>