<?php
function treeSet()
{
	if(!$_POST) {
		return;
	}
	$value=array_pop($_POST);
	$d=null;
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
function treeDisplay($d, $profondeur=0) {
	if(!$d) {
		return;
	}
	$op=$d['operator'];
	$GLOBALS['nb']++;
	if (sizeof($d)<3)
		$op='';
	if ($profondeur % 2 == 0)
		echo "<div class='operation operation_even operation" . $profondeur . "'><select class='profondeur" . $profondeur . "' name ='operator".$GLOBALS['nb']."' onchange='update_operation(this, " . $profondeur . ")'>
				<option value='OPERATION' ".(($op=='')?'selected':'').">OPERATION</option>
				<option value='OR' ".(($op=='OR')?'selected':'').">OR</option>
				<option value='AND' ".(($op=='AND')?'selected':'').">AND</option>
		</select>";
	else
		echo "<div class='operation operation" . $profondeur . "'><select class='profondeur" . $profondeur . "' name ='operator".$GLOBALS['nb']."' onchange='update_operation(this, " . $profondeur . ")'>
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
		treeDisplay($d[0], $profondeur+1);
		treeDisplay($d[1], $profondeur+1);
	}
	echo "</div>";
}
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
require_once ("../PDO/Gateway.php");

/* Si affichage de l'arbre de la règle */
if(isset($_GET["id"]))
{
	$id=$_GET["id"];
	$val=Gateway::getRuleNameRootEntity($id); // $val['name'] : nom de la règle; $val['id'] : id de la racine de l'arbre
	$idR=$val["id"];
}
/* Si modification de la règle */
else if(isset($_GET["modify"]))
{
	$id=$_GET["modify"]; // Contient l'id de la règle
	$val=Gateway::getRuleNameRootEntity($id); // $val['name'] : nom de la règle; $val['id'] : id de la racine de l'arbre
	$idR=$val["id"]; // Racine de l'arbre
	$_POST=array_reverse($_POST); // Inversion des données de $_POST
	$donnee=treeSet(); // Création de l'arbre
	$idRoot=Gateway::insertTree($donnee,$idR);
	if($idRoot!=null) {
		Gateway::setRuleTreeRoot($id,$idRoot);
		$idR=$idRoot;
	}
	header("Location: ../Controlleur/FiltreTree.php?id=".$id."&success=true");
}
$name=$val["name"];
$entity=$val["entity"];
$data=null;
if($idR!=null)
	$data=Gateway::getRuleTree($idR); // On retrouve l'arbre grâce à sa racine
if($data==null){
	$data = array(
		"operator" => null,
		"pred" => null,
	);
}
$profondeur = 0;

$configurations = Gateway::getConfigurationByFilterRule($_GET["id"]);

$section = "Définition d'une règle";

include ('../Vue/filtre/FiltreTree.php');
?>

