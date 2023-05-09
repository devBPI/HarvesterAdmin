function restart()
{
	if((confirm("Voulez vous vraiment arrêter le moissonneur ?")))
	{
		var request = $.ajax({
		url: "../Controlleur/Reboot.php",
		type: "post",
		success: function(response){
			alert("Le moissonneur à été arrêté");
		}
		});
	}
}