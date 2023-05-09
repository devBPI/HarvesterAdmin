<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<title>Paramétrage</title>
</head>

<?php
include ('../Vue/Header.php');
?>

<body>
	<div class="content">
		<div class="triple-column-container">
			<div class="column" style="height:80px">
			<a href="../Controlleur/Traduction.php" class="buttonlink">&laquo; Retour traduction</a>	
			</div>
			<div class="column" style="height:80px">
			<h3>Configuration actuelle : <?php echo $name;?></h3>
			</div>
		</div>

		<FORM action="TraductionConfiguration.php?modify=<?php echo $id;?>" method="post" id='conf' onsubmit="return confirm('Voulez vous vraiment modifier ces règles ?');">
			<table id="conf" class="table-config">
				<thead><tr>
					<th scope="col">Entité</th>
					<th scope="col">Champ</th>
					<th scope="col">Règles de traduction</th>
					<th scope="col">Insensible à la casse</th>
					<th scope="col">Suppression des espaces</th>
					<th scope="col"></th>
				</tr></thead>
				<tr class="hidden_field"><?php
						echo "<td scope=\"row\" data-label=\"Entité\"><select onclick='display_entity(this)'><option value='0'>Aucun choisi</option>";
						foreach($entities as $e)
						{
							echo "<option value='".$e['entity']."'>".$e['entity']."</option>";
						}
						echo "</select></td><td/><td data-label=\"Règles de traduction\"><select name='set'><option value='0'>Aucun choisi</option>";
						include("../Vue/ComboBox.php");
						echo "</select></td><td data-label=\"Insensible à la casse\"><input type='checkbox' name='case'/></td><td data-label=\"Suppression des espaces\"><input type='checkbox' name='trim'/></td>
							<td><button class='but' type='button' title='Supprimer un set' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td>";
					?>
				</tr>
				<?php
					if(!empty($conf))
					{
						foreach($conf as $key => $value)
						{
							echo "<tr class='entity' id='".$value['property']."'>";
							echo "<td scope=\"row\" data-label=\"Entité\"><select onclick='display_entity(this)'><option value='0'>Aucun choisi</option>";
							foreach($entities as $e)
							{
								echo "<option value='".$e['entity']."' ".(($e['entity']==$value['entity'])?'selected':'').">".$e['entity']."</option>";
							}
							echo "</select></td><td/><td data-label=\"Règles de traduction\"><select name='set".$key."'><option value='0'>Aucun choisi</option>";
							$id_param=$value['id'];
							include("../Vue/ComboBox.php");
							echo "</select></td>
							<td data-label=\"Insensible à la casse\"><input type='checkbox' name='case".$key."' ".(($value['case']!='f')?'':'checked')."/></td>
							<td data-label=\"Suppression des espaces\"><input type='checkbox' name='trim".$key."' ".(($value['trim']!='f')?'':'checked')."/></td>
							<td><button class='but' type='button' title='Supprimer un set' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
						}
					}
					else
					{
						echo "<tr class='entity' id='new'>";
						echo "<td scope=\"row\" data-label=\"Entité\"><select onclick='display_entity(this)'><option value='0'>Aucun choisi</option>";
						foreach($entities as $e)
						{
							echo "<option value='".$e['entity']."'>".$e['entity']."</option>";
						}
						echo "</select></td><td/><td data-label=\"Règles de traduction\"><select name='set-1'><option value='0'>Aucun choisi</option>";
						include("../Vue/ComboBox.php");
						echo "</select></td>
						<td data-label=\"Insensible à la casse\"><input type='checkbox' name='case'/></td><td data-label=\"Suppression des espaces\"><input type='checkbox' name='trim'/></td>
						<td><button class='but' type='button' title='Supprimer un set' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
					}
				?>
				<tr style="background-color:#dbe0e0"><td/><td/><td/><td/><td/><td><button class='ajout but' type='button' title='Ajouter une traduction' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement)'><img src='../ressources/add.png' width='30px' height='30px'/></button></td></tr>
			</table>
			<input type="submit" value="Modifier" class="buttonlink"/>
		</FORM>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
	<script src="../js/entities.js"></script>
</body>
</html>
