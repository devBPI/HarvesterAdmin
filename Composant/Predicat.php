<?php
require_once ("../PDO/Gateway.php");
if(isset($_POST['id'])){
	$_GET['id']=$_POST['id'];
}
Gateway::connection();
$functions = Gateway::getFilterCode();
$entities = Gateway::getEntities();
$ruleEntity = Gateway::getRuleEntity($_GET['id'])['entity'];
$predicats = Gateway::getPredicatsByEntity($ruleEntity);
$num=(isset($_POST['num']))?$_POST['num']:$GLOBALS['nb'];
?>
<tr><th width='40%'>Prédicat</th><th>Entité</th><th>Champ</th><th>Fonction</th><th>Valeur</th></tr>

<?php
if(isset($d) && $d!=null) {
	$value = Gateway::getPredicat($d['pred'])[0];
	if($value) {
		?>
		<tr class="entity" id="<?= $value['property'] ?>">
			<td>
				<select onchange='update_predicat(this, <?= json_encode($predicats, JSON_HEX_APOS) ?>)' name='entity<?= $num ?>' required>
					<option value=''>Choississez un prédicat</option>
		<?php foreach($predicats as $p) { ?>
			<option value='<?= $p["code"] ?>' <?= (($p['code']==$value['code'])?'selected':'') ?> ><?= $p["code"] ?></option>
		<?php } ?>
				</select>
			</td>
			<td><?= str_replace("_", "_<wbr>",$value['entity']) ?></td>
			<td><?= str_replace("_", "_<wbr>",$value['property']) ?></td>
			<td><?= str_replace("_", "_<wbr>",$value['function_code']) ?></td>
			<td><?= $value['val'] ?></td>
		</tr>
	<?php
	}
	else { ?>
		<tr class="entity" id="new">
			<td>
				<select onchange='update_predicat(this,<?= json_encode($predicats, JSON_HEX_APOS) ?>)' name='entity<?= $num ?>' required>
					<option value=''>Choississez un prédicat</option>
		<?php foreach($predicats as $p) { ?>
			<option value='<?= $p["code"] ?>'><?= $p["code"] ?></option>
		<?php } ?>
			</select></td>
			<td></td><td></td><td></td><td></td>
		</tr>
	<?php }
}
else { ?>
	<tr class='entity' id='new'>
	<td>
	<select onchange='update_predicat(this,<?= json_encode($predicats, JSON_HEX_APOS) ?>)' name='entity<?= $num ?>' required><option value=''>Choississez un prédicat</option>
		<?php foreach($predicats as $p) { ?>
			<option value='<?= $p['code'] ?>'><?= $p['code'] ?></option>
		<?php } ?>
		</select></td>
	<td></td><td></td><td></td><td></td>
<?php } ?>