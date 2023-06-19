<?php

/**
 * Utilisé pour l'affichage des configurations associées à une règle de filtres / de traduction
 */
class TabConfigsAssociees
{
	static function makeTab($configurations) {
		$str = '
		<div class="border_div" style="margin-top:5%">
		<table class="table-backoffice" >
			<tr>
				<th colspan="2" style="width:100%">Configurations associées</th>
			</tr>';
		if ($configurations) {
			foreach ($configurations as $configuration) {
				$str = $str . '<tr>
				<th>Nom abrégé</th>
				<td><a style="text-decoration: none" href="../Vue/FicheIndividuelle.php?param=' . $configuration['id'] . '">' . $configuration['name'] . '<a></td>
			</tr>';
			}
			$str = $str . '</table>
	</div>';
		}

		return $str;
	}
}