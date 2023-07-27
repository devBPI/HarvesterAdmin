<?php

include_once("../Composant/predicat/PredicatClass.php");
include_once("../Composant/RapportComposant.php");

/*
	Arbres pour les pages FiltreTree.php et RapportsEdition.php
	(vues : filtre/FiltreTree.php, rapports/RapportConfigurationAffichage.php et rapports/RapportConfigurationEdition.php)
*/

class TreeComposant
{
	private static $nb = 0;

	private static function incrementNb()
	{
		self::$nb++;
	}

	private static function getNb(): int
	{
		return self::$nb;
	}

	public static function tree_display($donnee, $profondeur, $parameters_array)
	{
		/* CODE COMMUN POUR REPORTING, REPORTING VIEWONLY ET FILTER TREE */
		if (!is_array($donnee)) {
			return;
		}
		if ($parameters_array["tree_type"] == "report") {
			$parameters_array["op"] = $donnee["operator"] ?? null;
		} else if ($parameters_array["tree_type"] == "filter") {
			$parameters_array["op"] = ($donnee["operator"] && sizeof($donnee) >= 3) ? $donnee["operator"] : null;
		}

		unset($donnee["operator"]); // Pour une meilleure gestion des indices ($nb)
		if (isset($donnee["id"])) unset($donnee["id"]); // Pour une meilleure gestion des indices ($nb)

		$parameters_array["operation_even"] = "";
		$parameters_array["operation_even_1"] = "";
		if ($profondeur % 2 == 0) $parameters_array["operation_even"] = "operation_even";
		else $parameters_array["operation_even_1"] = "operation_even";

		// Incrémentation de l'indice ($nb)
		self::incrementNb();

		// Mise en forme de l'indice ($nb)
		if (self::getNb() < 10) $parameters_array["i"] = "00" . self::getNb();
		else if (self::getNb() < 100) $parameters_array["i"] = "0" . self::getNb();
		// Racine ou non
		if (self::getNb() == 1) $parameters_array["racine"] = "racine";
		else $parameters_array["racine"] = "";

		/* FIN CODE COMMUN POUR LES TROIS */
		if ($parameters_array["tree_type"] == "report" && isset($parameters_array["for_what"]) && $parameters_array["for_what"] == "viewonly") {
			self::tree_display_report_viewonly($donnee, $profondeur, $parameters_array);
		}
		else {
			self::tree_display_report_filter($donnee, $profondeur, $parameters_array);
		}
		return self::getNb();
	}

	private static function tree_display_report_filter($donnee, $profondeur, $parameters_array)
	{

		if ($parameters_array["op"] == null) {
			if ($parameters_array["tree_type"] == "report") {
				list($operators, $operators_short, $data_to_show) = getOperatorsDataToShow($parameters_array["data_type"]);
				if ($parameters_array["data_type"] == "PROCESS")
					echo insert_criterias_processus([$donnee], $data_to_show, $operators, $operators_short, self::getNb());
				else
					echo insert_criterias_donnees([$donnee], $data_to_show, $operators, $operators_short, self::getNb());
			} else if ($parameters_array["tree_type"] == "filter") {
				(new PredicatClass($_GET['id']))->display_predicate($donnee, self::getNb());
			}
		} else {
			$nb_children_group = 0;
			$nb_children_criteria = 0;
			$nb_children_tot = 0;
			if ($parameters_array["tree_type"] == "report") {
				foreach ($donnee as $d) {
					if (isset($d["operator"])) {
						$nb_children_group++;
					} else {
						$nb_children_criteria++;
					}
					$nb_children_tot++;
				}

			} else if ($parameters_array["tree_type"] == "filter") {
				foreach ($donnee as $key => $value) {
					if (is_array($value) || ($key == "pred" && $value != null)) {
						$nb_children_group++; // Distinction groupe / critere inutile
						$nb_children_criteria++; // Distinction groupe / critere inutile
						$nb_children_tot++;
					}
				}
			}

			$a_disabled_group = "";
			$a_disabled_criteria = "";
			$title_group = "";
			$title_criteria = "";
			$event_group = "";
			$event_criteria = "";

			// Activation / Désactivation des évènements d'ajout de critères / groupes
			if ($parameters_array["tree_type"] == "report") {
				if ($nb_children_group > 0) {
					$a_disabled_criteria = "a_disabled";
					$title_criteria = "title=\"Ce groupe n'accepte que des groupes de critères\"";
					$event_group = "onclick=\"add_group(this.parentElement, {$profondeur} + 1)\"";
				} else {
					$a_disabled_group = "a_disabled";
					$title_group = "title=\"Ce groupe n'accepte que des critères simples\"";
					$event_criteria = "onclick=\"add_critere_or_donnee(this.parentElement.parentElement, 'critere')\"";
				}
			} else if ($parameters_array["tree_type"] == "filter") {
				if ($nb_children_tot == 2) {
					$a_disabled_criteria = "a_disabled";
					$a_disabled_group = "a_disabled";
					$title_criteria = "title=\"Ce groupe est complet\"";
					$title_group = "title=\"Ce groupe est complet\"";
				} else {
					$event_group = "onclick=\"add_group(this.parentElement, {$profondeur} + 1)\"";
					$event_criteria = "onclick=\"add_critere_or_donnee(this.parentElement.parentElement, 'critere')\"";
				}
			}

			$or_selected = "";
			$and_selected = "";

			if ($parameters_array["op"] == "OR") $or_selected = "selected";
			else if ($parameters_array["op"] == "AND") $and_selected = "selected";
			echo <<<HTML
<div id="operation_{$parameters_array["i"]}" class="div_operation {$parameters_array["operation_even"]} {$parameters_array["racine"]}">
	<div class="div_operation_ext">
		<select aria-label="Opérateur du groupe" name="operator_group_{$parameters_array["i"]}" class="group_operator {$parameters_array["racine"]}">
			<option value="OR" {$or_selected}>OR</option>
			<option value="AND" {$and_selected}>AND</option>
		</select>
		<input type="hidden" id="nb_children_operator_group_{$parameters_array["i"]}" name="nb_children_operator_group_{$parameters_array["i"]}" value="{$nb_children_group}">
		<input type="hidden" id="nb_children_operator_criteria_{$parameters_array["i"]}" name="nb_children_operator_criteria_{$parameters_array["i"]}" value="{$nb_children_criteria}">
		<input type="hidden" id="nb_children_operator_{$parameters_array["i"]}" name="nb_children_operator_{$parameters_array["i"]}" value="{$nb_children_tot}" pattern="^[1-9][0-9]*">
	</div>
	<div class="div_operation_int">
		<div class="div_operation_dotted"></div>
		<div class="div_operation_int_int {$parameters_array["operation_even_1"]}">
			<div id="div_operation_sub_int_{$parameters_array["i"]}" class="prof_{$profondeur}">
HTML;
			foreach ($donnee as $d) {
				self::tree_display($d, $profondeur + 1, $parameters_array);
			}

			if ($parameters_array["tree_type"] == "report" && $parameters_array["racine"] == "racine") {
				echo <<<HTML
			</div>
			<div id="div_add_group_critere_{$parameters_array["i"]}">
				<a tabindex="0" id="a_add_group_{$parameters_array["i"]}" class="div_add_group {$a_disabled_group}" {$title_group} {$event_group}>+ Ajouter un groupe</a>
				<a tabindex="0" id="a_add_critere_{$parameters_array["i"]}" class="div_add_critere {$a_disabled_criteria}" {$title_criteria} {$event_criteria}>+ Ajouter un critère</a>
			</div>
		</div>
	</div>
</div>
HTML;
			} else {
				echo <<<HTML
			</div>
			<div id="div_add_group_critere_{$parameters_array["i"]}">
				<a tabindex="0" id="a_add_group_{$parameters_array["i"]}" class="div_add_group {$a_disabled_group}" {$title_group} {$event_group}>+ Ajouter un groupe</a>
				<a tabindex="0" id="a_add_critere_{$parameters_array["i"]}" class="div_add_critere {$a_disabled_criteria}" {$title_criteria} {$event_criteria}>+ Ajouter un critère</a>
				<button class="but delete" type="button" title="Supprimer un groupe et son contenu" onclick="delete_group(this.parentElement.parentElement.parentElement.parentElement, {$profondeur})">
					<img alt="Supprimer un groupe" src="../ressources/cross.png" style="width:30px;height:30px">
				</button>
			</div>
		</div>
	</div>
</div>
HTML;
			}
		}
	}

	private static function tree_display_report_viewonly($donnee, $profondeur, $parameters_array)
	{
		if ($parameters_array["op"] == null) {
			echo <<<HTML
		<div class="critere_rapport_posting">
			<div class="criteria_left">{$donnee["default_name"]}</div>
			<div class="criteria_middle">{$donnee["label"]}</div>
			<div class="criteria_right">{$donnee["value_to_compare"]}</div>
		</div>
HTML;

		} else {

			echo <<<HTML
<div id="operation_{$parameters_array["i"]}" class="critere_rapport_posting div_operation {$parameters_array["operation_even"]} {$parameters_array["racine"]}">
	<div class="div_operation_ext">
		<div aria-label="Opérateur du groupe" id="operator_group_{$parameters_array["i"]}" class="group_operator {$parameters_array["racine"]}">
			<p>{$parameters_array["op"]}</p>
		</div>
	</div>
	<div class="div_operation_int">
		<div class="div_operation_dotted"></div>
		<div class="div_operation_int_int {$parameters_array["operation_even_1"]}">
			<div id="div_operation_sub_int_{$parameters_array["i"]}" class="prof_{$profondeur}">
HTML;
			foreach ($donnee as $d) {
				self::tree_display($d, $profondeur + 1, $parameters_array);
			}
			echo <<<HTML
			</div>
		</div>
	</div>
</div>
HTML;
		}

	}
}