<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title>Génération du rapport au format CSV</title>
</head>
<body style="cursor: progress; transition: 5s" onload="getresult()">
<p id="paragraphe" style="font-size: 16px">Génération du rapport, veuillez patienter...<br/>Le téléchargement commencera juste après.</p>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
    /*function post(path, params, method='post') {
        const form = document.createElement('form');
        form.method = method;
        form.action = path;

        for (let [key, value] of params) {
            	console.log(key);
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = key;
                hiddenField.value = value;

                form.appendChild(hiddenField);
        }

        document.body.appendChild(form);
        console.log(form);
		form.submit();
    } */
	function getresult() {
		/*let map = new Map();
        map.set("report_type", "processus");
        map.set("submit_value", "generate");
        map.set("report_id", 10);
        map.set("generate_csv", "true");
        console.log(map); */
        //post("../Controlleur/RapportsGeneration.php", map);

		var myUrl = new URL(window.location.toLocaleString());
        var type = myUrl.searchParams.get("report_type");
        var id = myUrl.searchParams.get("id");
        var filename = myUrl.searchParams.get("name");
        var request = $.ajax({
            url: "../Controlleur/RapportsGeneration.php",
            type: "post",
            data: "report_type="+type+"&submit_value=generate&report_id="+id+"&generate_csv=true",
            success: function (response) {
                document.getElementById("paragraphe").innerHTML = response;
                let dl = document.createElement("a");
                let blobObject = new Blob(['\ufeff'+response],{
                    type: "text/csv;charset=utf-8;"
                });
                let date = new Date();
				let day = date.getDate();
                let month = date.getMonth()+1;
                let year = date.getFullYear();
                dl.href = URL.createObjectURL(blobObject);
                dl.download = filename + "__" + day + "_" + month + "_" + year + ".csv";

                dl.click();

                window.close();
			}
		});
    }

</script>
</body>
</html>
