<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once("../PDO/Gateway.php");

if (isset($_POST["submit_value"])) {

}

$section = "Gestion de la liste de diffusion";

$mailing_list = Gateway::getMailingList();

include("../Vue/alerts_logs/AlertesMailingList.php");

?>