<!-- Il y a différentes div et boutons de menus car je n'ai encore décidé lesquels j'utiliserai -->
<?php
ini_set("display_errors",1);
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
$limit = 15;
if (! empty($_GET)) {
    $name = $_GET['name'];
    $grabber = $_GET['connecteur'];
    $page = (! empty($_GET['page'])) ? $_GET['page'] : 1;
    $order = (! empty($_GET['order'])) ? $_GET['order'] : "name";
} else if (! empty($_POST)) {
    $name = $_POST['textNom'];
    $grabber = $_POST['list_grabber'];
    $page = 1;
    $order = "name";
} else {
    $name = "";
    $grabber = 0;
    $page = 1;
    $order = "name";
}
$start_from = ($page - 1) * $limit;
if (! empty($name)) {
    if ($grabber != 0) {
        $sql = "SELECT COUNT(id) FROM configuration.harvest_grab_configuration WHERE name LIKE '%" . $name . "%' AND grabber_id=" . $grabber . ";";
        $query = "SELECT G.name grabber, HC.id, HC.code, HC.name, B.name as public_name, HC.differential FROM configuration.grabber G, configuration.harvest_grab_configuration HGC, configuration.harvest_configuration HC
	LEFT JOIN configuration.search_base B on B.code = HC.search_base_code 
	WHERE G.id = HGC.grabber_id and HGC.id = HC.grab_configuration_id AND HGC.name LIKE '%" . $name . "%' AND grabber_id=" . $grabber . " ORDER BY " . $order . " LIMIT " . $limit . " OFFSET " . $start_from . ";";
    } else {
        $sql = "SELECT COUNT(id) FROM configuration.harvest_grab_configuration WHERE name LIKE '%" . $name . "%';";
        $query = "SELECT G.name grabber, HC.id, HC.code, HC.name, B.name as public_name, HC.differential FROM configuration.grabber G, configuration.harvest_grab_configuration HGC, configuration.harvest_configuration HC
	 LEFT JOIN configuration.search_base B on B.code = HC.search_base_code 
     WHERE G.id = HGC.grabber_id and HGC.id = HC.grab_configuration_id AND HGC.name LIKE '%" . $name . "%' ORDER BY " . $order . " LIMIT " . $limit . " OFFSET " . $start_from . ";";
    }
} else {
    if ($grabber != 0) {
        $sql = "SELECT COUNT(id) FROM configuration.harvest_grab_configuration WHERE grabber_id=" . $grabber . ";";
        $query = "SELECT G.name grabber, HC.id, HC.code, HC.name, B.name as public_name, HC.differential FROM configuration.grabber G, configuration.harvest_grab_configuration HGC, configuration.harvest_configuration HC
	LEFT JOIN configuration.search_base B on B.code = HC.search_base_code 
    WHERE G.id = HGC.grabber_id and HGC.id = HC.grab_configuration_id AND grabber_id=" . $grabber . " ORDER BY " . $order . " LIMIT " . $limit . " OFFSET " . $start_from . ";";
    } else {
        $sql = "SELECT COUNT(id) FROM configuration.harvest_grab_configuration;";
        $query = "SELECT G.name grabber, HC.id, HC.code, HC.name, B.name as public_name, HC.differential,
				(SELECT max(modification_date) FROM configuration.harvest_task HT where HC.id = HT.configuration_id) date
				FROM configuration.grabber G, configuration.harvest_grab_configuration HGC, configuration.harvest_configuration HC
                LEFT JOIN configuration.search_base B on B.code = HC.search_base_code 				
                WHERE G.id = HGC.grabber_id and HGC.id = HC.grab_configuration_id
				ORDER BY " . $order . " LIMIT " . $limit . " OFFSET " . $start_from . ";";
    }
}
require_once ("../Gateway.php");
Gateway::connection();
$data = Gateway::getConfigurationGrabber();
$conf = Gateway::select($query);
$total_records = Gateway::countHarvestConfiguration($sql);
$total_pages = ceil($total_records / $limit);
$section = "Accueil";
include ("../Vue/Accueil.php");
?>

<!-- Revue Stage 01 -->
<!-- manque le </html> fermant, qui est dans ce cas dupliqué dans les Vues, ce qui donne des réusltats tels que:
<html style="overflow-x:hidden;overflow-y:auto;">
<link rel="stylesheet" href="../Vue/Style/DEVstyle.css" />
<link rel="stylesheet" href="../Vue/Style/style.css" />
<html>
<!-- Entête avec les différents boutons du menu --\>
<div name="entete" id="menu-deroulant" class="entete">
Lors de l'affichage de la page principale

 -->
