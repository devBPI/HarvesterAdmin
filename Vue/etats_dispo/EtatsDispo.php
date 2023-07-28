<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html lang="fr">
<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="../../css/style.css"/>
	<link rel="stylesheet" href="../../css/composants.css"/>
	<link rel="stylesheet" href="../../css/accueilStyle.css"/>
	<title>États de Disponibilité</title>
</head>
<body>
<?php include('../Vue/common/Header.php'); ?>

<div class="content" style="width:90%">
	<div class="button_top_div_with_margin" style="float:right">
		<?php
		if ($modify == "false") {
			echo "<a href='EtatsDispo.php?modify=true' class='submit-button'>Modifier les états de disponibilité</a>";
		} else {
			echo "<a href='EtatsDispo.php?modify=false' class='submit-button'>Finir les modifications</a>";
		}
		?>
	</div>
	<table class="table-config">
		<thead>
		<tr>
<?php	if ($modify == "true") { ?>
			<th id="th_cell_code" class="order_asc" style="width:10%;cursor:default;">Code</th>
			<th id="th_cell_dispo" class="order_asc" style="width:20%;cursor:default;">Disponibilité</th>
			<th id="th_cell_to_harvest" class="order_asc" style="width:20%;cursor:default;">À moissonner</th>
			<th id="th_cell_label" class="order_asc" style="width:20%;cursor:default;">Label</th>
<?php	} else { ?>
			<th id="th_cell_code" class="order_asc" style="width:10%;cursor:pointer;" onclick="maj_col('code')">Code</th>
			<th id="th_cell_dispo" class="order_asc" style="width:20%;cursor:pointer;" onclick="maj_col('dispo')">Disponibilité</th>
			<th id="th_cell_to_harvest" class="order_asc" style="width:20%;cursor:pointer;" onclick="maj_col('to_harvest')">À moissonner</th>
			<th id="th_cell_label" class="order_asc" style="width:20%;cursor:pointer;" onclick="maj_col('label')">Label</th>
<?php	} ?>
		</tr>
		</thead>
		<tbody id="emplacement_tableau">
<?php 	if ($modify == "true") { ?>
			<tr>
				<FORM action="EtatsDispo.php?add=true" method="post"
					  onsubmit="return confirm('Voulez vous vraiment ajouter ce status ?');">
					<td data-label="Code">
						<textarea aria-label="Code" name="code"></textarea>
					</td>
					<td data-label="Disponibilité">
						<select aria-label="Disponibilité" name="list_dispo" style="width: 100%">
							<option value=""></option>
							<option value="D">D</option>
						</select>
					</td>
					<td data-label="À moissonner">
						<select aria-label="À moissonner" name="to_harvest" style="width: 100%">
							<option value="t">True</option>
							<option value="f">False</option>
						</select>
					</td>
					<td data-label="Label">
						<textarea aria-label="Label" name="label"></textarea>
					</td>
					<th><input type='submit' class='button primairy-color' style='width:100%' value="Ajouter"/></th>
				</FORM>
			</tr>
			<?php foreach ($data as $var) {
				?>
				<form action="EtatsDispo.php?code=<?= $var['code'] ?>" method="post" onsubmit="return confirm('Voulez vous vraiment modifier ce status ?');">
				<tr>
					<td data-label="Code"> <?= $var['code'] ?></td>
					<td data-label="Disponibilité">
						<select aria-label="Disponibilité" name="list_dispo" style="width: 100%">
							<option value=""
								<?php echo ($var['dispo_flag'] == "") ? 'selected' : ''; ?>></option>
							<option value="D"
								<?php echo ($var['dispo_flag'] != "") ? 'selected' : ''; ?>>D
							</option>
						</select>
					</td>
					<td data-label="À moissonner">
						<select aria-label="À moissonner" name="to_harvest" style="width: 100%">
							<option value="t"
								<?php echo ($var['select_to_harvest'] == "t") ? 'selected' : ''; ?>>
								True
							</option>
							<option value="f"
								<?php echo ($var['select_to_harvest'] == "f") ? 'selected' : ''; ?>>
								False
							</option>
						</select>
					</td>
					<td data-label="Label"><textarea aria-label="Label" name="label"><?= $var['label'] ?></textarea></td>
					<th>
						<input type='submit' class='button primairy-color' style='width:100%' value="Modifier"/>
					</th>
					</FORM>
					<form onsubmit="return confirm('Voulez vous vraiment supprimer ce statut ?');"
						  action="EtatsDispo.php?delete=<?= $var['code'] ?>" method="post">
						<td style="background-color:#1fe0;">
							<input type="image" id="cross" name="cross" alt="Supprimer un statut" src="../../ressources/cross.png" width="20px" height="20px">
						</td>
					</form>
				</tr>
<?php			}
		} ?>
		</tbody>
	</table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../../js/toTop.js"></script>
<script src="../../js/etats_dispo/sort_etats_dispo.js"></script>
</body>
</html>