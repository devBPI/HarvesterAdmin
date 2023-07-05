<html lang="fr">

<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<link rel="stylesheet" href="../css/accueilStyle.css" />
<title>Modification de Configuration</title>
</head>

<?php require '../Composant/ComboBox.php'; ?>

<!-- Body (Div contenant tout (ou presque)) -->

<body>
	<?php
	include ('../Vue/common/Header.php');
	?>
	<div class="content">
		<form class="form-modif-config" action="#" method="post" onsubmit="return confirm('Voulez vous vraiment modifier cette configuration ?');">
			<table class="tab_fiche_individuelle">
				<thead>
					<tr>
						<th class="left_column">Code</label></th>
						<th class="right_column"><?= $dataConf["code"] ?>
							<a class="buttonlink" style="float:right;margin-top:-8px;font-weight:normal" href="../Vue/FicheIndividuelle.php?param=<?php echo $_GET['param'] ?>">
								&laquo; Fiche configuration
							</a>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="left_column"><label for="form_nom_abrege">Nom abrégé * :</label></td>
						<td class="right_column"><input type="text" id="form_nom_abrege" name="form_nom_abrege" value="<?= $dataConf['name'] ?>" required /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_nom_public">Base de recherche * :</label></td>
						<td class="right_column"><input type="text" id="form_nom_public" name="form_nom_public" value="<?= $dataConf['public_name'] ?>" required /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_url_publique">URL Publique * :</label></td>
						<td class="right_column"><input type="text" id="form_url_publique" name="form_url_publique" value="<?= $dataConf['public_url'] ?>" required /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_list_grabber">Connecteur * :</label></td>
						<td class="right_column"><select id="form_list_grabber" name="form_list_grabber" required>
						<option value="">Sélectionnez un grabber</option>
								<?= ComboBox::makeComboBox($grabber, $dataConf['grabber_id']) ?>
							</select></td>
					</tr>
					<tr><td class="tab_fiche_individuelle_separation" colspan="2"></td></tr>
					<tr>
						<td class="left_column"><label for="form_list_mapping" >Nom Mapping * :</label></td>
						<td class="right_column"><select id="form_list_mapping" name="form_list_mapping" required><
							<option value="">Sélectionnez un mapping</option>
								<?= ComboBox::makeComboBox($mapping, $dataConf['mapping_id']) ?>
							</select></td>
					</tr>
					<tr><td class="tab_fiche_individuelle_separation" colspan="2"></td></tr>
					<tr>
						<td class="left_column"><label for="form_nom_filtre">Nom Filtre :</label></td>
						<td class="right_column"><div class="sizeable_table">
							<div class="hidden_field">
							</div>
							<div>
								<select id="form_nom_filtre" name="form_nom_filtre">
									<option value="">Sélectionnez un filtre</option>
									<?= ComboBox::makeComboBox($exclusion, $dataConf['filter_id'] ?? null) ?>
								</select>
								<a href="javascript:void(0);" class="filtre_add" title="Ajouter une exclusion"><img src="../ressources/add.png"/></a>
							</div>
						</div></td>
					</tr>
					<tr><td class="tab_fiche_individuelle_separation" colspan="2"></td></tr>
					<tr>
						<td class="left_column"><label for="form_URL">URL</label></td>
						<td class="right_column"><input type="text" id="form_URL" name="form_URL" value="<?= $dataConf['url'] ?>" size="78" /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_URL_set">URL Set</label></td>
						<td class="right_column"><input type="text" id="form_URL_set" name="form_URL_set" value="<?= $dataConf['url_set'] ?>" size="78" /></td>
					</tr>
					<tr><td class="tab_fiche_individuelle_separation" colspan="2"></td></tr>
					<tr>
						<td class="left_column"><label for="form_separateur">Séparateur CSV</label></td>
						<td class="right_column"><input type="text" id="form_separateur" name="form_separateur" value="<?= $dataConf['csv_separator'] ?>"/></td>
					</tr>
					<tr>
					<td colspan="2">
					<fieldset>
						<legend>Différentiel</legend>
						<span>
							<?php
								if (isset($value) && $value['differential'] == 'f') {
							?>
							<input type="radio" id="form_differentiel_false" name="form_differentiel" value="false" checked />
							<label for="form_differentiel_false">Non-Différentiel</label>
							<input type="radio" id="form_differentiel_false" name="form_differentiel" value="true" disabled />
							<label for="form_differentiel_true">Différentiel</label>
							<?php
								} else {
							?>
							<input type="radio" id="form_differentiel_false" name="form_differentiel" value="false" disabled />
							<label for="form_differentiel_false">Non-Différentiel</label>
							<input type="radio" id="form_differentiel_true" name="form_differentiel" value="true" checked />
							<label for="form_differentiel_true">Différentiel</label>
							<?php
								}
							?>
						</span>
					</fieldset>
					</td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_max_attempts">Nombre maximum de tentatives : </label></td>
						<td class="right_column"><input type="text" id="form_max_attempts" name="form_max_attempts" pattern="^\d+$" value="<?= $dataConf['max_attempts_number'] ?>" /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_timeout">Timeout : </label></td>
						<td class="right_column"><input type="text" id="form_timeout" name="form_timeout" value="<?= $dataConf['timeout_sec'] ?>" /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_business_id">Préfixe business ID * : </label></td>
						<td class="right_column"><input type="text" id="form_business_id" name="form_business_id" value="<?php echo $dataConf['business_base_prefix']; ?>" required /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_additional_configuration_of">Subordonnée à la configuration : </label></td>
						<td class="right_column"><input type="text" id="form_additional_configuration_of" name="form_additional_configuration_of" value="<?= $dataConf['additional_configuration_of'] ?>" readonly /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_parcours"> Parcours : </label></td>
						<td class="right_column">
						<div class='sizeable_table'>
							<div class='hidden_field'>
								<textarea id="form_parcours" name="form_parcours"></textarea>
								<button class="but" type="button" title="Supprimer un parcours" onclick="delete_field(this.parentElement)"><img src="../ressources/cross.png" width="30px" height="30px"></button>
							</div>
						<div>
						<?php if(!empty($dataConf['parcours'])){ ?>
							<div>
								<textarea id="parcours" name="parcours0"><?= $dataConf["parcours"][0]["parcours"] ?></textarea>
								<button class="but" type="button" title="Supprimer un parcours" onclick="delete_field(this.parentElement)"><img src="../ressources/cross.png" width="30px" height="30px"></button>
							</div>
							<?php for($i=1;$i<count($dataConf['parcours']);$i++) { ?>
							<div>
								<textarea id="parcours" name="parcours<?= $i ?>"><?= $dataConf['parcours'][$i]['parcours'] ?></textarea>
								<button class="but" type="button" title="Supprimer un parcours" onclick="delete_field(this.parentElement)"><img src="../ressources/cross.png" width="30px" height="30px"></button>
							</div>
							<?php }
						}
						else { ?>
							<div>
								<textarea id="parcours" name="parcours0"></textarea>
								<button class="but" type="button" title="Supprimer un parcours" onclick="delete_field(this.parentElement)"><img src="../ressources/cross.png" width="30px" height="30px"></button>
							</div>
						</div>
						<button class="ajout but" type="button" title="Ajouter un parcours" onclick="add_new_field(this.parentElement)"><img src="../ressources/add.png" width="30px" height="30px"/></button>
						<?php } ?>
					</div></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_format_natif">Format natif des données exposées :</label></td>
						<td class="right_column"><input type="text" id="form_format_natif" name="form_format_natif" /></td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_acces">Accès :</label></td>
						<td class="right_column">
							<input type="checkbox" name="INTERNAL" value="INTERNAL" <?php foreach ($dataConf['profile'] as $access) { if ($access == "INTERNAL") echo "checked"; } ?>>Profil Interne (hors WIFI-Bpi)</input>
							<input type="checkbox" name="WIFI-BPI" value="WIFI-BPI" <?php foreach ($dataConf['profile'] as $access) { if ($access == "WIFI-BPI") echo "checked"; } ?>>Wifi de la BPI</input>
							<input type="checkbox" name="EXTERNAL" value="EXTERNAL" <?php foreach ($dataConf['profile'] as $access) { if ($access == "EXTERNAL") echo "checked"; } ?>>Profil Externe (hors WIFI-Bpi)</input>
						</td>
					</tr>
					<tr>
						<td class="left_column"><label for="form_commentaires">Commentaires :</label></td>
						<td class="right_column">
							<textarea style="box-shadow: 0px 0px 0px;" id="form_commentaires" name="form_commentaires" rows=10 cols=50>
							<?= (! empty($dataConf['note'])) ? $dataConf['note'] : "" ?>
							</textarea>
						</td>
					</tr>
					<tr><td class="tab_fiche_individuelle_separation" colspan="2"></td></tr>
				<tr>
					<td colspan="2">
						<input class="button primairy-color round" type="submit" name="update" value="Valider les modifications"/>
					</td>
				</tr><?php include '../Controlleur/UpdateConfiguration.php'; ?>
				</tbody>
			</table>
		</form>
	</div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
</body>
<!-- Fin du body -->

</html>


