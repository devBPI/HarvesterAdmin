function add_new_field(elt, jsEventPage) {
	console.log(elt);
	let d = null;
	let pos_node = 1;
	if(elt.tagName!="TABLE")
	{
		if(elt.childNodes[0].nodeType==1)
		{
			d = elt.childNodes[0].cloneNode(true);
			pos_node = 1;
		}
		else
		{
			d = elt.childNodes[1].cloneNode(true);
			pos_node = 3;
		}
		d.className="";
		setNewName(d, elt.childNodes[pos_node].childElementCount);
		elt.childNodes[pos_node].appendChild(d);
	}
	else
	{
		let d = null;
		let nb = elt.childNodes[1].childElementCount;
		if(elt.childNodes[1].childNodes[2].nodeType==1) {
			d = elt.childNodes[1].childNodes[2].cloneNode(true);
		}
		else {
			d = elt.childNodes[1].childNodes[1].cloneNode(true);
		}
		d.className="";
		d.id = d.id + nb;
		setNewName(d, nb);
		elt.childNodes[1].insertBefore(d,document.getElementById("add_row")); // Ajout de la nouvelle ligne
		// Màj de l'event javascript si nécessaire
		if (jsEventPage == "filtre_predicat") {
			let k = document.getElementsByName("function" + nb)[0];
			k.addEventListener("change", function () {
				display_valueBox(this, nb, "")
			});
		}
	}
}
function setNewName(element,nb)
{
	Array.prototype.forEach.call(element.childNodes,function(elt){
		if(elt.tagName!="SELECT" && elt.childElementCount>0)
		{
			setNewName(elt,nb);
		}
		else
		{
			switch(elt.type)
			{
				case "checkbox":
					elt.checked=false;
				case "select":
					elt.value=0;
			}
			if(elt.name!="value-1" && elt.type != 'checkbox'){
				elt.required = true;
			}
			if(elt.name=="newEnt"){
				elt.name = "entity" + nb;
			} else {
				elt.name += nb;
			}
		}
		if (elt.id) {
			elt.id = elt.id + nb; // Notamment pour <td id="valueBox"> dans FiltrePredicat.php
		}
	});
}
function delete_field(elt)
{
	if((elt.parentElement.tagName!="tbody" && elt.parentElement.childElementCount>0) || elt.parentElement.childElementCount>4)
	{
		elt.parentElement.removeChild(elt);
	}
}