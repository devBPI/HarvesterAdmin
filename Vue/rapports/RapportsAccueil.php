<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />
	<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css" />
	<title>Paramétrage des rapports sur les <?= $type ?> </title>
</head>

<body>
<?php
include('../Vue/Header.php');
if ($type=="processus") $page = "RapportsProcessus";
else $page = "RapportsDonnees";
?>
<div class="content">
	<table class="table-config">
		<thead>
			<tr>
				<th width="70%">Nom de la configuration</th>
				<th width="15%">Éditer</th>
				<th width="15%">Afficher</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($configurations as $configuration) { ?>
			<tr>
				<td><?= $configuration["name"] ?></td>
				<td>
					<a href="../Controlleur/<?= $page ?>Edition.php?id=<?= $configuration["id"] ?>">
						<img src="../ressources/edit.png" alt="Modifier la configuration" width="30px" height="30px"/>
					</a>
				</td>
				<td>
					<a href="../Controlleur/<?= $page ?>Edition.php?id=<?= $configuration["id"] ?>&viewonly">
						Afficher
					</a>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="3" style="text-align: left">
				<a href="../Controlleur/<?= $page ?>Edition.php">
					<img src="../../ressources/add.png" alt="Ajouter une configuration" width="30px" height="30px">
				</a>
			</td>
			</td>
		</tr>
		</tbody>
	</table>
</div>

<?php if (isset($msg_type)) { ?>
<div id="page-mask" style="display:block"></div>
<div class="form-popup" id="validateForm" style="display:block">
	<div class="form-container" id="formProperty">
		<h3><?= $msg_title ?></h3>
		<div class="form-popup-corps">
			<p id="<?= $msg_type=="action_error"?"msgAlert":"" ?>"><?= $msg_text ?></p>
			<button onclick="closeForm()" class="buttonlink">OK</button>
		</div>
	</div>
</div>
<?php } ?>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="/js/pop_up.js"></script>
<script type="text/javascript">
</script>

</html>