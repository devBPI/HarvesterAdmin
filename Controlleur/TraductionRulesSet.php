<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
$section = "Ensembles de règles de traduction";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$data = Gateway::getRulesSet();
$set=[];

foreach($data as $k => $v)
{
	$set[$k]=$v['name'];
}

// Vaut toujours "false" sauf quand !isset
$mod = isset($_GET['modify'])?"false":"true";

if(!empty($_POST)) {
	unset($_POST['t']);
	$t1=array_diff($_POST,$set);
	$t2=array_diff($set,$_POST);
	$tr=array_diff_key($t2,$t1);
	Gateway::updateRulesSet($t1,$t2);
	Gateway::deleteRulesSet($tr);
	$set = $_POST;
} else {
	if($mod=="false") {
		$section = "Édition des ensembles de règles de traduction";
	}
}
include ('../Vue/traduction/TraductionRulesSet.php');
?>


