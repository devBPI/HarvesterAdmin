<?php

require_once("../PDO/Gateway.php");
$donnees = Gateway::getStatus();

if (isset($_POST['champ']) && isset($_POST['ordre'])) {
	$champ = $_POST['champ'];
	$ordre = $_POST['ordre'];
	echo triTab($donnees, $champ, $ordre);
} else {
	echo makeTab($donnees);
}

/** Trie des données, renvoie un tableau html
 * @param $donnees array de données
 * @param $champ string champ sur lequel porte le tri
 * @param $ordre string asc ou desc
 * @return string le tableau html trié
 */
function triTab($donnees, $champ, $ordre)
{
	$nv_donnees = $donnees;
	switch ($champ) {
		case "code": usort($nv_donnees, "triTabCode"); break;
		case "dispo": usort($nv_donnees, "triTabDispo"); break;
		case "to_harvest": usort($nv_donnees, "triTabToHarvest"); break;
		default: usort($nv_donnees, "triTabLabel");
	}
	if ($ordre == "asc")
		$nv_donnees = array_reverse($nv_donnees);
	return makeTab($nv_donnees);
}

/** Crée le tableau en html
 * @param $donnees array de données
 * @return string html du contenu du tableau (pas d'en-têtes)
 */
function makeTab($donnees)
{
	$str = "";
	foreach ($donnees as $donnee) {
		$str = $str . '<tr>' .
			'<form action="EtatsDispo.php?code=' . $donnee['code'] . '" method="post" onsubmit="return confirm(\'Voulez vous vraiment modifier ce status ?\');">' .
			'<td data-label="Code">' . $donnee['code'] . '</td>' .
			'<td data-label="Disponibilité">';
		if ($donnee['dispo_flag'] != "")
			$str = $str . $donnee['dispo_flag'];
		$str = $str . '</td>' .
			'<td data-label="À moissonner">';
		if ($donnee['select_to_harvest'] == "f")
			$str = $str . 'False';
		else
			$str = $str . 'True';
		$str = $str . '</td>' .
			'<td data-label="Label">' . $donnee['label'] . '</td>' .
			'</form>' .
			'</tr>';
	}
	return $str;
}

/* -------------------------- Fonctions de tri -------------------------- */
function triTabCode($a, $b)
{
	if ($a['code'] > $b['code']) return 1;
	return -1;
}

function triTabDispo($a, $b)
{
	if ($a['dispo_flag'] < $b['dispo_flag']) return 1;
	return -1;
}

function triTabToHarvest($a, $b)
{
	if ($a['select_to_harvest'] < $b['select_to_harvest']) return 1;
	return -1;
}

function triTabLabel($a, $b)
{
	if ($a['label'] < $b['label']) return 1;
	return -1;
}

?>