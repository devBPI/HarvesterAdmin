$().ready(function()
{
	$("#tricolor").load("../Vue/common/Tricolor.php");
	var $tricolordiv = $("#tricolor");
	setInterval(function()
	{
		$tricolordiv.load("../Vue/common/Tricolor.php");
	}, 1000); // Refresh scores every seconds
});
