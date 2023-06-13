<!-- PHP de la cartouche contenant un tableau affichant le planning pour une configuration -->
<?php
ini_set("display_errors", 0);
error_reporting(0);

Gateway::connection();

if (isset($_GET['param'])) {
    $id = $_GET['param'];
}

$data = Gateway::getPlanifsForCartridge($id);

if (! $data) { ?>
    Aucune moisson n'est planifiée.
<?php
} else {
    /* Création du tableau */
    ?><table class="table-backoffice" style="width: 100.15%;">
	<th>Jour de la Moisson</th>
	<th>Heure de la Moisson</th>
    <?php
    /* Lignes */
    foreach ($data as $var) {
        ?><tr><?php

        /* Colonne 1 (Jour de la Moisson) */
        ?><td><?php
        switch ($var['dow']) {
            case 2:
                {
                    $jour = "Lundi";
                    break;
                }
            case 3:
                {
                    $jour = "Mardi";
                    break;
                }
            case 4:
                {
                    $jour = "Mercredi";
                    break;
                }
            case 5:
                {
                    $jour = "Jeudi";
                    break;
                }
            case 6:
                {
                    $jour = "Vendredi";
                    break;
                }
            case 7:
                {
                    $jour = "Samedi";
                    break;
                }
            case 1:
                {
                    $jour = "Dimanche";
                    break;
                }
            case null:
                {
                    $jour = "Quotidienne";
                }
        }
        echo $jour;
        /* Colonne 2 (Heure de la Moisson) */
        ?></td>
		<td><?php
        if ($var['h'] == 0) {
            $var['h'] = "00";
        }
        if ($var['m'] == 0) {
            $var['m'] = "00";
        }
        echo $var['h'] . ":" . $var['m'];
    }
    /* Fin de la ligne */
    ?>
	
	</tr><?php
    /* Fin du Tableau */
    ?></table><?php
     }
    ?>