<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script src="../js/add_fields.js"></script>
<script src="../js/select_destination.js"></script>
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
	<div id='category' style='margin-top:5%;width:auto;margin-left:5%;'>
		<h2>Règles de traduction</h2>
		<h3><?php echo $set;?></h3>
		<a href="../Controlleur/Traduction.php">Retour traduction</a><br>
		<?php
		if(isset($_GET['set']) and !empty($cat))
		{
			foreach($cat as $c)
			{
				echo "<input type='checkbox' id='".$c['name']."' onclick='display()' ".((in_array($c['name'],$checked))?'checked':'').">".$c['name']."</input>";
			}
		}
		?>
	</div>
	<FORM action="TraductionSet.php?modify=<?php echo urlencode($set);?>" method="post" class="left"  onsubmit="return confirm('Voulez vous vraiment modifier ces règles ?');">
		<table id="rule" class="sizeable_field table-backoffice">
			<th>Entrée</th><th>Cible de traduction</th><th/>
			<tr class="hidden_field">
				<?php
					echo "<td><input type='text' name='input' style='height:20px;'></input></td>
					<td><select name='rep' class='select_destination'>";
					echo "</select></td><td><button class='but' type='button' title='Supprimer une traduction' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td>";
				?>
			</tr>
			<?php
			if(isset($_GET['set']))
			{
				if(!empty($data))
				{
					foreach($data as $key => $rule)
					{
						echo "<tr><td><input type='text' name='input".$key."' style='height:20px;' value=\"".$rule['input']."\"/></td>
						<td><select name='rep".$key."' class='select_destination' id='".$rule['rep']."'>";
						echo "</select></td><td><button class='but' type='button' title='Supprimer une traduction' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
					}
				}
				else
				{
					echo "<tr><td><input type='text' name='input-1' style='height:20px;'></input></td>
					<td><select name='rep-1' class='select_destination'>";
					echo "</select></td><td><button class='but' type='button' title='Supprimer une traduction' onclick='delete_field(this.parentElement.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button></td></tr>";
				}
				echo "<tr style='background-color:#dbe0e0'><td/><td/><td><button class='ajout but' type='button' title='Ajouter une traduction' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement)'><img src='../ressources/add.png' width='30px' height='30px'/></button></td></tr>";
				echo '</table>
				<input type="submit" value="Valider" class="button primairy-color round"/>';
			}
			else
			{
				foreach($data as $rule)
				{
					echo "<tr><td>".$rule['input']."</td>
					<td>".$rule['rep']."</td></tr>";
				}
				echo '</table>
				<a href="TraductionSet.php?set='.$set.'" value="Modifier" class="button primairy-color round">Modifier</a>';
			}
			?>
	</FORM>
	
	<div class="right" style="margin-top:10%;border:1px solid black">
		<table class="table-backoffice" ><th>Configuration associée</th>
			<?php
			foreach($conf as $c)
			{
				echo "<tr><td>".$c['name']."</td></tr>";
			}
			?>
		</table>
	</div>
</body>
</html>
