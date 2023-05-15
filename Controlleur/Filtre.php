<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
$section = "Filtre";
$rule=Gateway::getFilterRule();
$categories=Gateway::getPredicats();
$data=Gateway::getConf();
include ('../Vue/filtre/Filtre.php');
?>


