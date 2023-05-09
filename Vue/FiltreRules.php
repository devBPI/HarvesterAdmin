<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
<!-- Ajout du ou des fichiers javaScript-->
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/selectStyle.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />

<title>Paramétrage</title>
</head>

<?php
include ('../Vue/Header.php');
?>

<body>
	<div class="content">
		<FORM action="FiltreRules.php?modify=true" method="post" onsubmit="return confirm('Voulez vous vraiment modifier les ensembles de règles ?');">
			<h2>Règles de filtrage</h2>
			<a href="../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour filtre</a>
			<div class="sizeable_table">
				<div>
					<?php
						if($mod=='false')
						{
							echo "<table class='table-config'> <tr><th scope=\"col\" width=50%>Nom</th><th scope=\"col\" width=40%>Entité</th><th scope=\"col\" width=10%/></tr>";
							echo " <tr class='hidden_field'>
									<td>
										<input type='text' name='namenew'/>
									</td>
									<td>
										<select name='entitynew'><option value='0'>Aucune choisie</option>";
										foreach($entities as $e)
										{
											echo "<option value='".$e."'>".$e."</option>";
										}
									echo"</select>
									</td>
									<td>
										<button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button>
									</td>
								</tr>";
							if(!empty($data))
							{
								foreach($data as $k => $value)
								{
									echo "<tr><td><input type='text' name='name".$value['id']."' value=\"".$value['name']."\"/></td>
									<td><select name='entity".$k."'><option value='0'>Aucune choisie</option>";
										foreach($entities as $e)
										{
											echo "<option value='".$e."' ".(($e==$value['entity'])?'selected':'').">".$e."</option>";
										}
									echo"</select></td>
									<td><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
								}
							}
							else
							{
								echo "<div><input type='text' name='-1'/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></div>";
							}
							echo "<tr style='background-color:#dbe0e0' id='add_row'><td><button class='ajout but' type='button' title='Ajouter' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement)' style=\"float:left\"><img src='../ressources/add.png' width='30px' height='30px'/></button></td><td/><td><input type=\"submit\" value=\"Valider\"/></td></tr></table>";
					?>
			</div>
				<?php } 
				else{
					echo "<table class='table-config'><th width=60%>Nom</th><th width=40%>Entité</th>";
					foreach($data as $key => $value)
					{
						echo "<tr><td>".$value['name']."</td><td>".$value['entity']."</td></tr>";
					}
					echo "</table><a href='FiltreRules.php?modify=false' class=\"submit-button\">Modifier</a></div>";
				}?>
		</FORM>
	</div>

	<?php if($_GET['modify']=='true') : ?>
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
</body>
</html>