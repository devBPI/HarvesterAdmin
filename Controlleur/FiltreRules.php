<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$entities=Gateway::getEntities();
if(isset($_GET['modify'])) {
	$mod = $_GET['modify'];
	if(isset($_POST["submitted"]))
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
				$donnees[$nb]['name']=$value;
			}
			if(preg_match('/(entity)/',$k))
			{
				$donnees[$nb]['entity']=$value;
			}
		}
		$array_error = Gateway::updateFilterRules($donnees);
		// Remplissage du tableau $data (contient les données qui viennent d'être saisies)
		foreach ($donnees as $key => $value) {
			$data[] = [
				"id" => $key,
				"name" => $value["name"],
				"entity" => $value["entity"]
			];
		}
	}
}
else if(!isset($array_error) || (count($array_error) == 0))
{
	$mod='true';
}
// Si on n'a pas effectué de modifications
if (!isset($array_error) || (count($array_error) == 0)) {
	$data = Gateway::getFilterRuleOrderBy32();
}

$section = "Filtre - Édition des règles";
include ('../Vue/filtre/FiltreRules.php');
?>


