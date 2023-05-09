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
	<?php
		if(isset($_GET['modify']))
		{
			echo "<div style='margin-top:5%;margin-left:5%'><h2>Cibles de traduction</h2><h3>".$name."</h3>
			<a href='../Controlleur/Traduction.php'>Retour traduction</a><table class='table-backoffice'>";
			foreach($trads as $t)
			{
				echo "<tr><td>".$t."<td></tr>";
			}
			echo "</table><div style='margin-top:2%'><a href='TraductionDestination.php?cat=".$name."' class='button primairy-color round'>Modifier</a></div></div>";
		}
		else
		{
			?>
			<FORM action="TraductionDestination.php?modify=<?php echo $name;?>" method="post" style="margin-top:5%" class="left" onsubmit="return confirm('Voulez vous vraiment modifier ces libellés ?');">
				<a href="../Controlleur/Traduction.php">Retour traduction</a>
				<div class="sizeable_table">
					<div class="hidden_field">
						<input style='width:500px' type='text' name='d'/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button>
					</div>
					<div>
						<?php
							if(!empty($trads))
							{
								foreach($trads as $k => $trad)
								{
									echo "<div><input style='width:500px' type='text' name='".$k."' value=\"".$trad."\"/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></div>";
								}
							}
							else
							{
								echo "<div><input style='width:500px' type='text' name='-1'/></div>";
							}
						?>
					</div>
					<button style='margin-left:500px' class='ajout but' type='button' title='Ajouter une traduction' onclick='add_new_field(this.parentElement)'><img src='../ressources/add.png' width='30px' height='30px'/></button>
				</div>
				<input type="submit" value="Valider" class="button primairy-color round"/>
			</FORM>
			<?php
		}
	?>
</body>
</html>
