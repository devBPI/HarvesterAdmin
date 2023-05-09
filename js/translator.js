var paterns=['X2','test'];

function ajaxResponOk(response)
{
    output.innerHTML=response;
}

function traduction()
{
	var input, output, patern;
	output=document.getElementById("output");
	input=document.getElementById("input").value;
	patern=document.getElementById("patern").value;
	var request = $.ajax({
		url: "../Controlleur/Translator.php",
		type: "post",
		data: 'input='+input+'&patern='+patern,
		success: function(response){
		  ajaxResponOk(response);
		},
		error:function(data){
			alert(data.statusText);
		}
	});

}