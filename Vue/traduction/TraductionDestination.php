<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html lang="fr">
<head>
	<script
		src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
	<!-- Ajout du ou des fichiers javaScript-->
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="../css/style.css"/>
	<link rel="stylesheet" href="../css/accueilStyle.css"/>
	<link rel="stylesheet" href="../css/composants.css"/>
	<link rel="stylesheet" href="../css/selectStyle.css"/>
	<link rel="stylesheet" href="../css/filtres_traductions/tradStyle.css"/>

	<title>Cibles de traduction : <?= $name ?></title>
</head>

<?php
require('../Vue/traduction/TabTraductionRulesCategory.php');
include('../Vue/common/Header.php');
?>

<body id="haut" style="height: auto; width: auto;">
<div class="content traduction">
	<?php if (isset($id) && $id!=null) { ?>
	<div class="config_name_and_sub_title">
		<h3 class="config_name">Ensemble : <?= $name ?></h3>
		<p class="sub_title">Contenu de l'ensemble de cibles de traduction</p>
	</div>
	<div class="btn_div">
		<a href="../Controlleur/Traduction.php" class="buttonlink">« Retour aux traductions</a>
	</div>

	<?= TabTraductionRulesCategory::makeTab($mod, $set, "TraductionDestination.php","Nom des cibles de traduction", "Éditer les cibles", $id); ?>
	<?php
	} else {
	?>
		<p class="avertissement">Vous n'avez pas sélectionné d'ensemble de cibles de traduction.</p>
	<?php } ?>
</div>
</body>
</html>
