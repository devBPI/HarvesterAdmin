<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}

include ("../Composant/ErrorReportingConfig.php");

$rows = 20;

require_once ("../PDO/Gateway.php");
Gateway::connection();
if(isset($_GET['id']))
{
	Gateway::reprise($_GET['id']);
	?>
		<script> window.location="HistoriqueMoisson.php";</script>
	<?php
}
$page=1;
if(isset($_GET['page']))
{
	Gateway::reprise($_GET['page']);
	if(is_numeric($_GET['page']) && $_GET['page'] > 0)
		$page=$_GET['page'];
}

$total_records = Gateway::countHarvests();
$total_pages = ceil($total_records / $rows);

$order = (isset($_GET['order']))?$_GET['order']:"id DESC";
$tasks = Gateway::getTasksPagined($order, $rows, $page);
foreach($tasks as $key => $t)
{
	// if(preg_match('/(PENDING)/',$t['status']))
	if($t['status'] != 'INDEXED' AND $t['status'] != 'TO_HARVEST' AND !preg_match('/(ERROR)/',$t['status'])) // On affiche tout le temps la progress bar sauf etat initial et final
	{
		$tasks[$key]['progress']=Gateway::getProgress($t['id']);
	}
}
$section = "Historique des moissons";
include("../Vue/moissons/HistoriqueMoisson.php");
?>
