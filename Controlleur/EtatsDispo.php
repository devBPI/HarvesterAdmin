<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
if(!empty($_GET['add']) and $_GET['add']=="true" and $_POST['code']!='')
{
	Gateway::insertStatus($_POST['code'], $_POST['list_dispo'], $_POST['to_harvest'], $_POST['label']);
	$_GET['add']="false";
}
else if(!empty($_GET['delete']))
{
	Gateway::deleteStatus($_GET['delete']);
}
else if (!empty($_POST)) {
    Gateway::updateStatus($_GET['code'], $_POST['list_dispo'], $_POST['to_harvest'], $_POST['label']);
}

$data = Gateway::getStatus();
$modify = (isset($_GET['modify'])) ? $_GET['modify'] : "false";
$section = "États de Disponibilité";
include("../Vue/etats_dispo/EtatsDispo.php");
?>