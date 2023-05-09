function display()
{
	var s=document.getElementById("rule");
	var request = $.ajax({
		url: "../Composant/"+s.name+".php",
		type: "post",
		data: 'id='+s.value,
		success: function(response){
		  replace(response);
		}
	});
}
function replace(response)
{
	document.getElementById("conf").innerHTML=response;
}
