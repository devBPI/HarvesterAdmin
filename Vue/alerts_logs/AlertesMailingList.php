<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
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
	<form method="post" onsubmit="return confirm('Valider les modifications apportées à la liste des destinataires ?')">
		<table class="table-config table_alertes_enabled">
			<tbody>
			<tr class="hidden_field">
				<td>
					<input type="hidden" id="input_old_id_" name="old_id_" value="null"/>
					<input type="email" id="input_id_" name="id_" placeholder="mail@nom-de-domaine.fr" value=""/>
				</td>
				<td><input type="checkbox" id="input_is_enabled_" name="is_enabled_" value="" checked/></td>
				<td><label for="input_is_enabled_" id="label_">Activée</label></td>
				<td class="td_cross">
					<button class="but" type="button" title="Supprimer une cible" onclick="delete_field(this.parentElement.parentElement)"><img src="../ressources/cross.png" width="30px" height="30px">
					</button>
				</td>
			</tr>
			<tr>
				<th>Adresse mail</th>
				<th colspan="2">État de l'adresse mail</th>
				<th class="td_cross"></th>
			</tr>
<?php foreach($mailing_list as $recipient) { ?>
				<tr>
					<td>
						<input type="hidden" id="input_old_id_<?= $i ?>" name="old_id_<?= $i ?>" value="<?= $recipient["mail"] ?>" required/>
						<input type="email" id="input_id_<?= $i ?>" name="id_<?= $i ?>" placeholder="mail@nom-de-domaine.fr" value="<?= $recipient["mail"] ?>" required/>
					</td>
<?php if($recipient["is_enabled"]=="t") { ?>
					<td class="td_switch">
						<label class="switch">
							<input type="checkbox" id="input_is_enabled_<?= $i ?>" name="is_enabled_<?= $i ?>" value="" onchange="change_label_text(this, <?= $i ?>)" checked>
							<span class="slider"></span>
						</label>
					</td>
					<td class="td_label">
						<label for="input_is_enabled_<?= $i ?>" id="label_<?= $i ?>">Activée</label>
					</td>
<?php } else { ?>
					<td class="td_switch">
						<label class="switch">
							<input type="checkbox" id="input_is_enabled_<?= $i ?>" name="is_enabled_<?= $i ?>" value="" onchange="change_label_text(this, <?= $i ?>)">
							<span class="slider"></span>
						</label>
					</td>
					<td class="td_label">
						<label for="input_is_enabled_<?= $i ?>" id="label_<?= $i ?>">Désactivée</label>
					</td>
<?php } ?>
					<td class="td_cross">
						<button class="but" type="button" title="Supprimer une cible" onclick="delete_field(this.parentElement.parentElement)">
							<img src="../ressources/cross.png" width="30px" height="30px">
						</button>
					</td>
				</tr>
<?php $i++; } ?>
				<tr style="background-color:rgba(0,0,0,0);border:none" id="add_row">
					<td colspan="3"></td>
					<td class="td_cross">
						<button class="ajout but" type="button" title="Ajouter une ligne" onclick="add_new_field(this.parentElement.parentElement.parentElement.parentElement, 'alertes_parametrage')">
							<img src="../ressources/add.png" width="30px" height="30px"/></button>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="button_end_div_with_margin">
			<button type="submit" name="submit_value" value="save">Enregistrer la liste de diffusion</button>
		</div>
	</form>
</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script src="../js/add_fields.js"></script>
<script type="text/javascript">
    function change_label_text(element, indice) {
        if (element.checked)
            document.getElementById("label_"+indice).innerHTML = "Activée";
        else
            document.getElementById("label_"+indice).innerHTML = "Désactivée";
    }
</script>

</html>