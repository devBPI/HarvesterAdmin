<?php
	require_once("../PDO/Gateway.php");
	Gateway::connection();
	$status = Gateway::getMoissonStatus($_POST['val']);
	echo "<div>".$status."</div>";
?>
