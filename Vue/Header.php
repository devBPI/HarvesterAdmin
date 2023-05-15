<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<link rel="stylesheet" href="../css/header.css" />
<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css" />

<div class="envBackgroundColor">
    <nav>
        <label for="drop" class="toggle">&#8801; Menu</label>
        <input type="checkbox" id="drop" />
        <ul class="menu">
            <li> 
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
                    <li class="envBackgroundColor">
                        <!-- Second Tier Drop Down -->
                        <label for="drop-3" class="toggle">Alertes et Reporting</label>
                        <a href="#">Alertes et Reporting</a>
                        <input type="checkbox" id="drop-3"/>
                        <ul>
                            <li class="envBackgroundColor"><a onclick="window.location='../Controlleur/AlertesReporting.php';" href="#">Alertes</a></li>
                            <li class="envBackgroundColor"><a onclick="window.location='../Controlleur/JournalLogs.php';" href="#">Logs</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li> 
                <!-- First Tier Drop Down -->
                <label for="drop-4" class="toggle">Tâches Annexes</label>
                <a href="#">Tâches Annexes</a>
                <input type="checkbox" id="drop-4"/>
                <ul>
                    <li class="envBackgroundColor"><a onclick="window.location='../Controlleur/TacheAnnexeSurDemande.php';" href="#">Tâche sur demande</a></li>
                    <li class="envBackgroundColor"><a onclick="window.location='../Controlleur/PlanningTachesAnnexes.php';" href="#">Planning</a></li>
                    <li class="envBackgroundColor"><a onclick="window.location='../Controlleur/HistoriqueTachesAnnexes.php';" href="#">Historique</a></li>
                </ul>
            </li>
        </ul>

        <div style="height:60px; float:right;">
		<?php
		if($ini['version']=="DEV")
		{
			echo '<button class="boutonlink" onclick="restart()"><img style="width:20px;height:20px;" src="../ressources/stop.png"/></button>';
            require_once("../Vue/Tricolor.php");
        }
        ?>
	    </div>

        <h2 class="titleMenu" onclick="window.location='../Controlleur/Accueil.php';"><?= $ini['version'] ?></h2>
    </nav>
</div>

<h1 class="header"><?= $section ?></h1>