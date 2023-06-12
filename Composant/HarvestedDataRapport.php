<?php

require_once("../Composant/ComboBox.php");
require_once("../PDO/Gateway.php");

// Remplir ComboBox avec les noms de types
if (isset($_POST["champs"]) && $_POST["champs"] == "type_data") {
	 $types_donnees = Gateway::getResourceTypes();
	 $types_donnees_formatees = [];
	 foreach ($types_donnees as $type_donnees) {
		 $types_donnees_formatees[] = [
			 "id" => $type_donnees["type"],
			 "name" => $type_donnees["type"]
		 ];
	 }
	echo ComboBox::makeComboBox($types_donnees_formatees);
}

?>