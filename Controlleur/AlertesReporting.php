<html style="overflow-x: hidden; overflow-y: auto;">
<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}




require_once ("../Gateway.php");
Gateway::connection();

if(isset($_POST['deleteRow']) && $_POST['deleteRow']!=''){
	Gateway::deleteAlert($_POST['deleteRow']);
}
$order = (isset($_GET['order']))?$_GET['order']:"creation_time DESC";
$alerts = Gateway::getAlerts($order);


$limit = 100;
if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}
;
$start_from = ($page - 1) * $limit;
$section = "Alertes";
include ("../Vue/AlertesReporting.php");
?>

