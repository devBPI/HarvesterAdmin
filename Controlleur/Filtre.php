<?php
require_once ("../Gateway.php");
Gateway::connection();
$section = "Filtre";
$rule=Gateway::getFilterRule();
$categories=Gateway::getPredicats();
$data=Gateway::getConf();
include ('../Vue/Filtre.php');
?>


