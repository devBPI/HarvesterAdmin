// Accueil.php

// ValidParamétrage.php
function openForm() {
    document.getElementById("validateForm").style.display = "block";
    document.getElementById("page-mask").style.display = "block";
}

// Accueil.php
function openFormWithMsg(message) {
    document.getElementById("validateForm").style.display = "block";
    document.getElementById("page-mask").style.display = "block";
    document.getElementById("msgAlert").innerHTML = message;
}

// HistoriqueMoisson.php
function openFormWithId(id) {
    document.getElementById("validateForm" + id).style.display = "block";
    document.getElementById("page-mask").style.display = "block";
    //document.getElementById("msgAlert" + id).innerHTML = message.replaceAll("- ","<br/>");
}

// Accueil.php, HistoriqueMoisson.php, FiltreConfiguration.php, FiltrePredicat.php, TraductionConfiguration.php
function closeForm(id=null) {
    if (id == null)
        document.getElementById("validateForm").style.display = "none";
    else
        document.getElementById("validateForm" + id).style.display = "none";
    document.getElementById("page-mask").style.display = "none";
}

// FicheIndividuelle.php
// Rend les messages des alertes en pop-up jQuery
function makeJQueryUIPopUP(number_of_alerts) {
    for (let i = 1; i < number_of_alerts; i++) {
        $(function () {
            $("#alertPopUp" + i).dialog({
                autoOpen: false,
                width: 400,
                show: {
                    effect: "clip",
                    duration: 200
                },
                hide: {
                    effect: "clip",
                    duration: 200
                }
            });
            $("#alertOpener" + i).on("click", function () {
                $("#alertPopUp" + i).dialog("open");
            });
        });
    }
}