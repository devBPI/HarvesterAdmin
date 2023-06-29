<?php

$ini = @parse_ini_file("../etc/configuration.ini", true);
if (!$ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}

require_once("../PDO/Gateway.php");

if (isset($_POST["submit_value"])) {
	unset($_POST["old_id_"]);
	unset($_POST["id_"]);
	unset($_POST["is_enabled_"]);
	unset($_POST["submit_value"]);
	$i = 0;
	foreach ($_POST as $key => $value) {
		if (preg_match('/(old_id_)/', $key)) {
			// Ajout de l'attribut is_enabled à faux si is_enabled
			// n'existe pas pour l'élément précédent
			if ($i != 0 && !isset($tab_mailing_list[$i]["is_enabled"]))
				$tab_mailing_list[$i]["is_enabled"] = "f";
			// Incrément de $i et remplissage du tableau
			$tab_mailing_list[++$i]["old_id"] = $value;
		} else if (preg_match('/(id_)/', $key)) {
			$tab_mailing_list[$i]["id"] = $value;
		} else {
			$tab_mailing_list[$i]["is_enabled"] = "t";
		}
	}
	// Ajout de l'attribut is_enabled à faux si is_enabled n'existe pas pour le dernier élément
	if ($i != 0 && !isset($tab_mailing_list[$i]["is_enabled"]))
		$tab_mailing_list[$i]["is_enabled"] = "f";
	$array_error = Gateway::updateMailingList($tab_mailing_list);
	if (count($array_error) > 0) {
		$mailing_list = [];
		foreach ($tab_mailing_list as $value) {
			$mailing_list[] = ["mail" => $value["old_id"],
				"new_mail" => $value["id"],
				"is_enabled" => $value["is_enabled"]
			];
		}
	} else {
		$mailing_list = Gateway::getMailingList();
		for ($i = 0; $i < count($mailing_list); $i++) {
			$mailing_list[$i]["new_mail"] = $mailing_list[$i]["mail"];
		}
	}
} else {
	$mailing_list = Gateway::getMailingList();
	for ($i = 0; $i < count($mailing_list); $i++) {
		$mailing_list[$i]["new_mail"] = $mailing_list[$i]["mail"];
	}
}

$section = "Gestion de la liste de diffusion";

include("../Vue/alerts_logs/AlertesMailingList.php");

?>