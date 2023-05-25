<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<link rel="stylesheet" href="../css/enteteStyle.css" />
<link rel="stylesheet" href="../css/enteteMenuStyle.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/environments/<?php echo strtolower($ini['version']);?>-style.css" />
<script src="../js/reboot.js"></script>
<div id="entete" class="entete envBackgroundColor envBorderColor">



	<div id="all-menus" class="allMenus">
		<nav class="navMenus">
			<ul>
				<li class="mainMenu">Gestion des Moissons
					<ul class="menuContent envBackgroundColor envBorderColor">
						<li class="itemDeroulable"><a class="lienItem" href=#>Paramétrage</a>
    						  <ul class="submenuContent envBackgroundColor envBorderColor" style="top:40px;left:300px;"> 
    						  	   <li onclick="window.location='../Controlleur/Accueil.php';"><a class="lienItem" href=#>Configurations</a></li>
    					           <li onclick="window.location='../Controlleur/Mapping.php';"><a class="lienItem" href=#>Mapping</a></li>
    					           <li onclick="window.location='../Controlleur/Filtre.php';"><a class="lienItem" href=#>Filtre</a></li>
    					           <li onclick="window.location='../Controlleur/Traduction.php';"><a class="lienItem" href=#>Traduction</a></li>
    					           <li onclick="window.location='../Controlleur/EtatsDispo.php';"><a class="lienItem" href=#>Etats de Disponibilité</a></li>
    				          </ul>
						</li>
						<li onclick="window.location='../Controlleur/MoissonSurDemande.php';"><a class="lienItem" href=#>Moisson sur Demande</a></li>
						<li onclick="window.location='../Controlleur/PlanningMoisson.php';"><a class="lienItem" href=#>Planning des Moissons</a></li>
						<li onclick="window.location='../Controlleur/HistoriqueMoisson.php';"><a class="lienItem" href=#>Historique des Moissons</a></li>
						<li class="itemDeroulable"><a class="lienItem" href=#>Alertes, Logs et Reporting</a>
							<ul class="submenuContent envBackgroundColor envBorderColor" style="top:180px;left:300px;">
									<li onclick="window.location='../Controlleur/AlertesReporting.php';"><a class="lienItem" href=#>Alertes</a></li>
					                <li onclick="window.location='../Controlleur/JournalLogs.php';"><a class="lienItem" href=#>Logs</a></li>
							</ul>
						</li>

					</ul>
				</li>
				
				<li class="mainMenu ">Gestion des Taches Annexes
					<ul class="menuContent envBackgroundColor envBorderColor">
						<li onclick="window.location='../Controlleur/TacheAnnexeSurDemande.php';"><a class="lienItem" href=#>Tâche Annexe sur Demande</a></li>
						<li onclick="window.location='../Controlleur/PlanningTachesAnnexes.php';"><a class="lienItem" href=#>Planning Tâches Annexes</a></li>
						<li onclick="window.location='../Controlleur/HistoriqueTachesAnnexes.php';"><a class="lienItem" href=#>Historique Tâches Annexes</a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>


	<a href="../Controlleur/Accueil.php"><h2 class="enteteTitle" style="top:-27px"><?php echo $ini['version']; ?></h2></a>
	<h1 class="enteteTitle" style="top:-8px"> <?php echo $section; ?></h1>
	<div style="float:right;text-align:right;margin-top:-45px">
		<?php
		if($ini['version']=="DEV")
		{
			echo '<button style="background-color:rgba(0,0,0,0);border:none;position:fixed;right:0px;top:25px;z-index:4" onclick="restart()"><img style="width:20px;height:20px;"src="../ressources/stop.png"/></button>';
		}
		?>
		<div id="tricolor" class="enteteTricolor">
			<?php include("../Vue/Tricolor.php");?>
		</div>
	</div>

</div>


