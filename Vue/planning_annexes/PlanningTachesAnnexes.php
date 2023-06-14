
<html>
<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<!-- ajout du ou des fichiers CSS-->
	<title>Planning des Taches Annexes</title>
</head>

<body name="haut" style="overflow:visible">
	<?php include('../Vue/common/Header.php'); ?>

	<div class="content" style="width:90%">
		<div class="triple-column-container">
			<div class="column" style="height:80px">
				<a href="../Vue/PlanificationSideTask.php" class="buttonlink" style="background-color:#77b8dd">Ajouter une Planification</a>
			</div>
		</div>
		<div class="quadra-eq-column-container">
			<div class="column" style="height:400px">
					<h3> LUNDI </h3>
					<?php $dow=2;
					include '../Vue/planning_annexes/affichagePlanningSideTask.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> MARDI </h3>
					<?php $dow=3;
					include '../Vue/planning_annexes/affichagePlanningSideTask.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> MERCREDI </h3>
					<?php $dow=4;
					include '../Vue/planning_annexes/affichagePlanningSideTask.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> JEUDI </h3>
					<?php $dow=5;
					include '../Vue/planning_annexes/affichagePlanningSideTask.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> VENDREDI </h3>
					<?php $dow=6;
					include '../Vue/planning_annexes/affichagePlanningSideTask.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> SAMEDI </h3>
					<?php $dow=7;
					include '../Vue/planning_annexes/affichagePlanningSideTask.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> DIMANCHE </h3>
					<?php $dow=1;
					include '../Vue/planning_annexes/affichagePlanningSideTask.php' ?>
			</div>
		</div>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../../js/toTop.js"></script>
</body>
<!-- Fin du body -->

</html>
