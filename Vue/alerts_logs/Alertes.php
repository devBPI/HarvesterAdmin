<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/composants.css">
	<link rel="stylesheet" href="../css/accueilStyle.css">
	<link rel="stylesheet" href="../css/alerts_logs/alertes.css">
	<title>Alertes</title>
</head>
<body id="haut">
	<?php
	include('../Vue/common/Header.php');
	$url = "Alertes.php?&order=";
	?>
	<div class="content" style="width:90%">
		<table class="table-config">
			<thead><tr>
<?php

$arrow = "";
$sens = "";
if ($order == "creation_time") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "creation_time DESC") {
    $arrow = "▲";
}
echo "<th style=\"cursor:pointer;width:10%;height:30px\" onclick = 'location.href=\"" . $url . "creation_time " . $sens . "\"'>Création ". $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "level") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "level DESC") {
    $arrow = "▲";
}
echo "<th style=\"cursor:pointer;width:10%\" onclick = 'location.href=\"" . $url . "level " . $sens . "\"'>Niveau " . $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "category") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "category DESC") {
    $arrow = "▲";
}
echo "<th style=\"cursor:pointer;width:10%\" onclick = 'location.href=\"" . $url . "category " . $sens . "\"'>Catégorie " . $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "configuration_name") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "configuration_name DESC") {
    $arrow = "▲";
}
echo "<th style=\"cursor:pointer;width:10%\" onclick = 'location.href=\"" . $url . "configuration_name " . $sens . "\"'>Configuration " . $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "configuration_id") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "configuration_id DESC") {
    $arrow = "▲";
}
echo "<th style=\"cursor:pointer;width:10%\" onclick = 'location.href=\"" . $url . "configuration_id " . $sens . "\"'>Configuration Id " . $arrow . "</th>";


echo "<th style=\"cursor:pointer;width:35%\">Message</th>";


$arrow = "";
$sens = "";
if ($order == "status") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "status DESC") {
    $arrow = "▲";
}

echo "<th style=\"width:5%\" onclick=\"suppressAlert()\"></th></tr></thead>";

echo"<tbody id=\"displaytbody\">";

if ($alerts && count($alerts) > 0) {
foreach ($alerts as $alert) {
    
	//$creationTimeSyst = date('d-m-Y H:i:s', strtotime($alert['creation_time'])) . " AlertesReporting.php";
	$creationTimeSyst = date('d-m-Y H:i:s', strtotime($alert['creation_time']));
	$level = $alert['level'];
	$levelStyle = $level."_level";
	$category = $alert['category'];
	$configurationName = str_replace(["_"," ("],["_<wbr/>", " <br>("],$alert['configuration_name']);
	$configurationId = $alert['configuration_id'];
	$message = $alert['message'];

	?>
	<tr style="text-align:center">
		<td>
			<div><?= empty($creationTimeSyst)?"-":$creationTimeSyst ?></div>
		</td>
		<td class="<?= $levelStyle ?>">
			<div><?= empty($level)?"-":$level ?></div>
		</td>
		<td>
			<div><?= empty($category)?"-":$category ?></div>
		</td>
		<td>
			<div><?= empty($configurationName)?"-":$configurationName ?></div>
		</td>
		<td>
			<div><?= empty($configurationId)?"-":$configurationId ?></div>
		</td>
		<td>
			<div><?= empty($message)?"-":$message ?></div>
		</td>
		<td>
			<div class="button-hover" onclick="deleteRow(<?= $alert["id"]?>)" id="<?= $alert['id']?>" style="cursor:pointer">
				<img src="../ressources/cross.png" alt="Supprimer une ligne" style="width:20px;height:20px">
			</div>
		</td>
	</tr>
	<?php
	}
}?>
	</tbody>
	</table>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script>
		function deleteRow(i){
			post("Alertes.php",{deleteRow : i});
  	  }

		/**
		 * envoie une requête à l'url spécifié depuis un formulaire.
		 * @param {string} path le chemin où on doit envoyer le formulaire
		 * @param {object} params les paramètres à faire passer dans le formulaire
		 * @param {string} [method=post] la méthode du formulaire, codé en dur par simplicité (déjà précisé dans le nom de la fonction)
		 */
		function post(path, params, method='post') {
			const form = document.createElement('form');
			form.method = method;
			form.action = path;

			for (const key in params) {
				if (params.hasOwnProperty(key)) {
					const hiddenField = document.createElement('input');
					hiddenField.type = 'hidden';
					hiddenField.name = key;
					hiddenField.value = params[key];

					form.appendChild(hiddenField);
				}
			}

			document.body.appendChild(form);
			form.submit();
		}

	</script>
</body>
</html>