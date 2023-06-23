<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
$section = "Filtre";
$rules=Gateway::getFilterRuleOrderBy32();
$categories=Gateway::getPredicatsOrderByCode();
$data=Gateway::getConf();
for ($i=0; $i < count($data); $i++) {
	$data[$i]["confname"] = Gateway::getConfigurationName($data[$i]["id"])["name"];
}
include ('../Vue/filtre/Filtre.php');
?>
