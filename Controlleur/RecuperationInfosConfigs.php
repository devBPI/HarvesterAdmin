<?php
    if (isset($_GET['param'])) {
        $id = $_GET['param'];
    }
    require_once("../Gateway.php");
    Gateway::connection();
    $data = Gateway::getInfoConfig($id);
    $data['parcours']=Gateway::getParcours($id);
	$data['trad']=Gateway::getTranslation($id);
    include("../Vue/RecuperationInfosConfigs.php");
?>
