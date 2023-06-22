<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
$data = Gateway::getFilterByConf($_POST['id']);
echo 	"<th width=30%>Entité</th><th width=70%>Règles de filtrage</th>";
if($data)
{
	foreach($data as $value)
	{
		echo "<tr><td>".str_replace("_", "_<wbr/>", $value['entity'])."</td><td>".$value['name']."</td></tr>";
	}
}
?>


