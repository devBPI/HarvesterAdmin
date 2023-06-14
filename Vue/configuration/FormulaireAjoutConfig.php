<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<title>Ajout de Configuration</title>
</head>
<?php
	require '../Composant/ComboBox.php';
	include ('../Vue/common/Header.php');
?>
<body>
	<div class="content">
		<FORM action="" method="post">
			<div class="triple-column-container" style="height:100px">
				<div class="column">
					<a href="../Controlleur/Accueil.php" class="buttonlink">&laquo; Retour</a>
				</div>
				<div></div>
				<div>
					<input class="buttonlink" type="submit" name="insert" value="Ajouter la configuration" />
				</div>
			</div>
			<div class="cartouche-solo" style="width:100%;height:auto">
				<div class="row">
					<div class="col-25">
						<label for="codeconfig">Code de configuration (*)</label>
					</div>
					<div class="col-50">
						<input type="text" name="textCodeConfig" />
					</div>
				</div>
				<?php
				echo "Nom abrégé * : "?> <input type="text" name="textName" />
				<?php
				echo "Nom Public * : ";
				?>
				<input type="text" name="textNomPublic" />
				URL Publique * :
				<input type="text" name="textUrlPublique" />
				Connecteur * :
				<select id='list_grabber' name='list_grabber'><option value='0'>Aucun choisi</option>
					<?= ComboBox::makeComboBox($grabber); ?>
				</select>
				Nom Mapping * :
				<select id='list_mapping' name='list_mapping'><option value='0'>Aucun choisi</option>
					<?= ComboBox::makeComboBox($mapping); ?>
				</select>
				Nom Filtre :
				<select id='list_exclusion' name='list_exclusion'><option value='0'>Aucun choisi</option>
					<?= ComboBox::makeComboBox($filtre); ?>
				</select>
				Nom Traduction :
				<select id='list_translation' name='list_translation'><option value='0'>Aucun choisi</option>
					<?= ComboBox::makeComboBox($traduction); ?>
				</select>
				Url :<input type="text" name="textUrl" size="78" />
				Url set : <input type="text" name="textUrlSet" size="78" />
				<?php
				echo "Separateur CSV : ";
				?> <input type="text" name="textSeparateur" />
					<?php
					echo "Differentiel : ";
					?>
					<input type="radio" name="differential" value="false" checked> Non-Différentiel
					<input type="radio" name="differential" value="true" disabled="disabled"> Différentiel
				<?php
				echo "Nombre de tentatives : ";
				?> <input type="text" name="textAttempts" />
				<?php
				echo "Timeout : ";
				?> <input type="text" name="texTimeout" />
				<?php
				echo "Préfixe business ID * : ";
				?> <input type="text" name="textBusiness" /> 
					<?php
					echo "Subordonnée à :";
					include '../Vue/combobox/ComboBoxConfigs.php';
					?>
					Format Natif des données exposées : <input type="text" name="textFormatNatif" />
					Commentaire :
					<TEXTAREA style="box-shadow: 0px 0px 0px;" id="textNote" name="textNote" rows=10 cols=50></TEXTAREA>
				<?php
				include '../Vue/configuration/InsertConfiguration.php';
				?>
			</div>
		</FORM>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
</body>
</html>

