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

	<title>Cibles de traduction</title>
</head>

<?php
include ('../Vue/traduction/TabTraductionRulesCategory.php');
include('../Vue/common/Header.php');
?>

<body id="haut" style="height: auto; width: auto;">
<div class="content traduction">
	<div class="btn_div">
		<a href="../Controlleur/Traduction.php" class="buttonlink">« Retour aux traductions</a>
	</div>

	<?= TabTraductionRulesCategory::makeTab($mod,$set,"TraductionCategory.php", "Cibles de traduction", "Éditer les ensembles de cibles"); ?>

</div>
</body>
</html>