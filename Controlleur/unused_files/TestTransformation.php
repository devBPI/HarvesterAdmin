<?php
// Controlleur inutilisé au 20/06/2023 (car page inaccessible depuis BO, sauf si l'utilisateur possède le lien)
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
require_once ("../PDO/Gateway.php");
Gateway::connection();
$section = "Test Transformation";
include ('../Vue/unused_files/TestTransformation.php');
?>


