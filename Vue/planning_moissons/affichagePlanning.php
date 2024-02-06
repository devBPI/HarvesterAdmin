<div style="overflow-y: auto; height:300px">
<table class="table-planning">
	<th style="width:40%">Périodicité</th>
	<th style="width:20%">Heure</th>
	<th style="width:30%;overflow:visible">Configuration</th>
	<th style="width:10%"></th>
	<?php
	if($dowData[$dow]) {
		foreach ($dowData[$dow] as $var) {
			?><tr>
		<?php
		if($var['dowim_restriction']==null){
			echo '<td style="background-color:steelblue">Hebdomadaire</td>';
		} else {
		    // dowim = day of week in month 
			switch($var['dowim_restriction']){
				case 1:
					echo '<td style="background-color:seagreen">'.$var['dowim_restriction'].'er '.$journame.'</br>';
					$rec_day = 'first';
					break;
				case 2:
					echo '<td style="background-color:seagreen">'.$var['dowim_restriction'].'ème '.$journame.'</br>';
					$rec_day = 'second';
					break;
				case 3:
					echo '<td style="background-color:seagreen">'.$var['dowim_restriction'].'ème '.$journame.'</br>';
					$rec_day = 'third';
					break;
				case 4:
					echo '<td style="background-color:seagreen">'.$var['dowim_restriction'].'ème '.$journame.'</br>';
					$rec_day = 'fourth';
					break;
				case 5:
					echo '<td style="background-color:seagreen">'.$var['dowim_restriction'].'ème '.$journame.'</br>';
					$rec_day = 'fifth';
					break;
			}
			$calcul_jour = new DateTime($rec_day.' '.$dayname.' of this month'); // with time set to midnight
			$today = new DateTime("today"); // this object represents current date/time with time set to midnight
			
			// Remarque : pour simplifier on dit que si le calcul du nieme jour du mois tombe aujourd'hui
			// alors on laisse la date du jour (meme si l'heure de lancement est passee)
			
			if ($calcul_jour < new DateTime() && $calcul_jour != $today) { // si le nieme mardi (pour l'exemple) du mois est avant maintenant (passe)
    			$calcul_jour->modify($rec_day.' '.$dayname.' of next month');
	       	}
			setlocale(LC_TIME, array('fr_FR.utf8','fra'));
			$date_moisson = strftime('%d %B', $calcul_jour->getTimeStamp());
			echo '('.$date_moisson.')</td>';
		}
		?>
			<td><?php
			if ($var['h'] == 0) {
				$var['h'] = "00";
			}
			if ($var['m'] == 0) {
				$var['m'] = "00";
			}
			echo $var['h'] . ":" . $var['m'];
			?></td>
				<td><?php
				if ($var['dow'] == null && $var['dom'] == null && $var['dowim_restriction'] == null) {
				echo str_replace("_","_<wbr/>",$var['name']) . " (Quotidienne)";
			} else {
				echo str_replace("_","_<wbr/>",$var['name']);
		}

		?></td>
			<td>
				<form
					onsubmit="return confirm('Voulez vous vraiment supprimer cette planification ?');"
					action="../../Controlleur/PlanningMoisson.php?id=<?php echo $var['id']; ?>"
					method="post">
					<input type="image" alt="Supprimer la planification" id="cross" name="cross" src="../../ressources/cross.png" width="20px" height="20px">
				</form>
			</td>
		</tr>
		<?php

		}
	}
	?>
	
</table>
</div>
