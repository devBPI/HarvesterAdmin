<html lang="fr">
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
$nb = 0;
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
<?php if (!$query_empty_or_error) { ?>
	<table class="table-config">
		<thead>
		<tr id="head_tableau" style="cursor:pointer">
			<?php foreach ($report["result"][0] as $key => $value) { ?>
				<th id="th_cell_<?= $nb ?>" class="order_asc" onclick="maj_col(<?= $nb++ ?>)"><?= $key ?></th>
			<?php } ?>
		</tr>
		</thead>
		<tbody id="emplacement_tableau">
			<?php foreach ($report["result"] as $ligne) { ?>
			<tr>
			<?php foreach ($ligne as $key => $value) { ?>
				<td><?= str_replace("_", "_<wbr>", $value) ?></td>
			<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
<?php } else { ?>
	<br/><br/>
	<p class="avertissement" style="text-align: left">
		Le rapport n'a retourné aucun résultat. Cela peut signifier que les critères sont invalides ou en conflit les uns
		avec les autres : veuillez vérifier la configuration du rapport <a href="../Controlleur/Rapports<?= ucfirst($type) ?>Edition.php?id=<?= $report_id ?>&viewonly">en cliquant ici</a>.</p>
	<p style="text-align: left; font-size:18px">De manière non-exhaustive, voici la liste des conflits qui peuvent conduire à un rapport vide :</p>
<?php if($type == "processus") { ?>
	<ul>
		<li style="font-size:18px">Date de début inférieure à date de fin</li>
		<li style="font-size:18px">Durée de la moisson supérieure à l'intervalle entre début et fin de moisson</li>
		<li style="font-size:18px">Critères "Fin de la moisson" et "Statut" d'erreur sélectionnés en même temps (une moisson en erreur n'a pas de date de fin)</li>
		<li style="font-size:18px">...</li>
	</ul>
<?php } else { ?>
	<ul>
		<li style="font-size:18px">Intervalle de dates mal configuré</li>
		<li style="font-size:18px">...</li>
	</ul>
<?php } ?>
	<br/><br/>
<?php }	?>

	<?= str_replace(["WHERE", "FROM", "GROUP BY"], ["<br>WHERE", "<br>FROM", "<br>GROUP BY"], $requete_generee ?? "") ?>
</div>


</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">
	let nb_col = <?= $nb ?>; // Nombre de colonnes
</script>
<script src="../js/rapports/sort_reporting.js"></script>


</html>