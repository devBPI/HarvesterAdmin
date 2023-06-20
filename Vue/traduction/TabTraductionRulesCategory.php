<?php

class TabTraductionRulesCategory
{
	/**
	 * @param $mod string true ou false (mode modification ou non)
	 * @param $set array données
	 * @param $controller string nom du controlleur .php
	 * @param $denomination string ce que contiendra l'en-tête du tableau
	 * @param $msg_edition string message du bouton d'édition
	 * @param $id string identifiant de l'ensemble de cibles si nécessaire
	 * @return string le code HTML de <div class="sizeable_table">, qui contient un tableau
	 */
	static function makeTab($mod, $set, $controller, $denomination, $msg_edition, $id=null)
	{
		$str = '<div class="sizeable_table">
		<div class="hidden_field">
			<input type="text" name="t" style="width:300px;"/>
			<button class="but" type="button" title="Supprimer une cible" onclick="delete_field(this.parentElement)">
				<img src="../ressources/cross.png" width="30px" height="30px">
			</button>
		</div>';
		if ($mod == 'false') {
			if ($id == null) {
				$str = $str . '<form action="' . $controller . '" method="post" class="left"
			  onsubmit="return confirm(\'Confirmer les modifications ?\');">';
			} else {
				$str = $str . '<form action="' . $controller . '?id=' . $id . '&modify=true" method="post" class="left"
			  onsubmit="return confirm(\'Confirmer les modifications ?\');">';
			}

				$str = $str . '<div>
				<table class="table-config">
					<tbody>
					<tr class="hidden_field" id="new" name="pred">
						<td>
							<input type="text" name="t" style="max-width:500px;"/>
						</td>
						<td class="td_cross">
							<button class="but" type="button" title="Supprimer la ligne"
									onclick="delete_field(this.parentElement.parentElement)">
								<img src="../ressources/cross.png" width="30px" height="30px"/>
							</button>
						</td>
					</tr>
					<tr>
						<th>' . $denomination . '</th>
						<th class="td_cross"></th>
					</tr>';
			if (!empty($set)) {
				foreach ($set as $key => $value) {
					$str = $str . '<tr>
							<td>
								<input style="max-width:500px;" type="text" name="' . $key . '" value="' . $value . '"/>
							</td>
							<td class="td_cross">
								<button class="but" type="button" title="Supprimer une cible"
										onclick="delete_field(this.parentElement.parentElement)"><img src="../ressources/cross.png"
																						width="30px" height="30px"/>
								</button>
							</td>
						</tr>';
				}
			} else {
				//echo "<input type='text' name='-1'/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img src='../ressources/cross.png' width='30px' height='30px'/></button></div>";
			}
			$str = $str . '<tr style="background-color:rgba(0,0,0,0);" id="add_row">
					<td></td>
						<td class="td_cross">
							<button class="ajout but" type="button" title="Ajouter une ligne"
									style="cursor:pointer"
									onclick="add_new_field(this.parentElement.parentElement.parentElement.parentElement)">
								<img src="../ressources/add.png" width="30px" height="30px"/></button>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div style="display:flex;justify-content: flex-end;flex-direction: row">
				<input type="submit" value="Enregistrer" class="button primairy-color round"/>
			</div>
		</form>
	</div>';
		} else {
			$str = $str . '<div class="border_div">
	<table class="table-config">
		<thead>
		<tr><th>' . $denomination . '</th></tr>
		</thead>
		<tbody>';
			foreach ($set as $key => $value) {
				$str = $str . '<tr><td>' . $value . '</td></tr>';
			}
			$str = $str . '</table>
	</div>
	<div style="display:flex;justify-content: flex-end;flex-direction: row">
			';
			if ($id == null) {
				$str = $str . '<a href="' . $controller . '?modify=false" class="buttonpage">' . $msg_edition . '</a>
				</div>';
			} else {
				$str = $str . '<a href="' . $controller . '?id=' . $id . '&modify=false" class="buttonpage">' . $msg_edition . '</a>
				</div>';
			}
		}
		return $str;
	}
}