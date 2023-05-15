<!-- Il y a différentes div et boutons de menus car je n'ai encore décidé lesquels j'utiliserai -->
<html style="overflow-y: auto; overflow-x: hidden;">
<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
require_once ("../PDO/Gateway.php");
Gateway::connection();

if (! empty($_GET['id'])) {
    $idSup = $_GET['id'];
    $supp = Gateway::deleteMoissonPlanning($idSup);
}
$dowData;
for ($i = 1; $i <= 7; $i ++) {
    $dowData[$i] = Gateway::getMoissonPlanifForEveryDayOfWeek($i);
}
$section = "Planning des moissons";
include("../Vue/planning_moissons/PlanningMoisson.php");
?>