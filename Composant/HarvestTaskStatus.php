<?php
	require_once("../Gateway.php");
	Gateway::connection();
	$status = Gateway::getMoissonStatus($_POST['val']);
	echo "<div>".$status."</div>";
?>
