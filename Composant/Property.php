<?php
require_once ("../PDO/Gateway.php");
Gateway::connection();
$data = Gateway::getNotice($_POST['id']);
$selected = (isset($_POST['selected']))?$_POST['selected']:null;
echo "<select><option value=''>Aucun choisi</option>";
if(!empty($data))
{
	foreach($data as $d)
	{
		echo "<option value='".$d."' ".(($d==$selected)?"selected":"").">".$d."</option>";
	}
}
echo "</select>";
 ?>


