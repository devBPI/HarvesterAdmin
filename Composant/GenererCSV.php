<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title>Génération du rapport au format CSV</title>
</head>
<body style="cursor: progress; transition: 5s" onload="getresult()">
<p id="paragraphe" style="font-size: 16px">Génération du rapport, veuillez patienter...<br/>Le téléchargement commencera juste après.</p>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
    /* var myUrl = new URL(window.location.toLocaleString());
    var type = myUrl.searchParams.get("report_type");
    var id = myUrl.searchParams.get("id");
	window.location.href="../Controlleur/RapportsGeneration.php?report_type="+type+"&submit_value=generate&report_id="+id+"&generate_csv=true"; */
	function getresult() {
		var myUrl = new URL(window.location.toLocaleString());
        var type = myUrl.searchParams.get("report_type");
        var id = myUrl.searchParams.get("id");
        var filename = myUrl.searchParams.get("name");
        var request = $.ajax({
            url: "../Controlleur/RapportsGeneration.php",
            type: "post",
            data: "report_type="+type+"&submit_value=generate&report_id="+id+"&generate_csv=true",
            success: function (response) {
                let dl = document.createElement("a");
                let blobObject = new Blob(['\ufeff'+response],{
                    type: "text/csv;charset=utf-8;"
                });
                let date = new Date();
				let day = date.getDate();
                if (day < 10)
                    day = "0"+day;
                let month = date.getMonth()+1;
                if (month < 10)
                	month = "0"+month;
                let year = date.getFullYear();
                dl.href = URL.createObjectURL(blobObject);
                dl.download = filename + "__" + day + "-" + month + "-" + year + ".csv";
                document.body.appendChild(dl);
                dl.click();
                document.body.appendChild(dl);
            	window.close();
			}
		});
    }

</script>
</body>
</html>
