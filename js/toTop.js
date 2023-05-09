$().ready(function()
{
	$("#tricolor").load("../Vue/Tricolor.php");
	var $tricolordiv = $("#tricolor");
	setInterval(function()
	{
		$tricolordiv.load("../Vue/tricolor.php");
	}, 1000); // Refresh scores every seconds
});
