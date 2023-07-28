<?php
	$parcours = array();
	$filtres = array();
	$trads = array();
	$acces = array("INTERNAL"=>$_POST['INTERNAL'] ?? false,"EXTERNAL"=>$_POST['EXTERNAL'] ?? false,"WIFI-BPI"=>$_POST['WIFI-BPI'] ?? false);
	foreach($_POST as $key => $value)
	{
		if(preg_match('/(parcours)/',$key))
		{
			$parcours[$key]=$value;
		}
		if(preg_match('/(list_exclusion)/',$key))
		{
			$filtres[$key]=$value;
		}
		if(preg_match('/(list_translation)/',$key))
		{
			$trads[$key]=$value;
		}
	}
	require_once("../PDO/Gateway.php");
	Gateway::connection();
    $name=$_POST['form_nom_abrege'] ?? null;
    $publicName=$_POST['form_nom_public'] ?? null;
    $publicUrl=$_POST['form_url_publique'] ?? null;
    $grabber=$_POST['form_list_grabber'] ?? null;
    $mapping=$_POST['form_list_mapping'] ?? null;
    $exclusion=$_POST['list_exclusion'] ?? null;  // *
    $translation=$_POST['list_translation'] ?? null; // *
    $url=$_POST['form_URL'] ?? null;
    $urlSet=$_POST['form_URL_set'] ?? null;
    $urlAdd="&set=".$urlSet; 
    $separateur=$_POST['form_separateur'] ?? null;
    $dif=$_POST['form_differentiel'] ?? null;
    $attempts=$_POST['form_max_attempts'] ?? null;
    $timeout=$_POST['form_timeout'] ?? null;
    $business=$_POST['form_business_id'] ?? null;
    $liaison=$_POST['form_additional_configuration_of'] ?? null;
    $commentaire=$_POST['form_commentaires'] ?? null;

    if($exclusion==0){
        $exclusion=NULL;
    }
    if($translation==0){
        $translation=NULL;
    }
    if($liaison==0){
        $liaison=NULL;
    }
    if(!strcmp($url,"")!=0){
        $url=NULL;
    }
    if(!strcmp($urlSet,"")!=0){
        $urlSet=NULL;
    }
    if(!strcmp($attempts,"")!=0){
        $attempts=NULL;
    }
    if(!strcmp($timeout,"")!=0){
        $timeout=NULL;
    }
    if(!strcmp($commentaire,"")!=0){
        $commentaire=NULL;
    }
	
    if(isset($_POST['update'])){     
        if(isset($grabber,$mapping,$id)  && strcmp($name,"")!=0 &&  strcmp($business,"")!=0 && strcmp($publicName,"")!=0 && $mapping>0){
            $exclusion = (($exclusion == null)? "NULL": $exclusion);
            $translation = (($translation == null)? "NULL": $translation);
            $url = $url;
            $urlAdd = $urlAdd;
            $urlSet = $urlSet;
            $separateur = $separateur;
            $dif = (($dif=="false")? "FALSE":"TRUE");
            $publicUrl = $publicUrl;
            $attempts = (($attempts == NULL)? "NULL" : $attempts);
            $timeout = (($timeout == NULL)? "NULL" : $timeout);
            $business = $business;
            $liaison = (($liaison == NULL)? "NULL" : $liaison);
            $publicName = $publicName;
            $commentaire = (($commentaire == NULL)? NULL : $commentaire);

            $query1 = "UPDATE configuration.harvest_grab_configuration SET name = $1, grabber_id = ".$grabber.", url = $2, url_addition = $3, url_set = $4, csv_separator = $5, max_attempts_number=".$attempts.", timeout_sec=".$timeout." WHERE id = (SELECT grab_configuration_id FROM configuration.harvest_configuration WHERE id = ".$id.")";
            $upt1 = Gateway::prepare("update_harvest_grab_configuration", $query1);
            $upt1 = Gateway::executeStatement("update_harvest_grab_configuration", array($name, $url,$urlAdd,$urlSet,$separateur));
            // anciennement $upt1 = Gateway::insert($query1);
            
            $query2 = "UPDATE configuration.harvest_configuration SET name = $1, filter_id= ".$exclusion.", mapping_id= ".$mapping.", translation_id= ".$translation.", differential= ".$dif.", business_base_prefix= '".$business."', additional_configuration_of= ".$liaison.", public_url = $2, note = $3 WHERE ID= ".$id;
            $upt2 = Gateway::prepare("update_harvest_configuration", $query2);
            $upt2 = Gateway::executeStatement("update_harvest_configuration", array($name, $publicUrl,$commentaire));
           // anciennement $upt2 = Gateway::insert($query2);
			
            $query3 = "UPDATE configuration.search_base SET name = $1 WHERE code = (SELECT search_base_code FROM configuration.harvest_configuration WHERE id = ".$id.")";
            $upt3 = Gateway::prepare("update_configuration_search_base", $query3);
            $upt3 = Gateway::executeStatement("update_configuration_search_base", array($publicName));
            // anciennement $upt3 = Gateway::insert($query3);
            
            
            
            Gateway::accesUpdate($id,$acces);
			Gateway::updateParcours($parcours, $id);
            
			if($upt1 && $upt2 && $upt3) {
                echo "<script type='text/javascript'>document.location.replace('../Vue/FicheIndividuelle.php?param=".$id."');</script>";
            }
            else {
                ?>
                <div id="divAccepter" style="top:-3%; left:0%;width:100%; position:absolute;">
                <span style="color:red">Erreur durant la requête (Veuillez vérifier les données saisies)</span>
                </div>
                <?php
            }
        }
        
        else {
        ?>
        <div id="divAccepter" style="top:-3%; left:0%;width:100%; position:absolute;">
            <span style="color:red">Veuillez remplir tous les champs.</span>
        </div>
        <?php
        }
   }
?>
