<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />
	<link rel="stylesheet" href="../css/reporting.css" />
	<link rel="stylesheet" href="../css/environments/<?= strtolower($ini['version']) ?>-style.css" />
	<title>Paramétrage des rapports</title>

</head>

<body>
<?php
require "../Composant/ComboBox.php";
include('../Vue/common/Header.php');
if ($type == "processus") $page = "Processus";
else $page = "Donnees";
?>
<div class="content">
	<div style="display:flex;justify-content: space-between;">
	<?php if ($type == "processus") { ?>
		<a href="../../Controlleur/Rapports.php?id=processus" class="buttonlink" style="float:none; height:16px">« Retour aux rapports sur les processus</a>
	<?php } else { ?>
		<a href="../../Controlleur/Rapports.php?id=donnees" class="buttonlink" style="float:none">« Retour aux rapports sur les métadonnées</a>
	<?php } ?>
	<?php if (isset($_GET["id"]) && $_GET["id"] != "") { ?>
		<form action="../Controlleur/Rapports.php?id=<?= $type ?>" method="post" style="margin-bottom:0"
			  onsubmit="return confirm('Souhaitez-vous supprimer cette configuration de rapport ? Cette action est irréversible.');">
			<input type="hidden" id="input_delete_id" name="report_id" value="<?= $_GET["id"] ?>">
			<input type="submit" class="buttonlink buttonlinkdanger" name="submit_type" value="Supprimer le rapport"/>
		</form>
	<?php } ?>
	</div>
	<form method="post" id="formRapport">
	<!-- Section titre du rapport -->
	<div class="border_div param_content_div" style="padding:5px;">
		<table style="width: 100%">
			<tbody>
				<tr>
					<td width="200px">
						<label class="formLabel formRapportLeft" for="input_name_rapport">Titre du rapport</label>
					</td>
					<td style="border-left:2px solid dimgrey">
						<input type="text" id="input_name_rapport" name="name_rapport"
							   placeholder="Titre identifiant le rapport"
							<?= isset($configuration)?"value='".$configuration["name"]."'":"" ?>
							   required/>
						<?php if ($msg_error != null) { ?>
						<p class="avertissement_light" style="text-align: left">
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
				<button type="button" class="ajout but" title="Ajouter un critère" style="cursor:pointer"
						onclick="add_critere_or_donnee(this.parentElement, 'critere')">
					<img src="../../ressources/add.png" width="30px" height="30px"/>
				</button>
				<!-- élément reproductible pour l'ajout des critères -->
				<div class="critere_rapport" id="critere_rapport_" style="display:none">
					<input type="hidden" id="input_id_cond_" name="id_cond_" value="" />
					<?php if(isset($type) && $type == "processus") { ?>
					<select class="champ formRapportLeft" id="cb_champ_cond_" name="champ_cond_" onchange="display_related_operator(this)">
						<option value="">Sélectionnez un champ</option>
						<optgroup label="Informations sur la moisson">
						<?= ComboBox::makeComboBox($data_to_show["general_infos"]) ?>
						</optgroup>
						<optgroup label="Suivi de la moisson">
							<?= ComboBox::makeComboBox($data_to_show["follow_up"]) ?>
							<option value="inserted_external_link" disabled>Nombre d'insertions dans external_link</option>
							<option value="inserted_solr" disabled>Nombre d'insertions dans Solr'</option>
						</optgroup>
						<optgroup label="Nombre de moissons">
							<?= ComboBox::makeComboBox($data_to_show["number_of_results_infos"]) ?>
						</optgroup>
					</select>
					<select class="operateur" id="cb_operateur_cond_" name="operateur_cond_">
						<?= ComboBox::makeComboBox($operators); ?>
					</select>
					<input type="text" class="valeur" id="input_valeur_cond_" name="valeur_cond_" placeholder="Valeur de comparaison" pattern="[0-9]*"/>
					<select class="champ" id="cb_valeur_cond_" name="valeur_cond_" style="display:none">
					</select>
					<button class="but delete" type="button" title="Supprimer un critère" style="cursor:pointer;"
							onclick="delete_critere_or_donnee(this.parentElement, 'critere')">
						<img alt="Supprimer un critère" src="../ressources/cross.png" width="30px" height="30px">
					</button>
					<?php } else { ?>
					<select class="champ formRapportLeft" id="cb_champ_cond_" name="champ_cond_" onchange="display_related_operator(this)">
						<optgroup label="Informations sur la notice">
							<option value="creation_date">Date de collecte de la notice</option>
						</optgroup>
						<optgroup label="Informations sur la ressource">
							<option value="date_publishing">Date de publication</option>
							<option value="type_data">Type de ressource</option>
						</optgroup>
						<optgroup label="Suivi de la ressource">
							<option value="type_share">Proportion de [type] sur l'ensemble des données collectées</option>
						</optgroup>
					</select>
					<select class="operateur" id="cb_operateur_cond_" name="operateur_cond_">
						<?= ComboBox::makeComboBox($operators); ?>
					</select>
					<input type="text" class="valeur" id="input_valeur_cond_" name="valeur_cond_" placeholder="Valeur de comparaison"/>
					<?php } ?>
				</div>
				<?php if ($configuration != null) {
					if ($type == "processus") {
						require_once "../Composant/HarvestTaskRapport.php";
						echo insert_criterias($configuration["criterias"], $data_to_show, $operators, $operators_short, "processus");
					}
				} ?>
			</div>
		</fieldset>
	</div>

	<!-- Section données du rapport -->
	<div class="border_div param_content_div">
		<fieldset class="param_fieldset">
			<legend>Données affichées</legend>
			<div id="donnees_affichees">
				<button type="button" class="ajout but" title="Ajouter une donnée à afficher" style="cursor:pointer" onclick="add_critere_or_donnee(this.parentElement, 'donnee')">
					<img src="../../ressources/add.png" width="30px" height="30px"/>
				</button>
				<!-- élément reproductible pour l'ajout de donnees -->
				<div class="donnee_affichee" id="donnee_affichee_" style="display:none">
					<input type="hidden" id="input_id_champ_aff_" name="id_champ_aff_" value="" />
					<?php if(isset($type) && $type == "processus") { ?>
					<select class="champ_donnee" id="cb_champ_aff_" name="display_champ_aff_" onchange="change_value_input(this)">
						<option value="">Sélectionnez un champ</option>
						<?= ComboBox::makeComboBox($data_to_show_for_display); ?>
					</select>
					<input type="text" class="champ_donnee" id="input_name_champ_aff_" name="name_champ_aff_" placeholder="Dénomination de la donnée"/>
					<button class="but delete" type="button" title="Supprimer une donnée à afficher" onclick="delete_critere_or_donnee(this.parentElement, 'donnee')">
						<img alt="Supprimer un critère" src="../ressources/cross.png" width="30px" height="30px">
					</button>
					<?php } ?>
				</div>
				<?php if ($configuration != null) {
					if ($type == "processus") {
						require_once "../Composant/HarvestTaskRapport.php";
						echo insert_display_values($configuration["data_to_display"], $data_to_show_for_display, "processus");
					}
				} ?>
			</div>
		</fieldset>
	</div>
		<button class="submit_disabled" id="input_save" type="submit" name="submit_value" value="save" disabled>Enregistrer la configuration</button>
	</form>
</div>

<?php include "../Vue/common/Footer.php" ?>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
    // Script d'initialisation des compteurs et numéros d'identifiants
    // Ne peut se mettre dans reporting.js car utilise du php
    let nb_criteres = <?= isset($configuration)?count($configuration["criterias"])+1:1 ?>; // Incrément pour l'identifiant / le nom des champs de critères de sélection
    let nb_donnees_affs = <?= isset($configuration)?count($configuration["data_to_display"])+1:1 ?>; // Incrément pour l'identifiant / le nom des champs de données à afficher
    let cpt_criteres = <?= isset($configuration)?count($configuration["criterias"]):0 ?>; // Compteur de critères du rapport
    let cpt_donnees_affs = <?= isset($configuration)?count($configuration["data_to_display"]):0 ?>; // Compteur de données à afficher

    $(document).ready(function() {
        disable_input();
        // Script permettant la redirection quand $_POST envoyé, seulement s'il n'y pas d'erreur
	<?php if(!empty($_POST) && $msg_error==null) { ?>
		window.location='../Controlleur/Rapports<?= $page ?>Edition.php?id=<?= ($_GET["id"]!=""?$_GET["id"]:$new_id) ?>&viewonly';
    <?php } ?>
    })
</script>
<script src="/js/reporting.js"></script>
</html>