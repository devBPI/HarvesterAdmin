<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../js/toTop.js"></script>
<script src='../js/translator.js'></script>
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../css/composants.css" />
<title>Test Transformation</title>
</head>
<body>
	<?php
		include("../Vue/Entete.php");
	?>
	<div style="margin-top:5%">
		<div class="right" style="width:40%;height:82.4%;">Sortie<div style="background-color:white;width:100%;height:100%;border:solid 1px black"><pre id="output"></pre></div>
		</div>
		<div class="left" style="width:40%">Entrée<TEXTAREA id="input" style="width:100%;height:40%;border:solid 1px black"></TEXTAREA>
		</div>
		<div class="left" style="width:40%">Patern<TEXTAREA id="patern" style="width:100%;height:40%;border:solid 1px black"></TEXTAREA>
		</div>
		<div style="margin-left:48.5%">
			<input id="boutton" type="submit" class="button primairy-color round" onclick="traduction()"/>
		</div>
	</FORM>

</body>
</html>


