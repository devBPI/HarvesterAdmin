<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}

$rows = 20;

require_once ("../Gateway.php");
Gateway::connection();
if(isset($_GET['id']))
{
	Gateway::reprise($_GET['id']);
	?>
		<script> window.location="HistoriqueTachesAnnexes.php";</script>
	<?php
}
$page=1;
if(isset($_GET['page']))
{
	Gateway::reprise($_GET['page']);
	if(is_numeric($_GET['page']) && $_GET['page'] > 0)
		$page=$_GET['page'];
}

$total_records = Gateway::countSideTasks();
$total_pages = ceil($total_records / $rows);

$order = (isset($_GET['order']))?$_GET['order']:"id DESC";
$tasks = Gateway::getSideTasksPagined($order, $rows, $page);

$section = "Historique des Tâches Annexes";
include ("../Vue/HistoriqueTachesAnnexes.php");
?>