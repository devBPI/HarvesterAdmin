<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
require_once("../PDO/Gateway.php");
Gateway::connection();
if(isset($_GET['niv']) or isset($_GET['n']))
{
	$niv=isset($_GET['n'])?$_GET['n']:$_GET['niv'];
	$n=$niv;
	$s=(isset($_GET['page']))?$_GET['page']:1;
	$data = Gateway::getLogs($niv,$s-1);
	switch($niv)
	{
		case "WARN":
			$niv="ERROR";
			break;
		case "ERROR":
			$niv="";
			break;
		default:
			$niv="WARN";
			break;
	}	
}
else
{
	$s=(isset($_GET['page']))?$_GET['page']:1;
	$data = Gateway::getLogs("",$s-1);
	$n="";
	$niv="WARN";
}
$nb=ceil(Gateway::countLogs($n)/15);
$section = "Logs";

include("../Vue/alerts_logs/JournalLogs.php");
?>

