<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
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
	<link rel="stylesheet" href="../css/tradStyle.css"/>

	<title>Règles de traduction</title>
</head>

<?php
require ('../Vue/traduction/TabTraductionRulesCategory.php');
include('../Vue/Header.php');
?>

<body name="haut" id="haut" style="height: auto; width: auto;">
<div class="content traduction">
	<div class="btn_div">
		<a href="../Controlleur/Traduction.php" class="buttonlink">« Retour aux traductions</a>
	</div>

	<?= TabTraductionRulesCategory::makeTab($mod,$set,"TraductionRulesSet.php", "Règles de traduction", "Éditer les ensembles de règles"); ?>

</div>
</body>
</html>