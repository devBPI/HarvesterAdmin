<?php
$ini = @parse_ini_file("../etc/configuration.ini", true);
if (! $ini) {
    $ini = @parse_ini_file("../etc/default.ini", true);
}
?>
<html lang="fr">
<head>
<!-- Ajout du ou des fichiers javaScript-->
<meta charset="utf-8" />
<link rel="stylesheet" href="../css/style.css" />
	<link rel="stylesheet" href="../css/composants.css" />
	<link rel="stylesheet" href="../css/selectStyle.css" />
	<link rel="stylesheet" href="../css/accueilStyle.css" />
	<link rel="stylesheet" href="../css/formStyle.css" />

<title>Paramétrage</title>
</head>

<?php
include ('../Vue/common/Header.php');
?>

<body>
	<div class="content">
		<FORM action="FiltreRules.php?modify=false" method="post" onsubmit="return confirm('Voulez vous vraiment modifier les ensembles de règles ?');">
			<h2>Règles de filtrage</h2>
			<div class="button_top_div_with_margin">
				<a href="../../Controlleur/Filtre.php" class="buttonlink">&laquo; Retour aux filtres</a>
			</div>
			<div class="sizeable_table">
				<div>
					<?php
						if($mod=='false')
						{ ?>
							<table class='table-config'>
								<tr>
									<th scope="col" style="width:50%">Nom</th>
									<th scope="col" style="width:40%">Entité</th>
									<th scope="col" style="width:10%"></th>
								</tr>
								<tr class='hidden_field'>
									<td>
										<input type='text' aria-label="Nom de la règle" name='namenew'/>
									</td>
									<td>
										<select aria-label="Entité sur laquelle porte la règle" name='entitynew'><option value='0'>Sélectionnez une entité</option>
										<?php foreach($entities as $e)
										{
											echo "<option value='".$e."'>".$e."</option>";
										} ?>
										</select>
									</td>
									<td>
										<button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement.parentElement)'>
											<img alt="Supprimer une cible" src='../ressources/cross.png' width='30px' height='30px'>
										</button>
									</td>
								</tr>
							<?php if(!empty($data))
							{
								foreach($data as $k => $value)
								{ ?>
									<tr>
										<td>
										<?php
										$est_error = false;
										if (isset($array_error)) {
											foreach ($array_error as $error) {
											if ($error["id"] == $value["name"]) {
											$est_error = true; ?>
											<input type="text" aria-label="Nom de la règle en erreur" class="input-error" name="name<?= $value['id'] ?>" value="<?= $value['name']?>"/>

												<?php
											}
											}
										}
										if (!$est_error) { ?>
											<input type="text" aria-label="Nom de la règle" name="name<?= $value['id'] ?>" value="<?= $value['name']?>"/>

										<?php } ?>
										</td>
										<td><select aria-label="Entité sur laquelle porte la règle" name='entity<?= $k?>'><option value='0'>Sélectionnez une entité</option>
										<?php foreach($entities as $e)
										{
											echo "<option value='".$e."' ".(($e==$value['entity'])?'selected':'').">".$e."</option>";
										}?>
										</select></td>
										<td><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement.parentElement)'><img alt="Supprimer une cible" src='../ressources/cross.png'/ width='30px' height='30px'></button></td>
									</tr>
								<?php }
							}
							else
							{
								echo "<div><input type='text' name='-1'/><button class='but' type='button' title='Supprimer une cible' onclick='delete_field(this.parentElement)'><img alt='Supprimer une cible' src='../ressources/cross.png' width='30px' height='30px'></button></div>";
							}?>
							<tr style='background-color:#dbe0e0' id='add_row'><td><button class='ajout but' type='button' title='Ajouter' onclick='add_new_field(this.parentElement.parentElement.parentElement.parentElement)' style='float:left'><img alt="Ajouter" src='../ressources/add.png' width='30px' height='30px'/></button></td><td></td><td><input name='submitted' type='hidden'><input type='submit' value='Valider'/></td></tr></table>
					</div>
				<?php }
				else {
					echo "<table class='table-config'><th width=60%>Nom</th><th width=40%>Entité</th>";
					foreach($data as $key => $value)
					{
						echo "<tr><td>".$value['name']."</td><td>".$value['entity']."</td></tr>";
					}
					echo "</table>
							<div class='button_end_div_with_margin'>
								<a href='FiltreRules.php?modify=false' class=\"submit-button\">Modifier les règles</a>
							</div>
							</div>";
				}?>
		</FORM>
	</div>

	<?php if(!empty($_POST) && isset($array_error) && (count($array_error) == 0)) : ?>
	<div id="page-mask" style="display:block"></div>
	<div class="form-popup" id="validateForm" style="display:block">
		<div class='form-container' id='formProperty'>
		<form action="../Controlleur/Filtre.php" class="form-container" id="formProperty">
			<h3>Modification</h3>
			<div class='form-popup-corps'>
				<p>Les modifications ont bien été enregistrées.</p>
				<button type="submit" name="submitted" class="buttonlink">OK</button>
			</div>
		</form>
		</div>
	</div>
	<?php endif; ?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="../js/toTop.js"></script>
	<script src="../js/add_fields.js"></script>
	<script src="../js/filtres_traductions/entities.js"></script>
</body>
</html>