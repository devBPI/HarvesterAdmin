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
	<link rel="stylesheet" href="../css/selectStyle.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />

	<title>Paramétrage</title>
</head>

<body>
	<?php include ('../Vue/Header.php'); ?>

	<div class="content">
			<a href="../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour filtre</a><br>
			<?php
			if(isset($_GET['set']))
			{
				foreach($cat as $c)
				{
					echo "<input type='checkbox' id='".$c['name']."' onclick='display()' ".((in_array($c['name'],$checked))?'checked':'').">".$c['name']."</input>";
				}
			}
			?>

		<FORM action="FiltrePredicat.php" method="post">
			<table class="table-config">
			<tr><th style='display:none'></th><th>Code</th><th>Entité</th><th>Champ</th><th>Fonction</th><th>Valeur</th><th width=10%></th></tr>
						<tr class='hidden_field' id='new' name='pred'>
						<td style='display:none'><input name='idnew' value='vide'/></td>
						<td><input type='text' name='code'/></td>
						<td><select name='newEnt' onchange='display_entity(this)'><option value=''>Aucun choisi</option>
						<?php foreach($entities as $e)
						{
							echo "<option value='".$e."'>".$e."</option>";
						}?>
						</select></td><td name='entity-1'></td>
						<td><select name='function-1'>
						<?php 
							foreach($functions as $f)
							{
								echo "<option value='".$f['code']."' ".(($v['function_code']==$f['code'])?'selected':'').">".$f['code']."</option>";
							}	
						?>
							</select></td>
						<td><input name='value-1' type='text'/></td>
						<td><button class='but' type='button' title='Supprimer un prédicat' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>

				<?php
					if(isset($value))
					{
						foreach($value as $k => $v)
						{
							echo "<tr class='entity' id='".$v['property']."' name='pred'><td style='display:none'><input type='text' name='id".$k."' value='".$v['id']."' /></td>";
							echo "<td><input type='text' value='".$v['code']."' name='code".$k."' required /></td>";
							echo "<td><select name='entity".$k."' onchange='display_entity(this)' required><option value=''>Aucun choisi</option>";
							foreach($entities as $e)
							{
								echo "<option value='".$e."' ".(($e==$v['entity'])?'selected':'').">".$e."</option>";
							}
							echo "</select></td><td name='entity".$k."'/>";
							echo "<td><select name='function".$k."' required>";
							foreach($functions as $f)
							{
								echo "<option value='".$f['code']."' ".(($v['function_code']==$f['code'])?'selected':'').">".$f['code']."</option>";
							}					
							echo "</select></td><td><input name='value".$k."' type='text' value='".$v['val']."'/></td>
							<td><button class='but' type='button' title='Supprimer un prédicat' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
						}
					}
					else
					{
						
						echo "<tr class='entity' id='new' name='pred'><td style='display:none'><td><input type='text' name='idnew' value='vide'/></td>";
						echo "<td><input type='text' name='code'/></td>";
						echo "<td><select onchange='display_entity(this)' name='entity-1'><option value=''>Aucun choisi</option>";
						foreach($entities as $e)
						{
							echo "<option value='".$e['entity']."'>".$e['entity']."</option>";
						}
						echo "</select></td><td />";
						echo "<td><select name='function-1'>";
						foreach($functions as $f)
						{
							echo "<option value='".$f['code']."' ".(($v['function_code']==$f['code'])?'selected':'').">".$f['code']."</option>";
						}					
						echo "</select></td><td><input name='value-1' type='text'/></td>
							<td><button class='but' type='button' title='Supprimer un prédicat' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";

					}
				?>	
				<tr style="background-color:rgba(0,0,0,0);" id="add_row"><td><button class='ajout but' type='button' title='Ajouter un prédicat' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement)'><img src='../ressources/add.png' width='30px' height='30px'/></button></td><td></td><td></td><td></td><td></td><td><input type='submit'/></td></tr>
			</table>
		</FORM>
	</div>

	<?php if(!empty($_POST)) : ?>
	<div id="page-mask" style="display:block"></div>
	<div class="form-popup" id="validateForm" style="display:block">
		<form action="../Controlleur/Filtre.php" class="form-container" id="formProperty">
			<h3>Modification</h3>
			<p>Les modifications ont été enregistrées.</p>
			<div class="row">
				<button type="submit" name="submitted" class="btn" style="float:right">OK</button>
			</div>
		</form>
	</div>
	<?php endif; ?>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
	<script src="../js/entities.js"></script>
	<script>
		function openForm() {
            document.getElementById("validateForm").style.display = "block";
            document.getElementById("page-mask").style.display = "block";
        }
	</script>
</body>
</html>
