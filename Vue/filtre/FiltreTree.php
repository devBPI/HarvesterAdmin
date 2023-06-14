<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/filtreStyle.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<link rel="stylesheet" href="../../css/formStyle.css" />
	<title><?= $name ?></title>
</head>

<?php
require '../Vue/configuration/TabConfigsAssociees.php';
include('../Vue/common/Header.php');
?>

<body>
<div class="content">
	<div class="hidden_field" id="operation"><select class="profondeur<?= $profondeur ?>" name ="operator" onchange="update_operation(this,<?= $profondeur ?>)">
			<option value='OPERATION'>OPERATION</option>
			<option value='OR'>OR</option>
			<option value='AND'>AND</option>
		</select>
	</div>
	<form action="FiltreTree.php?modify=<?= $id ?>" method="post" id='filter_rule'>
		<div class="triple-column-container">
			<div class="column">
				<a href="../../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour aux filtres</a>
			</div>
			<div class="column">
				<div class="config_name_and_sub_title">
					<h3 class="config_name"><?= $name ?></h3>
					<p class="sub_title">Portant sur l'entité <?= $entity ?> </p>
				</div>
			</div>
			<div class="column">
				<input type="submit" value="Enregistrer la règle" />
			</div>
		</div>

		<?php if(isset($data)) {
			$GLOBALS['nb']=0;
			treeDisplay($data);
		}
		?>
	</form>

	<?= TabConfigsAssociees::makeTab($configurations) ?>

</div>
<?php if(isset($_GET["success"]) && $_GET["success"]) { ?>
	<div id="page-mask" style="display:block"></div>
	<div class="form-popup" id="validateForm" style="display:block">
		<div class='form-container' id='formProperty'>
			<form action="../../Controlleur/Filtre.php" class="form-container" id="formProperty">
				<h3>Modification</h3>
				<div class='form-popup-corps'>
					<p>Les modifications ont bien été enregitrées.</p>
					<button type="submit" class="buttonlink">OK</button>
				</div>
			</form>
		</div>
	</div>
	</div>

<?php } ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../../js/toTop.js"></script>
<script src="../../js/predicate.js"></script>
<script src="../../js/filter_rule.js"></script>
</body>
</html>