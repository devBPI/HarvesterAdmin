<html lang="fr">
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/composants.css">
	<link rel="stylesheet" href="../css/accueilStyle.css">
	<link rel="stylesheet" href="../css/formStyle.css">
	<link rel="stylesheet" href="../css/reporting.css">
	<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css">
	<title>Rapport</title>
</head>

<body>
<?php
include('../Vue/common/Header.php');
if ($type=="processus") $page = "RapportsProcessus";
else $page = "RapportsDonnees";
$nb = 0;

$requete_generee = htmlspecialchars($requete_generee);
$group_by_index = strrpos($requete_generee, "GROUP BY") > 0 ? strrpos($requete_generee, "GROUP BY") : strlen($requete_generee);
$where_index = strrpos($requete_generee, "WHERE");
$from_index = strpos($requete_generee, "FROM");
$group_by = substr($requete_generee, $group_by_index+strlen("GROUP BY"), strlen($requete_generee) - $group_by_index - strlen("GROUP BY"));
$where = substr($requete_generee, $where_index+strlen("WHERE"), $group_by_index - $where_index - strlen("WHERE"));
$from = substr($requete_generee, $from_index+strlen("FROM"), $where_index - $from_index - strlen("FROM"));
$select = substr($requete_generee, strlen("SELECT"), $from_index - 0 -strlen("SELECT"));

if (isset($requetes_annexes)) {
	for ($i = 0; $i < count($requetes_annexes); $i++) {
		$requetes_annexes[$i]["query"] = htmlspecialchars($requetes_annexes[$i]["query"]);
		$group_by_index = strrpos($requetes_annexes[$i]["query"], "GROUP BY") > 0 ? strrpos($requetes_annexes[$i]["query"], "GROUP BY") : strlen($requetes_annexes[$i]["query"]);
		$where_index = strrpos($requetes_annexes[$i]["query"], "WHERE");
		$from_index = strpos($requetes_annexes[$i]["query"], "FROM");
		$requetes_annexes[$i]["group_by"] = substr($requetes_annexes[$i]["query"], $group_by_index+strlen("GROUP BY"), strlen($requetes_annexes[$i]["query"]) - $group_by_index - strlen("GROUP BY"));
		$requetes_annexes[$i]["where"] = substr($requetes_annexes[$i]["query"], $where_index+strlen("WHERE"), $group_by_index - $where_index - strlen("WHERE"));
		$requetes_annexes[$i]["from"] = substr($requetes_annexes[$i]["query"], $from_index+strlen("FROM"), $where_index - $from_index - strlen("FROM"));
		$requetes_annexes[$i]["select"] = substr($requetes_annexes[$i]["query"], strlen("SELECT"), $from_index - 0 - strlen("SELECT"));
	}
}

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
<?php }	?>
	<div class="generated_query">
		<h4 class="title_query title_main_query">Requête générée par la configuration du rapport</h4>
		<p style="text-align: left">
			<span class="first_keyword">SELECT</span><?= $select  ?>
			<br><br>
			<span class="first_keyword">FROM</span><?= $from ?>
			<br><br>
			<span class="first_keyword">WHERE</span><?= $where ?>
			<?php if ($group_by != "") { ?><br><br><span class="first_keyword">GROUP BY</span><?= $group_by ?><?php } ?>
		</p>
	</div>
<?php if (isset($requetes_annexes)) {
	foreach ($requetes_annexes as $rq) { ?>
	<div class="generated_query">
		<h4 class="title_query">Requête annexe utilisée sur le résultat de la requête principale</h4>
		<p style="text-align: left">
			<span class="first_keyword">SELECT</span><?= $rq["select"]  ?>
			<br><br>
			<span class="first_keyword">FROM</span><?= $rq["from"] ?>
			<br><br>
			<span class="first_keyword">WHERE</span><?= str_replace("{task_id}","<i>task_id</i>",$rq["where"]) ?>
			<?php if ($rq["group_by"] != "") { ?><br><br><span class="first_keyword">GROUP BY</span><?= $rq["group_by"] ?><?php } ?>
		</p>
			<div style="border-bottom: 1px solid grey; width:100%"></div>
		<p style="text-align: left">
			NB : <i>task_id</i> est remplacé par le task_id récupéré par la requête principale.
		</p>
	</div>
<?php }
} ?>
</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">
	let nb_col = <?= $nb ?>; // Nombre de colonnes
</script>
<script src="../js/rapports/sort_reporting.js"></script>


</html>