<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
$entities = Gateway::getEntities();
$section="Définition des prédicats";
unset($_POST['code']);
unset($_POST['function-1']);
unset($_POST['value-1']);
if(!empty($_POST))
{
	$donnee=array();
	$nb=0;
	/* On enlève les 4 premières valeurs de $_POST (celles du champ hidden) */
	array_shift($_POST);
	array_shift($_POST);
	array_shift($_POST);
	array_shift($_POST);
	foreach($_POST as $key => $value) {
		$k = str_replace('_',' ',$key);
		if (preg_match('/(id)/', $k)) {
			if (preg_match('/(new)/', $k)) {
				$nb = -abs($nb) - 1;
			} else {
				$nb = $value;
			}
		}
		if (preg_match('/(code)/', $k)) {
			$donnee[$nb]['code'] = $value;
		}
		if (preg_match('/(property)/', $k)) {
			$donnee[$nb]['property'] = $value;
		}
		if (preg_match('/(entity)/', $k)) {
			$donnee[$nb]['entity'] = $value;
		}
		if (preg_match('/(function)/', $k)) {
			$donnee[$nb]['function_code'] = $value;
		}
		if (preg_match('/(value)/', $k)) {
			// str_replace pour echapper l'apostrophe
			$donnee[$nb]['value'] = str_replace("'", "\'", $value);
		}
	}
	$array_error = array();
	$array_error = Gateway::updatePredicats($donnee);
}
$value = Gateway::getPredicatsOrderByEntityCode();
$functions = Gateway::getFilterCode();
include("../Vue/filtre/FiltrePredicat.php");
?>
