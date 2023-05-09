<?php
if (isset($_POST['list_mapping'])) {
    $id = $_POST['list_mapping'];
}
if (isset($_POST['list_exclusion'])) {
    $id = $_POST['list_exclusion'];
}
if (isset($_POST['list_translation'])) {
    $id = $_POST['list_translation'];
}
if (isset($_GET['table']))
{
	$table = $_GET['table'];
}
if (isset($_POST['textArea'])) {
    $text = $_POST['textArea'];
}
if (isset($id_param)) {
    $id = $id_param;
}

require_once ("../Gateway.php");
Gateway::connection();
if (! isset($id)) {
    $id = 0;
}

/* CTLG-378 */
if(isset($table) && $table=="mapping") 
{
    $res = Gateway::getMappingWithId($id); 
}
else
{
    $res = Gateway::getConfiguration($table, $id); // comme avant
}
if($res!=null){
    $nom = $res['name'];
    $def = $res['definition'];
}
?>