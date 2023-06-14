<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<link rel="stylesheet" href="../css/ui-dialog-style.css">
<!-- ajout du ou des fichiers CSS-->
<title>Fiche Individuelle</title>
</head>
<body>
<?php
	$section="Détails de la configuration";
	include ('../Vue/common/Header.php');
?>
	<div class="content">
		<div class="triple-column-container" style="height:50px">
			<div>
                <a href="../Controlleur/Accueil.php" class="buttonlink">&laquo; Retour</a>
			</div>
			<div>
			</div>
			<!--<div>
				<a href="../Controlleur/ModifConfiguration.php?param=<?php echo $_GET['param'] ?>" class="buttonlink" style="float:right">Modifier la configuration</a>
			</div>-->
        </div>
		<div class="double-column-container">
			<div class="column">
				<?php
					include '../Controlleur/RecuperationInfosConfigs.php';
				?>
			</div>
			<div class="column">
					<div>
						<h2>Alertes</h2>
						<div>
							<?php include '../Vue/alerts_logs/CartoucheAlertes.php' ?>
						</div>
					</div>
				<i>Cartouche : Reporting</i>
				<div>
					<h2>Planning des Moissons</h2>
					<div>
						<?php include '../Vue/planning_moissons/CartouchePlanning.php' ?>
					</div>
				</div>

				<div>
					<h2>Historique des Moissons</h2>
					<div>
						<?php include '../Vue/planning_moissons/CartoucheHistorique.php' ?>
					</div>
				</div>

			</div>
		</div>
	</div>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">
    let number_of_alerts = <?= count($alerts)+1 ?? 1 ?>;
</script>
<script src="../js/fiche_individuelle.js"></script>

</html>
