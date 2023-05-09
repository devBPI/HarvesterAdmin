<?php
require_once ("../Gateway.php");
Gateway::connection();
$data = Gateway::getSetByConf($_POST['id']);
echo 	"<th>Entité</th><th>Règles de traduction</th>";
if($data)
{
	foreach($data as $value)
	{
		echo "<tr><td>".$value['property']."</td><td>".$value['name']."</td></tr>";
	}
}
if($_POST['id']>0){
?>
<a href="../Controlleur/TraductionConfiguration.php?id=<?php echo $_POST['id'];?>">Editer configuration</a>
<?php } ?>


