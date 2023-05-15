<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
	$ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="../../css/style.css" />
	<link rel="stylesheet" href="../../css/composants.css" />
	<link rel="stylesheet" href="../../css/filtreStyle.css" />
	<link rel="stylesheet" href="../../css/accueilStyle.css" />
	<title>Paramétrage</title>
</head>

<?php
include('../Vue/Header.php');
?>

<body>
<div class='content'>
	<div class='hidden_field' id='operation'><select name ='operator' onchange='update_operation(this)'>
			<option value='OPERATION'>OPERATION</option>
			<option value='OR'>OR</option>
			<option value='AND'>AND</option>
		</select>
	</div>
	<FORM action="FiltreTree.php?modify=<?php echo $id;?>" method="post" id='filter_rule'>
		<div class="triple-column-container">
			<div class="column">
				<a href="../../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour filtre</a>
			</div>
			<div class="column">
				<h3><?php echo $name;?></h3>
			</div>
			<div class="column">
				<input type='submit'/>
			</div>
		</div>

		<?php if(isset($data))
		{
			$GLOBALS['nb']=0;
			treeDisplay($data);
		}
		?>
	</FORM>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="../../js/toTop.js"></script>
<script src="../../js/predicate.js"></script>
<script src="../../js/filter_rule.js"></script>
</body>
</html>