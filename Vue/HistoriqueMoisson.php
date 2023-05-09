<html>
<head>
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<!-- Ajout du ou des fichiers javaScript-->
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<link rel="stylesheet" href="../css/formStyle.css" />
<title>Historique des Moissons</title>
</head>
<body name="haut" id="haut" style="height: 100%;">
	<?php
include ('../Vue/Header.php');
$url = "HistoriqueMoisson.php?&order=";




?>
	<div class="content" style="width:90%">
	<table class="table-config">
		<thead>
<?php
$arrow = "";
$sens = "";
if ($order == "name") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "name DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "name " . $sens . "\"'>Nom abrégé" . $arrow . "</th>";

echo "<th scope=\"col\"'>Progression</th>";

$arrow = "";
$sens = "";
if ($order == "status") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "status DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "status " . $sens . "\"'>Statut" . $arrow . "</th>";
?>
		<th scope="col">Documents <br>insérés
		</th>
		<th scope="col">Volume de notices<br> attendu
		</th>
<?php

$arrow = "";
$sens = "";
if ($order == "creation_date") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "creation_date DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "creation_date " . $sens . "\"'>Création Demande" . $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "start_time") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "start_time DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "start_time " . $sens . "\"'>Début Moisson" . $arrow . "</th>";

$arrow = "";
$sens = "";
if ($order == "end_time") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "end_time DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" onclick = 'location.href=\"" . $url . "end_time " . $sens . "\"'>Fin Moisson" . $arrow . "</th>";
?>
		<th scope="col">Durée effective moisson</th>
		<th scope="col">Message</th>
		<?php
foreach ($tasks as $task) {
    // On s'occupe de créer et formater les date comme on le souhaite
    $creationDateSyst = date('d-m-Y H:i:s', strtotime($task['creation_date'])) . " ";
    $modificationDateSyst = date('d-m-Y H:i:s', strtotime($task['modification_date'])) . " ";
    $originalStartTime = $task['start_time'];
    $originalEndTime = $task['end_time'];
    
    $totalEffectiveDurationSec = $task['total_effective_duration_sec'];
    
    
    
    if(!empty($originalStartTime)){
       $harvestStartDateSyst = date('d-m-Y H:i:s', strtotime($originalStartTime)) . " ";
    }else{
       $harvestStartDateSyst = "";
    }
    
    if(!empty($originalEndTime)){
        $harvestEndDateSyst = date('d-m-Y H:i:s', strtotime($originalEndTime)) . " ";
    }else{
        $harvestEndDateSyst = "";
    }
    
    $harvestTaskCreationDate = date_create($creationDateSyst);
    $harvestTaskModificationDate = date_create($modificationDateSyst);
   // $harvestStartTime = date_create($harvestStartDateSyst);
   //  $harvestEndTime = date_create($harvestEndDateSyst);

    ?></thead><tr><?php

        ?><td scope="row" data-label="Nom"><?php
        echo $task['name'];
        ?></td>
			<td data-label="Progression"><?php
			if(isset($task['progress']))
			{
				echo "<div class='progress' name='PRGR-".$task['id']."'/>";
			}
        ?></td>
			<td data-label="Statut"><?php
			if(preg_match('/(ERROR)/',$task['status']))
			{
			    /* Ancien code
				echo "<FORM action='../Controlleur/HistoriqueMoisson.php?id=".$task['id']."' method='post' onsubmit=\"return confirm('Voulez vous vraiment relancer cette moisson ?');\">
				<input type='submit' class='error-light-color' style='margin-top:10%' value='".$task['status']."'/></FORM>";
				*/
			    
			    echo "<div name='error-form-div'>";
			    
			    // Formulaire englobant
			    // echo "<FORM action='../Controlleur/HistoriqueMoisson.php?id=".$task['id']."' method='post' onsubmit=\"return confirm('Voulez vous vraiment relancer cette partie de moisson ?');\">";
			    
			    
			    //echo "<div>*".$task['has_no_more_recent_indexed']."*</div>";
			    
			    if($task['has_no_more_recent_indexed'] == 'true' OR $task['has_no_more_recent_indexed'] == 't' ){ // CTLG-400 (pour se preserver de la double-relance du INDEX_ERROR)

    			    // Libelle Statut (en gras car pas de plus recent indexe)
    			    
    			    echo "  <div style = 'color:red; font-weight : bold;'>".$task['status']."</div>";
			    } else{
			        
			        // Libelle Statut (pas en gras)
			        
			        echo "  <div style='color:red;>".$task['status']."</div>";
			        
			    }

					// Bouton Relance
	
					echo "    <button name='reload-button' class='error-light-color'>";
					echo "      <img src='../ressources/reload.png' width='15px' height='15px'/>";
					echo "    </button></div>";
	    	    
			    // Fin Formulaire englobant		
			    // echo "</FORM>";
			    
			    echo "</div>";
			}
			else
			{
				echo "<div class='statusprogress' name='STAT-".$task['id']."'/div>";
			}
        ?></td>
			<td data-label="Documents insérés"><?php
			if($task['notices_number'] > 0){
			    echo $task['notices_number'];
			}else{
			    echo "-";
		    }
        ?></td>
			<td data-label="Nombre attendu"><?php
			if($task['expected_notices_number'] > 0){
			    echo $task['expected_notices_number'];
			}else{
			    echo "-";
			}
        ?></td>
			<td data-label="Création demande"><?php
        if(!empty($creationDateSyst)){
            echo $creationDateSyst;
        }else{
            echo "-";
        }
        ?></td>
        	<td data-label="Début moisson"><?php
        	if(!empty($harvestStartDateSyst)){
        	    echo $harvestStartDateSyst;
        	}else{
        	    echo "-";
        	}
        ?></td>
			<td data-label="Fin moisson"><?php
			if(!empty($harvestEndDateSyst))
			{
			    echo $harvestEndDateSyst;
			}else{
			    echo "-";
			}
        ?></td>
			<td data-label="Durée effective"><?php
			
			if(!empty($totalEffectiveDurationSec)){
			    
			    $temp = $totalEffectiveDurationSec % 3600;
			    
			    $hours = ( $totalEffectiveDurationSec - $temp ) / 3600 ;
			    
			    $temp2 = $temp % 60 ;
			    
			    $mins = ( $temp - $temp2 ) / 60;
			    
			    $secs = $temp2;
			    
			    
			    if($hours < 10){
			        $hours = '0'.$hours;
			    }
			    
			    if($mins < 10){
			        $mins = '0'.$mins;
			    }
			    
			    if($secs < 10){
			        $secs = '0'.$secs;
			    }
			    
			    echo "".$hours."h".$mins."m".$secs."s";
			    
			}else{
			    echo "-";
			}
        ?></td>
			<td data-label="Message"><?php
			if("Erreur" !== '' && strncmp($task['message'], "Erreur", strlen("Erreur")) === 0){
				$message=str_replace(CHR(10),"</br>",$task['message']);
				$message=str_replace(CHR(13),"</br>",$message);  
				$message = htmlspecialchars($message, ENT_QUOTES);
				echo "<div onclick='openForm(\"".$message."\")' style=\"color:red; font-weight:bold;\">ERROR  <img src=\"../ressources/message.png\" width='20px' height='20px'/></div>";
			} else {
				echo $task['message'];
			}
        ?></td><?php
        ?></tr><?php
	}
	?>
	</table>
	</div>
	<br />
	<div style="margin : 0 auto; padding:3% 0; width: max-content;">
	<?php
		if($page>3){
			echo "<a href='HistoriqueMoisson.php?page=1' class='buttonpage'>&laquo;</a>\t";
		}

		$index_lower = 2; 
		$index_upper = 2;
		if(($page-2)<1){
			$index_upper+=1-($page-2);
			$index_lower-=1-($page-2);
		} else if (($page+2)>$total_pages){
			$index_lower+=($page+2)-$total_pages;
			$index_upper-=($page+2)-$total_pages;
		}
		for ($i = $page-$index_lower; $i <= $page+$index_upper; $i ++)
		{
			if($i==$page){
				echo "<a href='HistoriqueMoisson.php?page=" . $i . "' class='buttonpage' style='background-color:#4b6a7c'>" . $i . "</a>\t";
			} else {
				echo "<a href='HistoriqueMoisson.php?page=" . $i . "' class='buttonpage'>" . $i . "</a>\t";
			}
		}
		if($page<$total_pages-2){
			echo "<a href='HistoriqueMoisson.php?page=" . $total_pages . "' class='buttonpage'>&raquo;</a>";
		}
	?>
	</div>

	<div id="page-mask"></div>
	<div class="form-popup" id="messageForm">
		<div class="form-container" id="formProperty">
			<h3>Message d'erreur</h3>
			<p id="msgAlert"></p>
			<div class="row">
				<div onclick="closeForm()" class="buttonlink" style="float:right">OK</div>
			</div>
		</div>
	</div>

	<script src='../js/progress.js'></script>
	<script src='../js/histo-task-status.js'></script>
	<script>
		function openForm(message) {
            document.getElementById("messageForm").style.display = "block";
            document.getElementById("page-mask").style.display = "block";
			document.getElementById("msgAlert").innerHTML = message;
        }

		function closeForm() {
			document.getElementById("messageForm").style.display = "none";
			document.getElementById("page-mask").style.display = "none";
		}
	</script>
</body>
</html>
