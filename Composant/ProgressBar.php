<?php
	require_once("../PDO/Gateway.php");
	Gateway::connection();
	$val = Gateway::getProgress($_POST['val']);
	$status = Gateway::getMoissonStatus($_POST['val']);
	echo '<div class="round" style="overflow-y:hidden"><div class="prog">Moisson:'.$val.'%</div><div class="bar" style="width:'.$val.'%;text-align:center"></div></div>';
?>
