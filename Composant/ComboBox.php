<?php
class ComboBox
{
	// Remplace ComboBox.php et ComboBoxFiltreTrad.php
	static function makeComboBox($data, $id_param = null) {
		$str = "";
		foreach ($data as $combo_key => $var) {
			if (isset($var['confname'])) {
				if(isset($var['id']))
					$str = $str . '<option value="' . $var['id'] . '"' . (($id_param == $var['id']) ? ' selected' : '') . '>' . $var['confname'] . '</option>';
				else
					$str = $str . '<option value="' . $combo_key . '"' . (($id_param == $combo_key) ? ' selected' : '') . '>' . $var['confname'] . '</option>';
			} else {
				if(isset($var['id']))
					$str = $str . '<option value="' . $var['id'] . '"' . (($id_param == $var['id']) ? ' selected' : '') . '>' . $var['name'] . '</option>';
				else
					$str = $str . '<option value="' . $combo_key . '"' . (($id_param == $combo_key) ? ' selected' : '') . '>' . $var['name'] . '</option>';
			}
		}
		return $str;
	}

	// Remplace ComboBoxHeure.php
	static function makeComboBoxHeure() {
		$str = '<option value="null">Heure</option>';
		for ($i = 0; $i < 24; $i++) {
			for ($j = 0; $j < 60; $j+=30) {
				$str = $str . '<option value="' . str_pad($i,2,'0',STR_PAD_LEFT) . ':' . str_pad($j,2,'0',STR_PAD_LEFT) . '">'
					. str_pad($i,2,'0',STR_PAD_LEFT) . ':' . str_pad($j,2,'0',STR_PAD_LEFT) . '</option>';
			}
		}
		return $str;
	}

	// Remplace ComboBoxJour.php
	static function makeComboBoxJour() {
		return '<option value="0">Jour</option>
		<option value="1">Lundi</option>
		<option value="2">Mardi</option>
		<option value="3">Mercredi</option>
		<option value="4">Jeudi</option>
		<option value="5">Vendredi</option>
		<option value="6">Samedi</option>
		<option value="7">Dimanche</option>';
	}

	// Remplace ComboBoxSemaine.php
	static function makeComboBoxSemaine() {
		$str = '<option value="0">Semaine</option>';
		for ($i = 1; $i <= 5; $i ++)
			$str = $str . '<option value =' . $i . '> Semaine n°' . $i . '</option>';
		return $str;
	}

	// Remplace ComboBoxOccurence.php
	static function makeComboBoxOccurence() {
		$str = '<option value="0">Occurence</option>';
    	for ($i = 1; $i <= 5; $i ++)
			$str = $str . '<option value =' . $i . '> Occurence n°' . $i . '</option>';
		return $str;
	}
}