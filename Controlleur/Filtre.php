<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
$section = "Filtre";
//$rule=Gateway::getFilterRule();
$rule=Gateway::getFilterRuleOrderBy32();
$categories=Gateway::getPredicatsOrderBy12();
$data=Gateway::getConf();
for ($i=0; $i < count($data); $i++) {
	$data[$i]["confname"] = Gateway::getConfigurationName($data[$i]["id"])["name"];
}
include ('../Vue/filtre/Filtre.php');
?>


