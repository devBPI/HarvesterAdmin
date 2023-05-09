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
<!-- ajout du ou des fichiers CSS-->
<title>Paramétrage</title>
</head>
<body>
	<?php include ('../Vue/Header.php'); ?>

	<div class="content" style="width:90%">
		<div class="column" style="height:80px;float:right">
			<?php
			if ($modify == "false") 
			{
				echo "<a href='EtatsDispo.php?modify=true' class='buttonlink' style='background-color:#77b8dd'>Modifier</a>";
			}
			else 
			{
				echo "<a href='EtatsDispo.php?modify=false' class='buttonlink' style='background-color:#77b8dd'>Finir modification</a>";
			}
			?>
		</div>
		<table class="table-config">
			<thead>
				<th scope="col" style="width:10%">Code</th>
				<th scope="col" style="width:20%">Disponibilité</th>
				<th scope="col" style="width:20%">Â moissonner</th>
				<th scope="col" style="width:20%">Label</th>
			</thead>
			<?php if($modify=="true"){?>
				<tr>
				<FORM action="EtatsDispo.php?add=true" method="post" onsubmit="return confirm('Voulez vous vraiment ajouter ce status ?');">
					<td scope="row" data-label="Code">
						<TEXTAREA name="code"></TEXTAREA>
					</td>
					<td data-label="Disponibilité">
						<select name="list_dispo" style="width: 100%">
							<option value="">
							<option value="D">D</option>
						</select>
					</td>
					<td data-label="Â moissonner">
						<select name="to_harvest" style="width: 100%">
							<option value="t">True</option>
							<option value="f">False</option>
						</select>
					</td>
					<td data-label="Label">
						<TEXTAREA name="label"></TEXTAREA>
					</td>
					<th><input type='submit' class='button primairy-color' style='width:100%' value="Ajouter"/></th>
				</FORM>
			</tr>
			<?php }
			foreach ($data as $var) {
			?>
				<tr>
					<FORM action="EtatsDispo.php?code=<?php echo $var['code'];?>" method="post" onsubmit="return confirm('Voulez vous vraiment modifier ce status ?');">
						<td scope="row" data-label="Code">
							<?php echo $var['code'];?>
						</td>
						<?php if($modify=="true"){?>
							<td data-label="Disponibilité">
								<select name="list_dispo" style="width: 100%">
									<option value=""
										<?php echo ($var['dispo_flag']=="") ? 'selected' : '';?>></option>
									<option value="D"
										<?php echo ($var['dispo_flag']!="") ? 'selected' : '';?>>D</option>
								</select>
							</td>
							<td data-label="Â moissonner">
								<select name="to_harvest" style="width: 100%">
									<option value="t"
										<?php echo ($var['select_to_harvest']=="t") ? 'selected' : '';?>>
										True
									</option>
									<option value="f"
										<?php echo ($var['select_to_harvest']=="f") ? 'selected' : '';?>>
										False
									</option>
								</select>
							</td>
						<td data-label="Label"><TEXTAREA name="label"><?php echo $var['label'];?></TEXTAREA></td>
						<th>
							<input type='submit' class='button primairy-color' style='width:100%' value="Modifier"/>
						</th>
						<?php } else{?>
							<td data-label="Disponibilité">
								<?php echo $var['dispo_flag'];?>
							</td>
						<td data-label="Â moissonner">
							<?php echo ($var['select_to_harvest']=="f")?"False":"True";?>
						</td>
						<td data-label="Label">
							<?php echo $var['label'];?>
						</td>
						<?php } ?>
					</FORM>
					<?php if($modify=="true"){?>
						<form onsubmit="return confirm('Voulez vous vraiment supprimer ce status ?');" action="EtatsDispo.php?delete=<?php echo $var['code'] ?>" method="post">
							<td style="background-color:#1fe0;"><input type="image" id="cross" name="cross" src="../ressources/cross.png" width="20px" height="20px"></td>
						</form>
					<?php }?>
				</tr>
			<?php } ?>
		</table>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
</body>
</html>