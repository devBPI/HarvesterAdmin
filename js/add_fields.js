function add_new_field(elt)
{
	if(elt.tagName!="TABLE")
	{
		if(elt.childNodes[0].nodeType==1)
		{
			var d = elt.childNodes[0].cloneNode(true);
			d.className="";
			setNewName(d, elt.childNodes[1].childElementCount);
			elt.childNodes[1].appendChild(d);
		}
		else
		{
			var d = elt.childNodes[1].cloneNode(true);
			d.className="";
			setNewName(d, elt.childNodes[3].childElementCount);
			elt.childNodes[3].appendChild(d);
		}
	}
	else
	{
		if(elt.childNodes[1].childNodes[2].nodeType==1)
		{
			var d = elt.childNodes[1].childNodes[2].cloneNode(true);
			var nb = elt.childNodes[1].childElementCount;
		}
		else
		{
			var d = elt.childNodes[1].childNodes[1].cloneNode(true);
			var nb = elt.childNodes[1].childElementCount;
		}
		d.className="";
		setNewName(d, nb);
		elt.childNodes[1].insertBefore(d,document.getElementById("add_row"));

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
				elt.name+=nb;
			}
		}
	});
}
function delete_field(elt)
{
	if((elt.parentElement.tagName!="TBODY" && elt.parentElement.childElementCount>1) || elt.parentElement.childElementCount>4)
	{
		elt.parentElement.removeChild(elt);
	}
}