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
	<title>Planning des Moissons</title>
</head>

<body style="overflow:visible">
	<?php include('../Vue/common/Header.php'); ?>

	<div class="content" style="width:90%">
		<div class="triple-column-container" style="height:50px">
			<div class="column">
				<a href="../Vue/PlanificationMoisson.php" class="buttonlink" style="background-color:#77b8dd">Ajouter une Planification</a>
			</div>
		</div>
		<div class="quadra-eq-column-container">
			<div class="column" style="height:400px">
					<h3> LUNDI </h3>
					<?php $dow=2; $dayname="monday"; $journame="Lundi";
					include '../Vue/planning_moissons/affichagePlanning.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> MARDI </h3>
					<?php $dow=3; $dayname="tuesday"; $journame="Mardi";
					include '../Vue/planning_moissons/affichagePlanning.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> MERCREDI </h3>
					<?php $dow=4; $dayname="wednesday"; $journame="Mercredi";
					include '../Vue/planning_moissons/affichagePlanning.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> JEUDI </h3>
					<?php $dow=5; $dayname="thursday"; $journame="Jeudi";
					include '../Vue/planning_moissons/affichagePlanning.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> VENDREDI </h3>
					<?php $dow=6; $dayname="friday"; $journame="Vendredi";
					include '../Vue/planning_moissons/affichagePlanning.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> SAMEDI </h3>
					<?php $dow=7; $dayname="saturday"; $journame="Samedi";
					include '../Vue/planning_moissons/affichagePlanning.php' ?>
			</div>
			<div class="column" style="height:400px">
					<h3> DIMANCHE </h3>
					<?php $dow=1; $dayname="sunday"; $journame="Dimanche";
					include '../Vue/planning_moissons/affichagePlanning.php' ?>
			</div>
		</div>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../../js/toTop.js"></script>
</body>
<!-- Fin du body -->

</html>
