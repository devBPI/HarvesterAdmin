<head>
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<!-- Ajout du ou des fichiers javaScript-->
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<!-- ajout du ou des fichiers CSS-->
<title>Journal des Logs</title>
</head>
<body name="haut" id="haut">
<?php
include ('../Vue/Header.php');
?>
    <div class="content">
		<div id="divTable"
			style="top: 0; left: 0.5%; width: 98%; height: auto; position: relative;">
			<table class="table-backoffice">
				<th>id</th>
				<th>dated</th>
				<th>thread</th>
				<th>lvl</th>
				<th>message</th>
				<?php
    /* Lignes */
    foreach ($data as $var) { ?>
		<tr>
			<td><?= $var['id']; ?></td>
			<td><?= $var['dated']; ?></td>
			<td><?= $var['thread']; ?></td>
			<td><?= $var['lvl']; ?></td>
			<td><?= $var['message']; ?></td>
		</tr>
	<?php } ?>
			</table>
		</div>
		<div
			style="width: 100%; left: 0.5%; margin-top: 1%; z-index: 2; position: relative;">
            <?php
            $pagLink = "<div class='pagination'><a href='JournalLogs.php?page=1' style='margin-top:0.5%; position:absolute:'>&laquo;</a>";
            echo '';
            for ($i = 1; $i <= $total_pages; $i ++) {
                $pagLink .= "<a href='JournalLogs.php?page=" . $i . "' class='active' style='margin:0.5%;'>" . $i . "</a>";
            }
            ;
            $pageFin = $i - 1;
            echo $pagLink . "<a href='JournalLogs.php?page=" . $pageFin . "' style='margin-top:0.5%;'>&raquo;</a> </div>";
            ?>
        </div>
	</div>
</body>
</html>