<?php
    if (isset($_GET['param'])) {
        $id = $_GET['param'];
    }
    require_once("../PDO/Gateway.php");
    Gateway::connection();
    $data = Gateway::getInfoConfig($id);
    $data['parcours']=Gateway::getParcours($id);
	$data['trad']=Gateway::getTranslation($id);
    $data['filters']=Gateway::getFilterByConf($id);
    include("../Vue/configuration/RecuperationInfosConfigs.php");
?>
