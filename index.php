<html lang="fr">
<h2>
<?php 
	//ini_set("display_errors",1);

	include ("./Composant/ErrorReportingConfig.php"); // . et non .. car à la racine du projet

	$ini = parse_ini_file("etc/configuration.ini");
	if(!$ini)
	{
		$ini = @parse_ini_file("etc/default.ini");
	}
	$section="Connection"
?>
</h2>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="js/toTop.js"></script>
	<!-- Ajout du ou des fichiers javaScript-->
	<meta charset="utf-8" />

	<link rel="stylesheet" href="css/environments/<?php echo $ini['version'];?>-style.css" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="css/composants.css" />
    <title>Connexion</title>
</head>
<body id="haut" style="margin:0 0">
	<div class="entete" style="width:100%;height:7%">
		<h2 style="text-align:center"><?php echo $ini['version'];?></h2>
	</div>
    <div style="box-shadow: 0px 0px 5px #555;background-color: white;width: 40%;height: auto;margin-left: auto;margin-right: auto;margin-top: 10%;margin-bottom: auto;padding-bottom:1%;">
          <br>
           <center><h2>Connexion</h2></center>
        <form action="index.php" method="post">
           <br>
            <div class="right" style="margin-right:40%">Login : <input type="text" name="textLogin" /></div>
            <br><br>
            <div class="right" style="margin-right:40%">Mot de passe :<input type="password" name="textMdp" /></div>
            <input type="submit" style="margin-top:6.5%;margin-left:35%; width:30%;position:static;" class="button primairy-color round" value="Connexion" />
        </form>
        <?php 
            if(isset($_POST['textLogin'],$_POST['textMdp'])){
                if("admin"==$_POST['textLogin'] && "admin"==$_POST['textMdp'])
		{
                     header('Location: Controlleur/Accueil.php');
                }
                else
		{
	?>
			<div id="divAccepter" style="width:100%;">
			<center><font color="red">Login et/ou Mot de Passe inconnu</font></center>
			</div>
	<?php
                }
            }
        ?>
    </div>
</body>
</html>
