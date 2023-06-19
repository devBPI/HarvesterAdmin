<!-- PHP de la cartouche contenant un tableau affichant l'historique pour une configuration -->
<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

Gateway::connection();

if (isset($_GET['param'])) {
    $id = $_GET['param'];
}

$data = Gateway::getTasksForCartridge($id);

if (!$data) {
    echo "Aucune tache dans l'historique.\n";
} 
else {
    /* Création du tableau */
    ?><table class="table-backoffice">
	<th>Date</th>
	<th>Statut</th>
	<th>Documents insérés</th>
	<th>Durée totale</th>
        <?php
    /* Lignes */
    foreach ($data as $var) {
        ?><tr>
            <td><?php
            $creationDateSyst = date('d-m-Y', strtotime($var['creation_date'])) . " ";
            echo $creationDateSyst;
            /* Colonne 2 (Statut) */
            ?></td>
            <td><?= str_replace("_", "_<wbr>",$var['status']) ?></td>
            <td>Indisponible</td>
            <td><?php


            $totalEffectiveDurationSec = $var['total_effective_duration_sec'];

            if(!empty($totalEffectiveDurationSec)){

                $temp = $totalEffectiveDurationSec % 3600;
                $hours = ( $totalEffectiveDurationSec - $temp ) / 3600 ;
                $temp2 = $temp % 60 ;
                $mins = ( $temp - $temp2 ) / 60;
                $secs = $temp2;

                if($hours < 10) {
                    $hours = '0'.$hours;
                }

                if($mins < 10) {
                    $mins = '0'.$mins;
                }

                if($secs < 10) {
                    $secs = '0'.$secs;
                }

                echo "".$hours."h".$mins."m".$secs."s";

            } else {
                echo "-";
            } ?>
            </td>
        </tr><?php
    }
    /* Fin du tableau */
    ?></table>
<?php
}

?>
