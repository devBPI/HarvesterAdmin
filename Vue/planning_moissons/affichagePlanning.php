<div style="overflow-y: auto; height:300px">
<table class="table-planning">
	<th width=40%>Périodicité</th>
	<th width=20%>Heure</th>
	<th width=30% style="overflow:visible">Configuration</th>
	<th width=10%></th>
	<?php
	foreach ($dowData[$dow] as $var) {
		?><tr>
	<?php
	if($var['dom']==null){
		echo '<td style="background-color:steelblue">Hebdomadaire</td>';
	} else {
		switch($var['dom']){
			case 1:
				echo '<td style="background-color:seagreen">'.$var['dom'].'er '.$journame.'</td>';
				$rec_day = 'first';
				break;
			case 2:
				echo '<td style="background-color:seagreen">'.$var['dom'].'ème '.$journame.'</br>';
				$rec_day = 'second';
				break;
			case 3:
				echo '<td style="background-color:seagreen">'.$var['dom'].'ème '.$journame.'</br>';
				$rec_day = 'third';
				break;
			case 4:
				echo '<td style="background-color:seagreen">'.$var['dom'].'ème '.$journame.'</br>';
				$rec_day = 'fourth';
				break;
			case 5:
				echo '<td style="background-color:seagreen">'.$var['dom'].'ème '.$journame.'</br>';
				$rec_day = 'fifth';
				break;
		}
		$calcul_jour = new DateTime($rec_day.' '.$dayname.' of this month');
		if ($calcul_jour < new DateTime()) {
			$calcul_jour->modify($rec_day.' '.$dayname.' of next month');
		}
		setlocale(LC_TIME, array('fr_FR.utf8','fra'));
		$date_moisson = utf8_encode(strftime('%d %B', $calcul_jour->getTimeStamp()));
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
		if ($var['dow'] == null && $var['dom'] == null) {
			echo $var['name'] . " (Quotidienne)";
		} else {
			echo $var['name'];
	}
	
    ?></td>
		<td>
			<form
				onsubmit="return confirm('Voulez vous vraiment supprimer cette planification ?');"
				action="../../Controlleur/PlanningMoisson.php?id=<?php echo $var['id']; ?>"
				method="post">
				<input type="image" id="cross" name="cross" src="../../ressources/cross.png" width="20px" height="20px">
			</form>
			</tr>
	<?php
	   
	}
	?>
	
</table>
</div>