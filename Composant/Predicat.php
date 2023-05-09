<?php
require_once ("../Gateway.php");
if(isset($_POST['id'])){
	$_GET['id']=$_POST['id'];
}
Gateway::connection();
$functions = Gateway::getFilterCode();
$entities = Gateway::getEntities();
$ruleEntity = Gateway::getRuleEntity($_GET['id'])['entity'];
$predicats = Gateway::getPredicatsByEntity($ruleEntity);
$num=(isset($_POST['num']))?$_POST['num']:$GLOBALS['nb'];
echo "<tr><th width='40%'>Prédicat</th><th>Entité</th><th>Champ</th><th>Fonction</th><th>Valeur</th></tr>";

if(isset($d))
{
	$value = Gateway::getPredicat($d['pred'])[0];
	if($value)
	{
		echo "<tr class='entity' id='".$value['property']."'>";
		echo "<td>";
		echo "<select onchange='update_predicat(this,".json_encode($predicats).")' name='entity".$num."'><option value=''>Choississez un prédicat</option>";
		foreach($predicats as $p)
		{
			echo "<option value='".$p['code']."' ".(($p['code']==$value['code'])?'selected':'').">".$p['code']."</option>";
		}
		echo "</select></td>";
		echo "<td>".$value['entity']."</td>";
		echo "<td>".$value['property']."</td>";
		echo "<td>".$value['function_code']."</td>";
		echo "<td>".$value['val']."</td></tr>";
	}
	else
	{
		echo "<tr class='entity' id='new'>";
		echo "<td>";
		echo "<select onchange='update_predicat(this,".json_encode($predicats).")' name='entity".$num."'><option value=''>Choississez un prédicat</option>";
		foreach($predicats as $p)
		{
			echo "<option value='".$p['code']."'>".$p['code']."</option>";
		}
		echo "</select></td>";
		echo "<td></td><td></td><td></td><td></td>";
	}
}
else
{
	echo "<tr class='entity' id='new'>";
	echo "<td>";
	echo "<select onchange='update_predicat(this,".json_encode($predicats).")' name='entity".$num."'><option value=''>Choississez un prédicat</option>";
		foreach($predicats as $p)
		{
			echo "<option value='".$p['code']."'>".$p['code']."</option>";
		}
		echo "</select></td>";
	echo "<td></td><td></td><td></td><td></td>";
}

 ?>


