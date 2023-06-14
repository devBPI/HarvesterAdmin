<!-- Permet de gérer l'affichage du tricolor en haut. -->
<?php
	$ini = @parse_ini_file("../etc/configuration.ini", true);
	if (! $ini) {
		$ini = @parse_ini_file("../etc/default.ini", true);
	}
	ini_set("display_errors", 0);
	error_reporting(0);
	require_once ("../PDO/Gateway.php");
	Gateway::connection();
	$colorData = Gateway::getColor()[0];
	$ver = Gateway::getVersion();

    echo "<div style='display:block;float:right'>";
	echo "<div style='float:right;" . (($colorData['status'] == "green") ? "background:#00CC00" : "") . ";' class='tricolorRectangle'></div>";
	echo "<div style='float:right;" . (($colorData['status'] == "orange") ? "background:#FFCC00" : "") . ";' class='tricolorRectangle'></div>";
    echo "<div style='float:right;" . (($colorData['status'] == "red") ? "background:#DD0000" : "") . ";' class='tricolorRectangle'></div>";
	echo "</div>";
	if($ini['version']=='DEV')
	{
		echo "<p style='margin:auto;color:#ffffff'>version ".$ver."</p>";
	}
	else
	{
		echo "<div><p style='color:#ffffff'>version ".$ver."</p></div>";
	}
?>
