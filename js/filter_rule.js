var doc, op,nb_op;
var id_new=0;
$(document).ready(function(){
	doc = document.getElementById('filter_rule');
	op = document.getElementsByClassName('hidden_field')[0];
	nb_op=-1;
});

function update_operation(element)
{
	while(element.parentElement.childElementCount>1)
	{
		element.parentElement.removeChild(element.nextSibling);
	}
	if(element.value=='OPERATION')
	{
		var p = document.createElement('TABLE');
		p.className='table-config';
		element.parentElement.appendChild(p);
		init_predicate(p);
	}
	else if(element.value!='0')
	{
		for(i=0;i<2;i++)
		{
			var d=op.cloneNode(true);
			d.className='operation';
			d.childNodes[0].name+=nb_op;		
			element.parentElement.appendChild(d);
			nb_op--;
			update_operation(d.childNodes[0]);
		}
	}
}

function update_predicat(element,array)
{
	var p = element.parentElement;
	var selected = element.options[element.selectedIndex].text;
	for (predicat of array){
		if(predicat.code==selected){
			p.nextSibling.innerHTML = '<td>' + predicat.entity + '</td>';
			p.nextSibling.nextSibling.innerHTML = '<td>' + predicat.property + '</td>';
			p.nextSibling.nextSibling.nextSibling.innerHTML = '<td>' + predicat.function_code + '</td>';
			p.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML = '<td>' + predicat.val + '</td>';
		}
	}
	id_new++;
}