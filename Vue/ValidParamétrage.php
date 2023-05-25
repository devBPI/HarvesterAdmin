<html>
<?php
session_start();
require_once("../PDO/Gateway.php");
Gateway::connection();
if(isset($_POST["submitted"])){
    Gateway::insert("UPDATE configuration." . $_GET['table'] . " SET name = '" . $_SESSION['new_name'] . "',definition = '" . implode(PHP_EOL,$_SESSION["session_properties"]) . "' WHERE id='" . $_GET['id'] . "'");
    switch($_GET['table'])
    {
        case 'mapping': $table="Mapping";break;
        case 'exclusion': $table="Filtre";break;
        case 'translation': $table="Traduction";break;
    }
    header('Location: ../Controlleur/'.$table.'.php');
}
if (isset($_GET['param'])) {
    $id_config = $_GET['param'];
}
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/codeEditor.css" />
    <link rel="stylesheet" href="../css/formStyle.css" />		
	<title>Paramétrage : Modification</title>
</head>


<body name="haut" id="haut">
<?php
$table = $_GET['table'];
$id = $_GET['id'];


if(isset($table,$id))
{
    
    if($table == "mapping")
    {
        $conf = Gateway::getMappingWithId($id);
    }
    else
    {
        $conf = Gateway::getConfiguration($table,$id); // comme avant
    }
    
	$def = $conf['definition'];
	$nom = $conf['name'];
}
$title = null;
if ($table == "exclusion") {
    $title = "un Filtre";
} else if ($table == "translation") {
    $title = "une Traduction";
} else if ($table == "mapping") {
    $title = "un Mapping";
}
$section = "Modification d'" . $title;
include ('../Vue/Header.php');
include 'affichageParamétrage.php';

$properties_list = $_SESSION["session_properties"];
$def_preview = implode(PHP_EOL,$properties_list);
?>

<div class="content">
    <div class="triple-column-container">
		<div class="column" style="height:80px">
			<?php echo (isset($nom))? 
				"<a href=\"../Vue/ModifParamétrage.php?table=".$table."&id=".$id."\" class=\"buttonlink\">&laquo; Retour aux modifications</a>":"";	
			?>
		</div>
		<div class="column" style="height:80px">
			<H3><?php echo $_SESSION['new_name']; ?></H3>
		</div>
		<div class="column" style="height:80px">
		<div onclick="openForm()" class="buttonlink" style="float:right">Valider le mapping</div>
		</div>
	</div>
    <div class="double-eq-column-container">
        <div class="column-full" style="text-align:left">
            <H3>Original</H3>
            <textarea id="code_original" name="code_original"><?php echo (isset($def))? $def:"";?></textarea>
        </div>
        <div class="column-full" style="text-align:left">
            <H3>Prévisualisation</H3>
            <textarea id="code_preview" name="code_preview"><?php echo (isset($def_preview))? $def_preview:"";?></textarea>
        </div>
    </div>	
</div>
<div id="page-mask"></div>
<div class="form-popup" id="validateForm">
    <form action="" method="post" class="form-container" id="formProperty">
        <h3>Modification</h3>
        <p>Les modifications ont été enregistrées.</p>
        <div class="row">
            <button type="submit" name="submitted" class="btn" style="float:right">OK</button>
        </div>
    </form>
</div>

	<!-- Ajout des scripts -->
	<script language="javascript" type="text/javascript" src="https://codemirror.net/5/lib/codemirror.js"></script>
	<script language="javascript" type="text/javascript" src="https://codemirror.net/5/mode/perl/perl.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/pop_up.js"></script>
    <script src="../js/mapping.js"></script>
	<script>
		$('select[name="list_exist_propriety"]').on('change',function(){
			var selectIndex=$('select option:selected').val();
			var content = <?php echo json_encode($properties_content); ?>;
			$('#exist_content').val(content[selectIndex-1]);
		});

		var editor_original = CodeMirror.fromTextArea(document.getElementById('code_original'), {
			lineNumbers: true,
			lineWrapping: true,
			matchBrackets: true,
			theme: 'ambiance',
			scrollbarStyle: null,
			readOnly: true,
		});

		var editor_preview = CodeMirror.fromTextArea(document.getElementById('code_preview'), {
			lineNumbers: true,
			lineWrapping: true,
			matchBrackets: true,
			theme: 'ambiance',
            readOnly: true,
		});

		$( document ).ready(function(){
			var lines_orig = editor_original.getValue().split('\n');
			var lines_prev = editor_preview.getValue().split('\n');
			for(var i=0 ; i<lines_orig.length ; i++){
				$copie = false;
				for(var j=0; j<lines_prev.length ; j++){
					if(lines_orig[i]==lines_prev[j]){
						$copie = true;
					}
				}
				if($copie==true){
					editor_original.doc.removeLineClass(i,'background','CodeMirror-changedline-o-background');
				} else {
					editor_original.doc.addLineClass(i,'background','CodeMirror-changedline-o-background');
				}
			} 
			for(var i=0 ; i<lines_prev.length ; i++){
				$copie = false;
				for(var j=0; j<lines_orig.length ; j++){
					if(lines_prev[i]==lines_orig[j]){
						$copie = true;
					}
				}
				if($copie==true){
					editor_preview.doc.removeLineClass(i,'background','CodeMirror-changedline-p-background');
				} else {
					editor_preview.doc.addLineClass(i,'background','CodeMirror-changedline-p-background');
				}
			}
			<?php $properties_content["list_exist_propriety"] = $_POST['list_exist_propriety']; ?>
		});
	</script>
</body>
<!-- Fin du body -->

</html>



