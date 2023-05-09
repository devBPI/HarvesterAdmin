<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script src="../js/add_fields.js"></script>
<!-- Ajout du ou des fichiers javaScript-->
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/selectStyle.css" />

<title>Paramétrage</title>
</head>

<?php
include ('../Vue/Header.php');
?>

<body name="haut" id="haut" style="height: auto; width: auto;">
	<FORM action="TraductionCategory.php?modify=true" method="post" style="margin-top:5%" class="left" onsubmit="return confirm('Voulez vous vraiment modifier les ensembles de règles ?');">
		<h2>Ensembles de cible de traduction</h2>
		<a href="../Controlleur/Traduction.php">Retour traduction</a>
		<div class="sizeable_table">
			<div class="hidden_field">
				<input type='text' name='t' style='height:30px;width:300px;'/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button>
			</div>
			<div>
				<?php
					if($mod=='false')
					{
						if(!empty($set))
						{
							foreach($set as $key => $value)
							{
								echo "<div><input style='height:30px;width:300px;' type='text' name='".$key."' value=\"".$value."\"/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></div>";
							}
						}
						else
						{
							echo "<div><input type='text' name='-1'/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></div>";
						}
				?>
			</div>
			<button style='margin-left:300px' class='ajout but' type='button' title='Ajouter une traduction' onclick='add_new_field(this.parentElement)'><img src='../ressources/add.png' width='30px' height='30px'/></button>
		</div>
		<input type="submit" value="Valider" class="button primairy-color round"/>
				<?php } 
				else{
					echo "<div style='border:1px solid black;'><table class='table-backoffice' >";
					foreach($set as $key => $value)
					{
						echo "<tr><td>".$value."</td></tr>";
					}
					echo "</table></div><a href='TraductionCategory.php?modify=false'>Modifier</a></div>";
				}?>
	</FORM>
</body>
</html>