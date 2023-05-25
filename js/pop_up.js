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

// Accueil.php, HistoriqueMoisson.php, FiltreConfiguration.php, FiltrePredicat.php
function closeForm(id=null) {
    if (id == null)
        document.getElementById("validateForm").style.display = "none";
    else
        document.getElementById("validateForm" + id).style.display = "none";
    document.getElementById("page-mask").style.display = "none";
}