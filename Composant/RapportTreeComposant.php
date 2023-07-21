<?php
class RapportTreeComposant
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
		if ($parameters_array["tree_type"] == "report") {

			if (isset($parameters_array["for_what"]) && $parameters_array["for_what"] == "viewonly") {
				self::tree_display_report_viewonly($donnee, $profondeur, $parameters_array);
			}
			else {
				self::tree_display_report($donnee, $profondeur, $parameters_array);
			}
		}
		return self::getNb();
	}

	private static function tree_display_report($donnee, $profondeur, $parameters_array)
	{
		if (!is_array($donnee)) {
			return;
		}
		$op = $donnee["operator"] ?? null;
		$data_type = $parameters_array["data_type"];
		//var_dump($donnee);

		unset($donnee["operator"]); // Pour une meilleure gestion des indices ($nb)
		unset($donnee["id"]); // Pour une meilleure gestion des indices ($nb)

		$operation_even = "";
		$operation_even_1 = "";
		if ($profondeur % 2 == 0) $operation_even = "operation_even";
		else $operation_even_1 = "operation_even";

		if ($op == null) {
			list($operators, $operators_short, $data_to_show) = getOperatorsDataToShow($data_type);
			self::incrementNb();
			if ($data_type == "PROCESS")
				echo insert_criterias_processus([$donnee], $data_to_show, $operators, $operators_short, self::getNb());
			else
				echo insert_criterias_donnees([$donnee], $data_to_show, $operators, $operators_short, self::getNb());
		} else {

			$nb_children_group = 0;
			$nb_children_criteria = 0;
			foreach ($donnee as $d) {
				if (isset($d["operator"])) {
					$nb_children_group++;
				} else {
					$nb_children_criteria++;
				}
			}
			$nb_children_tot = $nb_children_group+$nb_children_criteria;

			$a_disabled_group = "";
			$a_disabled_criteria = "";
			$title_group = "";
			$title_criteria = "";
			$event_group = "";
			$event_criteria = "";
			// Activation / Désactivation des évènements d'ajout de critères / groupes
			if ($nb_children_group > 0) {
				$a_disabled_criteria = "a_disabled";
				$title_criteria = "title=\"Ce groupe n'accepte que des groupes de critères\"";
				$event_group = "onclick=\"add_group(this.parentElement, {$profondeur} + 1)\"";
			} else {
				$a_disabled_group = "a_disabled";
				$title_group = "title=\"Ce groupe n'accepte que des critères simples\"";
				$event_criteria = "onclick=\"add_critere_or_donnee(this.parentElement.parentElement, 'critere')\"";
			}

			// Incrémentation de l'indice ($nb)
			self::incrementNb();
			// Mise en forme de l'indice ($nb)
			if (self::getNb() < 10) $i = "00" . self::getNb();
			else if (self::getNb() < 100) $i = "0" . self::getNb();

			if (self::getNb() == 1) $racine = "racine";
			else $racine = "";

			$or_selected = "";
			$and_selected = "";
			$except_selected = "";

			if ($op == "OR") $or_selected = "selected";
			else if ($op == "AND") $and_selected = "selected";
			else if ($op == "EXCEPT") $except_selected = "selected";
			echo <<<HTML
<div id="operation_{$i}" class="div_operation {$operation_even} {$racine}">
	<div class="div_operation_ext">
		<select aria-label="Opérateur du groupe" name="operator_group_{$i}" class="group_operator {$racine}">
			<option value="OR" {$or_selected}>OR</option>
			<option value="AND" {$and_selected}>AND</option>
			<option value="EXCEPT" {$except_selected} disabled>EXCEPT</option>
		</select>
		<input type="hidden" id="nb_children_operator_group_{$i}" name="nb_children_operator_group_{$i}" value="{$nb_children_group}">
		<input type="hidden" id="nb_children_operator_criteria_{$i}" name="nb_children_operator_criteria_{$i}" value="{$nb_children_criteria}">
		<input type="hidden" id="nb_children_operator_{$i}" name="nb_children_operator_{$i}" value="{$nb_children_tot}" pattern="^[1-9][0-9]*">
	</div>
	<div class="div_operation_int">
		<div class="div_operation_dotted"></div>
		<div class="div_operation_int_int {$operation_even_1}">
			<div id="div_operation_sub_int_{$i}" class="prof_{$profondeur}">
HTML;
			foreach ($donnee as $d) {
				self::tree_display_report($d, $profondeur + 1, $parameters_array);
			}
			if ($racine == "racine") {
				echo <<<HTML
			</div>
			<div id="div_add_group_critere_{$i}">
				<a tabindex="0" id="a_add_group_{$i}" class="div_add_group {$a_disabled_group}" {$title_group} {$event_group}>+ Ajouter un groupe</a>
				<a tabindex="0" id="a_add_critere_{$i}" class="div_add_critere {$a_disabled_criteria}" {$title_criteria} {$event_criteria}>+ Ajouter un critère</a>
			</div>
		</div>
	</div>
</div>
HTML;
			} else {
				echo <<<HTML
			</div>
			<div id="div_add_group_critere_{$i}">
				<a tabindex="0" id="a_add_group_{$i}" class="div_add_group {$a_disabled_group}" {$title_group} {$event_group}>+ Ajouter un groupe</a>
				<a tabindex="0" id="a_add_critere_{$i}" class="div_add_critere {$a_disabled_criteria}" {$title_criteria} {$event_criteria}>+ Ajouter un critère</a>
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
		if (!is_array($donnee)) {
			return;
		}
		$op = $donnee["operator"] ?? null;
		$data_type = $parameters_array["data_type"];
		//var_dump($donnee);

		unset($donnee["operator"]); // Pour une meilleure gestion des indices ($nb)
		unset($donnee["id"]); // Pour une meilleure gestion des indices ($nb)

		$operation_even = "";
		$operation_even_1 = "";
		if ($profondeur % 2 == 0) $operation_even = "operation_even";
		else $operation_even_1 = "operation_even";
		if ($op == null) {
			self::incrementNb();
			echo <<<HTML
		<div class="critere_rapport_posting">
			<div class="criteria_left">{$donnee["default_name"]}</div>
			<div class="criteria_middle">{$donnee["label"]}</div>
			<div class="criteria_right">{$donnee["value_to_compare"]}</div>
		</div>
HTML;

		} else {

			// Incrémentation de l'indice ($nb)
			self::incrementNb();
			// Mise en forme de l'indice ($nb)
			if (self::getNb() < 10) $i = "00" . self::getNb();
			else if (self::getNb() < 100) $i = "0" . self::getNb();

			if (self::getNb() == 1) $racine = "racine";
			else $racine = "";
			echo <<<HTML
<div id="operation_{$i}" class="critere_rapport_posting div_operation {$operation_even} {$racine}">
	<div class="div_operation_ext">
		<div aria-label="Opérateur du groupe" id="operator_group_{$i}" class="group_operator {$racine}">
			<p>{$op}</p>
		</div>
	</div>
	<div class="div_operation_int">
		<div class="div_operation_dotted"></div>
		<div class="div_operation_int_int {$operation_even_1}">
			<div id="div_operation_sub_int_{$i}" class="prof_{$profondeur}">
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

?>