<?php
require_once("../PDO/Gateway.php");
Gateway::connection();
echo "<option value=0>Aucun choisi</option>";
foreach($_POST as $c)
{
	$destination = Gateway::getDestination($c);
	foreach($destination as $dest)
	{
		echo "<option value='".$dest['value']."'>".$dest['value']."</option>";
	}
}
?>


