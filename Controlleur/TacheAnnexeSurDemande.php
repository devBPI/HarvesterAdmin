<!-- Il y a différentes div et boutons de menus car je n'ai encore décidé lesquels j'utiliserai -->
<html style="overflow-y: auto; overflow-x: hidden;">
<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
require_once ("../PDO/Gateway.php");
Gateway::connection();
$codes = Gateway::getConfigCodes();

$section = "Tâche Annexe sur Demande";
include ("../Vue/taches_annexes/TacheAnnexeSurDemande.php");

$name = $_POST['taskname'];
$param = $_POST['taskparameter'];

// Recuperation de l'action en cours
$action = '';

if (isset($_POST['launch'])) {
    $action = 'launch_task';
}

switch ($action) {
    case 'launch_task':
        {
            if ($name != null) {
                if($name == "PURGE"){
                    $param = Gateway::getIdFromCode($param);
                    $ins = Gateway::insertSideTask($name, $param);
                } else if($name == "BIBLIO_DATA_FULLFILLMENT"){
                    $ins = Gateway::insertSideTask($name, $param);
                } else {
                    $ins = Gateway::insertSideTask($name, "");
                }
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/HistoriqueTachesAnnexes.php');</script>";
                }
            } else {
                ?>
                <div id="divAccepter" style="width: 100%;">
                	<font color="red">Veuillez remplir tous les champs.</font>
                </div>
				<?php
            }
            break;
        }
}


?>