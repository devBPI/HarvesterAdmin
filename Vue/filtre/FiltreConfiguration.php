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
<title>Paramétrage</title>
</head>

<?php
include('../Vue/Header.php');
?>

<body>
	<div class="content">
		<div class="triple-column-container">
				<div class="column">
					<a href="../../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour filtre</a>
				</div>
				<div class="column">
					<h3><?php echo $configname['name']; ?></h3>
				</div>
		</div>

		<FORM action="FiltreConfiguration.php?modify=<?php echo $id;?>" method="post" id='conf' onsubmit="return confirm('Voulez vous vraiment modifier ces règles ?');">
		<h3><?php echo $name;?></h3>
			<table class="table-config" id="conf">
				<tr><th width=30%>Entité</th><th width=50%>Règles de filtrage</th><th></th><th></th><th></th></tr>
				<tr class="hidden_field"><?php
						echo "<td><select name='entity' onchange='display_rules(this)'><option value=''>Aucun choisi</option>";
						foreach($entities as $e)
						{
							echo "<option value='".$e."'>".$e."</option>";
						}
						echo "</select></td><td><select name='rule' hidden><option value=''>Aucun choisi</option></select>";
						echo "<td><input type='checkbox' name='case'/></td><td><input type='checkbox' name='trim'/></td>";
						echo "<td><button class='but' type='button' title='Supprimer un set' onclick='suppRegle(this)'><img src='../../ressources/cross.png'/ width='30px' height='30px'></button></td>";
					?>
				</tr>
				<?php
					if(!empty($conf))
					{
						foreach($conf as $key => $value)
						{
							echo "<tr id='".$value['id']."'>";
							echo "<td><select name='entity".$key."' onchange='display_rules(this)'><option value=''>Aucun choisi</option>";
							foreach($entities as $e)
							{
								echo "<option value='".$e."' ".(($e==$value['entity'])?'selected':'').">".$e."</option>";
							}
							echo "</select></td><td><select name='rule".$key."'><option value=''>Aucun choisi</option>";
							foreach($data as $d)
							{
								echo (($d['entity']==$value['entity'])?"<option value='".$d['id']."' ".(($d['name']==$value['name'])?'selected':'').">".$d['name']."</option>":"");
							}
							echo "</select></td>
							<td><input type='checkbox' name='case'/></td><td><input type='checkbox' name='trim'/></td>
							<td><button class='but' type='button' title='Supprimer un set' onclick='suppRegle(this)'><img src='../../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
						}
					}
					else
					{
						echo "<tr id='new'>";
						echo "<td><select name='entitynew' onchange='display_rules(this)'><option value='0'>Aucun choisi</option>";
						foreach($entities as $e)
						{
							echo "<option value='".$e."'>".$e."</option>";
						}
						echo "</select></td><td><select name='rule' hidden><option value='0'>Aucun choisi</option>";
						echo "</select></td>
						<td><input type='checkbox' name='case'/></td><td><input type='checkbox' name='trim'/></td>
						<td><button class='but' type='button' title='Supprimer un set' onclick='suppRegle(this)'><img src='../../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
					}
				?>
				<tr style="background-color:#dbe0e0" id="add_row"><td></td><td></td><td></td><td></td><td><button class='ajout but' type='button' title='Ajouter une traduction' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement)'><img src='../../ressources/add.png' width='30px' height='30px'/></button></td></tr>
			</table>
			<input type="submit" value="Modifier" class="button primairy-color round"/>
		</FORM>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../../js/toTop.js"></script>
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
