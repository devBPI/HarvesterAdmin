<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html lang="fr">
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
	<script src="../js/filtres_traductions/select_destination.js"></script>
	<script src="../js/filtres_traductions/update_tab_traduction_set.js"></script>
	<!-- Ajout du ou des fichiers javaScript-->
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="../css/style.css"/>
	<link rel="stylesheet" href="../css/accueilStyle.css"/>
	<link rel="stylesheet" href="../css/composants.css"/>
	<link rel="stylesheet" href="../css/selectStyle.css"/>
	<link rel="stylesheet" href="../css/tradStyle.css"/>

	<title>Règles de traduction : <?= $rules_set["name"] ?></title>
</head>

<?php
require '../Vue/configuration/TabConfigsAssociees.php';
include('../Vue/common/Header.php');
?>

<body id="haut" style="height: auto; width: auto;">
<div class="content traduction">
	<div class="config_name_and_sub_title">
		<h3 class="config_name">Ensemble : <?= $rules_set["name"] ?></h3>
		<p class="sub_title">Contenu de l'ensemble de règles de traduction</p>
	</div>
	<div class="button_top_div">
		<a href="../Controlleur/Traduction.php" class="buttonlink">« Retour aux traductions</a>
	</div>

	<div class="sizeable_table">
		<div class="hidden_field">
			<input type="text" name="t" style="width:300px;"/>
			<button class="but" type="button" title="Supprimer une cible" onclick="delete_field(this.parentElement)">
				<img src="../ressources/cross.png" width="30px" height="30px">
			</button>
		</div>
		<?php if (isset($_GET["modify"]) && $_GET["modify"] == "false") { ?>
		<table class="table-config">
			<tbody>
			<tr>
				<th style="background-color:#56acde">Catégorie</th>
				<th style="background-color:#56acde">
					<select onchange="update_tab_cibles(this, <?= $rules_set["id"] ?>)" required>
						<option value="">Sélectionnez une catégorie de cibles</option>
						<?php foreach ($categories as $category) {
							if ($category["id"] == $rules_set["category"]["id"]) { ?>
								<option value="<?= $category["id"] ?>" selected><?= $category["name"] ?></option>
							<?php } else { ?>
								<option value="<?= $category["id"] ?>"><?= $category["name"] ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</th>
				<th style="display:none"></th>
			</tr>
			<tr>
				<th>Valeur d'entrée</th>
				<th>Cible de traduction</th>
			</tr>
			</tbody>
		</table>
		<form action="TraductionSet.php?id=<?= $rules_set["id"] ?>&modify=true" method="post"
			  onsubmit="return confirm('Confirmer les modifications ?');">
			<input name='sent_via_form' type='hidden' value=""/>
			<table class="table-config">
				<tbody id="interieur_tableau">
				<tr class="hidden_field" id="new">
					<td>
						<input type="text" name="rule_input_value_"/>
					</td>
					<td>
						<select name="destination_">
						<?php foreach ($cibles as $cible) {
							if ($cible["category_id"] == $rules_set["category"]["id"]) { ?>
							<option value="<?= $cible["id"] ?>"><?= $cible["value"] ?></option>
						<?php }
						} ?>
						</select>
					</td>
					<td class="td_cross">
						<button class="but" type="button" title="Supprimer une cible"
								onclick="delete_field(this.parentElement.parentElement)"><img src="../ressources/cross.png"
																							  width="30px" height="30px"/>
						</button>
					</td>
				</tr>
				<?php if(!empty($rules)) {
				foreach($rules as $key => $rule) { ?>
					<tr>
						<td>
							<input type="text" name="rule_input_value_<?= $key ?>" value="<?= $rule["rule_input_value"] ?>"/>
						</td>
						<td>
							<select name="destination_<?= $key ?>" required>
								<?php foreach ($cibles as $cible) {
									if ($cible["category_id"] == $rules_set["category"]["id"]) {
										if ($cible["id"] == $rule["cible_id"]) { ?>
										<option value="<?= $cible["id"] ?>" selected><?= $cible["value"] ?></option>
										<?php } else { ?>
										<option value="<?= $cible["id"] ?>"><?= $cible["value"] ?></option>
									<?php }
									}
								} ?>
							</select>
						</td>
						<td class="td_cross">
							<button class="but" type="button" title="Supprimer une cible"
									onclick="delete_field(this.parentElement.parentElement)"><img src="../ressources/cross.png"
																								  width="30px" height="30px"/>
							</button>
						</td>
					</tr>
				<?php } ?>
				<?php }
				?>
				<tr id="add_row">
					<td></td>
					<td></td>
					<td class="td_cross">
						<button class="ajout but" type="button" title="Ajouter une ligne"
								onclick="add_new_field(this.parentElement.parentElement.parentElement.parentElement)">
							<img src="../ressources/add.png" width="30px" height="30px"/></button>
					</td>
				</tr>
				</tbody>
				</table>
			<div class="button_end_div">
				<input type="submit" value="Valider les modifications" class="button primairy-color round"/>
			</div>
		</form>
		<?php } else { ?>
		<table class="table-config">
		<tbody>
			<tr>
				<th style="background-color:#56acde">Catégorie</th>
				<th style="background-color:#56acde">
			<?php
			if ($rules_set["category"]["id"] != -1) {
			foreach ($categories as $category) {
			if ($category["id"] == $rules_set["category"]["id"]) { ?>
					<input type="text" value="<?= $category["name"] ?>" readonly>
			<?php }
			}
			} else { ?>
				<input type="text" value="Aucune catégorie de cibles" readonly>
			<?php } ?>
			</th>
		</tr>
		<tr>
			<th>Valeur d'entrée</th>
			<th>Cible de traduction</th>
		</tr>
			<?php if(!empty($rules)) {
			foreach($rules as $key => $rule) { ?>
				<tr>
					<td>
						<p><?= $rule["rule_input_value"] ?></p>
					</td>
					<td>
						<?php foreach ($cibles as $cible) {
							if ($cible["category_id"] == $rules_set["category"]["id"]) {
								if ($cible["id"] == $rule["cible_id"]) { ?>
									<p><?= $cible["value"] ?></p>
								<?php }
							}
						} ?>
						</td>
					</tr>
			<?php } ?>
			<?php }
			?>
		</tbody>
		</table>
		<div class="button_end_div">
				<a href="TraductionSet.php?id=<?= $rules_set["id"] ?>&modify=false" class="buttonpage">Éditer l'ensemble de règles</a>
		</div>
		<?php }?>
	</div>

	<?= TabConfigsAssociees::makeTab($configurations) ?>

</div>
</body>
</html>
