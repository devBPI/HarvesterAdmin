var statuselems = document.getElementsByClassName("statusprogress");
updateStatuses(statuselems);
setInterval(function()
	{
		updateStatuses(statuselems);
	}, 1000);
	
function updateStatuses(statuselems)
{
	Array.prototype.forEach.call(statuselems,function(statuselement) {
	  var request = $.ajax({
		url: "../Composant/HarvestTaskStatus.php",
		type: "post",
		data: 'val='+(statuselement.getAttribute('name')).substring(5),
		success: function(response){
			statuselement.innerHTML=response;
		}
	  });
	});
}


