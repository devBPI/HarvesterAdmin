<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
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
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="../css/style.css"/>
	<link rel="stylesheet" href="../css/accueilStyle.css"/>
	<link rel="stylesheet" href="../css/composants.css"/>
	<link rel="stylesheet" href="../css/selectStyle.css"/>
	<link rel="stylesheet" href="../css/tradStyle.css"/>

	<title>Paramétrage</title>
</head>

<?php
include('../Vue/Header.php');
?>

<body name="haut" id="haut" style="height: auto; width: auto;">
<div class="content traduction">
	<div class="btn_div">
		<a href="../Controlleur/Traduction.php" class="buttonlink">« Retour aux traductions</a>
	</div>
	<div class="sizeable_table">
		<?php
		if ($mod == 'false') {
		?>
		<form action="TraductionRulesSet.php?modify=true" method="post" class="left"
			  onsubmit="return confirm('Voulez vous vraiment modifier les ensembles de règles ?');">
			<?php if (!empty($set)) { ?>
			<div>
				<table class="table-config">
					<tbody>
					<tr class="hidden_field" id="new" name="pred">
						<td>
							<input type="text" name="t" style="width:300px;"/>
						</td>
						<td class="td_cross">
							<button class="but" type="button" title="Supprimer une cible"
									onclick="delete_field(this.parentElement.parentElement)">
								<img src="../ressources/cross.png" width="30px" height="30px"/>
							</button>
						</td>
					</tr>
					<tr>
						<th>Nom de l'ensemble de règles</th>
						<th class="td_cross"></th>
					</tr>
					<?php
					foreach ($set as $key => $value) { ?>
						<tr>
							<td>
								<input style="width:300px;" type="text" name="<?= $key ?>" value="<?= $value ?>"/>
							</td>
							<td class="td_cross">
								<button class="but" type="button" title="Supprimer une cible"
										onclick="delete_field(this.parentElement.parentElement)">
									<img src="../ressources/cross.png" width="30px" height="30px"/>
								</button>
							</td>
						</tr>
						<?php
					} ?>
					<?php
					} else {
						//echo "<div><input type='text' name='-1'/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png' width='30px' height='30px'/></button></div>";
					}
					?>
					<tr style="background-color:rgba(0,0,0,0);" id="add_row">
						<td></td>
						<td class="td_cross">
							<button class="ajout but" type="button" title="Ajouter une traduction"
									style="cursor:pointer"
									onclick="add_new_field(this.parentElement.parentElement.parentElement.parentElement)">
								<img src="../ressources/add.png" width="30px" height="30px"/></button>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<input type="submit" value="Valider" class="button primairy-color round"/>
		</form>
	</div>
	<?php } else { ?>
	<div class="border_div">
		<table class="table-config">
			<thead>
			<tr>
				<th>Règles de traduction</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($set as $key => $value) {
				echo "<tr><td>" . $value . "</td></tr>";
			} ?>
			</tbody>
		</table>
	</div>
	<div class="btn_div">
		<a href="TraductionRulesSet.php?modify=false" class="buttonpage">Éditer les ensembles de règles</a>
	</div>
<?php } ?>
</div>
</body>
</html>