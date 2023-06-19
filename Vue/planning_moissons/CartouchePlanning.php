<!-- PHP de la cartouche contenant un tableau affichant le planning pour une configuration -->
<?php
<<<<<<< HEAD
ini_set("display_errors", 1);
error_reporting(E_ALL);
=======
//ini_set("display_errors", 1);
//error_reporting(E_ALL);

include("../../Composant/ErrorReportingConfig.php");
>>>>>>> 9787e2ccb2dfbcf325e9d351472748a4b8051e07

Gateway::connection();

if (isset($_GET['param'])) {
    $id = $_GET['param'];
}

$data = Gateway::getPlanifsForCartridge($id);
if (!$data) { ?>
    Aucune moisson n'est planifiée.
<?php } else {
    /* Création du tableau */
?>
	<table class="table-backoffice" style="width: 100.15%;">
		<th>Jour de la Moisson</th>
		<th>Heure de la Moisson</th>
		<?php
		/* Lignes */
		foreach ($data as $var) {
			switch ($var['dow']) {
				case 2:
					$jour = "Lundi";
					break;
				case 3:
					$jour = "Mardi";
					break;
				case 4:
					$jour = "Mercredi";
					break;
				case 5:
					$jour = "Jeudi";
					break;
				case 6:
					$jour = "Vendredi";
					break;
				case 7:
					$jour = "Samedi";
					break;
				case 1:
					$jour = "Dimanche";
					break;
				case null:
					$jour = "Quotidienne";
			}

			if ($var['h'] == 0) {
				$var['h'] = "00";
			}
			if ($var['m'] == 0) {
				$var['m'] = "00";
			} ?>
		<tr>
			<?php /* Colonne 1 (Jour de la Moisson) */?>
			<td><?= $jour ?></td>
			<?php /* Colonne 2 (Heure de la Moisson) */ ?>
			<td><?= $var['h'] . ":" . $var['m'] ?></td>
		<?php }
		/* Fin de la ligne */ ?>
		</tr>
	<?php /* Fin du Tableau */?>
	</table><?php
     }
    ?>
