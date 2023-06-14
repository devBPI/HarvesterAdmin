<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/selectStyle.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" />
	<title>Configuration et règles de filtrage</title>
</head>

<?php
include('../Vue/common/Header.php');
?>

<body>
	<div class="content">
		<div class="triple-column-container">
				<div>
					<a href="../../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour aux filtres</a>
				</div>
				<div class="column">
					<div class="config_name_and_sub_title">
						<h3 class="config_name">Configuration : <?= $configname['name'] ?></h3>
						<p class="sub_title"></p>
					</div>
				</div>
		</div>
		<?php if (!empty($array_error)) { ?>
			<div class="avertissement">
				<p style="text-align:left">ERREURS :<br/>
				<?php foreach ($array_error as $error) { ?>
				Il existe déjà une règle associée à l'entité <?= $error["entity"] ?> pour la configuration <?= $configname['name'] ?>. Une seule a été conservée.</br>
				<?php } ?>
				</p>
			</div>
		<?php } ?>
		<form action="FiltreConfiguration.php?modify=<?= $id ?>" method="post" id="conf" onsubmit="return confirm('Voulez vous vraiment modifier ces règles ?');">
		<h3><?= $name ?></h3>
			<table class="table-config" id="conf">
				<tr><th width=30%>Entité</th><th width=50%>Règles de filtrage</th><th></th></tr>
				<tr class="hidden_field"><?php
						echo "<td><select name='entity' onchange='display_rules(this)'><option value=''>Sélectionnez une entité</option>";
						foreach($entities as $e)
						{
							echo "<option value='".$e."'>".$e."</option>";
						}
						echo "</select></td><td><select name='rule' hidden><option value=''>Sélectionnez une entité</option></select>";
						echo "<td style='text-align:right'><button class='but' type='button' title='Supprimer un set' onclick='suppRegle(this)'><img src='../../ressources/cross.png'/ width='30px' height='30px'></button></td>";
					?>
				</tr>
				<?php
					if(!empty($conf))
					{
						foreach($conf as $key => $value)
						{
							echo "<tr id='".$value['id']."'>";
							echo "<td><select name='entity".$key."' onchange='display_rules(this)' required><option value=''>Sélectionnez une entité</option>";
							foreach($entities as $e) {
								echo "<option value='".$e."' ".(($e==$value['entity'])?'selected':'').">".$e."</option>";
							}
							echo "</select></td><td><select name='rule".$key."' required><option value=''>Sélectionnez une règle</option>";
							foreach($data as $d)
							{
								echo (($d['entity']==$value['entity'])?"<option value='".$d['id']."' ".(($d['name']==$value['name'])?'selected':'').">".$d['name']."</option>":"");
							}
							echo "</select></td>
							<td style='text-align:right'><button class='but' type='button' title='Supprimer un set' onclick='suppRegle(this)'><img src='../../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
						}
					}
					else
					{
						echo "<tr id='new'>";
						echo "<td><select name='entitynew' onchange='display_rules(this)' required><option value=''>Sélectionnez une entité</option>";
						foreach($entities as $e)
						{
							echo "<option value='".$e."'>".$e."</option>";
						}
						echo "</select></td><td><select name='rule' hidden><option value=''>Sélectionnez une entité</option>";
						echo "</select></td>
						<td style='text-align:right'><button class='but' type='button' title='Supprimer un set' onclick='suppRegle(this)'><img src='../../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
					}
				?>
				<tr style="background-color:#dbe0e0" id="add_row"><td></td><td></td><td style='text-align:right'><button class='ajout but' type='button' title='Ajouter une règle' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement)'><img src='../../ressources/add.png' width='30px' height='30px'/></button></td></tr>
			</table>
			<input type="submit" value="Modifier" class="button primairy-color round"/>
		</form>
	</div>

	<?php  // $_POST est rempli après envoi du formulaire (bouton "Enregistrer")
	if(!empty($_POST)) : ?>
		<div id="page-mask" style="display:block"></div>
		<div class="form-popup" id="validateForm" style="display:block">
			<div class='form-container' id='formProperty'>
				<?php if (isset($array_error) && $array_error != null) { ?>
					<!--<form action="#" class="form-container" id="formProperty">-->
				<?php } else { ?>
				<form action="../../Controlleur/Filtre.php" class="form-container" id="formProperty">
					<?php } ?>
					<h3>Modification</h3>
					<div class='form-popup-corps'>
						<?php if (!(isset($array_error) && $array_error != null)) { ?>
						<p>Les modifications ont bien été enregistrées.</p>
						<button type="submit" class="buttonlink">OK</button>
					</div>
				</form>
			<?php } else { ?>
				<p>Les modifications valides ont bien été enregistrées.</p>
				<p class="avertissement_light">Certaines modifications n'ont pas été prises en compte.<br/>
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
	<script src="../../js/entities.js"></script>
	<script>
		function suppRegle(element){
			var td = element.parentNode;
			var tr = td.parentNode;
			tr.parentNode.removeChild(tr);
		}
	</script>
</body>
</html>
