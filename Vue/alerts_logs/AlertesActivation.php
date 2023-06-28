<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/selectStyle.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" />
	<link rel="stylesheet" href="../../css/alerts_logs/alertes_activation.css" />

	<title>Activation des alertes</title>
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
	<form method="post">
	<table class="table-config table_alertes_enabled">
		<thead>
			<tr>
				<th>Nom de l'alerte</th>
				<th colspan="2">État de l'alerte</th>
			</tr>
		</thead>
		<tbody>
<?php foreach($alert_jobs as $alert_job) { ?>
			<tr>
				<td>
					<input type="hidden" id="input_id_<?= $i ?>" name="id_<?= $i ?>" value="<?= $alert_job["id"] ?>" required/>
					<?= $alert_job["name"] ?>
				</td>
<?php if($alert_job["is_enabled"]=="t") { ?>
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
			</tr>
<?php $i++; } ?>
		</tbody>
	</table>
		<div class="button_end_div_with_margin">
			<button type="submit" name="submit_value" value="save">Enregistrer la configuration</button>
		</div>
	</form>
</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">
	function change_label_text(element, indice) {
        if (element.checked)
            document.getElementById("label_"+indice).innerHTML = "Activée";
		else
            document.getElementById("label_"+indice).innerHTML = "Désactivée";
	}
</script>

</html>
