<div style="overflow-y: auto; height:300px">
<table class="table-planning">
	<th>Heure</th>
	<th>Tâche</th>
	<th style="width:20px;"></th>
<?php
foreach ($dowData[$dow] as $var) { // $dow est le jour de la semaine souhaite, dowData de dow est l'ensemble des planifs du jour J
    ?><tr><?php

    ?><td><?php
    if ($var['h'] == 0) {
        $var['h'] = "00";
    }
    if ($var['m'] == 0) {
        $var['m'] = "00";
    }
    echo $var['h'] . ":" . $var['m'];
    ?></td>
    
		<td><?php

    // echo $var['name'] . " (dow = ".$var['dow'].")"." (dom = ".$var['dom'].")";
    
		
	$tasklabel = $var['name'];
	
	if ($var['parameter'] != null){
	    $tasklabel = $tasklabel."-".$var['parameter'];
	}
		
    if ($var['dow'] == null && $var['dom'] == null) {
        $tasklabel = $tasklabel. " (Quotidienne)";
    } 
    
    echo $tasklabel;

    ?></td>
		<td>
			<form
				onsubmit="return confirm('Voulez vous vraiment supprimer cette planification ?');"
				action="../Controlleur/PlanningTachesAnnexes.php?id=<?php echo $var['id']; ?>"
				method="post">
				<input type="image" id="cross" name="cross" src="../ressources/cross.png" width="20px" height="20px">
			</form>
	<?php
	   
	}
	?>
	
	</tr>
</table>
</div>