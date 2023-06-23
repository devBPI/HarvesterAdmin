<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
if(isset($_GET['modify']))
{
	unset($_POST['entity']);
	unset($_POST['rule']);
	$id = $_GET['modify'];
	$nb=-1;
	$donnee=array();
	foreach($_POST as $key => $value)
	{
		if(preg_match('/(entity)/',$key))
		{
			$nb++;
			$donnee[$nb]['entity']=$value;
		}
		if(preg_match('/(property)/',$key) || preg_match('/(rule)/',$key))
		{
			$donnee[$nb]['rule']=$value;
		}
	}
	$array_error = Gateway::updateFilterConfiguration($id,$donnee);
}
$entities = Gateway::getEntities();
$conf = Gateway::getFilterByConf($id);
$data = Gateway::getFilterRules();
$configname = Gateway::getConfigurationName($id);
$section = "Configuration et règles de filtrage";
include "../Vue/filtre/FiltreConfiguration.php";
?>


