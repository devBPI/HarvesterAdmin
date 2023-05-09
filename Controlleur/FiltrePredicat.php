<?php
require_once ("../Gateway.php");
Gateway::connection();
$entities = Gateway::getEntities();
$section="Filtre - Définition des prédicats";
unset($_POST['code']);
unset($_POST['function-1']);
unset($_POST['value-1']);
if(!empty($_POST))
{
	$a=false;
	$donnee=array();
	$nb=0;
	foreach($_POST as $key => $value)
	{
		$k = str_replace('_',' ',$key);
		if(preg_match('/(id)/',$k))
		{
			if(preg_match('/(new)/',$k))
			{
				$nb=-abs($nb)-1;
			}
			else
			{
				$nb=$value;
			}
		}
		if(preg_match('/(code)/',$k))
		{
			$donnee[$nb]['code']=$value;
		}
		if(preg_match('/(property)/',$k))
		{
			$donnee[$nb]['property']=$value;
		}
		if(preg_match('/(entity)/',$k))
		{
			$donnee[$nb]['entity']=$value;
		}
		if(preg_match('/(function)/',$k))
		{
			$donnee[$nb]['function_code']=$value;
		}
		if(preg_match('/(value)/',$k))
		{
			$donnee[$nb]['value']=$value;
		}
	}
	Gateway::updatePredicats($donnee);
}
$value = Gateway::getPredicats();
$functions = Gateway::getFilterCode();
include("../Vue/FiltrePredicat.php");
?>


