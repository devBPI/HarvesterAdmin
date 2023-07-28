<html lang="fr">
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<meta charset="utf-8" />

<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<link rel="stylesheet" href="../css/alerts_logs/alertes.css" />

<title>Accueil</title>
</head>
<body >
	<?php include('../Vue/common/Header.php'); ?>
	<div class="content" style="width:90%">
		<table class="table-config">
			<thead>
			<tr>
				<th scope="col" style="width:20%">ID</th>
				<th scope="col" style="width:10%"><a class="button" href="JournalLogs.php?niv=<?php echo $niv;?>">Niveau</a></th>
				<th scope="col" style="width:10%">Date</th>
				<th scope="col" style="width:60%">Message</th>
			</tr>
			</thead>
			<?php if($data) {
			foreach($data as $alerte) {
				echo "<tr><td scope=\"row\" data-label=\"ID\">".$alerte['user_id']."</td><td data-label=\"Niveau\" class='".(($alerte['level']=="WARN")?"warn":"error")."'>".$alerte['level']."</td><td data-label=\"Date\">".$alerte['date']."</td><td data-label=\"Message\">".$alerte['message']."</td></tr>";
			} }
			?>
		</table>
		<div style="margin : 0 auto; padding:3% 0; width: max-content;">
		<?php
		if($s>3){
			echo "<a href='JournalLogs.php?n=".$n."&page=1' class='buttonpage'>&laquo;</a>\t";
		}

		$index_lower = 2; 
		$index_upper = 2;
		if(($s-2)<1){
			$index_upper+=1-($s-2);
			$index_lower-=1-($s-2);
		} else if (($s+2)>$nb){
			$index_lower+=($s+2)-$nb;
			$index_upper-=($s+2)-$nb;
		}
		for ($i = $s-$index_lower; $i <= $s+$index_upper; $i ++)
		{
			if($i==$s){
				echo "<a href='JournalLogs.php?n=".$n."&page=" . $i . "' class='buttonpage' style='background-color:#4b6a7c'>" . $i . "</a>\t";
			} else {
				echo "<a href='JournalLogs.php?n=".$n."&page=" . $i . "' class='buttonpage'>" . $i . "</a>\t";
			}
		}
		if($s<$nb-2){
			echo "<a href='JournalLogs.php?n=".$n."&page=" . $nb . "' class='buttonpage'>&raquo;</a>";
		}
	?>
	</div>
</body>
</html>
