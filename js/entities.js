var elt;
var nb=0;
function display_entity(element)
{
	elt=element;
	$(elt).removeClass("input-error");
	if(elt.value!=0)
	{
		elt=elt.parentNode;
		var request = $.ajax({
			url: "../Composant/Property.php",
			type: "post",
			data: 'id='+elt.children[0].value,
			success: function(response){
			  replace(response);
			}
		});
	}
}

function display_rules(element)
{
	elt=element;
	elt.parentElement.nextSibling.innerHTML=null;
	if(elt.value!=0)
	{
		elt=elt.parentNode;
		var request = $.ajax({
			url: "../Composant/Rules.php",
			type: "post",
			data: 'selected='+element.options[element.selectedIndex].value+'&id='+elt.childNodes[0].value,
			success: function(response){
			  replace(response);
			}
		});
	}
}

function replace(response)
{
	var d=document.createElement("select");
	d.name="property"+nb;
	d.className="property";
	d.setAttribute('required', '');
	nb++;
	d.innerHTML=response;
	elt.nextElementSibling.innerHTML = "";
	elt.nextElementSibling.appendChild(d);
}

$(document).ready(function(){
	var doc = document.getElementsByClassName('entity');
	Array.prototype.forEach.call(doc,function(element) {
		if(element.getAttribute('name')=='pred') {
			elt=element.children[2];
		}
		else {
			elt=element.children[0];
		}
		if(element.id!="new") {
			var request = $.ajax({
				url: "../Composant/Property.php",
				type: "post",
				data: "id="+elt.children[0].value+"&selected="+element.id,
				success: function(response){
					replace(response);
				},
				async:false
			});
		}
	});
});