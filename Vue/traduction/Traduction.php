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
	<link rel="stylesheet" href="../css/tradStyle.css" />
	<title>Paramétrage</title>
</head>

<?php
include('../Vue/Header.php');
?>

<body name="haut" id="haut" style="height: auto; width: auto;">
	<div class="content">
		<div class="double-column-container">
			<div class="column" style="height:450px">
			<H3>Association Configuration-Règle</H3>
			<div class="cartouche-solo" style="width:auto;padding:5%;">
				<select id="rule" name="Trad">
				<option value="0">Aucune configuration choisie</option>
					<?php
						include '../Vue/combobox/ComboBox.php';
					?>
				</select>
				<div style="overflow-y: auto; height:400px">
					<table class="table-planning" id="conf">
						<th>Entité</th>
						<th>Règles de traduction</th>
					</table>
				</div>
			</div>
			</div>
			<div class="column">
				<H3>Règles de traduction</H3>
				<div style="overflow-y: auto; height:200px">
					<table class="table-planning">
						<th style="width:80%">Nom</th><th></th>
						<?php
							foreach($rules_set as $r)
							{
								echo "<tr><td>".$r['name']."</td>
								<td><a href='../Controlleur/TraductionSet.php?id=".$r['id']."&f=true' title='éditer'><img src='../ressources/edit.png' width='30px' height='30px'/></a></td>
								</tr>";
							}
							?>
					</table>
				</div>
				<a href='../Controlleur/TraductionRulesSet.php'>Modifier les règles</a>
				<H3>Cibles de traduction</H3>
				<div style="overflow-y: auto; height:200px">
					<table class="table-planning">
						<th style="width:80%">Nom</th><th></th>
						<?php
							foreach($categories as $value)
							{
								echo "<tr><td>".$value['name']."</td>
								<td><a href='../Controlleur/TraductionDestination.php?modify=".$value['name']."&f=true' title='éditer'><img src='../ressources/edit.png' width='30px' height='30px'/></a></td></tr>";
							}
						?>
					</table>
				</div>
				<a href='../Controlleur/TraductionCategory.php'>Modifier les ensembles de cible</a>
			</div>
		</div>
	</div>


	<!-- <div class="part left" style="width:45%;height:80%;margin-top:5%">
		<div class="primairy-color">
			<h3><font color='white'>Association Configuration-Règle</font></h3>
		</div>
		<div>
			<div class="custom-select" style="margin-top:3%;width:50%;height:2%;margin-left:5%">
				<select id="rule" name="Trad" class="select-hide">
					<option value="0">Aucune configuration choisie</option>
						<php
							include '../Vue/combobox/ComboBox.php';
						?>
				</select>
			</div>
			<script src="../js/select-item.js"></script>
			<table class="table-backoffice"  style="width:90%;margin-top:3%;margin-left:5%" id="conf">
				<th>Entité</th>
				<th>Règles de traduction</th>
			</table>
		</div>
	</div>

	<div class="right" style="margin-top:5%;width:40%">
		<div class="part" style="width:45%;float:right">
			<div class="primairy-color">
				<h3><font color='white'>Cibles de traduction</font></h3>
			</div>
			<table class="table-backoffice" style="width:100%">
				<php
					foreach($categories as $value)
					{
						echo "<tr><td>".$value['name']."</td>
						<td style='width:7%'><a href='../Controlleur/TraductionDestination.php?modify=".$value['name']."&f=true' title='éditer'><img src='../ressources/edit.png' width='20px' height='20px'/></a></td></tr>";
					}
				?>
			</table>
			<a href='../Controlleur/TraductionCategory.php'>Modifier les ensembles de cible de traductions</a>
		</div>
		<div class="part" style="width:45%;float:left;">
			<div class="primairy-color">
				<h3><font color='white'>Règles de traduction</font></h3>
			</div>
			<table class="table-backoffice" style="width:100%">
				<php
					foreach($rules_set as $value)
					{
						echo "<tr><td>".$value['name']."</td>
						<td style='width:7%'><a href='../Controlleur/TraductionSet.php?modify=".$value['name']."&f=true' title='éditer'><img src='../ressources/edit.png' width='20px' height='20px'/></a></td></tr>";
					}
				?>
			</table>
			<a href='../Controlleur/TraductionRulesSet.php'>Modifier les règles de traduction</a>
		</div>
		
	</div> -->

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
	<script src="../js/select.js"></script>
	<script src="../js/select-item.js"></script>
</body>
<!-- Fin du body -->

</html>
