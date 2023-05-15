<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$data = Gateway::getFilterRules();
$entities=Gateway::getEntities();
if(isset($_GET['modify']))
{
	$mod = $_GET['modify'];
	if($mod=='true')
	{
		$nb=0;
		unset($_POST['namenew']);
		unset($_POST['entitynew']);
		foreach($_POST as $key => $value)
		{
			$k = str_replace('_',' ',$key);
			if(preg_match('/(name)/',$k))
			{
				if(preg_match('/(new)/',$k))
				{
					$nb=-abs($nb)-1;
				}
				else
				{
					$nb=str_replace('name','',$k);
				}
				$donnee[$nb]['name']=$value;
			}
			if(preg_match('/(entity)/',$k))
			{
				$donnee[$nb]['entity']=$value;
			}
		}
		Gateway::updateFilterRules($donnee);
		$data = Gateway::getFilterRules();
	}
}
else
{
	$mod='true';
}
$section = "Filtre - Édition des règles";
include ('../Vue/filtre/FiltreRules.php');
?>


