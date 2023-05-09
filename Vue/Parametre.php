<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
session_start();
session_unset();
session_destroy();
?>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<title>Paramétrage</title>
</head>
<!-- Body (Div contenant tout (ou presque)) -->

<body>
<?php include('../Vue/Header.php'); ?>
<div class="content">
	<div class="cartouche-solo" style="width:100%;height:auto">
		<FORM action="<?php echo $section;?>.php" method="post" style="padding:5%">
			<div class="row">
				<div class="col-25">
					<label for="mapping">Nom du mapping</label>
				</div>
				<div class="col-50">
					<select id="list_mapping" name="list_mapping">
						<option value="0">Choisissez un mapping</option>
						<?php
							$i = 0;
							foreach ($data as $combo_key => $var) {
								$i ++;
								if(isset($var['id']))
								{
									echo '<option value="' . $var['id'] . '"' . (($id_param == $var['id']) ? ' selected' : '') . '>' . $var['name'] . '</option>';
								}
								else
								{
									echo '<option value="' . $combo_key . '"' . (($id_param == $combo_key) ? ' selected' : '') . '>' . $var['name'] . '</option>';
								}
							}
						?>
					</select>
				</div>
				<div class="col-25">
					<input type="submit" value="Afficher">
				</div>
			</div>
		</FORM>
	</div>
	<div class="triple-column-container">
		<div class="column" style="height:80px">
			<?php echo (isset($nom))? 
				"<a href=\"../Vue/ModifParamétrage.php?table=".$table."&id=".$id."\" class=\"buttonlink\">Modifier le mapping</a>":"";	
			?>
		</div>
		<div class="column" style="height:80px">
			<H3><?php echo (isset($nom))? "Mapping actuel : ".$nom:""; ?></H3>
		</div>
		<div class="column" style="height:80px">
		<a href="../Vue/ajoutParamétrage.php?table=<?php echo $table;?>" class="buttonlink" style="float:right">+ Ajouter un mapping</a>
		</div>
	</div>
	<?php if(isset($nom)): ?>
		<div class="double-column-container">
			<div class="column" style="height:600px">
					<TEXTAREA id="textArea" name="textArea" rows=32 readonly><?php echo (isset($def))? $def:"";?></TEXTAREA>
			</div>
			<div class="column" style="height:600px">
					<?php include '../Vue/affichageNomConfigs.php'; ?>
			</div>
		</div>
	<?php endif; ?>
</div>

	<!-- Ajout des scripts -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
</body>

</html>
