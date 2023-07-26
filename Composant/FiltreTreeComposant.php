<?php

include("../Composant/predicat/PredicatClass.php");

class FiltreTreeComposant
{
	private static $nb = 0;

	private static function incrementNb()
	{
		self::$nb++;
	}

	public static function getNb(): int
	{
		return self::$nb;
	}

	public static function tree_display($donnee, $profondeur, $parameters_array)
	{

		/* Recopie RapportTreeComposant */
		if (!is_array($donnee)) {
			return;
		}
		/* Fin recopie RapportTreeComposant */
		$op = ($donnee["operator"] && sizeof($donnee) >= 3) ? $donnee["operator"] : null;
		/* Recopie RapportTreeComposant */
		unset($donnee["operator"]); // Pour une meilleure gestion des indices ($nb)
		$operation_even = "";
		$operation_even_1 = "";
		if ($profondeur % 2 == 0) $operation_even = "operation_even";
		else $operation_even_1 = "operation_even";
		/* Fin recopie RapportTreeComposant */

		if ($op == null) { /* If/Else RapportTreeComposant */
			self::incrementNb();
			(new PredicatClass($_GET['id']))->display_predicate($donnee, self::getNb());
		} else {

			$nb_children=0;
			foreach ($donnee as $key => $value) {
				if (is_array($value) || ($key == "pred" && $value != null)) {
					$nb_children++;
				}
			}

			/* Recopie RapportTreeComposant */
			$a_disabled_group = "";
			$a_disabled_criteria = "";
			$title_group = "";
			$title_criteria = "";
			$event_group = "";
			$event_criteria = "";
			/* Fin recopie RapportTreeComposant */

			// Activation / Désactivation des évènements d'ajout de critères / groupes
			if ($nb_children == 2) {
				$a_disabled_criteria = "a_disabled";
				$a_disabled_group = "a_disabled";
				$title_criteria = "title=\"Ce groupe est complet\"";
				$title_group = "title=\"Ce groupe est complet\"";
			} else {
				$event_group = "onclick=\"add_group(this.parentElement, {$profondeur} + 1)\"";
				$event_criteria = "onclick=\"add_critere_or_donnee(this.parentElement.parentElement, 'filter')\"";
			}

			/* Recopie RapportTreeComposant */
			// Incrémentation de l'indice ($nb)
			self::incrementNb();
			// Mise en forme de l'indice ($nb)
			if (self::getNb() < 10) $i = "00" . self::getNb();
			else if (self::getNb() < 100) $i = "0" . self::getNb();

			if (self::getNb() == 1) $racine = "racine";
			else $racine = "";

			$or_selected = "";
			$and_selected = "";

			if ($op == "OR") $or_selected = "selected";
			else if ($op == "AND") $and_selected = "selected";
			/* Fin recopie RapportTreeComposant */
			echo <<<HTML
		<div id="operation_{$i}" class="div_operation {$operation_even} {$racine}">
			<div class="div_operation_ext">
			<select aria-label="Opérateur du groupe"  name="operator_group_{$i}" class="group_operator {$racine}">
				<option value="OR" {$or_selected}>OR</option>
				<option value="AND" {$and_selected}>AND</option>
			</select>
			<input type="hidden" id="nb_children_operator_group_{$i}" name="nb_children_operator_group_{$i}" value="{$nb_children}">
			<input type="hidden" id="nb_children_operator_criteria_{$i}" name="nb_children_operator_criteria_{$i}" value="{$nb_children}">
			<input type="hidden" id="nb_children_operator_{$i}" name="nb_children_operator_{$i}" value="{$nb_children}">
		</div>
		<div class="div_operation_int">
		<div class="div_operation_dotted"></div>
		<div class="div_operation_int_int {$operation_even_1}">
			<div id="div_operation_sub_int_{$i}" class="prof_{$profondeur}" style="display: flex; flex-direction: column; flex-grow: 1">
HTML;

			if ($op == null) {
				(new PredicatClass($_GET['id']))->display_predicate($donnee, self::getNb());
			} else {
				self::tree_display($donnee[0], $profondeur + 1, $parameters_array);
				self::incrementNb();
				self::tree_display($donnee[1], $profondeur + 1, $parameters_array);
			}
			echo <<<HTML
			</div>
			<div id="div_add_group_critere_{$i}">
				<a tabindex="0" id="a_add_group_{$i}" class="div_add_group {$a_disabled_group}" {$title_group} {$event_group}>+ Ajouter un groupe</a>
				<a tabindex="0" id="a_add_critere_{$i}" class="div_add_critere {$a_disabled_criteria}" {$title_criteria} {$event_criteria}>+ Ajouter un prédicat</a>
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