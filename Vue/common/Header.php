<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<link rel="stylesheet" href="../css/header.css" />
<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css" />

<header>
	<div class="envBackgroundColor menu_div">
		<nav class="envBackgroundColor">
			<label for="drop" class="toggle">&#8801; Menu</label>
			<input type="checkbox" id="drop" />
			<ul class="menu">
				<li class="menu_left">
					<!-- First Tier Drop Down -->
					<label for="drop-1" class="toggle">Moissons</label>
					<a href="#">Moissons</a>
					<input type="checkbox" id="drop-1"/>
					<ul>
						<li class="envBackgroundColor">
							<!-- Second Tier Drop Down -->
							<label for="drop-2" class="toggle">Paramétrage</label>
							<a href="#">Paramétrage</a>
							<input type="checkbox" id="drop-2"/>
							<ul>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/Accueil.php';" href="#">Configurations</a></li>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/Mapping.php';" href="#">Mapping</a></li>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/Filtre.php';" href="#">Filtre</a></li>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/Traduction.php';" href="#">Traduction</a></li>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/EtatsDispo.php';" href="#">Disponibilité</a></li>
							</ul>
						</li>
						<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/MoissonSurDemande.php';" href="#">Moisson sur demande</a></li>
						<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/PlanningMoisson.php';" href="#">Planning des moissons</a></li>
						<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/HistoriqueMoisson.php';" href="#">Historique des moissons</a></li>
					</ul>
				</li>
				<li class="menu_left">
					<!-- First Tier Drop Down -->
					<label for="drop-3" class="toggle">Tâches Annexes</label>
					<a href="#">Tâches Annexes</a>
					<input type="checkbox" id="drop-3"/>
					<ul>
						<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/TacheAnnexeSurDemande.php';" href="#">Tâche sur demande</a></li>
						<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/PlanningTachesAnnexes.php';" href="#">Planning</a></li>
						<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/HistoriqueTachesAnnexes.php';" href="#">Historique</a></li>
					</ul>
				</li>
				<li class="menu_right">
					<label for="drop-4" class="toggle">Surveillance</label>
					<a href="#">Surveillance</a>
					<input type="checkbox" id="drop-4"/>
					<ul>
						<li class="envBackgroundColor">
							<label for="drop-5" class="toggle">Rapports</label>
							<a href="#">Rapports</a>
							<input type="checkbox" id="drop-5"/>
							<ul>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/Rapports.php?id=processus';" href="#">Processus</a></li>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/Rapports.php?id=donnees';" href="#">Données collectées</a></li>
							</ul>
						</li>
						<li class="envBackgroundColor">
							<label for="drop-6" class="toggle">Alertes</label>
						<a href="#">Alertes</a>
							<input type="checkbox" id="drop-6"/>
							<ul>
								<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/Alertes.php'" href="#">Liste des alertes</a></li>
								<li class="envBackgroundColor"><a onclick="" href="#">Paramétrage</a></li>
							</ul>
						</li>
						<li class="envBackgroundColor"><a onclick="window.location='../Controlleur/JournalLogs.php';" href="#">Logs</a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>
	<div class="title_menu_div">
		<h2 class="title_menu" onclick="window.location='../Controlleur/Accueil.php';"><?= $ini['version'] ?></h2>
	</div>
	<div class="entete_tricolor_div">
		<?php
		if($ini['version']=="DEV")
		{
			echo '<button class="boutonlink" onclick="restart()"><img style="width:20px;height:20px;" src="../ressources/stop.png"/></button>';
		}
		require_once("../Vue/common/Tricolor.php");
		?>
	</div>
	<h1 class="header"><?= $section ?></h1>
</header>