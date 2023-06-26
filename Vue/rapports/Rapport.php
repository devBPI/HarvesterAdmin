<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />
	<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css" />
	<title>Rapport</title>
</head>

<body>
<?php
include('../Vue/common/Header.php');
if ($type=="processus") $page = "RapportsProcessus";
else $page = "RapportsDonnees";
?>
<div class="content">
	<div class="button_top_div">
		<?php if ($type == "processus") { ?>
			<a href="../../Controlleur/Rapports.php?id=processus" class="buttonlink" style="float:none; height:16px">« Retour aux rapports sur les processus</a>
		<?php } else { ?>
			<a href="../../Controlleur/Rapports.php?id=donnees" class="buttonlink" style="float:none">« Retour aux rapports sur les métadonnées</a>
		<?php } ?>
		<p style="text-align:right;margin:0;padding-top:12px">Version du <?= date("d/m/Y \à H:i:s") ?></p>
	</div>
	<table class="table-config">
		<thead>
		<tr>
			<?php foreach ($report["result"][0] as $key => $value) { ?>
				<th><?= $key ?></th>
			<?php } ?>
		</tr>
		</thead>
		<tbody>
			<?php foreach ($report["result"] as $ligne) { ?>
			<tr>
			<?php foreach ($ligne as $key => $value) { ?>
				<td><?= str_replace("_", "_<wbr>", $value) ?></td>
			<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?= str_replace(["WHERE", "FROM", "GROUP BY"], ["</br>WHERE", "</br>FROM", "</br>GROUP BY"], $requete_generee ?? "") ?>
</div>


</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">

</script>

</html>