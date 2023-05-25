<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$table = "translation";
require_once ("../PDO/Gateway.php");
Gateway::connection();
$data = Gateway::getCategory();
$set=[];
foreach($data as $k => $v)
{
	$set[$k]=$v['name'];
}
if(isset($_GET['modify']))
{
	$mod = $_GET['modify'];
	if($mod=='true')
	{
		unset($_POST['t']);
		$t1=array_diff($_POST,$set);
		$t2=array_diff($set,$_POST);
		$tr=array_diff_key($t2,$t1);
		Gateway::updateCategory($t1,$t2);
		Gateway::deleteCategory($tr);
		$set = $_POST;
	}
}
else{
	$mod='true';
}
$section = "Ensembles de cibles de traduction";
include ('../Vue/traduction/TraductionCategory.php');
?>


