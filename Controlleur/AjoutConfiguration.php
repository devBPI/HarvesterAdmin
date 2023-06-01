<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$section = "Ajouter une configuration";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$mapping=Gateway::getMapping();
$filtre=Gateway::getExclusion();
$traduction=Gateway::getAllTranslations();
$grabber=Gateway::getConfigurationGrabber();
include '../Vue/configuration/FormulaireAjoutConfig.php';
?>

