<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$name = (isset($_GET['cat'])) ? $_GET['cat'] : $_GET['modify'];
$t=Gateway::getDestination($name);
$trads=[];
if(!empty($t))
{
	foreach($t as $k => $tr)
	{
		$trads[$k]=$tr['value'];
	}
}
if(isset($_GET['modify']) and !isset($_GET['f']))
{
	$t1=array_diff($_POST,$trads);
	unset($t1['d']);
	$t2=array_diff($trads,$_POST);
	$tr=array_diff_key($t2,$t1);
	Gateway::updateDestination($t1,$t2,$name);
	Gateway::deleteDestination($tr);
	$trads = $_POST;
}
$section = "Traduction";
include ('../Vue/traduction/TraductionDestination.php');
?>


