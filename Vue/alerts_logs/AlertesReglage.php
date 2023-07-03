<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" /> <!-- Pour popup -->
	<link rel="stylesheet" href="../../css/alerts_logs/alertes_reglage.css" />

	<title>Activation des alertes</title>

</head>

<?php
include("../Vue/common/Header.php");
?>
<body>
<div class="content">
	<div class="button_top_div_with_margin">
		<a class="buttonlink" href="../Controlleur/AlertesParametrage.php" style="float:none; height:16px">« Retour</a>
	</div>
	<form method="post">
		<div class="alertes_params_div_title_line" style="background-color: transparent">
		<div class="alertes_params_div_title">
			Seuils de déclenchement des alertes
		</div>
		<div class="alertes_params_div_title">
			Pourcentages de différence tolérés
		</div>
		</div>
<?php for ($i = 0; $i < count($alert_parameters_tmp); $i++) { ?>
<?php if ($i % 2 == 0) $ligne = $parameters_threshold[$j_threshold++]; else $ligne = $parameters_percentage[$j_percentage++]; ?>
<?php if($i % 2 == 0) { ?>
			<div class="alertes_params_line">
<?php } ?>
				<div class="alertes_params_div_int">
					<input type="hidden" id="input_id_parameter_<?= $i ?>" name="id_<?= $i ?>" value="<?= $ligne["code"] ?>" required/>
					<div class="alertes_params_label_div">
						<label for="input_value_<?= $i ?>"><?= $ligne["name"] ?></label>
					</div>
					<input class="alertes_params_input" type="number" id="input_value_<?= $i ?>" name="value_<?= $i ?>" min="0" <?= ($i % 2 != 0)?'max="100"':'' ?> value="<?= $ligne["value"] ?>" required/>
				</div>
<?php if($i % 2 != 0) { ?>
			</div>
<?php } ?>
<?php } ?>
		<div class="button_end_div_with_margin">
			<button type="submit" name="submit_value" value="save">Enregistrer les seuils et pourcentages</button>
		</div>
	</form>
</div>

<?php if (!empty($_POST)) : ?>
<div id="page-mask" style="display:block"></div>
<div class="form-popup" id="validateForm" style="display:block">
	<div class="form-container" id="formProperty">
		<h3>Modification</h3>
		<div class="form-popup-corps">
			<p>Les seuils des alertes et pourcentages ont bien été enregistrés.</p>
			<button class="buttonlink" onclick="window.location.href='../Controlleur/AlertesParametrage.php'">OK</button>
		</div>
	</div>
</div>
<?php endif; ?>

</body>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="../js/toTop.js"></script>
<script type="text/javascript">
</script>

</html>