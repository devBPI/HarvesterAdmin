var set=true;
$().ready(function(){
	display();
});

function display()
{
	var c ="0=";
	var category = document.getElementById('category');
	Array.prototype.forEach.call(category.childNodes,function(element){
			if(element.type=="checkbox" && element.checked)
			{
				c+=element.id+"&"+element.id+"=";			
			}
	});
	console.log(c);
	var request = $.ajax({
		url: "../Composant/SelectDestination.php",
		type: "post",
		data: c,
		success: function(response){
		  replace(response);
		}
	});
}
function replace(response)
{
	Array.prototype.forEach.call(document.getElementsByClassName("select_destination"),function(element) {
		if(set)
		{
			var val = element.id;		
		}
		else
		{
			var val=element.value;
		}
		element.innerHTML=response;
		element.value=val;
	});
	set=false;
}

