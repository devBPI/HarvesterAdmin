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
<!-- ajout du ou des fichiers CSS-->
<title>Fiche Individuelle</title>
</head>
<body>
<?php
	$section="Détails de la configuration";
	include ('../Vue/Header.php');
?>
	<div class="content">
		<div class="triple-column-container" style="height:100px">
            <div class="column">
                <a href="../Controlleur/Accueil.php" class="buttonlink">&laquo; Retour</a>
            </div>
			<div></div>
			<div>
				<a href="../Controlleur/ModifConfiguration.php?param=<?php echo $_GET['param'] ?>" class="buttonlink" style="float:right">Modifier la configuration</a>
			</div>
        </div>
		<div class="double-column-container">
			<div class="column">
				<?php
					include '../Controlleur/RecuperationInfosConfigs.php';
				?>
			</div>

			<div class="column">
					<center>Cartouche : Alerte sur Moisson</center>

					<center>Cartouche : Reporting</center>
				<div>
					<div>
						<center>
							<h2>Planning des Moissons</h2>
						</center>
					</div>
					<div>
						<?php include '../Vue/planning_moissons/CartouchePlanning.php' ?>
					</div>
				</div>

				<div>
					<div>
						<center>
							<h2>Historique des Moissons</h2>
						</center>
					</div>
					<div>
						<?php include '../Vue/planning_moissons/CartoucheHistorique.php' ?>
					</div>
				</div>

			</div>
		</div>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
</body>

</html>
