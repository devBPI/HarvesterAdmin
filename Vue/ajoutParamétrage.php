<html>
<?php
require_once("../PDO/Gateway.php");
Gateway::connection();
if(isset($_POST["ajouter"])){
    $insQuery = "INSERT INTO configuration." . $_GET["table"] . " (name, definition) VALUES ( '" . $_POST["map_name"] . "', '" . $_POST["map_content"] . "' ) RETURNING id;";
    $ins = Gateway::insert($insQuery);
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
    <!-- ajout du ou des fichiers CSS-->
    <title>Paramétrage : Ajout</title>
</head>

<?php
$table = $_GET['table'];
$text = @str_replace("'", "''", $_POST['textArea']);
$name = @str_replace("'", "''", $_POST['name']);
$insQuery = "INSERT INTO configuration." . $table . " (name, definition) VALUES ( '" . $name . "', '" . $text . "' ) RETURNING id;";
$title = null;
if ($table == "exclusion") {
    $title = "un Filtre";
} else if ($table == "translation") {
    $title = "une Traduction";
} else if ($table == "mapping") {
    $title = "un Mapping";
}
$section = "Ajout d'" . $title;
include ('../Vue/common/Header.php');

?>

<body>
    <div class="content">
        <div class="button_top_div_with_margin">
            <a href="../Controlleur/Mapping.php" class="buttonlink">&laquo; Retour</a>
        </div>
        <div class="cartouche-solo" style="width:auto;height:auto;padding:5%">
            <div class="row">
                <div class="col-25">
                    <label for="mapping">Nom du mapping</label>
                </div>
                <div class="col-50">
                    <input type="text" id="name" name="name" placeholder="nom du mapping..." />
                </div>
                <div class="col-25">
                    <div onclick="openForm()" class="buttonlink" style="float:right">Valider le mapping</div>
                </div>
            </div>
        </div>
        <div class="column-full" style="text-align:left">
            <H3>Éditeur de texte</H3>
            <textarea id="code_preview" name="code_preview"><?php echo (isset($def_preview))? $def_preview:"";?></textarea>
        </div>

        <div id="page-mask"></div>
        <div class="form-popup" id="validateForm">
            <form action="" method="post" class="form-container" id="formProperty">
                <div class='form-container' id='formProperty'>
                    <h3>Modification</h3>
                    <div class="form-popup-corps">
                        <p>Les modifications ont bien été enregistrées.</p>
                        <button class="buttonlink" type="submit" name="ajouter" value="submit">OK</button>
                        <input id="map_name" name="map_name" type="hidden" value=""/>
                        <input id="map_content" name="map_content" type="hidden" value=""/>
                    </div>
                </div>
            </form>
        </div>

    </div>

    	<!-- Ajout des scripts -->
    <script type="text/javascript" src="https://codemirror.net/5/lib/codemirror.js"></script>
	<script type="text/javascript" src="https://codemirror.net/5/mode/perl/perl.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script>
        function openForm() {
            document.getElementById("validateForm").style.display = "block";
            document.getElementById("page-mask").style.display = "block";
            var namemap = document.getElementById("name").value
            if(document.getElementById("name").value==""){
                namemap = "default_name_mapping";
            }
            document.getElementById("map_name").value = namemap;
            document.getElementById("map_content").value = editor_preview.getValue();
        }

		$('select[name="list_exist_propriety"]').on('change',function(){
			var selectIndex=$('select option:selected').val();
			var content = <?= json_encode($properties_content ?? null) ?>;
			$('#exist_content').val(content[selectIndex-1]);
		});

		var editor_preview = CodeMirror.fromTextArea(document.getElementById('code_preview'), {
			lineNumbers: true,
			lineWrapping: true,
			matchBrackets: true,
			theme: 'ambiance',
		});

	</script>
</body>
<!-- Fin du body -->

</html>



