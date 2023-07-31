<html lang="fr">
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
	include("../Composant/ErrorReportingConfig.php");
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
						<input type="text" id="codeconfig" name="textCodeConfig" required>
					</div>
				</div>
				<label for="nomabrege">Nom abrégé * :</label><input type="text" id="nomabrege" name="textName" required>
				<label for="nompublic">Nom Public * :</label><input type="text" id="nompublic" name="textNomPublic" required>
				<label for="urlpublique">URL Publique * :</label><input type="text" id="urlpublique" name="textUrlPublique" required>
				<label for="connecteur">Connecteur * :</label>
				<select id="list_grabber" id="connecteur" name="list_grabber" required>
					<option value="">Sélectionnez un connecteur</option>
					<?= ComboBox::makeComboBox($grabber); ?>
				</select>
				<label for="nommapping">Nom Mapping * :</label>
				<select id="list_mapping" id="nommapping" name="list_mapping" required>
					<option value="">Sélectionnez un mapping</option>
					<?= ComboBox::makeComboBox($mapping); ?>
				</select>
				<label for="nomfiltre">Nom Filtre :</label>
				<select id="list_exclusion" id="nomfiltre" name="list_exclusion">
					<option value="">Sélectionnez un filtre</option>
					<?= ComboBox::makeComboBox($filtre); ?>
				</select>
				<label for="nomtrad">Nom Traduction :</label>
				<select id="list_translation" id="nomtrad" name="list_translation">
					<option value="">Sélectionnez une traduction</option>
					<?= ComboBox::makeComboBox($traduction); ?>
				</select>
				<label for="urltext">Url : </label><input type="text" id="urltext" name="textUrl" size="78" />
				<label for="urlset">Url set : </label><input type="text" id="urlset" name="textUrlSet" size="78" />
				<label for="separateur">Separateur CSV : </label><input type="text" id="separateur" name="textSeparateur" />
				<label> Differentiel : </label>
				<input type="radio" aria-label="Non-différentiel" name="differential" value="false" checked> Non-Différentiel
				<input type="radio" aria-label="Différentiel" name="differential" value="true" disabled="disabled"> Différentiel
				<label for="nbtempt">Nombre de tentatives : </label><input type="text" id="nbtempt" name="textAttempts" />
				<label for="timeout">Timeout : </label><input type="text" id="timeout" name="texTimeout" />
				<label for="prefixe">Préfixe business ID * :</label><input type="text" id="prefixe" name="textBusiness" required/>
				<label for="subordonnee">Subordonnée à : </label>
					<?php //include '../Vue/combobox/ComboBoxConfigs.php';?>
				<select id="subordonnee">
					<option value="-1">Sélectionnez une subordonnée</option>
				</select>
				<label for="formatnatif">Format Natif des données exposées : </label><input type="text" id="formatnatif" name="textFormatNatif" />
				<label for="textNote">Commentaire : </label><textarea style="box-shadow: 0 0 0;" id="textNote" name="textNote" rows=10 cols=50></textarea>
				<?php include '../Vue/configuration/InsertConfiguration.php'; ?>
			</div>
		</FORM>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
</body>
</html>

