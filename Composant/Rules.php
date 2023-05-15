<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
$data = Gateway::getFilterRules($_POST['id']);
$selected = (isset($_POST['selected']))?$_POST['selected']:null;
echo "<select><option value=''>Aucune sélection</option>";
if(!empty($data))
{
	foreach($data as $d)
	{
		echo (($d['entity']==$selected)?"<option value='".$d['id']."'>".$d['name']."</option>":"");
	}
}
echo "</select>";
 ?>


