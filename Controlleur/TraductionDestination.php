<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$name = (isset($_GET['cat'])) ? $_GET['cat'] : (isset($_GET['id'])?$_GET['id']:"");
$temp=Gateway::getDestination($name);
$set=[];
$id = isset($_GET['id']) ? $_GET['id']: null;
if(!empty($temp))
{
	foreach($temp as $key => $value)
	{
		$set[$key]=$value['value'];
	}
}
if(isset($_GET['id']) && isset($_GET['modify']) && $_GET['modify'] == "true")
{
	$t1=array_diff($_POST,$set);
	unset($t1['d']);
	$t2=array_diff($set,$_POST);
	$tr=array_diff_key($t2,$t1);
	Gateway::updateDestination($t1,$t2,$name);
	Gateway::deleteDestination($tr);
	$set = $_POST;
	$mod = "true";
} else if (isset($_GET['modify']) && $_GET['modify'] == "false") {
	$mod = "false";
} else {
	$mod = "true";
}
$section = "Traduction";
include ('../Vue/traduction/TraductionDestination.php');
?>


