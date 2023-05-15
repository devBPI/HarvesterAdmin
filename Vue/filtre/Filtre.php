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
	<link rel="stylesheet" href="../css/filtreStyle.css" />
	<link rel="stylesheet" href="../css/tradStyle.css" />
	<title>Paramétrage</title>
</head>

<body>
	<?php include('../Vue/Header.php'); ?>

	<div class="content">
		<div class="triple-column-container">
			<div class="column">
			<H3>Association Configuration-Règle</H3>
				<div style="height:450px;width:auto;margin-bottom:30px">
					<select id="rule" name="Filter" class="select-hide" onchange="changeHref(this)">
					<option value="0">Aucune configuration choisie</option>
						<?php
							include '../Vue/combobox/ComboBox.php';
						?>
					</select>
					<div style="overflow-y: auto; height:450px">
						<table class="table-planning" id="conf">
							<th width=30%>Entité</th>
							<th width=70%>Règles de filtrage</th>
						</table>
					</div>
				</div>
				<a class="buttonpage" id="buttonedit" style="background-color:grey">Éditer l'association</a>
			</div>
			<div class="column">
				<H3>Règles de filtrage</H3>
				<div style="overflow-y: auto; height:450px; margin-bottom:30px">
					<table class="table-planning">
						<th width=40%>Entité</th><th width=40%>Règle</th><th width=20%></th>
						<?php
							foreach($rule as $r)
							{
								echo "<tr><td>".$r['entity']."</td><td>".$r['name']."</td>
								<td><a href='../Controlleur/FiltreTree.php?id=".$r['id']."&f=true' title='éditer'><img src='../ressources/edit.png' width='30px' height='30px'/></a></td>
								</tr>";
							}
							?>
					</table>
				</div>
				<a href='../Controlleur/FiltreRules.php' class="buttonpage">Ajout / Suppression</a>
			</div>
			<div class="column">
				<H3>Prédicats</H3>
				<div style="overflow-y: auto; height:450px; margin-bottom:30px">
					<table class="table-planning">
						<th width=80%>Nom</th>
						<?php
							foreach($categories as $value)
							{
								echo "<tr><td>".$value['code']."</td></tr>";
							}
						?>
					</table>
				</div>
				<a href='../Controlleur/FiltrePredicat.php' class="buttonpage">Ajout / Suppression</a>
			</div>
		</div>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/select.js"></script>
	<script src="../js/select-item.js"></script>
	<script>
		function changeHref(element){
			var slct = element.options[element.selectedIndex].value;
			if(slct!="0"){
				document.getElementById('buttonedit').href="../Controlleur/FiltreConfiguration.php?id="+slct;
				document.getElementById('buttonedit').style="background-color:#77b8dd";
			} else {
				document.getElementById('buttonedit').style="background-color:grey";
				document.getElementById('buttonedit').href="";
			}
		}
	</script>
</body>
</html>
