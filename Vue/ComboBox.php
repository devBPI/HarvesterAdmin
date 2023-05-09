<?php
$i = 0;
foreach ($data as $combo_key => $var) {
    $i ++;
	if(isset($var['id']))
	{
		echo '<option value="' . $var['id'] . '"' . (($id_param == $var['id']) ? ' selected' : '') . '>' . $var['name'] . '</option>';
	}
	else
	{
		echo '<option value="' . $combo_key . '"' . (($id_param == $combo_key) ? ' selected' : '') . '>' . $var['name'] . '</option>';
	}
}

?>

