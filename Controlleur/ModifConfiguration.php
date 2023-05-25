<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$section="Modification de la configuration";
if (isset($_GET['param'])) {
    $id = $_GET['param'];
}

require_once("../PDO/Gateway.php");
Gateway::connection();
$dataConf = Gateway::getInfoConfig($id);
$dataConf['parcours']=Gateway::getParcours($id);
/*foreach($dataConf['profile'] as $p)
{
	//$dataConf['profile'][$p['code']]="";

}*/
$mapping=Gateway::getMapping();
$exclusion=Gateway::getExclusion();
$grabber=Gateway::getConfigurationGrabber();
include("../Vue/RecuperationModifConfig.php");
?>

