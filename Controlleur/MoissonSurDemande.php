<!-- Il y a différentes div et boutons de menus car je n'ai encore décidé lesquels j'utiliserai -->
<html style="overflow-y: auto; overflow-x: hidden;">
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
require_once ("../PDO/Gateway.php");
Gateway::connection();

$section = "Moisson sur Demande";
include ("../Vue/MoissonSurDemande.php");

$configwithoutfile = $_POST['configuration-select-whithout-file'];
$configwithfiles = $_POST['configuration-select-whith-files'];
$fileSelected = basename($_FILES['file-to-upload']['name']);
$tmpFileSelectedName = $_FILES['file-to-upload']['tmp_name'];

// echo '<div>EN COURS DE DEVELOPPEMENT</div>';
// echo '<div>config sans fichier = '.$configwithoutfile.'</div>';
// echo '<div>config avec fichier = '.$configwithfile.'</div>';
// echo '<div>fichier selectionne = '.$fileSelected.'</div>';
// echo '<div>tmp name fichier selectionne = '.$tmpFileSelectedName.'</div>';

// Recuperation de l'action en cours
$action = '';

if (isset($_POST['launch-without-file-button'])) {
    $action = 'launch_without_file';
} else {
    if (isset($_POST['launch-with-file-button'])) {
        $action = 'launch_with_file';
    }
}

if($_FILES['input_files']['name'][0]!=''){
    foreach($_FILES['input_files'] as $key => $attribute){
        foreach($_FILES['input_files'][$key] as $j => $value){
            $files_array[$j][$key] = $value;
        }
    }
}

if($_POST['formIgnoreValues']!=''){
    $ignoredValues = str_split($_POST['formIgnoreValues']);
}

// echo '<div>ACTION='.$action.'</div>';

// Si reload (voir HistoriqueMoisson.php)
if (isset($_POST['suppr']) && $_POST['suppr']) {
    Moisson::deleteMoisson($_POST['suppr']);
    $action='launch_without_file';
}
switch ($action) {
    case 'launch_without_file':
        {
            
            $statusfortaskinsert = 'TO_HARVEST';
            
            
            $islaunchwithoutgrab = isset($_REQUEST['type-moisson-without-file']);
            if ($islaunchwithoutgrab) {
                $statusfortaskinsert = 'GRABBED';
            }
            
            if ($configwithoutfile != 0) {
                $ins = Gateway::insertMoissonWithStatus($configwithoutfile, $statusfortaskinsert);
                if ($ins) {
                    echo "<script type='text/javascript'>document.location.replace('../Controlleur/HistoriqueMoisson.php');</script>";
                }
            } else {
                ?>
                <div id="divAccepter" style="width: 100%;">
                	<font color="red">Veuillez renseigner une configuration de moisson a lancer.</font>
                </div>
                <?php
        }
        break;
     }
    case 'launch_with_file':
    {
        $allfilesuploaded = true;
        if($_POST['formTypeCSV']=="simple"){
            $total = 2;
        } else {
            $total = count($_FILES['input_files']['name']);
        }
        for( $i=0 ; $i < $total-1 ; $i++ ) {
            if(!in_array($i,$ignoredValues)){
                // En fait lorsque l'on est ici le fichier a deja ete uploade (methode POST) 
                // et son contenu est dans un fichier temporaire (fichier dont le nom est visible via tmp_name)
                $fileSelected = basename($_FILES['input_files']['name'][$i]);
                
                if ($configwithfiles != 0 && $fileSelected != null) {
                    $fileTmpName = $_FILES['input_files']['tmp_name'][$i];
                    $fileName = $_FILES['input_files']['name'][$i];
                    
                    
                    // Verifications multiples	
                    
                    $erreur = '';
                    // On fait un tableau contenant les extensions autorisees.
                    $possible_extensions = array('.txt','.csv');
                    $actual_extension = strrchr($fileName, '.');
                    if(!in_array($actual_extension, $possible_extensions)) 
                    {
                        $allfilesuploaded = false;
                        $erreur = 'Le fichier doit etre de type txt ou csv.';
                    }
                    
                    // Taille maximum (en octets)
                    $taille_maxi = 10000000; // 10 Mo
                    // ATTENTION : la taille max du fichier doit etre bien compatble avec la valeur de upload_max_filesize (et post_max_size) dans php.ini //
                    
                    //Taille du fichier
                    $taille = filesize($fileTmpName);
                    if($taille>$taille_maxi)
                    {
                        $allfilesuploaded = false;
                        $erreur = 'Le fichier est trop volumineux (10 Mo maximum).';
                    }
                    
                    // Verification du type mime (inoperationnel car trop peu fiable)
                    // $possible_mime_types = array('text/plain','text/csv', 'text/x-csv', 'text/comma-separated-values','application/csv');
                    // $actual_type_mime = $_FILES['file-to-upload']['type'];
                    // echo '<div>TYPE MIME = '.$actual_type_mime.'</div>';
                    
                    // if(!in_array($actual_type_mime, $possible_mime_types))
                    // {
                    //    $erreur = 'Le fichier doit etre de type mime correspondant a un fichier txt ou csv (actuellement : '.$actual_type_mime.')';
                    // }
                    
                    if($erreur != '')
                    {
                        $allfilesuploaded = false;
                        echo '<div id="divAccepter" style="width: 100%;"><font color="red">Echec du telechargement : '.$erreur.'</font></div>';
                    }
                    else
                    {
                        
                        $dossier = '.';
                        $rootfolder = '/data/catalog_data/';
                        
                        // Recuperation du code de la base de recherche qui sera le repertoire 
                        // dans lequel le fichier sera depose
                        $baseCodeResult = Gateway::findSearchBaseAndConfigCodesForConfig($configwithfiles);
                        $searchBaseCode = $baseCodeResult[0]['base_code'];
                        $configurationCode = $baseCodeResult[0]['config_code'];
                        
                        
                        $dossier = $rootfolder.$searchBaseCode.'/'.$configurationCode.'/';
                        
                        ////////////////////////////////////////////////////////////////////
                        $uploadFile = $dossier . $fileName;
                        
                        // echo '<div>fichier de destination = '.$uploadFile.'</div>';
                        
                        
                        
                        $uploadsuccess = false;
                        
                        if (is_uploaded_file($fileTmpName)) {
                        // Ici le fichier est telecharge avec succes.
                        
                        // echo "File ". $_FILES['file-to-upload']['name'] ." téléchargé avec succès.\n";
                        // echo "Affichage du contenu\n";
                        // readfile($_FILES['file-to-upload']['tmp_name']);
                        
                        // Maintenant il faut deplacer le contenu du fichier temporaire
                        // vers l'emplacement voulu.
                            // echo "TmpName : ".$fileTmpName." UploadFile : ".$uploadFile;
                            $uploadsuccess = move_uploaded_file($fileTmpName, $uploadFile);
                        } else{
                            $allfilesuploaded = false;
                            // echo "File ". $fileName ." non téléchargé.\n";
                            // echo "Erreur =  ".$_FILES['input_files']['error'][$i].'';
                        }
                        
                        // En cas de volonte de transfert du fichier sur un autre serveur
                        // Il faudra utiliser le protocole sftp via une commande curl (voir curl_exec)
                        if(!$uploadsuccess)
                        {
                            $allfilesuploaded = false;
                            ?>
                            <div id="divAccepter" style="width: 100%;">
                                <font color="red">Echec de l'upload.</font>
                            </div>
                            <div>
                                <font color="red">
                                <?php 
                                // Ci-dessous des logs a activer en cas d'erreur
                                // echo print_r( $_FILES);
                                // $tmpFileSelectedName = $_FILES['file-to-upload']['error'];
                                ?>
                                </font>
                            </div>
                            <?php
                        }
                    }
                } else {
                    ?>
                    <div id="divAccepter" style="width: 100%;">
                        <font color="red">Veuillez renseigner une configuration de moisson ainsi qu'un fichier a charger.</font>
                    </div>
                    <?php
                }
            }
        }
        if($allfilesuploaded==true){
            $insertOk = Gateway::insertMoisson($configwithfiles);
            if (!$insertOk) 
            {
                ?>
                    <div id="divAccepter" style="width: 100%;">
                        <font color="red">Erreur lors de l'insertion de la moisson.</font>
                    </div>
                <?php
                $allfilesuploaded = false;
            } else {
                echo "<script type='text/javascript'>document.location.replace('../Controlleur/HistoriqueMoisson.php');</script>";
            }
        }
    }
 }

?>
