var elems = document.getElementsByClassName("progress");
update(elems);
setInterval(function()
	{
		update(elems);
	}, 1000);
	
function update(elems)
{
	Array.prototype.forEach.call(elems,function(element) {
	  var request = $.ajax({
		url: "../Composant/ProgressBar.php",
		type: "post",
		data: 'val='+(element.getAttribute('name')).substring(5),
		success: function(response){
			element.innerHTML=response;
		}
	  });
	});
}


