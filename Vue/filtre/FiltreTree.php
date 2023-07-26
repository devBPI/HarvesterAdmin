<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html lang="fr" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/composants.css">
	<link rel="stylesheet" href="../../css/filtreStyle.css">
	<link rel="stylesheet" href="../../css/accueilStyle.css">
	<link rel="stylesheet" href="../../css/formStyle.css">
	<link rel="stylesheet" href="../../css/tree.css">
	<title><?= $name ?></title>
</head>

<?php
require '../Vue/configuration/TabConfigsAssociees.php';
require '../Composant/ComboBox.php';
include('../Vue/common/Header.php');
?>

<body>
<div class="content">
	<form action="FiltreTree.php?id=<?= $id ?>" method="post">
		<div class="triple-column-container">
			<div class="column">
				<a href="../../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour aux filtres</a>
			</div>
			<div class="column">
				<div class="config_name_and_sub_title">
					<h3 class="config_name"><?= $name ?></h3>
					<p class="sub_title">Portant sur l'entité <?= $entity ?> </p>
				</div>
			</div>
			<div class="column" style="text-align:right">
				<input type="submit" name="form_submit" value="Enregistrer la règle" />
			</div>
		</div>
		<div class="border_div param_content_div">
			<div id="racine_-01" style="width: 100%; min-width: 875px;">
				<div class="div_operation_int" style="height: auto">
					<div class="div_operation_int_int" style="border: none; background-color: unset">
<?php if(isset($data)) { ?>
					<div id="div_operation_sub_int_-01" class="prof_-01">
<?php include("../Composant/FiltreTreeComposant.php");
	FiltreTreeComposant::tree_display($data, 0, ["tree_type" => "filter"]); ?>
					</div>
						<div id="div_add_group_critere_-01">
							<a tabindex="0" id="a_add_group_-01" class="div_add_group a_disabled">+ Ajouter un groupe</a>
							<a tabindex="0" id="a_add_critere_-01" class="div_add_critere a_disabled">+ Ajouter un prédicat</a>
						</div>
<?php } else { ?>
						<div id="div_operation_sub_int_-01" class="prof_-01"></div>
						<div id="div_add_group_critere_-01">
							<a tabindex="0" id="a_add_group_-01" class="div_add_group" onclick="add_group(this.parentElement, -1);">+ Ajouter un groupe</a>
							<a tabindex="0" id="a_add_critere_-01" class="div_add_critere" onclick="add_critere_or_donnee(this.parentElement.parentElement, 'critere')">+ Ajouter un prédicat</a>
						</div>
<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</form>

<?= TabConfigsAssociees::makeTab($configurations) ?>

</div>


<!-- élément reproductible pour l'ajout de groupes -->
<div id="operation_" class="div_operation" style="display:none">
	<div class="div_operation_ext">
		<select aria-label="Opérateur du groupe" name="operator_group_" class="group_operator">
			<option value="OR">OR</option>
			<option value="AND">AND</option>
		</select>
		<input type="hidden" id="nb_children_operator_group_" name="nb_children_operator_group_" value="0">
		<input type="hidden" id="nb_children_operator_criteria_" name="nb_children_operator_criteria_" value="0">
		<input type="hidden" id="nb_children_operator_" name="nb_children_operator_" value="0"  pattern="^[1-9][0-9]*">
	</div>
	<div class="div_operation_int">
		<div class="div_operation_dotted"></div>
		<div class="div_operation_int_int">
			<div id="div_operation_sub_int_" class="prof_"></div>
			<div id="div_add_group_critere_">
				<a tabindex="0" id="a_add_group_" class="div_add_group">+ Ajouter un groupe</a>
				<a tabindex="0" id="a_add_critere_" class="div_add_critere" onclick="add_critere_or_donnee(this.parentElement.parentElement, 'critere')">+ Ajouter un prédicat</a>
				<button class="but delete" type="button" title="Supprimer un groupe et son contenu">
					<img alt="Supprimer un groupe" src="../ressources/cross.png" style="width:30px;height:30px">
				</button>
			</div>
		</div>
	</div>
</div>

<!-- élément reproductible pour l'ajout de prédicats -->
<div id="critere_rapport_" style="display: none" class="div_predicate">
	<table class="table-config">
		<tr>
			<th style="width:40%">Prédicat</th>
			<th>Entité</th>
			<th>Champ</th>
			<th>Fonction</th>
			<th>Valeur</th>
		</tr>
		<tr class="entity" id="new_">
			<td>
				<select aria-label="Prédicat" name="entity_" onchange='update_predicat(this, <?= json_encode($predicats) ?>)' required>
					<option value="">Choississez un prédicat</option>
<?= ComboBox::makeComboBox($predicats_formates) ?>
				</select>
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
	</table>
	<button class="but delete" type="button" title="Supprimer un prédicat" onclick="delete_critere_or_donnee(this.parentElement, 'critere')">
		<img alt="Supprimer un prédicat" src="../ressources/cross.png" style="width:30px;height:30px">
	</button>
</div>

<?php if(isset($success) && $success == true) { ?>
<div id="page-mask" style="display:block"></div>
<div class="form-popup" id="validateForm" style="display:block">
	<div class="form-container" id="formProperty">
		<form action="../../Controlleur/Filtre.php" class="form-container" id="formProperty">
			<h3>Modification</h3>
			<div class="form-popup-corps">
				<p>Les modifications ont bien été enregistrées.</p>
				<button type="submit" class="buttonlink">OK</button>
			</div>
		</form>
	</div>
</div>

<?php } ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../../js/toTop.js"></script>
<script src="../../js/filtres_traductions/predicate.js"></script>
<script src="../../js/filtres_traductions/filter_rule.js"></script>
<script type="text/javascript">
    let nb_criteres = <?= isset($nb_criterias)&&isset($nb_groups)?$nb_criterias+$nb_groups+1:1 ?>; // Incrément pour l'identifiant / le nom des champs de critères de sélection
    let nb_groupes = <?= isset($nb_criterias)&&isset($nb_groups)?$nb_criterias+$nb_groups+1:1 ?>; // Incrément pour l'identifiant / le nom des groupes
    let cpt_criteres = <?= $nb_criterias ?? 0 ?>; // Compteur de critères du rapport
    let cpt_groupes = <?= $nb_groups ?? 0 ?>; // Compteur de groupes
	let page_type = "filter";
</script>
<script src="/js/tree_reporting_filter.js"></script>
</body>
</html>