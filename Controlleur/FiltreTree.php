<?php
function treeSet()
{
	if(!$_POST)
	{
		return;
	}
	$value=array_pop($_POST);
	$d;
	$d['operator']=$value;
	if($value=='OPERATION')
	{
		$d['predicat']=array_pop($_POST);
	}
	else
	{
		$d['gauche']=treeSet();
		$d['droite']=treeSet();
	}
	return $d;
}
function treeDisplay($d)
{

	if(!$d)
	{
		return;
	}
	$op=$d['operator'];
	$GLOBALS['nb']++;
	echo "<div class='operation'><select name ='operator".$GLOBALS['nb']."' onchange='update_operation(this)'>
				<option value='OPERATION' ".(($op=='')?'selected':'').">OPERATION</option>
				<option value='OR' ".(($op=='OR')?'selected':'').">OR</option>
				<option value='AND' ".(($op=='AND')?'selected':'').">AND</option>
		</select>";
	if($op=='')
	{
		echo"<table class='table-config'>";
		include("../Composant/Predicat.php");
		echo "</table>";
	}
	else
	{
		treeDisplay($d[0]);
		treeDisplay($d[1]);
	}
	echo "</div>";
}
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
require_once ("../Gateway.php");
Gateway::connection();
$section = "Filtre - Définition d'une règle";
if(isset($_GET['id']))
{
	$id=$_GET['id'];
	$val=Gateway::getRuleName($id);
	$idR=$val['id'];
}
else if(isset($_GET['modify']))
{
	$id=$_GET['modify'];
	$val=Gateway::getRuleName($id);
	$idR=$val['id'];
	$_POST=array_reverse($_POST);
	$donnee=treeSet();
	print_r($donnee);
	$idRoot=Gateway::insertTree($donnee,$idR);
	if($idRoot!=null)
	{
		Gateway::setRuleTreeRoot($id,$idRoot);
		$idR=$idRoot;
	}
	header('Location: ../Controlleur/FiltreTree.php?id='.$id.'&f=true');
}
$name=$val['name'];
$data=Gateway::getRuleTree($idR);
if($data==null){
	$data = array(
		"operator" => "",
		"pred" => "",
	);
}
include ('../Vue/FiltreTree.php');
?>


