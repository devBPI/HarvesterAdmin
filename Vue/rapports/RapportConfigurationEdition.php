<html lang="fr">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/composants.css">
	<link rel="stylesheet" href="../css/accueilStyle.css">
	<link rel="stylesheet" href="../css/formStyle.css">
	<link rel="stylesheet" href="../css/reporting.css">
	<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css">
	<title>Paramétrage des rapports</title>
</head>

<body>
<?php
require_once("../Composant/ComboBox.php");
include_once("../Vue/common/Header.php");
require_once ("../Composant/RapportComposant.php");
require_once ("../Composant/RapportTreeComposant.php");
if (isset($type) && $type == "processus") {
	$page = "Processus";
	$what = "moisson";
}
else {
	$page = "Donnees";
	$what = "ressource";
}
?>
<div class="content">
	<div class="button_top_div">
		<?php if ($type == "processus") { ?>
			<a href="../../Controlleur/Rapports.php?id=processus" class="buttonlink" style="float:none; height:16px">« Retour aux rapports sur les processus</a>
		<?php } else { ?>
			<a href="../../Controlleur/Rapports.php?id=donnees" class="buttonlink" style="float:none">« Retour aux rapports sur les métadonnées</a>
		<?php } ?>
		<?php if (isset($_GET["id"]) && $_GET["id"] != "" && isset($configuration) && $configuration!=null) { ?>
			<form action="../Controlleur/Rapports.php?id=<?= $type ?>" method="post" style="margin-bottom:0"
				  onsubmit="return confirm('Souhaitez-vous supprimer cette configuration de rapport ? Cette action est irréversible.');">
				<input type="hidden" id="input_delete_id" name="report_id" value="<?= $_GET["id"] ?>">
				<input type="submit" class="buttonlink buttonlinkdanger" name="submit_type" value="Supprimer le rapport">
			</form>
		<?php } ?>
	</div>
	<?php if ((isset($configuration) && $configuration!=null) || !isset($_GET["id"])) { ?>
		<form method="post" id="formRapport">
			<!-- Section titre du rapport -->
			<div class="border_div param_content_div" style="padding:5px;">
				<table style="width: 100%">
					<tbody>
					<tr>
						<td style="width:200px">
							<label class="formLabel" for="input_name_rapport">Titre du rapport</label>
						</td>
						<td style="border-left:2px solid dimgrey;text-align: left;display: flex; flex-grow:1">
							<input type="text" id="input_name_rapport" name="name_rapport" placeholder="Titre identifiant le rapport"
								   title="Les caractères interdits sont . , ; ' &quot; \ /"
								   pattern="[^.,;'&quot;/\\]*" <?= isset($configuration)?"value='".$configuration["name"]."'":"" ?> required>
							<?php if (isset($_GET["id"]) && $_GET["id"] != "" && isset($configuration) && $configuration!=null) { ?>
								<a onclick="openForm()" class="buttonlink" style="width:190px;margin-left:5px">Dupliquer le rapport</a>
							<?php } if ($msg_error != null) { ?>
								<p class="avertissement_light">
									<?= $msg_error ?>
								</p>
							<?php } ?>

						</td>
					</tr>
					</tbody>
				</table>
			</div>

			<!-- Section critères du rapport -->
			<div class="border_div param_content_div">
				<fieldset class="param_fieldset">
					<legend>Critères</legend>
					<div id="criteres_rapport">
						<?php if ($configuration != null) {
							if ($type == "processus") {
								RapportTreeComposant::tree_display($configuration["criterias_tree"],0, ["data_type" => "PROCESS", "tree_type" => "report"]);
							} else {
								RapportTreeComposant::tree_display($configuration["criterias_tree"], 0, ["data_type" => "METADATA", "tree_type" => "report"]);
							}
						} else { ?>
							<div id="operation_000" class="div_operation racine">
								<div class="div_operation_ext">
									<select aria-label="Opérateur du groupe" name="operator_group_000" class="group_operator racine">
										<option value="OR">OR</option>
										<option value="AND">AND</option>
										<option value="EXCEPT">EXCEPT</option>
									</select>
									<input type="hidden" id="nb_children_operator_group_000" name="nb_children_operator_group_000" value="0">
								</div>
								<div class="div_operation_int">
									<div class="div_operation_dotted"></div>
									<div class="div_operation_int_int">
										<div id="div_operation_sub_int_000" class="prof_0"></div>
										<div id="div_add_group_critere_000">
											<a id="a_add_group_000" class="div_add_group" onclick="add_group(this.parentElement, 1)">+ Ajouter un groupe</a>
											<a id="a_add_critere_000" class="div_add_critere" onclick="add_critere_or_donnee(this.parentElement.parentElement, 'critere')">+ Ajouter un critère</a>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</fieldset>
			</div>

			<!-- Section données du rapport -->
			<div class="border_div param_content_div">
				<fieldset class="param_fieldset">
					<legend>Données affichées</legend>
					<div id="donnees_affichees">
						<button type="button" class="ajout but" title="Ajouter une donnée à afficher" onclick="add_critere_or_donnee(this.parentElement, 'donnee')">
							<img src="../../ressources/add.png" alt="Ajouter une donnée à afficher" style="width:30px;height:30px">
						</button>
						<?php if ($configuration != null) {
							if ($type == "processus") {
								echo insert_display_values($configuration["data_to_display"], $data_to_show_for_display, "processus");
							} else {
								echo insert_display_values($configuration["data_to_display"], $data_to_show_for_display, "donnees");
							}
						} ?>
					</div>
				</fieldset>
			</div>
			<div class="button_end_div">
				<button class="submit_disabled" id="input_save" type="submit" name="submit_value" value="save" disabled>Enregistrer la configuration</button>
			</div>
		</form>
	<?php } else { ?>
		<div class="avertissement">
			La configuration demandée n'a pas été trouvée. Veuillez vérifier l'URL.
		</div>
	<?php } ?>
</div>

<!-- élément reproductible pour l'ajout de groupes -->
<div id="operation_" class="div_operation" style="display:none">
	<div class="div_operation_ext">
		<select aria-label="Opérateur du groupe" name="operator_group_" class="group_operator">
			<option value="OR">OR</option>
			<option value="AND">AND</option>
			<option value="EXCEPT">EXCEPT</option>
		</select>
		<input type="hidden" id="nb_children_operator_group_" name="nb_children_operator_group_" value="0">
	</div>
	<div class="div_operation_int">
		<div class="div_operation_dotted"></div>
		<div class="div_operation_int_int">
			<div id="div_operation_sub_int_" class="prof_"></div>
			<div id="div_add_group_critere_">
				<a id="a_add_group_" class="div_add_group">+ Ajouter un groupe</a>
				<a id="a_add_critere_" class="div_add_critere" onclick="add_critere_or_donnee(this.parentElement.parentElement, 'critere')">+ Ajouter un critère</a>
				<button class="but delete" type="button" title="Supprimer un groupe et son contenu">
					<img alt="Supprimer un groupe" src="../ressources/cross.png" style="width:30px;height:30px">
				</button>
			</div>
		</div>
	</div>
</div>

<!-- élément reproductible pour l'ajout de criteres -->
<div class="critere_rapport" id="critere_rapport_" style="display: none">
	<input type="hidden" id="input_id_cond_" name="id_cond_" value="">
	<select aria-label="Critère" class="champ" id="cb_champ_cond_" name="champ_cond_" onchange="display_related_operator(this)">
		<option value="">Sélectionnez un champ</option>
		<optgroup label="Informations sur la <?= $what ?>">
			<?= ComboBox::makeComboBox($data_to_show["general_infos"]) ?>
		</optgroup>
		<optgroup label="Suivi de la <?= $what ?>">
			<?= ComboBox::makeComboBox($data_to_show["follow_up"]) ?>
			<?php if ($type == "processus") { ?>
				<!-- <option value="inserted_solr" disabled>Nombre d'insertions dans Solr'</option> -->
			<?php } else { ?>
				<!--<option value="type_share" disabled>Proportion de [type] sur l'ensemble des données collectées</option>-->
			<?php } ?>
		</optgroup>
		<optgroup label="Nombre de <?= $what ?>s">
			<?= ComboBox::makeComboBox($data_to_show["number_of_results_infos"]) ?>
		</optgroup>
	</select>
	<select aria-label="Opérateur du critère" class="operateur" id="cb_operateur_cond_" name="operateur_cond_">
		<?= ComboBox::makeComboBox($operators); ?>
	</select>
	<input aria-label="Valeur à comparer" type="text" class="valeur" id="input_valeur_cond_" name="valeur_cond_" placeholder="Valeur de comparaison" pattern="[0-9]*">
	<select aria-label="Valeur à comparer" class="champ" id="cb_valeur_cond_" name="valeur_cond_" style="display:none">
	</select>
	<button class="but delete" type="button" title="Supprimer un critère"
			onclick="delete_critere_or_donnee(this.parentElement, 'critere')">
		<img alt="Supprimer un critère" src="../ressources/cross.png" style="width:30px;height:30px">
	</button>
</div>

<!-- élément reproductible pour l'ajout de donnees -->
<div class="donnee_affichee" id="donnee_affichee_" style="display:none">
	<input type="hidden" id="input_id_champ_aff_" name="id_champ_aff_" value="">
	<select aria-label="Colonne du rapport" class="champ_donnee" id="cb_champ_aff_" name="display_champ_aff_" onchange="change_value_input(this)">
		<option value="">Sélectionnez un champ</option>
		<optgroup label="Informations sur la <?= $what ?>">
			<?= ComboBox::makeComboBox($data_to_show_for_display["general_infos"]) ?>
		</optgroup>
		<optgroup label="Suivi de la <?= $what ?>">
			<?= ComboBox::makeComboBox($data_to_show_for_display["follow_up"]) ?>
		</optgroup>
	</select>
	<input aria-label="Dénomination de la donnée" type="text" class="champ_donnee" id="input_name_champ_aff_" name="name_champ_aff_" pattern="[^.,;/\\]*" placeholder="Dénomination de la donnée" title="Les caractères interdits sont . , ; \ /">
	<div class="reporting_arrow_div" title="Glisser-déposer pour changer l'ordre des données (colonnes du rapport)">
		<img alt="Glisser-déposer" src="../ressources/move.png">
	</div>
	<button class="but delete" type="button" title="Supprimer une donnée à afficher" onclick="delete_critere_or_donnee(this.parentElement, 'donnee')">
		<img alt="Supprimer une donnée à afficher" src="../ressources/cross.png" style="width:30px;height:30px">
	</button>
</div>

<div id="page-mask"></div>
<div class="form-popup" id="validateForm">
	<div class="form-container" id="formProperty">
		<h3>Validation</h3>
		<div class="form-popup-corps">
			<p>Dupliquer la configuration de ce rapport ?</p>
			<p class="avertissement_light">Attention : si vous avez effectué des modifications et ne les avez pas sauvegardées, elles ne seront pas dupliquées et seront perdues.</p>
			<div class="row">
				<div class="col-50">
					<div style="width:99%;margin-right:1%">
						<form action="../../Controlleur/Rapports.php?id=<?= $type ?>" method="post">
							<input type="hidden" id="input_duplicate_id" name="report_id" value="<?= $_GET["id"] ?? "" ?>">
							<button type="submit" name="submit_type" class="buttonlink" value="duplicate">Confirmer</button>
						</form>
					</div>
				</div>
				<div class="col-50">
					<div style="width:99%;margin-left:1%">
						<button type="submit" class="buttonlink buttongrey" onclick="closeForm()">Annuler</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include "../Vue/common/Footer.php" ?>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="../js/toTop.js"></script>
<script type="text/javascript">
    // Script d'initialisation des compteurs et numéros d'identifiants
    // Ne peut se mettre dans rapports/reporting.js car utilise du php
    let nb_criteres = <?= isset($configuration["nb_criterias"])?$configuration["nb_criterias"]+$configuration["nb_groups"]+1:1 ?>; // Incrément pour l'identifiant / le nom des champs de critères de sélection
    let nb_donnees_affs = <?= isset($configuration)?count($configuration["data_to_display"])+1:1 ?>; // Incrément pour l'identifiant / le nom des champs de données à afficher
    let nb_groupes = <?= isset($configuration["nb_groups"])?$configuration["nb_groups"]+$configuration["nb_criterias"]+1:1 ?>; // Incrément pour l'identifiant / le nom des groupes
    let cpt_criteres = <?= isset($configuration)?$configuration["nb_criterias"]:0 ?>; // Compteur de critères du rapport
    let cpt_donnees_affs = <?= isset($configuration)?count($configuration["data_to_display"]):0 ?>; // Compteur de données à afficher
    let cpt_groupes = <?= isset($configuration)?$configuration["nb_groups"]:0 ?>; // Compteur de groupes

    $(document).ready(function() {
        disable_input();
        $(".champ option[value='harvest_number_of_inserted_in_notices']").attr("disabled", true); // Pour dev, a enlever a un certain moment
        $(".champ option[value='harvest_number_of_inserted_in_external_link']").attr("disabled", true); // Pour dev, a enlever a un certain moment
        $(".champ option[value='notice_date_publishing_count']").attr("disabled", true); // Pour dev, a enlever a un certain moment
        $(".champ option[value='notice_number_of_rows']").attr("disabled", true); // Pour dev, a enlever a un certain moment
    });
</script>
<script src="/js/rapports/reporting.js"></script>
<script src="/js/pop_up.js"></script>

</html>