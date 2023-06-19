<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>

<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/selectStyle.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" />

	<title>Activation des alertes</title>

	<style>
        .table_alertes_enabled td {
            padding: 0 0 0 75px;
			text-align: left;
            border-bottom: 1px solid black;
		}

        .table-config th {
            background-color: #56acde;
			color: black;
        }

        .table_alertes_enabled tr:nth-child(even) {
            background-color: #EBF8FF;
        }

		.table_alertes_enabled input[type=checkbox] {
            margin-right: 10px;
            position: relative;
			width: 20px;
			height: 20px;
            top: 5px;
		}

	</style>
</head>

<body>
<?php
include("../Vue/common/Header.php");
require_once("../Composant/ComboBox.php");
$i = 0;
?>

<div class="content">
	<div style="display:flex;justify-content: space-between; margin-bottom: 5px">
		<a href="" class="buttonlink" style="float:none; height:16px">« Retour</a>
	</div>
	<form method="post">
	<table class="table-config table_alertes_enabled">
		<thead>
			<tr>
				<th>Nom de l'alerte</th>
				<th>État de l'alerte</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($alert_jobs as $alert_job) { ?>
			<tr>
				<td>
					<input type="hidden" id="input_id_<?= $i ?>" name="id_<?= $i ?>" value="<?= $alert_job["id"] ?>"/>
					<label for="input_id_<?= $i ?>"><?= $alert_job["name"] ?></label>
				</td>
				<td>
					<?php if($alert_job["is_enabled"]=="t") { ?>
					<input type="checkbox" id="input_is_enabled_<?= $i ?>" name="is_enabled_<?= $i ?>" value="" onchange="change_label_text(this, <?= $i ?>)" checked>
					<label for="input_is_enabled" id="label_<?= $i ?>">Activée</label>
					<?php } else { ?>
					<input type="checkbox" id="input_is_enabled_<?= $i ?>" name="is_enabled_<?= $i ?>" value="" onchange="change_label_text(this, <?= $i ?>)">
					<label for="input_is_enabled" id="label_<?= $i ?>">Désactivée</label>
					<?php } ?>
				</td>
			</tr>
			<?php $i++; } ?>
		</tbody>
		<!-- <input type="submit" name="submit_value" value="Enregistrer les changements"/> -->
	</table>
	</form>
</div>
</body>

<script type="text/javascript">
	function change_label_text(element, indice) {
        if (element.checked)
            document.getElementById("label_"+indice).innerHTML = "Activée";
		else
            document.getElementById("label_"+indice).innerHTML = "Désactivée";
	}
</script>

</html>
