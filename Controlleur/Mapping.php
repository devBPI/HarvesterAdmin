<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<?php
if (isset($_GET['param'])) {
    $id_param = $_GET['param'];
}
$table = "mapping";
$id;
$def;
require_once ("../Gateway.php");
Gateway::connection();
$data = Gateway::getMapping();
include ('../Controlleur/Parametre.php');
if (isset($id)) {
    $dataConf = Gateway::getNomConfig($table, $id);
}
$section = "Mapping";
$param = " maping";
include ('../Vue/Parametre.php');
?>


