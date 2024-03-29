<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}	
?>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/selectStyle.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" />

	<title>Définition des prédicats</title>
</head>

<body>
	<?php include('../Vue/common/Header.php'); ?>

	<div class="content">
		<div class="button_top_div_with_margin">
			<a href="../../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour aux filtres</a>
		</div>
			<?php
			if(isset($_GET['set']) && isset($cat))
			{
				foreach($cat as $c)
				{
					echo "<input type='checkbox' id='".$c['name']."' onclick='display()' ".((in_array($c['name'],$checked))?'checked':'').">".$c['name']."</input>";
				}
			}
			?>

		<form action="FiltrePredicat.php" method="post">
			<table class="table-config">
			<tr><th style="display:none"></th><th>Code</th><th>Entité</th><th>Champ</th><th>Fonction</th><th>Valeur</th><th style="width:10%"></th></tr>
			<tr class="hidden_field" id="new">
				<td style="display:none"><input aria-label="Id nouveau prédicat" name="idnew" value="vide"/></td>
				<td><input aria-label="Code nouveau prédicat" type="text" name="code"/></td>
				<td><select aria-label="Entité nouveau prédicat" name="newEnt" onchange='display_entity(this)'><option value="">Sélectionnez une entité</option>
				<?php if(isset($entities) && $entities) {
					foreach($entities as $e) {
						echo "<option value='".$e."'>".$e."</option>";
					}
				}?>
				</select></td><td></td>
				<td>
					<select aria-label="Fonction" name="function">
				<?php if(isset($functions) && $functions) {
					foreach ($functions as $f) {
						echo "<option value='" . $f['code'] . "' >" . $f['code'] . "</option>";
					}
				} ?>
					</select>
				</td>
				<td id="valueBox">
					<input aria-label="Valeur" name='value' type='text'/>
				</td>
				<td>
					<button class='but' type='button' title='Supprimer un prédicat' onclick='delete_field(this.parentElement.parentElement)'>
						<img alt="Supprimer un prédicat" src='../../ressources/cross.png' width='30px' height='30px'>
					</button>
				</td>
			</tr>

				<?php
					if(isset($value))
					{
						foreach($value as $k => $v)
						{
							echo "<tr class='entity' id='".$v['property']."' name='pred'><td style='display:none'><input type='text' name='id".$k."' value='".$v['id']."' /></td>";
							if (isset($array_error)) { // Le champ code s'affiche différemment s'il y a eu une erreur
								$est_error = false;
								foreach ($array_error as $error) {
									if ($error["id"] == $v["code"]) {
										echo "<td><input class='input-error' type='text' value='" . $v['code'] . "' name='code" . $k . "' required /></td>";
										$est_error = true;
									}
								}
								if (!$est_error) {
									echo "<td><input type='text' value='" . $v['code'] . "' name='code" . $k . "' required /></td>";
								}
							} else {
								echo "<td><input type='text' value='" . $v['code'] . "' name='code" . $k . "' required /></td>";
							}
							echo "<td><select name='entity".$k."' onchange='display_entity(this)' required><option value=''>Sélectionnez une entité</option>";
							foreach($entities as $e)
							{
								echo "<option value='".$e."' ".(($e==$v['entity'])?'selected':'').">".$e."</option>";
							}
							echo "</select></td><td name='champ".$k."'/>";
							echo "<td><select name='function".$k."' onchange='display_valueBox(this,". $k .",\"" . $v['val'] . "\")' required>"; // Ligne de sélection de IS_EMPTY, STARTS_WITH...
							foreach($functions as $f)
							{
								echo "<option value='".$f['code']."' ".(($v['function_code']==$f['code'])?'selected':'').">".$f['code']."</option>";
							}	?>
							</select></td>
							<td id="valueBox<?= $k ?>">
							<?php // Si IS_EMPTY ou IS_NOT_EMPTY : il ne faut pas remplir la valeur
							if ($v['function_code'] == "IS_EMPTY" || $v['function_code'] == "IS_NOT_EMPTY") { ?>
								<input aria-label="Valeur du prédicat" name="value<?= $k ?>" type="text" value="" disabled/>
							<?php } else { // Sinon il faut obligatoirement renseigner une valeur
								if ($v['val']) { ?>
								<input aria-label="Valeur du prédicat" name="value<?= $k ?>" type="text" value="<?= $v['val'] ?>" required/>
								<?php } else { ?>
								<input aria-label="Valeur du prédicat" name="value<?= $k ?>" type="text" value="" required/>
								<?php }
							} ?>
							</td>
							<td>
								<button class='but' type='button' title='Supprimer un prédicat' onclick='delete_field(this.parentElement.parentElement)'>
									<img alt="Supprimer un prédicat" src='../../ressources/cross.png' width='30px' height='30px'>
								</button>
							</td></tr>
						<?php }
					}
					else
					{ ?>
						<tr class='entity' id='new'><td style='display:none'><td><input aria-label="Id nouveau prédicat" type='text' name='idnew' value='vide'/></td>
						<td><input aria-label="Code nouveau prédicat" type='text' name='code'/></td>
						<td><select aria-label="Opérateur" onchange='display_entity(this)' name='entity-1'><option value=''>Aucun choisi</option>
						<?php if ($entities) {
							foreach ($entities as $e) {
								echo "<option value='" . $e['entity'] . "'>" . $e['entity'] . "</option>";
							}
						} ?>
						</select></td><td></td>
						<td><select aria-label="Fonction" onchange="display_valueBox(this, 0, '')" name="function">
						<?php if($functions) {
							foreach ($functions as $f) {
								echo "<option value='" . $f['code'] . "' " . (($v['function_code'] == $f['code']) ? 'selected' : '') . ">" . $f['code'] . "</option>";
							}
						}?>
						</select></td><td><input aria-label="Valeur" name='value-1' type='text'/></td>
						<td><button class='but' type='button' title='Supprimer un prédicat' onclick='delete_field(this.parentElement.parentElement)'><img alt="Supprimer un prédicat" src='../../ressources/cross.png' width='30px' height='30px'></button></td></tr>
				<?php } ?>
				<tr id="add_row">
					<td>
						<button class='ajout but' type='button' title='Ajouter un prédicat' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement, "filtre_predicat")'>
							<img alt="Ajouter un prédicat" src='../../ressources/add.png' width='30px' height='30px'/></button>
					</td>
					<td></td><td></td><td></td>
					<td colspan="2" style="text-align: right">
						<input type="submit" value="Enregistrer"/>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<?php  // $_POST est rempli après envoi du formulaire (bouton "Enregistrer")
	if(!empty($_POST)) : ?>
	<div id="page-mask" style="display:block"></div>
	<div class="form-popup" id="validateForm" style="display:block">
		<div class="form-container" id="formProperty">
		<?php if (isset($array_error) && $array_error != null) { ?>
		<!--<form action="#" class="form-container" id="formProperty">-->
		<?php } else { ?>
		<form action="../../Controlleur/Filtre.php" class="form-container" id="formProperty">
		<?php } ?>
			<h3>Modification</h3>
			<div class="form-popup-corps">
				<?php if (!(isset($array_error) && $array_error != null)) { ?>
				<p>Les modifications ont bien été enregistrées.</p>
				<button type="submit" class="buttonlink">OK</button>
			</div>
		</form>
				<?php } else { ?>
				<p>Les modifications valides ont bien été enregistrées.</p>
				<p class="avertissement_light">Les modifications suivantes n'ont pas été prises en compte :<br/>
					<?php foreach($array_error as $error) { ?>
						<?= $error['msg'] ?><br/>
					<?php } ?>
				</p>
				<button class="buttonlink" onclick="closeForm()">OK</button>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../../js/toTop.js"></script>
	<script src="../../js/pop_up.js"></script>
	<script src="../../js/add_fields.js"></script>
	<script src="../../js/filtres_traductions/entities.js"></script>
	<script src="../../js/filtres_traductions/predicate.js"></script>

</body>
</html>
