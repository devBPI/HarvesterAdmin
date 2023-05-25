<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$rules_set = Gateway::getRulesSet();
$categories = Gateway::getTranslationCategory();
if(isset($_POST['trad']))
{
	
	$id_param = ($_POST['trad']>0)?$_POST['trad']:null;
	if($id_param!=null)
	{
		$conf_set = Gateway::getSetByConf($id_param);
	}
}
$data = Gateway::getConf();
for ($i=0; $i < count($data); $i++) {
	$data[$i]["confname"] = Gateway::getConfigurationName($data[$i]["id"])["name"];
}
$section = "Traduction";
include ('../Vue/traduction/Traduction.php');
?>


