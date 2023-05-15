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
	unset($_POST['set']);
	$id = $_GET['modify'];
	$donnee=array();
	$nb=-1;
	foreach($_POST as $key => $value)
	{
		if(preg_match('/(property)/',$key))
		{
			$nb++;
			$donnee[$nb]['property']=$value;
		}
		if(preg_match('/(set)/',$key))
		{
			$donnee[$nb]['set']=$value;
		}
		if(preg_match('/(case)/',$key))
		{
			$donnee[$nb]['case']=$value;
		}
		if(preg_match('/(trim)/',$key))
		{
			$donnee[$nb]['trim']=$value;
		}
	}
	Gateway::updateTranslationConfiguration($id,$donnee);
}
$entities = Gateway::getEntities();
$conf = Gateway::getSetByConf($id);
$data = Gateway::getRulesSet();
$name = Gateway::getHarvestConfigurationDifferential($id)[0]['name'];
$section = "Traduction";
include "../Vue/traduction/TraductionConfiguration.php";
?>


