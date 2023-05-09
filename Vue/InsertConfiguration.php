<?php
// Connexion et récupération des variables
require_once ("../Gateway.php");
Gateway::connection();

$codeConfig=$_POST['textCodeConfig']; 
$name = $_POST['textName']; // String
$publicName = $_POST['textNomPublic'];
$publicUrl = $_POST['textUrlPublique'];
$grabber = $_POST['list_grabber'];
$mapping = $_POST['list_mapping'];
$exclusion = $_POST['list_exclusion'];
$translation = $_POST['list_translation'];
$url = $_POST['textUrl']; // String
$urlSet = $_POST['textUrlSet']; // String
$urlAdd = "&set=" . $urlSet; // String
$separateur = $_POST['textSeparateur']; // String
$dif = $_POST['differential'];
$attempts = $_POST['textAttempts']; // String
$timeout = $_POST['texTimeout']; // String
$business = $_POST['textBusiness']; // String
$liaison = $_POST['template'];
$commentaire = $_POST['textNote'];

if ($exclusion == 0) {
    $exclusion = NULL;
}
if ($translation == 0) {
    $translation = NULL;
}
if ($liaison == 0) {
    $liaison = NULL;
}
if (! strcmp($url, "") != 0) {
    $url = NULL;
}
if (! strcmp($urlSet, "") != 0) {
    $urlSet = NULL;
}
if (! strcmp($attempts, "") != 0) {
    $attempts = NULL;
}
if (! strcmp($timeout, "") != 0) {
    $timeout = NULL;
}
if (! strcmp($commentaire, "") != 0) {
    $commentaire = NULL;
}

// Vérification de l'action + Update
if (isset($_POST['insert'])) {
    $action = 'insert';
}

if ($action == 'insert') {
    
    echo "<div>Code config: ".$codeConfig."</div>";
    
    if (isset($grabber, $mapping) && strcmp($codeConfig, "") != 0 && strcmp($name, "") != 0 && strcmp($business, "") != 0 && strcmp($publicName, "") != 0) {

        $codeConfig = $codeConfig;
        $name = $name;
        $grabber = $grabber;
        $exclusion = (($exclusion == null) ? "NULL" : $exclusion);
        $mapping = $mapping;
        $translation = (($translation == null) ? "NULL" : $translation);
        $url = $url;
        $urlAdd = $urlAdd;
        $urlSet = $urlSet;
        $separateur = $separateur;
        $dif = (($dif == "false") ? "FALSE" : "TRUE");
        $publicUrl = $publicUrl;
        $attempts = (($attempts == NULL) ? "NULL" : $attempts);
        $timeout = (($timeout == NULL) ? "NULL" : $timeout);
        $business = $business;
        $liaison = (($liaison == NULL) ? "NULL" : $liaison);
        $publicName = $publicName;
        $commentaire = (($commentaire == NULL) ? NULL : $commentaire);

        
        $queryHarvestGrabConfiguration = "INSERT INTO configuration.harvest_grab_configuration (name, grabber_id, url, url_addition, url_metadata_prefix, url_set, csv_separator, max_attempts_number, timeout_sec) VALUES ('Grab_Conf_".$name."', ".$grabber.", $1, $2, NULL, $3, $4, ".$attempts.", ".$timeout.") RETURNING id;";
        
        echo "<div>Requete d'insertion grab : ".$queryHarvestGrabConfiguration."</div>";
        
        
        $ins1 = Gateway::prepare("insert_harvest_grab-configuration", $queryHarvestGrabConfiguration);
        $ins1 = Gateway::executeStatement("insert_harvest_grab-configuration",  array($url,$urlAdd,$urlSet,$separateur));
        // anciennement $ins1 = Gateway::insert($queryHarvestGrabConfiguration);
        
        echo "<div>Requete d'insertion grab executee</div>";
        
        $idHarvestGrabTable = pg_fetch_row($ins1)[0];
        //echo "<div>NEXTVAL: ".$idHarvestGrabTable."</div>";
        
        echo "<div>idHarvestGrabTable=".$idHarvestGrabTable."</div>";
            
        // Formattage code_config
        $accent = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
        $noaccent='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
        
        $toBeReplacedChars =  array("-", "'", " ", "+", "*", "/", ":", ",", ";", ".", "!", "?", ">", "<", "=", "%", "$");
        $replacementChars =   array("_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_");
        
        $codeConfig = strtr($codeConfig,$accent,$noaccent); 
        $codeConfig = str_replace($toBeReplacedChars, $replacementChars, $codeConfig);
        $codeConfig = strtoupper($codeConfig);
       
        
        echo "<div>Nouveau code config = ".$codeConfig."</div>";
        

        $queryHarvestConfiguration = "INSERT INTO configuration.harvest_configuration (code, name, grab_configuration_id, filter_id, mapping_id, translation_id, differential, public_url, business_base_prefix, additional_configuration_of, note) VALUES ('" . $codeConfig . "', $1, " . $idHarvestGrabTable . ", " . $exclusion . ", " . $mapping . ", " . $translation . ", " . $dif . ", $2, '" . $business . "', " . $liaison . ", $3);";
      
        echo "<div>Requete d'insertion harvest_configuration : ".$queryHarvestConfiguration."</div>";
        
        $ins2 = Gateway::prepare("insert_harvest_configuration", $queryHarvestConfiguration);
        $ins2 = Gateway::executeStatement("insert_harvest_configuration", array($name, $publicUrl,$commentaire));
        // anciennement $ins2 = Gateway::insert($queryHarvestConfiguration);
        
        echo "<div>Requete d'insertion harvest_configuration executee</div>";

        // On teste si il existe deja une base de recherche ayant exactement pour nom $publicName, on tente de recuperer le code
        $searchBaseCode = Gateway::getSearchBaseCodeForName($publicName);
        if ($searchBaseCode == NULL || $searchBaseCode == '') {
            // si on ne recupere rien alors on cree et insere un nouveau couple (code,name) dans la table search_base

            $searchBaseCode = strtr($publicName,$accent,$noaccent); 
            $searchBaseCode = str_replace($toBeReplacedChars, $replacementChars, $searchBaseCode);
            $searchBaseCode = strtoupper($searchBaseCode);
            
            if ($searchBaseCode != NULL && $searchBaseCode != '') {
                // Insertion d'une nouvelle ligne dans la table seach_base
                Gateway::insertSearchBase($searchBaseCode, $publicName);
            }
        } 
        
        
        echo "<div>Trace intermediaire</div>";
             
        if ($searchBaseCode != NULL && $searchBaseCode != '') {
            
            echo "<div>searchBaseCode=".$searchBaseCode."</div>";
            echo "<div>name=".$name."</div>";
            
            // on termine dans  par un update de harvest_configuration.search_base_code avec le code retrouve (ou celui nouvellement cree)
            $queryUpdateSearchBaseCode = "UPDATE configuration.harvest_configuration SET search_base_code = '". $searchBaseCode ."'  WHERE name = $1;";
            
            
            echo "<div>query3=".$queryUpdateSearchBaseCode."</div>";
            
            $ins3 = Gateway::prepare("update_harvest_configuration_search_base_code", $queryUpdateSearchBaseCode);
            $ins3 = Gateway::executeStatement("update_harvest_configuration_search_base_code", array($name));
            // anciennement : $ins3 = Gateway::insert($queryUpdateSearchBaseCode);
            
            echo "<div>query3 executed</div>";
        }
        
        echo "<div>Avant test final...</div>";
        echo "<div>ins1=".$ins1."</div>";
        echo "<div>ins2=".$ins2."</div>";
        echo "<div>ins3=".$ins3."</div>";
        
        if ($ins1 && $ins2 && $ins3) {
            echo "<div>Test final OK !</div>";
            
            echo "<script type='text/javascript'>document.location.replace('../Controlleur/Accueil.php');</script>";
        } else {
            echo "<div>Test final KO</div>";
            ?>
<div id="divAccepter"
	style="top: -3%; left: 0%; width: 100%; position: absolute;">
	<font color="red">Erreur durant la requête (Veuillez vérifier les
		données rentrées)</font>
</div>
<?php
        }
    } 
    else {
        ?>
<div id="divAccepter"
	style="top: -3%; left: 0%; width: 100%; position: absolute;">
	<font color="red">Veuillez remplir tous les champs.</font>
</div>
<?php
    }
}
?>
