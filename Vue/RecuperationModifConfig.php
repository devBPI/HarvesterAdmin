<html>

<head>
<!-- Ajout du ou des fichiers javaScript-->
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
	<!-- ajout du ou des fichiers CSS-->
<title>Modification de Configuration</title>
</head>



<!-- Body (Div contenant tout (ou presque)) -->

<body>
	<?php
	include ('Header.php');
	?>
	<div class="content" style="text-align:center">
		<FORM action="" method="post" onsubmit="return confirm('Voulez vous vraiment modifier cette configuration ?');">
			<p style="border-bottom: 1px solid;">
			

    
			<?php
			echo "Code de la configuration : ".$dataConf['code'];
			?>
			
				<br>
			<?php
			echo "Nom abrégé * : "?>  
				<input type="text" name="textName"
					value="<?php echo $dataConf['name']; ?>" /> <br>
				<?php
			echo "Nom Public * : ";
			?>
				<input type="text" name="textNomPublic"
					value="<?php echo $dataConf['public_name'] ?>" /> <br>
				<?php
			echo "URL Publique * : ";
			?>
				<input type="text" name="textUrlPublique"
					value="<?php echo $dataConf['public_url'] ?>" /> <br>
				<?php
			$id_param = $dataConf['grabber_id'];
			echo "Connecteur* : ";
			$data=$grabber;
			?>
			<select id="list_grabber" name="list_grabber">
				<option value="0">Aucun choisi</option>
				<?php
				include '../Vue/combobox/ComboBox.php';
				?>
			</select>
				<br>
				<br>
			</p>
			<p style="border-bottom: 1px solid;">
				<?php
			$id_param = $dataConf['mapping_id'];
			echo "Nom Mapping * : ";
			$data=$mapping;
			?>
			<select id="list_mapping" name="list_mapping">
				<option value="0">Aucun choisi</option>
				<?php
				include '../Vue/combobox/ComboBox.php';
				?>
			</select>
				<br>
				<br>
			</p>
			<?php
				$id_param = $dataConf['filter_id'];
				echo "Nom Filtre : ";
				$data=$exclusion;
			?>
			<div class="sizeable_table">
				<div class="hidden_field">
				
				</div>
				<div>
					<select name='select'>
						<option value="0">Aucun choisi</option>
						<?php
						include '../Vue/combobox/ComboBox.php';
						?>
					</select>
					<a href="javascript:void(0);" class="filtre_add" title="Ajouter une exclusion"><img src="../ressources/add.png"/></a>
				</div>
			</div>
				<br>
				<br>
			<p style="border-bottom: 1px solid;">
				<?php
			echo "Url : ";
			?><input type="text" name="textUrl"
					value="<?php echo $dataConf['url']; ?>" size="78" /> <br>
				<?php
			echo "Url set : ";
			?> <input type="text" name="textUrlSet"
					value="<?php echo $dataConf['url_set']; ?>" size="78" /> <br>
				<br>
			</p>
			<p>
				<?php
			echo "Separateur CSV : ";
			?> <input type="text" name="textSeparateur"
					value="<?php echo $dataConf['csv_separator']; ?>" /> <br>
					<?php
			echo "Differentiel : ";

			if ($value['differential'] == 'f') {
				?>
						<input type="radio" name="differential" value="false"
					checked> Non-Différentiel <input type="radio" name="differential"
					value="true" disabled="disabled"> Différentiel
					<?php
			} else {
				?>
						<input type="radio" name="differential" value="false"
					checked> Non-Différentiel <input type="radio" name="differential"
					value="true" disabled="disabled"> Différentiel
					<?php
			}
			?>

				<br>
					<?php
			echo "Nombre de tentatives : ";
			?><input type="text" name="textAttempts"
					value="<?php echo $dataConf['max_attempts_number']; ?>" /> <br>
					<?php
			echo "Timeout : ";
			?><input type="text" name="texTimeout"
					value="<?php echo $dataConf['timeout_sec']; ?>" /> <br>
					<?php
			echo "Préfixe business ID * : ";
			?><input type="text" name="textBusiness"
					value="<?php echo $dataConf['business_base_prefix']; ?>" /> <br>
					<?php
			echo "Subordonnée à la configuration :";
			?><input type="text" name="textAdditionalConfigOf"
					value="<?php echo $dataConf['additional_configuration_of']; ?>" /> <br>
					<?php
			echo "Parcours : <div class='sizeable_table'>";
				echo "<div class='hidden_field'>
					<TEXTAREA name='parcour'></TEXTAREA>
					<button class='but' type='button' title='Supprimer un parcours' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button>
				</div>";
				echo "<div>";
					if(!empty($dataConf['parcours']))
					{
						echo "<div>
							<TEXTAREA id='parcour' name='parcour0'>".$dataConf['parcours'][0]['parcours']."</TEXTAREA>
							<button class='but' type='button' title='Supprimer un parcour' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button>
						</div>";
						for($i=1;$i<count($dataConf['parcours']);$i++)
						{
							echo "<div>
								<TEXTAREA id='parcour' name='parcour".$i."'>".$dataConf['parcours'][$i]['parcours']."</TEXTAREA>
								<button class='but' type='button' title='Supprimer un parcour' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png'/ width='30px' height='30px'></button>	
							</div>";
						}
					}
					else
					{
						echo "<div>
								<TEXTAREA id='parcour' name='parcour0'></TEXTAREA>
								<button class='but' type='button' title='Supprimer un parcour' onclick='delete_field(this.parentElement)'><img src='../ressources/cross2.png'/ width='30px' height='30px'></button>
							</div>";
					}
				echo "</div>";
				echo '<button class="ajout but" type="button" title="Ajouter un parcour" onclick="add_new_field(this.parentElement)"><img src="../ressources/add.png" width="30px" height="30px"/></button>';
			echo "</div>";
			?>
			<br>
			<?php
			echo "Format Natif des données exposées : ";
			?> <input type="text" name="textFormatNatif" /> <br>
			Accès :
			<input type="checkbox" name="INTERNAL" value="INTERNAL" <?php echo (array_key_exists("INTERNAL",$dataConf['profile']))?"checked":"";?>>Profil Interne (hors WIFI-Bpi)</input>
			<input type="checkbox" name="WIFI-BPI" value="WIFI-BPI" <?php echo (array_key_exists("WIFI-BPI",$dataConf['profile']))?"checked":"";?>>Wifi de la BPI</input>
			<input type="checkbox" name="EXTERNAL" value="EXTERNAL" <?php echo (array_key_exists("EXTERNAL",$dataConf['profile']))?"checked":"";?>>Profil Externe (hors WIFI-Bpi)</input>
			<br>
			<?php					
			echo "Commentaire : ";
			?>
					<br>
				<TEXTAREA style="box-shadow: 0px 0px 0px;" id="textNote"
					name="textNote" rows=10 cols=50>
		<?php
			echo (! empty($dataConf['note'])) ? $dataConf['note'] : "";
			?>
		</TEXTAREA>
				<br>
				<br>
			</p>
			<br>
			<input class="button primairy-color round" type="submit" name="update" value="Valider les modifications"/>
			<?php
				include '../Controlleur/UpdateConfiguration.php';
			?>
		</FORM>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
</body>
<!-- Fin du body -->

</html>


