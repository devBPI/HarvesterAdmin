<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$special_char = array(1=>'(',2=>')');
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
// id == modifify ?
$set=(isset($_GET['set']))?$_GET['set']:urldecode($_GET['modify']);
if(isset($_GET['modify']) and !isset($_GET['f']))
{
	$donnee=array();
	$nb=-1;
	unset($_POST['input']);
	foreach($_POST as $key => $value)
	{
		$k = str_replace('_',' ',$key);
		if(preg_match('/(input)/',$k))
		{
			$nb++;
			$donnee[$nb]['input']=$value;
		}
		if(preg_match('/(rep)/',$k))
		{
			$donnee[$nb]['rep']=$value;
		}
	}
	Gateway::updateTranslationRule($donnee,$set);
}
$cat = Gateway::getCategory();
$data = Gateway::getTrads($set);
$conf = Gateway::getConfBySet($set);
$c = Gateway::getCategoryBySet($set);
$checked;
foreach($c as $k => $n)
{
	$checked[$k]=$n['name'];
}
$section = "Traduction";
include ('../Vue/traduction/TraductionSet.php');
?>


