var elt;
var nb=0;
function setPredicate(response)
{
	elt.innerHTML=response;
}
$(document).ready(function(){
	var doc = document.getElementsByClassName('predicat');
	Array.prototype.forEach.call(doc,function(element){
		elt=element;
		var request = $.ajax({
			url: "../Composant/Predicat.php",
			type: "post",
			data:'num='+nb,
			success: function(response){
			  setPredicate(response);
			},
			async:false
		});
		nb++;
	});
});
function init_predicate(element)
{
	elt=element;
	var parts = window.location.search.substr(1).split("&");
	var $_GET = {};
	for (var i = 0; i < parts.length; i++) {
		var temp = parts[i].split("=");
		$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
	}
	var request = $.ajax({
		url: "../Composant/Predicat.php",
		type: "post",
		data:'id='+$_GET['id']+'&num='+nb,
		success: function(response){
		  setPredicate(response);
		},
		async:false
	});
	nb++;
}