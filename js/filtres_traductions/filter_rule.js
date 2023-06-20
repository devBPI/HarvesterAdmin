var doc, op,nb_op = 0;
var id_new= 0;
var seuil = 5;
$(document).ready(function(){
	doc = document.getElementById('filter_rule');
	op = document.getElementsByClassName('hidden_field')[0];
	nb_op=-1; // Pas négatif pour ne pas interférer avec les valeurs positives de l'arbre de base
});

function update_operation(element, profondeur= 0)
{
	if (profondeur < (seuil-1)) {
		while (element.parentElement.childElementCount > 1) {
			element.parentElement.removeChild(element.nextSibling);
		}
		if (element.value == 'OPERATION') {
			var p = document.createElement('TABLE');
			p.className = 'table-config';
			element.parentElement.appendChild(p);
			init_predicate(p);
		} else if (element.value != '0') {
			for (i = 0; i < 2; i++) {
				var d = op.cloneNode(true);
				if ((profondeur + 1) % 2 == 0)
					d.className = "operation operation_even operation" + (profondeur+1);
				else
					d.className = "operation operation" + (profondeur+1);
				d.childNodes[0].name += nb_op;
				element.parentElement.appendChild(d);
				// JQuery pour suppression de l'ancien EventListener
				$("[name='" + d.childNodes[0].name + "']").removeAttr("onchange");
				// JQuery pour ajout de l'EventListener
				$("[name='" + d.childNodes[0].name + "']").on("change", function () {
					return update_operation(this, profondeur+1);
				});
				nb_op--;
				update_operation(d.childNodes[0], profondeur);
			}
		}
	} else {
		element.value = "OPERATION";
		return alert("Seuil de profondeur de l'arbre atteint (profondeur maximale : " + seuil + ").");
	}
}

function update_predicat(element,array)
{
	var p = element.parentElement;
	var selected = element.options[element.selectedIndex].text;
	for (predicat of array){
		if(predicat.code==selected){
			p.nextElementSibling.innerHTML = "<td>" + predicat.entity.replace("_", "_<wbr>") + "</td>";
			p.nextElementSibling.nextElementSibling.innerHTML = "<td>" + predicat.property.replace("_", "_<wbr>") + "</td>";
			p.nextElementSibling.nextElementSibling.nextElementSibling.innerHTML = '<td>' + predicat.function_code.replace("_", "_<wbr>") + '</td>';
			p.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling.innerHTML = '<td>' + predicat.val.replace("_", "_<wbr>") + '</td>';
		}
	}
	id_new++;
}
