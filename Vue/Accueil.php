<html>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<link rel="stylesheet" href="../css/formStyle.css" />
<link rel="stylesheet" href="../css/environments/<?php echo strtolower($ini['version']);?>-style.css" />

<title>Accueil</title>
</head>
<body name="haut">
	<?php include('../Vue/Header.php'); ?>
	<div class="content">
		<div class="double-column-container">
			<div class="column" style="height:400px">
				<div class="cartouche-solo">
					<FORM action="../Controlleur/Accueil.php" method="post" style="padding:5%">
						<H3>Recherche</H3>
						<div class="row">
							<div class="col-25">
								<label for="name">Nom</label>
							</div>
							<div class="col-75">
								<input type="text" id="name" name="textNom" placeholder="Nom de la configuration...">
							</div>
						</div>
						<div class="row">
							<div class="col-25">
								<label for="connecteur">Connecteur</label>
							</div>
							<div class="col-75">
								<select id="list_grabber" name="list_grabber">
									<option value="0">Choisissez un connecteur</option>
									<?php
										$i = 0;
										foreach ($data as $var) {
											$i ++;
											echo '<option value="' . $var['id'] . '"' . (($id_grabber == $var['id']) ? ' selected' : '') . '>' . $var['name'] . '</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="row">
							<input type="submit" value="Rechercher">
						</div>
					</FORM>
				</div>
			</div>
			<div class="column" style="height:400px">
				<div class="cartouche-solo" style="overflow-x:auto;">
					<H3>Alertes du jour</H3>
					<?php 
						$alerts = Gateway::getAlerts("creation_time DESC");
						// print_r($alerts);
						$today = date('d-m-Y');
						foreach ($alerts as $alert) {
							$creationTimeSyst = date('d-m-Y', strtotime($alert['creation_time']));
							if($creationTimeSyst == $today){
								echo "<div class='row' style='white-space: nowrap;'>";
								echo "<div class='col-75'>";
								echo "<div style='text-align:center;'>".$alert['id']." - (".date('H:i', strtotime($alert['creation_time'])).") ". $alert['level'] ." - ". $alert['category'] ."</div>";
								echo "</div>";
								echo "<div class='col-25'>";
								echo "<div onclick='openForm(\"".$alert['message']."\")'><img src=\"../ressources/message.png\" width='20px' height='20px'/></div>";
								echo "</div>";
								echo "</div>";							
							}
						}
					?>
				</div>
			</div>
		</div>


		<?php include ("../Vue/affichageDesConfigs.php");?>	
		<div class="triple-column-container">
			<div class="column" style="text-align:left">
				<a href="../Vue/ajoutConfiguration.php"class="buttonpage">Ajouter une Configuration</a>
			</div>
			<div class="column">
				<?php
					echo "<a href='Accueil.php?name=" . $name . "&connecteur=" . $grabber . "&page=1&order=" . $order . "' class='buttonpage'>&laquo;</a>\t";
					for ($i = 1; $i <= $total_pages; $i ++) {
					echo "<a href='Accueil.php?connecteur=" . $grabber . "&name=" . $name . "&page=" . $i . "&order=" . $order . "' class='buttonpage'>" . $i . "</a>\t";
					}
					;
					$pageFin = $i - 1;
					echo "<a href='Accueil.php?name=" . $name . "&connecteur=" . $grabber . "&page=" . $pageFin . "&order=" . $order . "' class='buttonpage'>&raquo;</a>";
				?>
			</div>
		</div>
	</div>

	<div id="page-mask"></div>
	<div class="form-popup" id="validateForm">
		<div class="form-container" id="formProperty">
			<h3>Modification</h3>
			<p id="msgAlert"></p>
			<div class="row">
				<div onclick="closeForm()" class="buttonlink" style="float:right">OK</div>
			</div>
		</div>
	</div>

	<!-- Ajout des scripts -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script>
		function openForm(message) {
            document.getElementById("validateForm").style.display = "block";
            document.getElementById("page-mask").style.display = "block";
			document.getElementById("msgAlert").innerHTML = message;
        }

		function closeForm() {
			document.getElementById("validateForm").style.display = "none";
			document.getElementById("page-mask").style.display = "none";
		}
	</script>
	
</body>
</html>
