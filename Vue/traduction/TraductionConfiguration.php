<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="../../css/style.css"/>
	<link rel="stylesheet" href="../../css/composants.css"/>
	<link rel="stylesheet" href="../../css/formStyle.css"/>
	<link rel="stylesheet" href="../../css/accueilStyle.css"/>
	<title>Configuration et règles de traduction</title>
</head>

<?php
require '../Composant/ComboBox.php';
include('../Vue/common/Header.php');
?>

<body>
<div class="content">
	<div class="triple-column-container">
		<div class="column" style="height:80px">
			<a href="../../Controlleur/Traduction.php" class="buttonlink">&laquo; Retour aux traductions</a>
		</div>
		<div class="column">
			<div class="config_name_and_sub_title">
				<h3 class="config_name" style="text-align:center">Configuration : <?= $name ?></h3>
				<p class="sub_title"></p>
			</div>
		</div>
	</div>
	<?php if (isset($array_error) && count($array_error) > 0) { ?>
		<div>
			<p class="avertissement" style="text-align:left; margin-bottom:0">Les modifications suivantes n'ont pas été prises en compte :</p>
			<p class="avertissement_light" style="text-align:left">
				<?php foreach ($array_error as $error) { ?>
					<?= $error['msg'] ?><br/>
				<?php } ?>

			</p>
		</div>
	<?php } ?>
	<form action="TraductionConfiguration.php?modify=<?= $id ?>" method="post" onsubmit="return confirm('Voulez vous vraiment modifier ces règles ?');">
		<table class="table-config">
			<tr>
				<th scope="col">Entité</th>
				<th scope="col">Champ</th>
				<th scope="col">Règles de traduction</th>
				<th scope="col">Insensible à la casse</th>
				<th scope="col">Suppression des espaces</th>
				<th scope="col"></th>
			</tr>
		</table>
		<table class="table-config">
			<tbody>
			<tr class="hidden_field">
				<td scope="row" data-label="Entité">
					<select name="entity" onchange="display_entity(this)">
						<option value="">Sélectionnez une entité</option>
						<?php foreach ($entities as $e) {
							echo "<option value='" . $e . "'>" . $e . "</option>";
						} ?>

					</select>
				</td>
				<td/>
				<td data-label="Règles de traduction">
					<select name="set">
						<option value="">Sélectionnez un ensemble de règles</option>
						<?= ComboBox::makeComboBox($data) ?>

				</td>
				<td>
					<input type="checkbox" name="trim"/>
				</td>
				<td>
					<input type="checkbox" name="case"/>
				</td>
				<td>
					<button class="but" type="button" title="Supprimer un set" style="cursor:pointer" onclick='delete_field(this.parentElement.parentElement)'>
						<img src="../../ressources/cross.png" width="30px" height="30px"></button>
				</td>
			</tr>
			<?php
			if (!empty($conf)) {
			foreach ($conf as $key => $value) { ?>

				<tr class="entity" id="<?= $value['property'] ?>">
					<td data-label="Entité">
						<?php
						$est_error = false;
						if (isset($array_error)) {
							foreach ($array_error as $error) {
								if ($error["id"] == $value["property"]) {
									$est_error = true; ?>

									<select name="entity<?= $key ?>" class="input-error" onchange="display_entity(this)" required>
										<option value="">Sélectionnez une entité</option>
										<?php foreach ($entities as $e) {
											echo "<option value='" . $e . "' " . (($e == $value['entity']) ? 'selected' : '') . ">" . $e . "</option>";
										} ?>

									</select>
									<?php
								}
							}
						}
						if (!$est_error) { ?>
						<select name="entity<?= $key ?>" onchange="display_entity(this)" required>
							<option value="">Sélectionnez une entité</option>
							<?php foreach ($entities as $e) {
								echo "<option value='" . $e . "' " . (($e == $value['entity']) ? 'selected' : '') . ">" . $e . "</option>";
							} ?>

						</select>
						<?php } ?>

					</td>
					<td/>
					<td data-label="Règles de traduction">
						<select name="set<?= $key ?>" required>
							<option value="">Sélectionnez un ensemble de règles</option>
							<?= ComboBox::makeComboBox($data, $value['id']) ?>

						</select>
					</td>
					<td data-label="Insensible à la casse"><input type="checkbox" name="case<?= $key ?>" <?= (($value['case'] == 'f') ? '' : 'checked') ?>/>
					</td>
					<td data-label="Suppression des espaces"><input type="checkbox" name="trim<?= $key ?>" <?= (($value['trim'] == 'f') ? '' : 'checked') ?>/>
					</td>
					<td>
						<button class="but" type="button" title="Supprimer un set" style="cursor:pointer" onclick="delete_field(this.parentElement.parentElement)">
							<img src="../../ressources/cross.png" width="30px" height="30px"/>
						</button>
					</td>
				</tr>
				<?php }
			} else { ?>

			<tr class="entity" id="new">
				<td data-label="Entité">
					<select name="entity" onchange="display_entity(this)" required>
						<option value="">Sélectionnez une entité</option>
						<?php foreach ($entities as $e) {
							echo "<option value='" . $e . "'>" . $e . "</option>";
						} ?>

					</select>
				</td>
				<td/>
				<td data-label="Règles de traduction">
					<select name="set-1" required>
						<option value="0">Sélectionnez un ensemble de règles</option>
						<?= ComboBox::makeComboBox($data) ?>

					</select>
				</td>
				<td data-label="Insensible à la casse">
					<input type="checkbox" name="case"/>
				</td>
				<td>
					<input type="checkbox" name="trim"/>
				</td>
				<td>
					<button class="but" type="button" title="Supprimer un set" style="cursor:pointer" onclick="delete_field(this.parentElement.parentElement)">
						<img src="../../ressources/cross.png" width="30px" height="30px"/></button>
				</td>
			</tr>
			<?php } ?>

			<tr style="background-color:rgba(0,0,0,0);" id="add_row">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>
					<button class="ajout but" type="button" title="Ajouter une traduction" style="cursor:pointer" onclick="add_new_field(this.parentElement.parentElement.parentElement.parentElement)">
						<img src="../../ressources/add.png" width="30px" height="30px"/>
					</button>
				</td>
			</tr>
			</tbody>
		</table>
		<input type="submit" value="Modifier" class="buttonlink"/>
	</form>

</div>

<?php // $_POST est rempli après envoi du formulaire
if (!empty($_POST) && (!isset($array_error) || (count($array_error) == 0))) : ?>
	<div id="page-mask" style="display:block"></div>
	<div class="form-popup" id="validateForm" style="display:block">
		<div class="form-container" id="formProperty">
			<form action="../../Controlleur/Traduction.php" class="form-container" id="formProperty">
				<h3>Modification</h3>
				<div class="form-popup-corps">
					<p>Les modifications ont bien été enregistrées.</p>
					<button type="submit" class="buttonlink">OK</button>
				</div>
			</form>
		</div>
	</div>
	</div>
<?php endif; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../../js/add_fields.js"></script>
<script src="../../js/entities.js"></script>
<script src="../../js/pop_up.js"></script>
<script src="../../js/toTop.js"></script>
</body>
</html>
