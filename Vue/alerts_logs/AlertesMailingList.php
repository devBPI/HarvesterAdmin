<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" /> <!-- Pour popup et input-error -->
	<link rel="stylesheet" href="../../css/alerts_logs/alertes_activation_mailing.css" />

	<title>Activation des envois de mail</title>
</head>

<body>
<?php
include("../Vue/common/Header.php");
$i = 0;
?>

<div class="content">
	<div class="button_top_div_with_margin">
		<a class="buttonlink" href="../Controlleur/AlertesParametrage.php" style="float:none; height:16px">« Retour</a>
	</div>
	<?php if (!empty($array_error)) { ?>
	<div class="avertissement">
		<p style="text-align:left">ERREURS :<br/>
			<?php foreach ($array_error as $error) { ?>
				L'adresse mail <?= $error ?> existe déjà.</br>
			<?php } ?>
		</p>
		</div>
	<?php } ?>
	<form method="post" onsubmit="return confirm('Valider les modifications apportées à la liste des destinataires ?')">
		<table class="table-config table_alertes_enabled">
			<tbody>
			<tr class="hidden_field">
				<td class="td_email">
					<input type="hidden" id="input_old_id_" name="old_id_" value="null"/>
					<input type="email" id="input_id_" name="id_" placeholder="mail@nom-de-domaine.fr" value=""/>
				</td>
				<td class="td_switch">
					<label class="switch">
						<input type="checkbox" id="input_is_enabled_" name="is_enabled_" value="" />
						<span class="slider"></span>
					</label>
				</td>
				<td class="td_label"><label for="input_is_enabled_" id="label_">Activée</label></td>
				<td class="td_cross">
					<button class="but" type="button" title="Supprimer un destinataire" onclick="delete_field(this.parentElement.parentElement)"><img src="../ressources/cross.png" width="30px" height="30px">
					</button>
				</td>
			</tr>
			<tr class="table_head">
				<th>Adresse mail</th>
				<th colspan="2">État de l'adresse mail</th>
				<th class="td_cross"></th>
			</tr>
<?php foreach($mailing_list as $recipient) {
	$recipient_class = '';
	if (isset($array_error) && count($array_error) > 0) {
	foreach ($array_error as $value) {
		if ($value == $recipient["new_mail"]) $recipient_class = 'class="input-error"';
	} } ?>
				<tr>
					<td class="td_email">
						<input type="hidden" id="input_old_id_<?= $i ?>" name="old_id_<?= $i ?>" value="<?= $recipient["mail"] ?>" required/>
						<input type="email" id="input_id_<?= $i ?>" <?= $recipient_class ?> name="id_<?= $i ?>" placeholder="mail@nom-de-domaine.fr" value="<?= $recipient["new_mail"] ?>" required/>
					</td>
<?php if($recipient["is_enabled"]=="t") { ?>
					<td class="td_switch">
						<label class="switch">
							<input type="checkbox" id="input_is_enabled_<?= $i ?>" name="is_enabled_<?= $i ?>" value="" onchange="change_label_text(this, <?= $i ?>)" checked/>
							<span class="slider" id="slider_<?= $i ?>"></span>
						</label>
					</td>
					<td class="td_label">
						<label for="input_is_enabled_<?= $i ?>" id="label_<?= $i ?>">Activée</label>
					</td>
<?php } else { ?>
					<td class="td_switch">
						<label class="switch">
							<input type="checkbox" id="input_is_enabled_<?= $i ?>" name="is_enabled_<?= $i ?>" value="" onchange="change_label_text(this, <?= $i ?>)"/>
							<span class="slider" id="slider_<?= $i ?>"></span>
						</label>
					</td>
					<td class="td_label">
						<label for="input_is_enabled_<?= $i ?>" id="label_<?= $i ?>">Désactivée</label>
					</td>
<?php } ?>
					<td class="td_cross">
						<button class="but" type="button" title="Supprimer un destinataire" onclick="delete_field(this.parentElement.parentElement)">
							<img src="../ressources/cross.png" width="30px" height="30px">
						</button>
					</td>
				</tr>
<?php $i++; } ?>
				<tr style="border:none" id="add_row">
					<td colspan="3"></td>
					<td class="td_cross">
						<button class="ajout but" type="button" title="Ajouter une ligne" onclick="add_new_field(this.parentElement.parentElement.parentElement.parentElement, 'alertes_parametrage')">
							<img src="../ressources/add.png" width="30px" height="30px"/>
						</button>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="button_end_div_with_margin">
			<button type="submit" name="submit_value" value="save">Enregistrer la liste de diffusion</button>
		</div>
	</form>
</div>


<?php if (!empty($array_error)) : ?>
	<div id="page-mask" style="display:block"></div>
	<div class="form-popup" id="validateForm" style="display:block">
		<div class="form-container" id="formProperty">
			<h3>Erreur : rejet des modifications</h3>
			<div class="form-popup-corps">
				<p class="avertissement">Les modifications n'ont pas été prises en compte.<br/>
				<p>Il existe des conflits d'adresse email (certaines adresses sont renseignées plusieurs fois).<br/>
					Veuillez corriger ces erreurs avant d'enregistrer.</p>
				<button class="buttonlink" onclick="closeForm()">OK</button>
			</div>
		</div>
	</div>
<?php elseif (isset($array_error)) : ?>
	<div id="page-mask" style="display:block"></div>
	<div class="form-popup" id="validateForm" style="display:block">
		<div class="form-container" id="formProperty">
			<h3>Modification</h3>
			<div class="form-popup-corps">
				<p>Les paramètres d'envois de mail ont bien été enregistrés.</p>
				<button class="buttonlink" onclick="window.location.href='../Controlleur/AlertesParametrage.php'">OK</button>
			</div>
		</div>
	</div>
<?php endif; ?>

</body>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="../js/toTop.js"></script>
<script type="text/javascript" src="../js/add_fields.js"></script>
<script type="text/javascript" src="../js/pop_up.js"></script>
<script type="text/javascript" src="../js/alerts_logs/checkbox_slider.js"></script>
<script type="text/javascript">
</script>

</html>