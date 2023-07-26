<?php

/**
 * Utilisé pour l'affichage des configurations associées à une règle de filtres / de traduction
 */
class TabConfigsAssociees
{
	static function makeTab($configurations) {
		$str = '
		<div class="border_div" style="margin-top:5%">
		<table class="table-backoffice" style="table-layout:initial">
			<tr style="border-bottom: 1px solid black">
				<th colspan="2" style="width:100%; background-color:#56acde">Configurations associées</th>
			</tr>';
		if($configurations) {
			foreach ($configurations as $configuration) {
				$str = $str . '<tr style="border-top: 1px solid white">
				<th style="width: 30%">Nom abrégé</th>
				<td><a style="text-decoration: none" href="../Vue/FicheIndividuelle.php?param=' . $configuration['id'] . '">' . $configuration['name'] . '<a></td>
			</tr>';
			}
		} else {
			$str = $str . '<tr style="border-top: 1px solid white">
				<td colspan="2" style="text-align:left">Aucune</td>
				</tr>';
		}
		$str = $str . '</table>
	</div>';

		return $str;
	}
}