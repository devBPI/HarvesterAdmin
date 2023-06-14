<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<link rel="stylesheet" href="../css/alertes.css" />
<title>Alertes</title>
</head>
<body name="haut" id="haut">
	<?php
	include('../Vue/common/Header.php');
	$url = "AlertesReporting.php?&order=";
	?>
	<div class="content" style="width:90%">
		<table class="table-config">
			<thead>
<?php

$arrow = "";
$sens = "";
if ($order == "creation_time") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "creation_time DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" width=10% height=30px; onclick = 'location.href=\"" . $url . "creation_time " . $sens . "\"'>Création ". $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "level") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "level DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" width=10% onclick = 'location.href=\"" . $url . "level " . $sens . "\"'>Niveau " . $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "category") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "category DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" width=10%' onclick = 'location.href=\"" . $url . "category " . $sens . "\"'>Catégorie " . $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "configuration_name") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "configuration_name DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" width=10%' onclick = 'location.href=\"" . $url . "configuration_name " . $sens . "\"'>Configuration " . $arrow . "</th>";


$arrow = "";
$sens = "";
if ($order == "configuration_id") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "configuration_id DESC") {
    $arrow = "▲";
}
echo "<th scope=\"col\" width=10%' onclick = 'location.href=\"" . $url . "configuration_id " . $sens . "\"'>Configuration Id " . $arrow . "</th>";


echo "<th scope=\"col\" width=35%'>Message</th>";


$arrow = "";
$sens = "";
if ($order == "status") {
    $arrow = "▼";
    $sens = "DESC";
} else if ($order == "status DESC") {
    $arrow = "▲";
}

echo "<th scope=\"col\" width=5% onclick=\"suppressAlert()\"</th></thead>";

echo"<tbody id=\"displaytbody\">";

foreach ($alerts as $alert) {
    
    //$creationTimeSyst = date('d-m-Y H:i:s', strtotime($alert['creation_time'])) . " AlertesReporting.php";
	$creationTimeSyst = date('d-m-Y H:i:s', strtotime($alert['creation_time']));
    ?><tr>
				
		<td><?php
		     if(empty($creationTimeSyst)){
    		    echo "<div style='text-align:center;'>-</div>";
    		}else{
    		    echo "<div style='text-align:center;'>".$creationTimeSyst."</div>";
    		}
        ?></td>
		
	
	
	<?php
	   $level = $alert['level'];
	   $levelStyle = $level."_level";
	
		echo "<td class='$levelStyle'>";
 
    		if(empty($level)){
    		    echo "<div style='text-align:center;'>-</div>";
    		}else{
    		    echo "<div style='text-align:center;'>".$level."</div>";
    		}
    	echo "</td>";
        ?>
        
        
        <td><?php
            $category = $alert['category'];
            if(empty($category)){
    		    echo "<div style='text-align:center;'>-</div>";
    		}else{
    		    echo "<div style='text-align:center;'>".$category."</div>";
    		}
        ?></td>
        
        <td><?php
            $configurationName = $alert['configuration_name'];
            if(empty($configurationName)){
    		    echo "<div style='text-align:center;'>-</div>";
    		}else{
    		    echo "<div style='text-align:center;'>".$configurationName."</div>";
    		}
        ?></td>
        
        <td><?php
            $configurationId = $alert['configuration_id'];
            if(empty($configurationId)){
    		    echo "<div style='text-align:center;'>-</div>";
    		}else{
    		    echo "<div style='text-align:center;'>".$configurationId."</div>";
    		}
        ?></td>
		
		<td><?php
		    $message = $alert['message'];
            if(empty($message)){
    		    echo "<div style='text-align:center;'>-</div>";
    		}else{
    		    echo "<div style='text-align:left; margin-left:10px;'>".$message."</div>";
    		}
        ?></td>

		<td>
			<div class="button-hover" onclick="deleteRow(<?= $alert["id"]?>)" id="<?= $alert['id']?>" style="cursor:pointer">
				<img src="../ressources/cross.png" width="20px" height="20px">
			</div>
		</td>


	</tr>
	<?php } ?>
	</tbody>
	</table>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script>
		function deleteRow(i){
			post("AlertesReporting.php",{deleteRow : i});
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